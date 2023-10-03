<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Models\Media;
use App\Models\Submission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Schemas\SubmissionFileSchema;
use Filament\Actions\Action as PageAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\MediaLibrary\Support\MediaStream;

class UploadFilesStep extends Component implements HasWizardStep, HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Upload Files';
    }

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard.steps.upload-files-step');
    }

    public function nextStep()
    {
        return PageAction::make('nextStep')
            ->label("Next")
            ->failureNotificationTitle("No files were added to the submission")
            ->successNotificationTitle("Saved")
            ->action(function (PageAction $action) {
                if (!$this->record->files()->exists()) {
                    return $action->failure();
                }
                $action->success();
                $this->dispatch('next-wizard-step');
            });
    }
}
