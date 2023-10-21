<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Detail extends \Livewire\Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public Submission $submission;

    public array $meta = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'meta' => $this->submission->getAllMeta()->toArray()
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('meta.title'),
                SpatieTagsInput::make('meta.keywords')
                    ->splitKeys([','])
                    ->placeholder('')
                    ->model($this->submission)
                    ->type('submissionKeywords'),
                TinyEditor::make('meta.abstract')
                    ->minHeight(300)
                    ->profile('basic')
            ]);
    }

    public function handleSubmitAction(Action $action): void
    {
        $this->form->validate();
        SubmissionUpdateAction::run(
            $this->form->getState(),
            $this->submission
        );
        $action->success();
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->icon("iconpark-check")
            ->label("Save")
            ->authorize('Submission:update')
            ->successNotificationTitle("Saved successfully")
            ->action(fn (Action $action) => $this->handleSubmitAction($action));
    }

    public function render()
    {
        return view('panel.livewire.submissions.forms.detail');
    }
}
