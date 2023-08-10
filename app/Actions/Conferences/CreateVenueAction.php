<?php

namespace App\Actions\Conferences;

use App\Models\Venue;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateVenueAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            Venue::create($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
