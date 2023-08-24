<?php

namespace App\Utils;

use Illuminate\Support\Arr;

class PermissionChecker
{
    public function checkFolder($path)
    {
        $path = base_path($path);

        if (! is_dir($path)) {
            return false;
        }

        if (! is_writable($path)) {
            return false;
        }

        return true;
    }

    public function checkFolders(array $paths)
    {
        $paths = Arr::mapWithKeys($paths, function ($path, $key) {
            return [$path => $this->checkFolder($path)];
        });

        return $paths;
    }

    public function isFoldersWritable(array $paths)
    {
        foreach ($paths as $path) {
            if (! $this->checkFolder($path)) {
                return false;
            }
        }

        return true;
    }
}
