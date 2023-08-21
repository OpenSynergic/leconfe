<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CommitteeMember extends Model implements Sortable
{
    use HasFactory, SortableTrait;
    protected $fillable = ['committee_id', 'name'];


    public function buildSortQuery()
    {
        return static::query()->where('committee_id', $this->committee_id);
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }
}
