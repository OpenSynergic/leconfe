<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'conference_id',
        'guard_name',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('conferences', function (Builder $builder) {
            if(app()->getCurrentConferenceId()){
                $scopeColumn =  config('permission.table_names.roles', 'roles') . '.conference_id';
                $builder->where($scopeColumn, null)->orWhere($scopeColumn, app()->getCurrentConferenceId());
            }
        });
    }
}
