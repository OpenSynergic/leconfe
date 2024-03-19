<?php

namespace App\Panel\Conference\Resources\SubmissionResource\Pages;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Conference\Resources\SubmissionResource;
use Filament\Resources\Pages\Page;

class CompleteSubmission extends Page
{
    public Submission $record;

    protected static ?string $title = '';

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.conference.resources.submission-resource.pages.complete-submission';

    public function mount(Submission $record)
    {
        abort_if($record->status == SubmissionStatus::Incomplete, 404);
    }
}
