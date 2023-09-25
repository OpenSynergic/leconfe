<?php

namespace App\Models\Scopes;

use App\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConferenceScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('conference_id', app()->getCurrentConference()?->getKey() ?? Application::CONTEXT_WEBSITE);
    }
}
