<?php

namespace App\Services\Analytics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class AnalyticEventGenerator
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/analytics/logs');
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Write an anonymized log entry to daily log file.
     */
    public function logEvent(Request $request, int $assocType, int $assocId, ?int $fileType = null): void
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $salt = $this->getDailySalt();
        $hashedIp = hash('sha256', $ip . $salt);

        $userId = auth()->check() ? auth()->id() : '-';
        $time = date('d/M/Y:H:i:s O');
        $url = $request->fullUrl();
        $userAgent = $request->userAgent() ?? 'Unknown';
        $classification = auth()->check() ? 'administrative' : 'user';

        // Apache Combined Log Format variant with custom key-values
        $logLine = sprintf(
            '%s %s %s [%s] "GET %s HTTP/1.1" 200 0 "-" "%s" ASSOC_TYPE=%d ASSOC_ID=%d FILE_TYPE=%s' . PHP_EOL,
            $hashedIp,
            $classification,
            $userId,
            $time,
            $url,
            $userAgent,
            $assocType,
            $assocId,
            $fileType ?? '0'
        );

        $fileName = sprintf('analytic_events_%s.log', date('Ymd'));
        $filePath = $this->storagePath . '/' . $fileName;

        $fp = fopen($filePath, 'a');
        if ($fp) {
            if (flock($fp, LOCK_EX)) {
                fwrite($fp, $logLine);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

    /**
     * Get or rotate daily 16-byte random salt, cached in memory per day.
     */
    protected function getDailySalt(): string
    {
        $cacheKey = 'analytic_daily_salt_' . date('Ymd');

        return Cache::remember($cacheKey, 86400, function () {
            return $this->getDailySaltFromFile();
        });
    }

    /**
     * Read or generate daily 16-byte random salt from file stream.
     */
    protected function getDailySaltFromFile(): string
    {
        $saltPath = storage_path('app/analytics/salt.txt');
        $directory = dirname($saltPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $today = date('Ymd');

        if (File::exists($saltPath)) {
            $fp = fopen($saltPath, 'c+');
            if ($fp && flock($fp, LOCK_EX)) {
                $content = trim(stream_get_contents($fp));
                $mtime = date('Ymd', filemtime($saltPath));
                if ($mtime === $today && !empty($content)) {
                    flock($fp, LOCK_UN);
                    fclose($fp);
                    return $content;
                }

                // Daily rotation required
                ftruncate($fp, 0);
                rewind($fp);
                $newSalt = bin2hex(random_bytes(16));
                fwrite($fp, $newSalt);
                fflush($fp);
                flock($fp, LOCK_UN);
                fclose($fp);
                return $newSalt;
            }
            if ($fp) {
                fclose($fp);
            }
        }

        // Generate secure 16-byte random salt
        $newSalt = bin2hex(random_bytes(16));
        $fp = fopen($saltPath, 'c+');
        if ($fp) {
            if (flock($fp, LOCK_EX)) {
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, $newSalt);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

        return $newSalt;
    }
}
