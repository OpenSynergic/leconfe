<?php

namespace App\Classes;

use App\Models\Setting;

class Settings
{
    protected $metaKeys = [];

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
        if (app()->getCurrentConferenceId() != 0) {
            $conferenceSetting = Setting::where('conference_id', app()->getCurrentConferenceId())->pluck('value', 'key');
            $globalSetting = Setting::where('conference_id', 0)->pluck('value', 'key');
            $conferenceSetting = $globalSetting->merge($conferenceSetting);
        } else {
            $conferenceSetting = Setting::where('conference_id', 0)->pluck('value', 'key');
        }

        foreach ($conferenceSetting->keys() as $key) {
            $this->metaKeys[$key] = $conferenceSetting->get($key);
        }

        return $this->metaKeys;
    }
}
