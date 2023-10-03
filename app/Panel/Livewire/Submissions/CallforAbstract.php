<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail\Decision;
use App\Panel\Livewire\Submissions\SubmissionDetail\Discussions;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class CallforAbstract extends Component implements HasForms, HasActions, HasTable
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithTable;

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
            // ->form([
            //     TextInput::make("OK")
            // ])
            ->action(function (Action $action) {
                // SubmissionUpdateAction::run([
                //     'status' => SubmissionStatus::Accepted
                // ], $this->submission);
                // $action->success();
            })
            ->requiresConfirmation();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Submission files")
            ->headerActions([
                ...SubmissionFilesTable::defaultHeaderActions()
            ])
            ->query(function () {
                return $this->submission->files()->getQuery();
            })
            ->columns([
                ...SubmissionFilesTable::defaultColumns()
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                LivewireEntry::make('submission-files-table')
                    ->livewire(SubmissionFilesTable::class, [
                        'record' => $this->submission,
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
