<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Forms;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Submission;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class References extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $submission;

    public array $meta = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'meta' => $this->submission->getAllMeta()->toArray(),
        ]);
    }

    public function submit()
    {
        SubmissionUpdateAction::run(
            $this->form->getState(),
            $this->submission
        );

        Notification::make()
            ->title(__('general.saved_successfuly'))
            ->success()
            ->send();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(function (): bool {
                return ! auth()->user()->can('editing', $this->submission);
            })
            ->schema([
                Textarea::make('meta.references')
                    ->label(__('general.references'))
                    ->hiddenLabel()
                    ->autosize(),
            ]);
    }

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.forms.references');
    }
}
