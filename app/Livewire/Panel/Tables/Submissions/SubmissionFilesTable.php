<?php

namespace App\Livewire\Panel\Tables\Submissions;

use App\Models\Media;
use App\Models\Submission;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\MediaLibrary\Support\MediaStream;

class SubmissionFilesTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public function render()
    {
        return view('livewire.panel.tables.submissions.submission-files');
    }

    protected function getTableQuery(): Builder
    {
        return $this->record->media()->where('collection_name', 'files')->getQuery();
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
                ->hidden(fn () => $this->record->getMedia('files')->isEmpty())
                ->color('gray')
                ->action(function () {
                    $downloads = $this->record->getMedia('files');

                    return MediaStream::create('files.zip')->addMedia($downloads);
                }),
            Action::make('upload')
                ->label('Upload Files')
                ->button()
                ->modalWidth('xl')
                ->form([
                    SpatieMediaLibraryFileUpload::make('files')
                        ->required()
                        ->disk('local')
                        ->previewable(false)
                        ->downloadable()
                        ->reorderable()
                        ->preserveFilenames()
                        ->collection('files')
                        ->model($this->record)
                        ->visibility('private')
                        ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
                            $component->saveUploadedFiles();
                        }),
                ])
                ->action(fn () => null),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Split::make([
                Tables\Columns\TextColumn::make('file_name')
                    ->size('sm')
                    ->url(fn (Media $record) => $record->getTemporaryUrl(now()->addMinutes(5))),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->date()
                //     // ->getStateUsing(fn ($record) => dd($record->created_at->toString()))
                //     ->size('sm'),
            ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // Action::make('download')
            //     ->icon('heroicon-o-download')
            //     ->action(fn (Media $record) => $record),
            EditAction::make()
                ->modalWidth('2xl')
                ->modalHeading('Edit file')
                ->form([
                    TextInput::make('file_name'),
                ]),
            DeleteAction::make(),
        ];
    }
}