<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Panel\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewSubmission extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.view-submission';

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function getTitle(): string
    {
        return $this->record->status == Submission::STATUS_WIZARD ? 'Submission Wizard' : 'Submission';
    }
}
