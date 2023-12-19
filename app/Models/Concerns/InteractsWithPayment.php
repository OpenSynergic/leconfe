<?php

namespace App\Models\Concerns;

use App\Models\Payment;
use App\Models\Topic;
use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait InteractsWithPayment
{
    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
