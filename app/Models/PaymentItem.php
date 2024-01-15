<?php

namespace App\Models;

use Akaunting\Money;
use App\Models\Concerns\BelongsToConference;
use App\Models\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class PaymentItem extends Model implements Sortable
{
    use BelongsToConference, SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'fees',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fees' => 'array',
        'type' => PaymentType::class,
    ];

    public function getAmount($currencyId)
    {
        $fee = collect($this->fees)->firstWhere('currency_id', $currencyId);

        if ($fee === null) {
            return null;
        }

        return $fee['fee'];
    }

    public function getFormattedAmount($currencyId)
    {
        return (new Money\Money(
            $this->getAmount($currencyId),
            (new Money\Currency(strtoupper($currencyId))),
            true
        ))->formatWithoutZeroes();
    }
}
