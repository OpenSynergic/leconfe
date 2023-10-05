<?php

namespace App\Panel\Livewire\Tables\Submissions;

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

    public string $category; // submissions-files or submission-papers

    public bool $viewOnly = false;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public function mount(Submission $record, bool $viewOnly = false, $category = 'submission-files')
    {
    }

    public function render()
    {
        return view('panel.livewire.tables.submissions.submission-files');
    }

    protected function getTableQuery(): Builder
    {
        if ($this->category == 'submission-papers') {
            return $this->record->papers()->getQuery();
        }
        return $this->record->files()->getQuery();
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
                    'submission-files' => 'Files',
                    'submission-papers' => 'Papers',
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
                    ->label('Download All Files')
                    ->button()
                    ->hidden($this->viewOnly)
                    ->color('gray')
                    ->action(function () {
                        $downloads = $this->record->files()->get();
                        return MediaStream::create('files.zip')->addMedia($downloads);
                    }),
                Action::make('upload')
                    ->label('Upload Files')
                    ->hidden($this->viewOnly)
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
                            ->disk('local')
                            ->previewable(false)
                            ->downloadable()
                            ->reorderable()
                            ->disk('submission-files')
                            ->preserveFilenames()
                            ->collection('submission-files')
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
                        $this->record->getMediaCollection('submission-files');
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
                    ->hidden($this->viewOnly),
            ]);
    }
}
