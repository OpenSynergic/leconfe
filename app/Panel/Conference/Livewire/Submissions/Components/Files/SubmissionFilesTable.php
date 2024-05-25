<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Files;

use App\Actions\SubmissionFiles\UploadSubmissionFileAction;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Support\MediaStream;

abstract class SubmissionFilesTable extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public const ACCEPTED_FILE_TYPES = ['pdf', 'docx', 'xls', 'png', 'jpg', 'jpeg'];

    public Submission $submission;

    public bool $viewOnly = false;

    protected ?string $category = null;

    protected string $tableHeading = 'Files';

    protected string $tableDescription = '';

    public function isViewOnly(): bool
    {
        return $this->viewOnly;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getAcceptedFiles(): array
    {
        return static::ACCEPTED_FILE_TYPES;
    }

    public function tableColumns(): array
    {
        return [
            TextColumn::make('media.file_name')
                ->wrap()
                ->label('Filename')
                ->color('primary')
                ->action(fn (Model $record) => $record->media)
                ->description(fn (Model $record) => $record->type->name),
        ];
    }

    public function downloadAllAction()
    {
        return TableAction::make('download_all')
            ->icon('iconpark-download-o')
            ->label('Download All Files')
            ->button()
            ->hidden(fn (): bool => $this->isViewOnly())
            ->color('gray')
            ->action(function (TableAction $action) {
                $files = $this->submission->media()->where('collection_name', $this->category)->get();
                if ($files->count()) {
                    return MediaStream::create('files.zip')->addMedia($files);
                }
                $action->failureNotificationTitle("There's nothing to download");
                $action->failure();
            });
    }

    public function uploadFormSchema(): array
    {
        return [
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
                        ->failureNotificationTitle('There was a problem creating the file type')
                        ->successNotificationTitle('File type created successfully');
                })
                ->createOptionUsing(function (array $data) {
                    SubmissionFileType::create($data);
                })
                ->reactive(),
            SpatieMediaLibraryFileUpload::make('files')
                ->required()
                ->previewable(false)
                ->downloadable()
                ->reorderable()
                ->disk('private-files')
                ->preserveFilenames()
                ->acceptedFileTypes(
                    fn (): array => collect($this->getAcceptedFiles())
                        ->map(fn ($ext) => MimeType::fromExtension($ext))
                        ->toArray()
                )
                ->collection($this->category)
                ->visibility('private')
                ->model(fn () => $this->submission)
                ->saveRelationshipsUsing(
                    static fn (SpatieMediaLibraryFileUpload $component) => $component->saveUploadedFiles()
                ),
        ];
    }

    public function handleUploadAction(array $data, TableAction $action)
    {
        $files = $this->submission->getMedia($this->category);
        foreach ($files as $file) {
            UploadSubmissionFileAction::run(
                $this->submission,
                $file,
                $this->category,
                SubmissionFileType::find($data['type'])
            );
        }
        $action->success();
    }

    public function uploadAction()
    {
        return TableAction::make('upload')
            ->icon('iconpark-upload')
            ->label('Upload Files')
            ->outlined()
            ->hidden(fn (): bool => $this->isViewOnly())
            ->modalWidth('xl')
            ->form(
                $this->uploadFormSchema()
            )
            ->successNotificationTitle('Files added successfully')
            ->failureNotificationTitle('There was a problem adding the files')
            ->action(
                fn (array $data, TableAction $action) => $this->handleUploadAction($data, $action)
            );
    }

    public function headerActions(): array
    {
        return [
            $this->downloadAllAction(),
            $this->uploadAction(),
        ];
    }

    public function tableActions(): array
    {
        return [
            TableAction::make('rename')
                ->icon('iconpark-edit')
                ->label('Rename')
                ->modalWidth('md')
                ->modalHeading('Edit file')
                ->modalHeading('Rename')
                ->hidden(
                    fn (): bool => $this->isViewOnly() || $this->submission->isDeclined()
                )
                ->successNotificationTitle('File renamed successfully')
                ->mountUsing(function (SubmissionFile $record, Form $form) {
                    $form->fill([
                        'file_name' => $record->media->file_name,
                    ]);
                })
                ->action(function (SubmissionFile $record, array $data, TableAction $action) {
                    $record->media->update([
                        'file_name' => $data['file_name'],
                        'name' => $data['file_name'],
                    ]);
                    $action->success();
                })
                ->modalSubmitActionLabel('Rename')
                ->form([
                    TextInput::make('file_name')
                        ->label('New Filename')
                        ->formatStateUsing(function (SubmissionFile $record) {
                            return str($record->media->file_name)->beforeLast('.'.$record->media->extension);
                        })
                        ->dehydrateStateUsing(function (SubmissionFile $record, $state) {
                            return str($state)->append('.'.$record->media->extension);
                        })
                        ->suffix(function (SubmissionFile $record) {
                            return '.'.$record->media->extension;
                        }),
                ]),
            DeleteAction::make()
                ->hidden(function (): bool {
                    if ($this->submission->isDeclined()) {
                        return true;
                    }

                    return $this->isViewOnly();
                }),
        ];
    }

    public function tableQuery(): Builder
    {
        return $this->submission
            ->submissionFiles()
            ->with(['media'])
            ->where('category', $this->category)
            ->getQuery();
    }

    public function tableDescription(): string
    {
        return $this->tableDescription;
    }

    public function tableHeading(): string
    {
        return $this->tableHeading;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->tableHeading())
            ->description($this->tableDescription())
            ->emptyStateHeading('No Files')
            ->query($this->tableQuery())
            ->columns($this->tableColumns())
            ->headerActions($this->headerActions())
            ->actions($this->tableActions())
            ->bulkActions($this->bulkActions());
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.files.media-file-table');
    }
}
