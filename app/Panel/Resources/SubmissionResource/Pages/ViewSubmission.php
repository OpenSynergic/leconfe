<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewSubmission extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    use InteractsWithRecord;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.view-submission';

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([]);
    }

    public function getTitle(): string
    {
        return $this->record->status == SubmissionStatus::Wizard ? 'Submission Wizard' : 'Submission';
    }
}
