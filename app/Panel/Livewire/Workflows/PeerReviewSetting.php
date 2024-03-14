<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class PeerReviewSetting extends WorkflowStage implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected ?string $stage = 'peer-review';

    protected ?string $stageLabel = 'Peer Review';

    public function mount()
    {
        $this->form->fill([
            'settings' => [
                'allowed_file_types' => $this->getSetting('allowed_file_types', ['pdf', 'docx', 'doc']),
                'start_at' => $this->getSetting('start_at', now()->addDays(1)->format('d F Y')),
                'end_at' => $this->getSetting('end_at', now()->addDays(14)->format('d F Y')),
                'invitation_response_days' => $this->getSetting('invitation_response_days', 14),
            ],
        ]);
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->icon('lineawesome-save-solid')
            ->label('save')
            ->successNotificationTitle('Setting saved')
            ->action(function (Action $action) {
                $this->form->validate();
                $data = $this->form->getState();
                foreach ($data['settings'] as $key => $value) {
                    $this->updateSetting($key, $value);
                }
                $action->success();
            });
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shout::make('settings.stage-closed')
                    ->hidden(fn (): bool => $this->isStageOpen())
                    ->color('warning')
                    ->content('The call for abstracts is not open yet, Start now or schedule opening'),
                Grid::make()
                    ->schema([
                        TagsInput::make('settings.allowed_file_types')
                            ->label('Allowed File Types')
                            ->helperText('Allowed file types')
                            ->splitKeys([',', 'enter', ' ']),
                        /**
                         * Question:
                         * 1. Should add min and max size?
                         * 2. Should add max number of files?
                         * 3. is the acceptedFileTypes is enough?
                         */
                        SpatieMediaLibraryFileUpload::make('settings.paper_templates')
                            ->model($this->conference)
                            ->previewable(false)
                            ->downloadable()
                            ->disk('private-files')
                            ->preserveFilenames()
                            ->visibility('private')
                            ->collection('paper-templates')
                            ->acceptedFileTypes(
                                ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                            )
                            ->helperText('Upload paper templates')
                            ->saveRelationshipsUsing(
                                static fn (SpatieMediaLibraryFileUpload $component) => $component->saveUploadedFiles()
                            )
                            ->label('Paper templates'),
                        TextInput::make('settings.invitation_response_days')
                            ->label('Invitation Response Deadline')
                            ->default(14)
                            ->helperText('Deadline for reviewers to respond to invitations')
                            ->numeric()
                            ->minLength(2)
                            ->columns(1)
                            ->suffix('Days'),
                        Fieldset::make('Review Deadline')
                            ->schema([
                                DatePicker::make('settings.start_at')
                                    ->label('Date start'),
                                DatePicker::make('settings.end_at')
                                    ->label('Date end'),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.peer-review-setting');
    }
}
