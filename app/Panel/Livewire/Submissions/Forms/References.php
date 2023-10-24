<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Submission;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class References extends \Livewire\Component implements HasForms
{
    use InteractsWithForms;

    public Submission $submission;

    public array $meta = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'meta' => $this->submission->getAllMeta()->toArray()
        ]);
    }

    public function submit()
    {
        $datas = $this->form->getState();
        SubmissionUpdateAction::run($datas, $this->submission);
        Notification::make()->body("Saved successfully")->success()->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TinyEditor::make('meta.references')
                    ->label('References')
                    ->minHeight(300)
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.forms.references');
    }
}
