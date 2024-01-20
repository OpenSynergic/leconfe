<?php

namespace App\Utils;

use App\Utils\UpgradeSchemas;
class UpgradeSchema 
{
    public static $schemas = [
        // 
    ];

    public static function getSchemasByVersion(string $installedVersion, string $applicationVersion)
    {
        $filteredActions = [];

        foreach (static::$schemas as $key => $value) {
            // filter upgrade script by comparing to database version and application version
            if (version_compare($installedVersion, $key, '<') && version_compare($applicationVersion, $key, '>=')) {
                $filteredActions[$key] = new $value($installedVersion, $applicationVersion);
            }
        }

        return $filteredActions;
    }
}