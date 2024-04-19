<?php

namespace App\Classes;

use App\Models\SiteSetting;

class Settings
{
    protected $metaKeys = [];

    public function set($key, $value)
    {
        if (is_array($key)) {
            if (app()->getCurrentConference() === null) {
                return SiteSetting::updateOrCreate(['conference_id' => 0, 'type' => gettype($value), 'key' => 'settings.' . $key], ['value' => $value]);
            }
            if (app()->getCurrentConference()) {
                return SiteSetting::updateOrCreate(['conference_id' => app()->getCurrentConference()->getOriginal('id'), 'type' => gettype($value), 'key' => 'settings.' . $key], ['value' => $value]);
            }
        }
        if (app()->getCurrentConference() === null) {
            return SiteSetting::updateOrCreate(['conference_id' => 0, 'type' => gettype($value), 'key' => 'settings.' . $key], ['value' => $value]);
        }
        if (app()->getCurrentConference()) {
            return SiteSetting::updateOrCreate(['conference_id' => app()->getCurrentConference()->getOriginal('id'), 'type' => gettype($value), 'key' => 'settings.' . $key], ['value' => $value]);
        }
    }

    public function get($key)
    {
        if (app()->getCurrentConference() === null) {
            return [
                $key => SiteSetting::where('conference_id', 0)->where('key', 'settings.' . $key)->latest()->pluck('value')->first()
            ];
        }
        if (SiteSetting::where('conference_id', app()->getCurrentConference()->getOriginal('id'))->where('key', 'settings.' . $key)->latest()->pluck('key')->first() && app()->getCurrentConference()) {
            return [
                $key => SiteSetting::where('conference_id', app()->getCurrentConference()->getOriginal('id'))->where('key', 'settings.' . $key)->latest()->pluck('value')->first()
            ];
        }
        return [
            $key => SiteSetting::where('conference_id', 0)->where('key', 'settings.' . $key)->latest()->pluck('value')->first()
        ];
    }

    public function all()
    {
        if (app()->getCurrentConference() === null) {
            $meta = SiteSetting::where('conference_id', 0)->pluck('value', 'key');
        }
        if (app()->getCurrentConference()) {
            $meta = SiteSetting::where('conference_id', app()->getCurrentConference()->getOriginal('id'))->pluck('value', 'key');
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
