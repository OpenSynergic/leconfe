<?php

namespace App\Classes;

class Settings
{
    protected $parsed_url;
    protected $path_segments;
    protected $getConference;
    protected $settings = [];
    protected $metaKeys = [];

    public function __construct()
    {
        $this->parsed_url = parse_url(url()->current());
        $this->path_segments = array_values(array_filter(explode('/', $this->parsed_url['path'])));
        $this->getConference = app()->getCurrentConference();
    }

    public function set($key, $value)
    {
        if ($this->getConference === null) {
            return app()->getSite()->setMeta($key, $value);
        }
        if ($this->getConference->hasMeta($key) && $this->getConference) {
            return $this->getConference->setMeta($key, $value);
        }
    }

    public function setMany($key)
    {
        if (is_array($key)) {
            if ($this->getConference === null) {
                return app()->getSite()->setManyMeta($key);
            }
            if ($this->getConference->hasMeta($key) && $this->getConference) {
                return $this->getConference->setManyMeta($key);
            }
        }
    }

    public function get($key)
    {
        if ($this->getConference === null) {
            return [
                $key => app()->getSite()->getMeta($key)
            ];
        }
        if ($this->getConference->hasMeta($key) && $this->getConference) {
            return [
                $key => $this->getConference->getMeta($key)
            ];
        }
        return [
            $key => app()->getSite()->getMeta($key)
        ];
    }

    public function all()
    {
        if ($this->getConference === null) {
            $meta = app()->getSite()->getAllMeta();
        } elseif ($this->getConference) {
            $meta = $this->getConference->getAllMeta();
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
