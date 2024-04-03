<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    abstract public function getModel(): Model;

    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        if (array_key_exists('meta', $data) && is_array($data['meta'])) {
            $model->setManyMeta($data['meta']);
        }

        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}
