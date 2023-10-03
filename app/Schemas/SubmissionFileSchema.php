<?php

namespace App\Schemas;

use App\Models\Media;
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Support\MediaStream;

class SubmissionFileSchema
{

    public static function defaultUploadForm(?Model $model = null): array
    {
        return [
            Select::make('type')
                ->required()
                ->options(fn () => SubmissionFileType::all()->pluck('name', 'id')->toArray())
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ])
                ->createOptionAction(function (Action $action) {
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
                ->model(fn () => $model)
                ->customProperties(function (Get $get) {
                    return [
                        'type' => $get('type'),
                    ];
                })
                ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
                    $component->saveUploadedFiles();
                })
        ];
    }

    public static function defaultTableColumns(): array
    {
        return [
            TextColumn::make('file_name')
                ->color('primary')
                ->action(function (Media $record) {
                    return $record;
                })
                ->description(function (Media $record) {
                    return SubmissionFileType::nameById($record->getCustomProperty('type'));
                })
        ];
    }

    // public static function defaultHeaderActions(): array
    // {
    //     return [
    //         Action::make('download_all')
    //             ->label('Download All Files')
    //             ->button()
    //             ->hidden(function (Table $table): bool {
    //                 return $table->getQuery()->exists();
    //             })
    //             ->color('gray')
    //             ->action(function (Table $table) {
    //                 $downloads = $table->getQuery()->get();
    //                 return MediaStream::create('files.zip')->addMedia($downloads);
    //             }),
    //         Action::make('upload')
    //             ->label('Upload Files')
    //             ->hidden(function (Table $table) {
    //             })
    //             ->button()
    //             ->modalWidth('xl')
    //             ->form([
    //                 Select::make('type')
    //                     ->required()
    //                     ->options(fn () => SubmissionFileType::all()->pluck('name', 'id')->toArray())
    //                     ->searchable()
    //                     ->createOptionForm([
    //                         TextInput::make('name')
    //                             ->required(),
    //                     ])
    //                     ->createOptionAction(function (Action $action) {
    //                         $action->modalWidth('xl')
    //                             ->failureNotificationTitle("There was a problem creating the file type")
    //                             ->successNotificationTitle("File type created successfully");
    //                     })
    //                     ->createOptionUsing(function (array $data) {
    //                         SubmissionFileType::create($data);
    //                     })
    //                     ->reactive(),
    //                 SpatieMediaLibraryFileUpload::make('submission-files')
    //                     ->required()
    //                     ->disk('local')
    //                     ->previewable(false)
    //                     ->downloadable()
    //                     ->reorderable()
    //                     ->disk('submission-files')
    //                     ->preserveFilenames()
    //                     ->collection('submission-files')
    //                     ->visibility('private')
    //                     ->model(fn () => static::$record)
    //                     ->customProperties(function (Get $get) {
    //                         return [
    //                             'type' => $get('type'),
    //                         ];
    //                     })
    //                     ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
    //                         $component->saveUploadedFiles();
    //                     })
    //             ])
    //             ->successNotificationTitle('Files added successfully')
    //             ->failureNotificationTitle('There was a problem adding the files')
    //             ->action(function (array $data, Action $action) {
    //                 static::$record->getMediaCollection('submission-files');
    //             })
    //     ];
    // }

    // public static function table(Table $table): Table
    // {
    //     return $table->columns([...static::defaultColumns()]);
    // }
}
