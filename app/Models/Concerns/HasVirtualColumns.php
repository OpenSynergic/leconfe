<?php

namespace App\Models\Concerns;

trait HasVirtualColumns
{
    public function save(array $options = [])
    {
        if (isset($this->virtualColumns)) {
            $this->attributes = array_diff_key($this->attributes, array_flip($this->virtualColumns));
        }

        return parent::save($options);
    }
}
