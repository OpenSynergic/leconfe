<?php

use Illuminate\Support\Arr;

if (! function_exists('data_only')) {
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
