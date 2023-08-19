<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Submission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ForTheEditorsStep extends Component implements HasForms, HasWizardStep
{
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
        return view('panel.livewire.wizards.submission-wizard.steps.for-the-editors-step');
    }

    protected function getFormModel(): string
    {
        return $this->record;
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                Section::make('For the Editors')
                    ->description('Please provide the following details in order to help our editorial team manage your submission.')
                    ->aside()
                    ->schema([
                        Hidden::make('submission_progress'),
                        SpatieTagsInput::make('disiplines')
                            ->helperText('Disciplines refer to specific areas of study or branches of knowledge that are recognized by university faculties and learned societies.')
                            ->placeholder('')
                            ->model($this->record)
                            ->type('submissionDisiplines'),
                        TinyEditor::make('meta.comments_for_the_editor')
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

        $this->dispatchBrowserEvent('next-wizard-step');
    }
}
