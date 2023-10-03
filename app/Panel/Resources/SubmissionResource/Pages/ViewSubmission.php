<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStatus;
use App\Panel\Resources\SubmissionResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Infolists\Components\VerticalTabs\Tabs as Tabs;
use App\Infolists\Components\VerticalTabs\Tab as Tab;
use App\Panel\Livewire\Submissions\CallforAbstract;
use App\Panel\Livewire\Submissions\PeerReview;
use App\Panel\Livewire\Submissions\SubmissionDetail;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewSubmission extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.view-submission';

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function getHeading(): string
    {
        return $this->record->getMeta('title');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->sticky()
                    ->tabs([
                        Tab::make("Call for Abstract")
                            ->icon("heroicon-o-information-circle")
                            ->schema([
                                LivewireEntry::make('call-for-abstract')
                                    ->livewire(CallforAbstract::class, [
                                        'submission' => $this->record
                                    ])
                            ]),
                        Tab::make("Peer Review")
                            ->icon("iconpark-checklist-o")
                            ->schema([
                                LivewireEntry::make('peer-review')
                                    ->livewire(PeerReview::class, [
                                        'submission' => $this->record
                                    ])
                            ])
                    ])
                    ->maxWidth('full')
            ]);
    }

    public function getTitle(): string
    {
        return $this->record->status == SubmissionStatus::Wizard ? 'Submission Wizard' : 'Submission';
    }
}
