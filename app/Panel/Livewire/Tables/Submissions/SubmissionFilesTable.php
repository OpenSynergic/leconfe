<?php

namespace App\Panel\Livewire\Tables\Submissions;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;
use App\Models\Media;
use App\Models\Submission;
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\MediaLibrary\Support\MediaStream;

class SubmissionFilesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public string $category;

    public bool $viewOnly = false;

    protected $listeners = ['refreshLivewire' => '$refresh'];


    public function render()
    {
        return view('panel.livewire.tables.submissions.submission-files');
    }

    protected function getTableQuery(): Builder
    {
        return match ($this->category) {
            SubmissionFileCategory::FILES => $this->record->files()->getQuery(),
            SubmissionFileCategory::PAPERS => $this->record->papers()->getQuery(),
            SubmissionFileCategory::REVIEWER_ASSIGNED_PAPERS => auth()->user()
                ->asParticipant()
                ->reviews()
                ->submission($this->record->id)
                ->first()
                ->files()
                ->getQuery(),
            SubmissionFileCategory::REVIEWER_FILES => auth()->user()
                ->asParticipant()
                ->reviews()
                ->submission($this->record->id)
                ->first()
                ->files()
                ->getQuery(),
            default => abort(404),
        };
    }

    protected function paginateTableQuery(Builder $query)
    {
        return $query->simplePaginate($this->getTableRecordsPerPage() == 'all' ? $query->count() : $this->getTableRecordsPerPage());
    }

    protected function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading(function (): string {
                return match ($this->category) {
                    SubmissionFileCategory::FILES => 'Files',
                    SubmissionFileCategory::PAPERS, SubmissionFileCategory::REVIEWER_ASSIGNED_PAPERS => 'Papers',
                    default => ''
                };
            })
            ->columns([
                TextColumn::make('file_name')
                    ->color('primary')
                    ->action(function (Media $record) {
                        return $record;
                    })
                    ->description(function (Media $record) {
                        return SubmissionFileType::nameById($record->getCustomProperty('type'));
                    })
            ])
            ->headerActions([
                Action::make('download_all')
                    ->icon("iconpark-download-o")
                    ->label('Download All Files')
                    ->button()
                    ->hidden($this->viewOnly)
                    ->color('gray')
                    ->action(function () {
                        $downloads = $this->record->files()->get();
                        return MediaStream::create('files.zip')->addMedia($downloads);
                    }),
                Action::make('upload')
                    ->icon("iconpark-upload")
                    ->label('Upload Files')
                    ->hidden(function (): bool {
                        if ($this->viewOnly) {
                            return true;
                        }
                        // If the submission has already been submitted, cannot upload the file.
                        return $this->record->user->id == auth()->id() && $this->record->stage != SubmissionStage::Wizard;
                    })
                    ->button()
                    ->modalWidth('xl')
                    ->form([
                        Select::make('type')
                            ->required()
                            ->options(
                                fn () => SubmissionFileType::all()->pluck('name', 'id')->toArray()
                            )
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ])
                            ->createOptionAction(function (FormAction $action) {
                                $action->modalWidth('xl')
                                    ->failureNotificationTitle("There was a problem creating the file type")
                                    ->successNotificationTitle("File type created successfully");
                            })
                            ->createOptionUsing(function (array $data) {
                                SubmissionFileType::create($data);
                            })
                            ->reactive(),
                        SpatieMediaLibraryFileUpload::make('submission-files')
                            ->required()
                            ->previewable(false)
                            ->downloadable()
                            ->reorderable()
                            ->disk('private-files')
                            ->preserveFilenames()
                            ->collection($this->category)
                            ->visibility('private')
                            ->model(fn () => $this->record)
                            ->customProperties(function (Get $get) {
                                return [
                                    'type' => $get('type'),
                                ];
                            })
                            ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
                                $component->saveUploadedFiles();
                            })
                    ])
                    ->successNotificationTitle('Files added successfully')
                    ->failureNotificationTitle('There was a problem adding the files')
                    ->action(function (array $data, Action $action) {
                        $this->record->getMediaCollection(SubmissionFileCategory::FILES);
                    })
            ])
            ->actions([
                EditAction::make()
                    ->label("Rename")
                    ->modalWidth('md')
                    ->modalHeading('Edit file')
                    ->modalHeading("Rename")
                    ->hidden($this->viewOnly)
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
                    ->hidden(function (): bool {
                        if ($this->viewOnly) {
                            return true;
                        }
                        return $this->record->user->id == auth()->id() && $this->record->stage != SubmissionStage::Wizard;
                    }),
            ]);
    }
}
