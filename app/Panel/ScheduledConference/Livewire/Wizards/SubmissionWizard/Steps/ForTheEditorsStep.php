<?php

namespace App\Panel\ScheduledConference\Livewire\Wizards\SubmissionWizard\Steps;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Components\Section;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Forms\Components\TinyEditor;
use App\Models\Submission;
use App\Panel\ScheduledConference\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ForTheEditorsStep extends Component implements HasForms, HasWizardStep, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public function mount($record)
    {
        $this->form->fill([
            'meta' => $record->getAllMeta(),
            'submission_progress' => 'review',
        ]);
    }

    public static function getWizardLabel(): string
    {
        return 'For the Editors';
    }

    public function render()
    {
        return view('panel.scheduledConference.livewire.wizards.submission-wizard.steps.for-the-editors-step');
    }

    protected function getFormModel(): string
    {
        return $this->record;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Section::make('For the Editors')
                        ->description('Please provide the following details in order to help our editorial team manage your submission.')
                        ->aside()
                        ->schema([
                            Hidden::make('submission_progress'),
                            TagsInput::make('meta.disiplines')
                                ->helperText('Disciplines refer to specific areas of study or branches of knowledge that are recognized by university faculties and learned societies.')
                                ->placeholder(''),
                            TinyEditor::make('meta.comments_for_the_editor')
                                ->minHeight(300)
                                ->label('Comments for the Editor')
                                ->profile('basic')
                                ->helperText('Please include any additional information that you believe would be valuable for our editorial staff to consider while evaluating your submission. This could include relevant background information, prior research, or any other context that may be helpful in assessing the quality and significance of your work.'),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        SubmissionUpdateAction::run($data, $this->record);

        $this->dispatch('next-wizard-step');
    }
}
