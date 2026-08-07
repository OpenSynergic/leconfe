<?php

namespace App\Actions\Analytics;

use Throwable;
use App\Models\AnalyticMetric;
use App\Models\AnalyticTemporaryRecord;
use App\Models\ScheduledConference;
use App\Services\Analytics\AnalyticStatsEngine;
use App\Services\Analytics\GeoLocationTool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessAnalyticStatsAction
{
    use AsAction;

    public const DOUBLE_CLICK_HTML = 10;   // 10 Seconds COUNTER Standard
    public const DOUBLE_CLICK_FILE = 30;   // 30 Seconds COUNTER Standard

    /**
     * Regex pattern to identify bot user agents based on OJS / COUNTER standard.
     */
    protected string $botRegex = '/(bot|googlebot|bingbot|crawler|spider|slurp|duckduckbot|baiduspider|yandexbot|facebookexternalhit|twitterbot|ahrefsbot|semrushbot|dotbot|rogue|python|curl|wget|httpclient|postman|php|guzzle|libwww-perl)/i';

    public function handle(bool $force = false): int
    {
        $engine = app(AnalyticStatsEngine::class);
        $engine->autoStage($force);
        $stagedFiles = $engine->getStagedFiles();
        $processedCount = 0;

        foreach ($stagedFiles as $fileInfo) {
            $stagedFile = is_string($fileInfo) ? $fileInfo : $fileInfo->getRealPath();
            $processingFile = $engine->moveToProcessing($stagedFile);
            $loadId = basename($processingFile);

            try {
                DB::beginTransaction();

                $validRecords = $this->parseAndFilterLogFile($processingFile, $loadId);

                if (!empty($validRecords)) {
                    // Batch insert to temporary staging table
                    foreach (array_chunk($validRecords, 500) as $chunk) {
                        AnalyticTemporaryRecord::insert($chunk);
                    }

                    // Aggregate & Transfer to OLAP Metrics table
                    $this->aggregateAndTransferToMetrics($loadId);
                }

                DB::commit();

                $engine->archiveFile($processingFile);
                $processedCount++;
            } catch (Throwable $th) {
                DB::rollBack();
                Log::error("Failed to process AnalyticStats log file [{$loadId}]: " . $th->getMessage());
                $engine->rejectFile($processingFile);
            }
        }

        return $processedCount;
    }

    /**
     * Parse log file, filter bots and COUNTER double-click entries.
     */
    protected function parseAndFilterLogFile(string $filePath, string $loadId): array
    {
        $geoTool = app(GeoLocationTool::class);
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        $lastEntries = [];
        $validRecords = [];
        $now = now()->toDateTimeString();

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Extract ASSOC_TYPE, ASSOC_ID, FILE_TYPE
            if (!preg_match('/^(\S+)\s+(\S+)\s+(\S+)\s+\[(.*?)\]\s+"GET\s+(\S+)\s+HTTP\/1\.1"\s+(\d+)\s+\d+\s+"-"\s+"([^"]+)"\s+ASSOC_TYPE=(\d+)\s+ASSOC_ID=(\d+)\s+FILE_TYPE=(\d+)/', $line, $matches)) {
                continue;
            }

            $hashedIp = $matches[1];
            $userRole = $matches[2];
            $timestampStr = $matches[4];
            $url = $matches[5];
            $httpStatus = (int)$matches[6];
            $userAgent = $matches[7];
            $assocType = (int)$matches[8];
            $assocId = (int)$matches[9];
            $fileType = (int)$matches[10];

            // 1. HTTP Status Filter (Must be 200 or 304)
            if (!in_array($httpStatus, [200, 304])) {
                continue;
            }

            // 2. Bot User-Agent Filter
            if (preg_match($this->botRegex, $userAgent)) {
                continue;
            }

            // 3. COUNTER Double-Click Filter (<10s HTML / <30s File)
            $timestamp = strtotime($timestampStr) ?: time();
            $entryHash = "{$assocType}_{$assocId}_{$hashedIp}";
            $timeThreshold = ($fileType > 0 || $assocType === 4) ? self::DOUBLE_CLICK_FILE : self::DOUBLE_CLICK_HTML;

            if (isset($lastEntries[$entryHash])) {
                $timeDiff = $timestamp - $lastEntries[$entryHash];
                if ($timeDiff < $timeThreshold) {
                    // Duplicate hit within double-click window -> drop hit
                    continue;
                }
            }

            $lastEntries[$entryHash] = $timestamp;
            $location = $geoTool->getLocation($hashedIp);

            $validRecords[] = [
                'assoc_type' => $assocType,
                'assoc_id'   => $assocId,
                'day'        => (int)date('Ymd', $timestamp),
                'entry_time' => $timestamp,
                'metric'     => 1,
                'country_id' => $location['country_id'],
                'region'     => $location['region'],
                'city'       => $location['city'],
                'load_id'    => $loadId,
                'file_type'  => $fileType ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);
        return $validRecords;
    }

    /**
     * Group aggregate temporary records and insert into analytic_metrics table.
     */
    protected function aggregateAndTransferToMetrics(string $loadId): void
    {
        // 1. Purge existing records for load_id to prevent duplicates
        AnalyticMetric::where('load_id', $loadId)->delete();

        // 2. Aggregate temporary records by assoc_type, assoc_id, day, country_id, region, city, file_type
        $aggregated = AnalyticTemporaryRecord::where('load_id', $loadId)
            ->selectRaw('assoc_type, assoc_id, day, country_id, region, city, file_type, count(metric) as total_metric')
            ->groupBy('assoc_type', 'assoc_id', 'day', 'country_id', 'region', 'city', 'file_type')
            ->get();

        $metricsToInsert = [];
        $now = now()->toDateTimeString();

        foreach ($aggregated as $row) {
            $confId = 1;
            $scheduledConfId = 1;
            $proceedingId = null;
            $submissionId = null;

            if ($row->assoc_type == 1) { // ASSOC_TYPE_SCHEDULED_CONFERENCE
                $scheduledConfId = $row->assoc_id;
                $scheduledConf = ScheduledConference::find($scheduledConfId);
                if ($scheduledConf) {
                    $confId = $scheduledConf->conference_id;
                }
            } elseif ($row->assoc_type == 2) { // ASSOC_TYPE_PROCEEDING
                $proceedingId = $row->assoc_id;
                $proceeding = \App\Models\Proceeding::find($proceedingId);
                if ($proceeding) {
                    $confId = $proceeding->conference_id;
                    $scheduledConfId = $proceeding->scheduled_conference_id ?? 1;
                }
            } elseif ($row->assoc_type == 3) { // ASSOC_TYPE_SUBMISSION
                $submissionId = $row->assoc_id;
                $submission = \App\Models\Submission::find($submissionId);
                if ($submission) {
                    $confId = $submission->conference_id;
                    $scheduledConfId = $submission->scheduled_conference_id ?? 1;
                }
            } elseif ($row->assoc_type == 4) { // ASSOC_TYPE_GALLEY
                $galley = \App\Models\SubmissionGalley::find($row->assoc_id);
                if ($galley && $galley->submission) {
                    $submissionId = $galley->submission_id;
                    $confId = $galley->submission->conference_id;
                    $scheduledConfId = $galley->submission->scheduled_conference_id ?? 1;
                } else {
                    $submission = \App\Models\Submission::find($row->assoc_id);
                    if ($submission) {
                        $submissionId = $submission->id;
                        $confId = $submission->conference_id;
                        $scheduledConfId = $submission->scheduled_conference_id ?? 1;
                    }
                }
            }

            $dayStr = (string)$row->day;
            $monthStr = substr($dayStr, 0, 6);

            $metricsToInsert[] = [
                'load_id'                 => $loadId,
                'conference_id'           => $confId,
                'scheduled_conference_id' => $scheduledConfId,
                'proceeding_id'           => $proceedingId,
                'submission_id'           => $submissionId,
                'assoc_type'              => $row->assoc_type,
                'assoc_id'                => $row->assoc_id,
                'day'                     => $dayStr,
                'month'                   => $monthStr,
                'file_type'               => $row->file_type,
                'country_id'              => $row->country_id,
                'region'                  => $row->region,
                'city'                    => $row->city,
                'metric_type'             => 'leconfe::analytic',
                'metric'                  => $row->total_metric,
                'created_at'              => $now,
                'updated_at'              => $now,
            ];
        }

        if (!empty($metricsToInsert)) {
            foreach (array_chunk($metricsToInsert, 500) as $chunk) {
                AnalyticMetric::insert($chunk);
            }
        }

        // Clean temporary staging table for this load_id
        AnalyticTemporaryRecord::where('load_id', $loadId)->delete();
    }
}
