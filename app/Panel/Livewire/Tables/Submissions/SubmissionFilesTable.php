<?php

namespace App\Panel\Livewire\Tables\Submissions;

use App\Models\Media;
use App\Models\Submission;
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\MediaLibrary\Support\MediaStream;

class SubmissionFilesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public function render()
    {
        return view('panel.livewire.tables.submissions.submission-files');
    }

    protected function getTableQuery(): Builder
    {
        return $this->record->files()->getQuery();
    }

    protected function getTableHeading(): string
    {
        return 'Submission Files';
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('download_all')
                ->label('Download All Files')
                ->button()
                ->hidden(fn () => !$this->record->files()->exists())
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
                    Select::make('type')
                        ->required()
                        ->options(fn () => SubmissionFileType::all()->pluck('name', 'id')->toArray())
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
                        ->model($this->record)
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
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Split::make([
                Tables\Columns\TextColumn::make('file_name')
                    ->size('sm')
                    ->description(function (Media $record) {
                        return SubmissionFileType::nameById($record->getCustomProperty('type'));
                    })
                    ->columnSpanFull()

            ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download')
                    ->icon('iconpark-download')
                    ->action(function (Media $record) {
                        return $record;
                    }),
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
                DeleteAction::make(),
            ])
        ];
    }
}
