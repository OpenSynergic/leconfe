<?php

namespace App\Classes;

use App\Models\Conference;

class Settings
{
    protected $parsed_url;
    protected $path_segments;
    protected $getConference;
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
        if ($this->getConference->hasMeta($key) && app()->getCurrentConference()->getOriginal('path') === $this->path_segments[0]) {
            return $this->getConference->setMeta($key, $value);
        }
    }

    public function get($key)
    {
        if ($this->getConference === null) {
            return [
                $key => app()->getSite()->getMeta($key)
            ];
        }
        if ($this->getConference->hasMeta($key) && app()->getCurrentConference()->getOriginal('path') === $this->path_segments[0]) {
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
            $allMeta = app()->getSite()->getAllMeta();
            foreach ($allMeta->keys() as $key) {
                $metaKeys[$key] = $allMeta->get($key);
            }
            return $metaKeys;
        }
    }
}
