<?php

namespace App\Classes;

use App\Models\Setting;

class Settings
{
    protected $settingKeys = [];

    public function set($key, $value)
    {
        return Setting::updateOrCreate(['conference_id' => app()->getCurrentConferenceId(), 'type' => gettype($value), 'key' => $key], ['value' => $value]);
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
        $conferenceSetting = Setting::where('conference_id', app()->getCurrentConferenceId())->pluck('value', 'key');
        $globalSetting = Setting::where('conference_id', 0)->pluck('value', 'key');
        $setting = $globalSetting->merge($conferenceSetting);

        foreach ($setting->keys() as $key) {
            $this->settingKeys[$key] = $setting->get($key);
        }
        return $this->settingKeys;
    }
}
