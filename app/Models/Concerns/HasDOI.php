<?php

namespace App\Models\Concerns;

use App\Models\DOI;
use App\Models\Topic;
use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasDOI
{
    /**
     * Get the model's DOI.
     */
    public function doi(): MorphOne
    {
        return $this->morphOne(DOI::class, 'doiable');
    }
}
