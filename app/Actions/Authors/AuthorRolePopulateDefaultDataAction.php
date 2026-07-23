<?php

namespace App\Actions\Authors;

use Throwable;
use App\Models\AuthorRole;
use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AuthorRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            $author = AuthorRole::firstOrCreate([
                'name' => 'Author',
                'conference_id' => $conference->getKey(),
            ]);

            $translator = AuthorRole::firstOrCreate([
                'name' => 'Translator',
                'conference_id' => $conference->getKey(),
            ]);

            $conference->setManyMeta([
                'citation_contributor_authors' => [$author->getKey()],
                'citation_contributor_translators' => [$translator->getKey()],
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
