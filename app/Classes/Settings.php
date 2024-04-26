<?php

namespace App\Classes;

use App\Models\Setting;

class Settings
{
    protected $settingKeys = [];

    public function set($key, $value)
    {
        return Setting::updateOrCreate(['conference_id' => app()->getCurrentConferenceId(), 'key' => $key], ['value' => $value, 'type' => gettype($value)]);
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
        return $this->castArrayValues($this->settingKeys);
    }

    public function castArrayValues(array $array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_null($value)) {
                $result[$key] = null;
            } else if ($value == 1 || $value == 0) {
                $result[$key] = (bool)$value;
            } else if (is_string($value) && $json = json_decode($value, true)) {
                $result[$key] = $json !== null ? $json : $value;
            } else if (is_numeric($value)) {
                $result[$key] = is_float($value) ? (float)$value : (int)$value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
