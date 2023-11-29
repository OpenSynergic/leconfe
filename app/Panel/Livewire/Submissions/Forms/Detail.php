<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Detail extends \Livewire\Component implements HasForms
{
    use InteractsWithForms;

    public Submission $submission;

    public array $meta = [];

    public array $topics = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'topics' => $this->submission->topics()->pluck('id')->toArray(),
            'meta' => $this->submission->getAllMeta()->toArray()
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(function (): bool {
                return auth()->user()->can('editing', $this->submission);
            })
            ->model($this->submission)
            ->schema([
                TextInput::make('meta.title'),
                Select::make('topics')
                    ->preload()
                    ->multiple()
                    ->relationship('topics', 'name')
                    ->label("Topic")
                    ->searchable(),
                SpatieTagsInput::make('meta.keywords')
                    ->splitKeys([','])
                    ->placeholder('')
                    ->type('submissionKeywords'),
                TinyEditor::make('meta.abstract')
                    ->required()
                    ->minHeight(300)
                    ->profile('basic')
            ]);
    }

    public function submit(): void
    {

        SubmissionUpdateAction::run(
            $this->form->getState(),
            $this->submission
        );

        Notification::make()
            ->body("Saved successfully")
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.livewire.submissions.forms.detail');
    }
}
