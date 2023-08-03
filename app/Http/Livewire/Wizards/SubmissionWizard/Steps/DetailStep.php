<?php

namespace App\Http\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\UpdateSubmissionAction;
use App\Http\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Models\Submission;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class DetailStep extends Component implements HasForms, HasWizardStep
{
    use InteractsWithForms;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public function mount($record)
    {
        $this->form->fill([
            'meta' => $record->getAllMeta(),
            'submission_progress' => 'upload-files'
        ]);
    }

    public static function getWizardLabel(): string
    {
        return 'Details';
    }

    protected function getFormModel(): string
    {
        return $this->record;
    }


    public function render()
    {
        return view('livewire.wizards.submission-wizard.steps.detail-step');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make([
                Section::make('Submission Details')
                    ->description('Please provide the following details to help us manage your submission in our system.')
                    ->aside()
                    ->schema([
                        Hidden::make('submission_progress'),
                        TextInput::make('meta.title')
                            ->required(),
                        SpatieTagsInput::make('keywords')
                            ->placeholder('')
                            ->model($this->record)
                            ->type('submissionKeywords'),
                        // TinyEditor::make('meta.abstract')
                        //     ->required()
                        //     ->profile('basic'),
                    ])
            ])
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $this->record = UpdateSubmissionAction::run($data, $this->record);

        $this->dispatchBrowserEvent('next-wizard-step');
    }
}
