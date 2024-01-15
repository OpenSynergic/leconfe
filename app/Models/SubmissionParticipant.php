<?php

namespace App\Models;

use App\Models\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionParticipant extends Model
{
    use HasFactory;

    protected $table = 'submission_has_participants';

    protected $fillable = [
        'submission_id',
        'user_id',
        'role_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeEditor(Builder $builder)
    {
        $roleEditor = Role::where('name', UserRole::Editor->value)->first();

        return $builder->where('role_id', $roleEditor->getKey());
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
