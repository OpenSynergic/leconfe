<?php

namespace App\Panel\Livewire\Workflows\PeerReview\Forms;

use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Stevebauman\Purify\Facades\Purify;

class Guidelines extends \Livewire\Component implements HasForms
{
    use InteractsWithForms, InteractWithTenant;

    public string $reviewGuidelines;

    public string $competingInterests;

    public function mount(): void
    {
        $this->form->fill([
            'reviewGuidelines' => $this->conference->getMeta('review_guidelines', ''),
            'competingInterests' => $this->conference->getMeta('competing_interests', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TinyEditor::make('reviewGuidelines')
                    ->minHeight(300),
                TinyEditor::make('competingInterests')
                    ->minHeight(300),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $this->conference->setMeta('review_guidelines', Purify::clean($data['reviewGuidelines']));
        $this->conference->setMeta('competing_interests', Purify::clean($data['competingInterests']));

        Notification::make()
            ->title('Success!')
            ->body('The guidelines have been updated.')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.livewire.workflows.peer-review.forms.guidelines');
    }
}
