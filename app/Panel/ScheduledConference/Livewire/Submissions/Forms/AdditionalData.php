<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Forms;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\Submission;
use App\Models\SubmissionFormItem;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class AdditionalData extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $submission;

    public array $data = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'submission_form_responses' => $this->submission->getMeta('submission_form_responses'),
        ]);
    }

    public function submit()
    {
        $data = $this->form->getState();
        $submissionFormResponses = SubmissionFormItem::filterOutUploadResponses(data_get($data, 'submission_form_responses'));

        if (array_key_exists('submission_form_responses', $data)) {
            $this->submission->setMeta('submission_form_responses', $submissionFormResponses);
        }

        $this->form->model($this->submission)->saveRelationships();

        Notification::make()
            ->body(__('general.saved_successfuly'))
            ->success()
            ->send();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(function (): bool {
                return ! auth()->user()->can('editing', $this->submission);
            })
            ->statePath('data')
            ->model($this->submission)
            ->schema([
                ...SubmissionFormItem::buildFormSchema(),
            ]);
    }

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.forms.additional-data');
    }
}
