<?php

namespace App\Classes;

use App\Models\Scopes\ConferenceScope;
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
        if (app()->getCurrentConferenceId()) {
            return [
                $key => Setting::where('key', $key)->latest()->pluck('value')->first()
            ];
        }
        return [
            $key => Setting::withoutGlobalScope(ConferenceScope::class)->where('key', $key)->latest()->pluck('value')->get()
        ];
    }

    public function all()
    {
        if (app()->getCurrentConferenceId()) {
            $meta = Setting::pluck('value', 'key');
        }
        $meta = Setting::withoutGlobalScope(ConferenceScope::class)->pluck('value', 'key');

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
