<?php

namespace App\Panel\Livewire\Tables\Submissions;

use App\Models\Media;
use App\Models\Submission;
use App\Schemas\SubmissionFileSchema;
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

class SubmissionFilesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public string $category; // submissions-files or submission-papers

    protected $listeners = ['refreshLivewire' => '$refresh'];

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

    protected function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading("Files")
            ->columns([
                ...SubmissionFileSchema::defaultTableColumns()
            ])
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
                DeleteAction::make(),
            ]);
    }
}
