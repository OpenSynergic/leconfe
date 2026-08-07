<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use App\Models\Concerns\BelongsToScheduledConference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticMetric extends Model
{
    use BelongsToConference, BelongsToScheduledConference, HasFactory;

    protected $table = 'analytic_metrics';

    protected $fillable = [
        'load_id',
        'conference_id',
        'scheduled_conference_id',
        'proceeding_id',
        'submission_id',
        'assoc_type',
        'assoc_id',
        'day',
        'month',
        'file_type',
        'country_id',
        'region',
        'city',
        'metric_type',
        'metric',
    ];

    public function proceeding(): BelongsTo
    {
        return $this->belongsTo(Proceeding::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
