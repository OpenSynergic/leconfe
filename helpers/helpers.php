<?php

use Illuminate\Support\Arr;

if (!function_exists('data_only')) {
    /**
     * Get a subset containing the provided keys with values from the target data.
     *
     * @param  mixed  $target
     * @param  array|mixed  $keys
     * @return array
     */
    function data_only($target, $keys)
    {
        $results = [];

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($target, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }
}

if (!function_exists('get_navigation_link_by_type')) {
    function get_navigation_link(?string $type, string $default = '#'): string
    {
        return match ($type) {
            'announcements' => route('livewirePageGroup.current-conference.pages.announcement-list'),
            'register' => route('livewirePageGroup.website.pages.register'),
            'login' => route('livewirePageGroup.website.pages.login'),
            'home' => route('livewirePageGroup.website.pages.home'),
            'about' => route('livewirePageGroup.current-conference.pages.about'),
            default => $default,
        };
    }
}

if (!function_exists('castArrayValues')) {
    function castArrayValues(array $array)
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

if (!function_exists('transformSettings')) {
    function transformSettings($settings)
    {
        $formattedSettings = [];

        foreach ($settings as $key => $value) {
            $currentArray = &$formattedSettings;
            $keys = explode(".", $key);

            foreach ($keys as $index => $keyPart) {
                if ($index === count($keys) - 1) {
                    $currentArray[$keyPart] = $value;
                } else {
                    if (!isset($currentArray[$keyPart])) {
                        $currentArray[$keyPart] = [];
                    }
                    $currentArray = &$currentArray[$keyPart];
                }
            }
        }

        return $formattedSettings;
    }
}
