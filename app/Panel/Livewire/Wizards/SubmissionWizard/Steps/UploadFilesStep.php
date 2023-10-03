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

class UploadFilesStep extends Component implements HasWizardStep, HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Upload Files';
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Submission Files")
            ->query(function (): Builder {
                return $this->record->files()->getQuery();
            })
            ->headerActions([
                Action::make('download_all')
                    ->label('Download All Files')
                    ->button()
                    ->color('gray')
                    ->action(function () {
                        $downloads = $this->record->files()->get();
                        return MediaStream::create('files.zip')->addMedia($downloads);
                    }),
                Action::make('upload')
                    ->label('Upload Files')
                    ->button()
                    ->modalWidth('xl')
                    ->form([
                        ...SubmissionFileSchema::defaultUploadForm($this->record)
                    ])
                    ->successNotificationTitle('Files added successfully')
                    ->failureNotificationTitle('There was a problem adding the files')
                    ->action(function (array $data, Action $action) {
                        $this->record->getMediaCollection('submission-files');
                    })
            ])
            ->actions([
                EditAction::make()
                    ->label("Rename")
                    ->modalWidth('md')
                    ->modalHeading('Edit file')
                    ->modalHeading("Rename")
                    ->modalSubmitActionLabel("Rename")
                    ->form([
                        TextInput::make('file_name')
                            ->label("New Filename")
                            ->formatStateUsing(function (Media $record) {
                                return str($record->file_name)->beforeLast('.' . $record->extension);
                            })
                            ->dehydrateStateUsing(function (Media $record, $state) {
                                return str($state)->append('.' . $record->extension);
                            })
                            ->suffix(function (Media $record) {
                                return '.' . $record->extension;
                            })
                    ]),
                DeleteAction::make()
            ])
            ->columns([
                ...SubmissionFileSchema::defaultTableColumns()
            ]);
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
