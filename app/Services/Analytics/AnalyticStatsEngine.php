<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\File;

class AnalyticStatsEngine
{
    protected string $logsDir;
    protected string $stageDir;
    protected string $processingDir;
    protected string $archiveDir;
    protected string $rejectDir;

    public function __construct()
    {
        $base = storage_path('app/analytics');
        $this->logsDir = $base . '/logs';
        $this->stageDir = $base . '/stage';
        $this->processingDir = $base . '/processing';
        $this->archiveDir = $base . '/archive';
        $this->rejectDir = $base . '/reject';

        foreach ([$this->logsDir, $this->stageDir, $this->processingDir, $this->archiveDir, $this->rejectDir] as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
        }
    }

    /**
     * Auto-stage log files from logs/ to stage/ (except today's active log file).
     */
    public function autoStage(bool $force = false): array
    {
        $todayLog = sprintf('analytic_events_%s.log', date('Ymd'));
        $stagedFiles = [];

        if (File::exists($this->logsDir)) {
            $files = File::files($this->logsDir);
            foreach ($files as $file) {
                if (($force || $file->getFilename() !== $todayLog) && $file->getExtension() === 'log') {
                    $target = $this->stageDir . '/' . $file->getFilename();
                    File::move($file->getRealPath(), $target);
                    $stagedFiles[] = $target;
                }
            }
        }

        return $stagedFiles;
    }

    /**
     * Move log file from stage/ to processing/.
     */
    public function moveToProcessing(string $filePath): string
    {
        if (dirname($filePath) === $this->processingDir) {
            return $filePath;
        }
        $filename = basename($filePath);
        $target = $this->processingDir . '/' . $filename;
        File::move($filePath, $target);
        return $target;
    }

    /**
     * Move processed log file to archive/.
     */
    public function archiveFile(string $filePath): void
    {
        $filename = basename($filePath);
        $target = $this->archiveDir . '/' . $filename;
        File::move($filePath, $target);
    }

    /**
     * Move failed log file to reject/.
     */
    public function rejectFile(string $filePath): void
    {
        $filename = basename($filePath);
        $target = $this->rejectDir . '/' . $filename;
        File::move($filePath, $target);
    }

    public function getStagedFiles(): array
    {
        $staged = File::files($this->stageDir);
        $processing = File::files($this->processingDir);
        return array_merge($staged, $processing);
    }
}
