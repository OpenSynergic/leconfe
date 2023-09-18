<?php

namespace App\Actions\SubmissionFiles;

use App\Models\Conference;
use App\Models\SubmissionFileType;
use Lorisleiva\Actions\Concerns\AsAction;

class FilesTypePopulateAction
{
    use AsAction;

    public function handle(Conference $conference)
    {
        foreach ([
            'Abstract',
            'Full Paper',
            'Pamflet',
            'Poster',
        ] as $type) {
            SubmissionFileType::firstOrCreate([
                'conference_id' => $conference->getKey(),
                'name' => $type,
            ]);
        }
    }
}
