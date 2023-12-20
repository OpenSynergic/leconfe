<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasPayment
{
    public function payment(): MorphOne;
}
