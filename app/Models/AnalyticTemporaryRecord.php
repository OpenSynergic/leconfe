<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use App\Models\Concerns\BelongsToScheduledConference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticTemporaryRecord extends Model
{
    use BelongsToConference, BelongsToScheduledConference, HasFactory;

    protected $table = 'analytic_temporary_records';

    protected $fillable = [
        'conference_id',
        'scheduled_conference_id',
        'assoc_type',
        'assoc_id',
        'day',
        'entry_time',
        'metric',
        'country_id',
        'region',
        'city',
        'load_id',
        'file_type',
    ];
}
