<?php

namespace App\Actions\SubmissionFiles;

use App\Models\SubmissionFileType;
use Lorisleiva\Actions\Concerns\AsAction;

class FilesTypePopulateAction
{
    use AsAction;

    public function handle()
    {
        foreach ([
            'Abstract',
            'Full Paper',
            'Pamflet',
            'Poster',
        ] as $type) {
            SubmissionFileType::firstOrCreate([
                'name' => $type,
            ]);
        }
    }
}
