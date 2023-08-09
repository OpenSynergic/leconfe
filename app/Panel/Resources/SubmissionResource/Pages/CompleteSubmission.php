<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Panel\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class CompleteSubmission extends Page
{
    public Submission $record;

    protected static ?string $title = '';

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.complete-submission';

    public function mount(Submission $record)
    {
        abort_if($record->status == Submission::STATUS_WIZARD, 404);
    }
}
