<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
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
