<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory, BelongsToConference;

    protected $table = 'setting';

    protected $fillable = [
        'type',
        'key',
        'value',
    ];

    public function setValueAttribute($value)
    {
        if (is_bool($value)) {
            $this->attributes['value'] = $value ? 1 : 0;
            return;
        }
        $this->attributes['value'] = $value;
    }

    public function getValueAttribute($value)
    {
        return (bool) $value;
    }
}
