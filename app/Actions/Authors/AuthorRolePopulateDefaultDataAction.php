<?php

namespace App\Actions\Authors;

use App\Models\AuthorRole;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AuthorRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                UserRole::Author->value,
                'Co Author',
            ] as $authorRole) {
                AuthorRole::firstOrCreate([
                    'name' => $authorRole,
                    'conference_id' => $conference->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
