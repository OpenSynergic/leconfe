<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail\Discussions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Livewire\Component;

class CallforAbstract extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Submission $submission;

    public function declineAction()
    {
        return Action::make('decline')
            ->outlined()
            ->color("danger")
            ->modalWidth("2xl")
            ->record($this->submission)
            ->modalHeading("Confirmation")
            ->modalSubmitActionLabel("Decline")
            ->extraAttributes(['class' => 'w-full'], true)
            ->form([
                Fieldset::make("Notification")
                    ->columns(1)
                    ->schema([
                        Checkbox::make('no-notification')
                            ->label("Don't send notification to author")
                            ->reactive()
                            ->default(false),
                        TextInput::make('email')
                            ->hidden(fn (Get $get): bool => $get('no-notification'))
                            ->disabled()
                            ->formatStateUsing(fn (Submission $record): string => $record->user->email),
                        RichEditor::make('message')
                            ->hidden(fn (Get $get): bool => $get('no-notification'))
                            ->disableToolbarButtons([
                                'assignFiles'
                            ])
                    ])
            ])
            ->icon("lineawesome-times-circle-solid");
    }

    public function acceptAction()
    {
        return Action::make('accept')
            ->modalHeading("Confirmation")
            ->modalSubmitActionLabel("Accept")
            ->modalWidth("2xl")
            ->outlined()
            ->record($this->submission)
            ->successNotificationTitle("Accepted")
            ->extraAttributes(['class' => 'w-full'])
            ->icon("lineawesome-check-circle-solid")
            ->form([
                Fieldset::make("Notification")
                    ->columns(1)
                    ->schema([
                        /**
                         * TODO:
                         * - We need to create a function for it because it is used frequently.
                         * 
                         * Something like:
                         *   UserNotificaiton::formSchema()
                         */
                        Checkbox::make('no-notification')
                            ->label("Don't send notification to author")
                            ->reactive()
                            ->default(false),
                        TextInput::make('email')
                            ->hidden(fn (Get $get): bool => $get('no-notification'))
                            ->disabled()
                            ->formatStateUsing(fn (Submission $record): string => $record->user->email),
                        RichEditor::make('message')
                            ->hidden(fn (Get $get): bool => $get('no-notification'))
                            ->disableToolbarButtons([
                                'assignFiles'
                            ])
                    ])
            ])
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'stage' => SubmissionStage::PeerReview,
                    'status' => SubmissionStatus::OnReview
                ], $this->submission);
                /**
                 * TODO: 
                 * -  Send notificaion
                 */
                $this->dispatch("refreshPeerReview");
                $action->success();
            });
    }


    public function render()
    {
        return view('panel.livewire.submissions.call-for-abstract', [
            'reviewStageOpen' => app()->getCurrentConference()->getMeta("workflow.peer-review.open", false),
        ]);
    }
}
