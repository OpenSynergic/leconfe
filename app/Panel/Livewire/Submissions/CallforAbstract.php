<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail\Discussions;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use App\Schemas\SubmissionFileSchema;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Livewire\Component;

class CallforAbstract extends Component implements HasForms, HasActions, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithInfolists;

    public Submission $submission;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function declineAction()
    {
        return Action::make('decline')
            ->outlined()
            ->color("danger")
            ->extraAttributes(['class' => 'w-full'], true)
            ->icon("lineawesome-times-circle-solid")
            ->requiresConfirmation();
    }

    public function acceptAction()
    {
        return Action::make('accept')
            ->outlined()
            ->record($this->submission)
            ->successNotificationTitle("Accepted")
            ->extraAttributes(['class' => 'w-full'])
            ->icon("lineawesome-check-circle-solid")
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'status' => SubmissionStatus::Accepted
                ], $this->submission);
                $action->success();
            })
            ->requiresConfirmation();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Submission files")
            // ->headerActions([
            //     ...SubmissionFilesTable::defaultHeaderActions()
            // ])
            ->query(function () {
                return $this->submission->files()->getQuery();
            })
            ->columns([
                ...SubmissionFileSchema::defaultTableColumns()
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                LivewireEntry::make('submission-files-table')
                    ->livewire(SubmissionFilesTable::class, [
                        'record' => $this->submission,
                        'category' => 'submission-files'
                    ])->columnSpanFull(),
                LivewireEntry::make('discussions')
                    ->livewire(Discussions::class, [
                        'submission' => $this->submission
                    ])->columnSpanFull(),
                // Grid::make(1)
                //     ->schema([
                //         LivewireEntry::make('Decision')
                //             ->livewire(Decision::class, [
                //                 'submission' => $this->submission
                //             ]),
                // LivewireEntry::make('Participant')
                //     ->livewire(Participants::class, [
                //         'submission' => $this->submission
                //     ])
                // ])
                // ->columnSpan(1),
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.call-for-abstract');
    }
}
