<?php

namespace App\Actions\Authors;

use App\Models\ContributorRole;
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
                'Presenter',
            ] as $authorRole) {
                ContributorRole::firstOrCreate([
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
