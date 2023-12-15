<?php

namespace App\Models;

use App\Models\Enums\PaymentState;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'state' => PaymentState::Pending,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'state' => PaymentState::class,
    ];
}
