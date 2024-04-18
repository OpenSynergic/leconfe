<?php

namespace App\Classes;

class Settings
{
    protected $metaKeys = [];

    public function set($key, $value)
    {
        if (app()->getCurrentConference() === null) {
            return app()->getSite()->setMeta('settings.' . $key, $value);
        }
        if (app()->getCurrentConference()->hasMeta('settings.' . $key) && app()->getCurrentConference()) {
            return app()->getCurrentConference()->setMeta('settings.' . $key, $value);
        }
    }

    public function setMany($key)
    {
        if (is_array($key)) {
            if (app()->getCurrentConference() === null) {
                return app()->getSite()->setManyMeta($key);
            }
            if (app()->getCurrentConference()->hasMeta('settings.' . $key) && app()->getCurrentConference()) {
                return app()->getCurrentConference()->setManyMeta($key);
            }
        }
    }

    public function get($key)
    {
        if (app()->getCurrentConference() === null) {
            return [
                $key => app()->getSite()->getMeta('settings.' . $key)
            ];
        }
        if (app()->getCurrentConference()->hasMeta('settings.' . $key) && app()->getCurrentConference()) {
            return [
                $key => app()->getCurrentConference()->getMeta('settings.' . $key)
            ];
        }
        return [
            $key => app()->getSite()->getMeta('settings.' . $key)
        ];
    }

    public function all()
    {
        if (app()->getCurrentConference() === null) {
            $meta = app()->getSite()->getAllMeta();
        } elseif (app()->getCurrentConference()) {
            $meta = app()->getCurrentConference()->getAllMeta();
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
