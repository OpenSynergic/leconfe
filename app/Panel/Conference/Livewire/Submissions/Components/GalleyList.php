<?php

namespace App\Panel\Conference\Livewire\Submissions\Components;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Submission;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\MimeType;
use App\Models\SubmissionGalley;
use App\Models\SubmissionFileType;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use App\Constants\SubmissionFileCategory;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Actions\SubmissionGalleys\CreateSubmissionGalleyAction;
use App\Actions\SubmissionGalleys\UpdateSubmissionGalleyAction;
use App\Actions\SubmissionGalleys\UpdateMediaSubmissionGalleyFileAction;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GalleyList extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;
    public bool $viewOnly = false;
    public const ACCEPTED_FILE_TYPES = ['pdf', 'docx', 'xls', 'png', 'jpg', 'jpeg'];

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.galley-list');
    }

    public function getQuery(): Builder
    {
        return $this->submission->galleys()
            ->with(['media', 'file.media'])
            ->orderBy('order_column')
            ->getQuery();
    }

    public function getGalleyFormSchema(): array
    {
        return [
            TextInput::make('label')
                ->label('Label')
                ->helperText(fn () => new HtmlString('
                    <p class="text-sm italic text-gray-600">
                        Typically used to identify the file format (e.g. PDF, HTML, etc.)
                    </p>
                '))
                ->required(),
            Toggle::make('is_remote_url')
                ->label('This galley will be available at a separate website.')
                ->live()
                ->default(false),
            TextInput::make('remote_url')
                ->label('Remote URL')
                ->visible(fn (Get $get) => $get('is_remote_url'))
                ->required()
                ->activeUrl()
                ->placeholder('https://example.com/galley.pdf'),
            Select::make('media.type')
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
                        ->color('primary')
                        ->failureNotificationTitle('There was a problem creating the file type')
                        ->successNotificationTitle('File type created successfully');
                })
                ->createOptionUsing(function (array $data) {
                    SubmissionFileType::create($data);
                })
                ->visible(fn (Get $get) => !$get('is_remote_url'))
                ->live(),
            SpatieMediaLibraryFileUpload::make('media.files')
                ->required()
                ->previewable(false)
                ->downloadable()
                ->reorderable()
                ->disk('private-files')
                ->acceptedFileTypes(
                    fn (): array => collect(static::ACCEPTED_FILE_TYPES)
                        ->map(fn ($ext) => MimeType::fromExtension($ext))
                        ->toArray()
                )
                ->preserveFilenames()
                ->live()
                ->collection(SubmissionFileCategory::GALLEY_FILES)
                ->visibility('private')
                ->visible(fn (Get $get) => !$get('is_remote_url'))
                ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, $context, SubmissionGalley $record, Get $get) {
                    if ($context == 'edit') {
                        $component->saveUploadedFiles();
                        UpdateMediaSubmissionGalleyFileAction::run($record, $component->getState(), $get('media.type'));
                        $component->deleteAbandonedFiles();
                    }
                })
                ->afterStateUpdated(function ($state, Set $set) {
                    $set('media.name', pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME));
                }),
            Checkbox::make('media.is_custom_name')
                ->label('Manually set the file name')
                ->visible(function (Get $get, $context) {
                    return ! $get('is_remote_url') && $context == 'create';
                })
                ->live(),
            TextInput::make('media.name')
                ->label('File Name')
                ->required()
                ->visible(function (Get $get, $context) {
                    $isRemoteUrl = $get('is_remote_url');
                    $hasFiles = $get('media.files');
                    $isCustomName = $get('media.is_custom_name', false);
                
                    return !$isRemoteUrl && $hasFiles && ($context === 'create' ? $isCustomName : true);
                })
                ->suffix(function (Get $get, $record) {
                    $mediaFile = $get('media.files');

                    if (!$mediaFile) {
                        return null;
                    }

                    $mediaFile = reset($mediaFile) instanceof TemporaryUploadedFile
                        ? reset($mediaFile)->getClientOriginalName()
                        : $record->file?->media->file_name;

                    return pathinfo($mediaFile, PATHINFO_EXTENSION) ?: null;
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->reorderable(fn () => $this->viewOnly ? false : 'order_column')
            ->heading('Galleys')
            ->columns([
                Split::make([
                    TextColumn::make('label')
                        ->color('primary')
                        ->url(
                            fn (SubmissionGalley $galley) => 
                                !$galley->remote_url ? route('submission-files.view', $galley->file->media->uuid) : $galley->remote_url,
                            true
                        )
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Galley')
                    ->modalWidth('2xl')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->successNotificationTitle('Galley added successfully')
                    ->failureNotificationTitle('There was a problem adding the galley')
                    ->form(static::getGalleyFormSchema())
                    ->using(function (array $data, \Livewire\Component $livewire) {
                        try {
                            $componentFile = ! $data['is_remote_url'] ?
                                $livewire->getMountedTableActionForm()->getComponent('mountedTableActionsData.0.media.files') :
                                null;

                            $newGalley = CreateSubmissionGalleyAction::run($this->submission, $data, $componentFile);

                            if ($newGalley instanceof SubmissionGalley) {
                                return $newGalley;
                            }
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    })
                    ->hidden($this->viewOnly),
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Galley updated successfully')
                    ->failureNotificationTitle('There was a problem updating the galley')
                    ->mutateRecordDataUsing(function (array $data, SubmissionGalley $record) {
                        $data['is_remote_url'] = (bool) $record->remote_url;
                        if ($record->file) {
                            $data['media']['type'] = $record->file->submission_file_type_id;
                            $data['media']['name'] = $record->file->media->name;
                        }
                        
                        return $data;
                    })
                    ->using(function (array $data, SubmissionGalley $record) {
                        UpdateSubmissionGalleyAction::run($record, $data);
                    })
                    ->form(static::getGalleyFormSchema())
                    ->hidden($this->viewOnly),
                DeleteAction::make()
                    ->hidden($this->viewOnly),
            ]);
    }
}