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
            $venue = Venue::create($data);
            return $venue;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
