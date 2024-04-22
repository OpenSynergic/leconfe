<?php

namespace App\Classes;

use App\Models\Setting;

class Settings
{
    protected $metaKeys = [];

    public function set($key, $value)
    {
        return Setting::updateOrCreate(['type' => gettype($value), 'key' => $key], ['value' => $value]);
    }

    public function get($key)
    {
        if ($setting = Setting::where('conference_id', app()->getCurrentConferenceId())->where('key', $key)->first()) {
            return $setting?->value;
        }
        $settingGlobal = Setting::where('conference_id', 0)->where('key', $key)->first();
        return $settingGlobal?->value;
    }

    public function all()
    {
        if (app()->getCurrentConferenceId() != 0) {
            $meta = Setting::pluck('value', 'key');
        } else {
            $meta = Setting::pluck('value', 'key');
        }

        if (isset($meta)) {
            $modifiedMeta = $meta->map(function ($value, $key) {
                if (strpos($key, 'settings.') === 0) {
                    $newKey = substr($key, strlen('settings.'));
                    return [$newKey => $value];
                }
                return [$key => $value];
            })->collapse();

            foreach ($modifiedMeta->keys() as $key) {
                $this->metaKeys[$key] = $modifiedMeta->get($key);
            }

            return $this->metaKeys;
        }
    }
}
