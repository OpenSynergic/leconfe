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
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
            return;
        }
        $this->attributes['value'] = $value;
    }

    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'integer':
                return (int)$value;
            case 'string':
                return (string)$value;
            case 'boolean':
                return (bool)$value;
            case 'float':
                return (float)$value;
            case 'array':
                return json_decode($value, true);
            case 'object':
                return json_decode($value);
            case 'date':
                return $value instanceof \DateTimeInterface ? $value : new \DateTimeImmutable($value);
            default:
                return $value;
        }
    }
}
