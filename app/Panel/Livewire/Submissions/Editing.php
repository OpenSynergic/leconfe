<?php

namespace App\Panel\Livewire\Submissions;

use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Editing extends \Livewire\Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public Submission $submission;

    public function publishAction()
    {
        return Action::make('publishAction')
            ->icon("iconpark-check")
            ->extraAttributes(['class' => 'w-full'], true)
            ->label("Publish")
            ->action(fn () => $this->dispatch('open-publication-tab'));
    }

    public function render()
    {
        return view('panel.livewire.submissions.editing');
    }
}
