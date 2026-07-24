<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Components;

use App\Actions\SubmissionGalleys\CreateSubmissionGalleyAction;
use App\Actions\SubmissionGalleys\UpdateMediaSubmissionGalleyFileAction;
use App\Actions\SubmissionGalleys\UpdateSubmissionGalleyAction;
use App\Constants\SubmissionFileCategory;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\Submission;
use App\Models\SubmissionFileType;
use App\Models\SubmissionGalley;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GalleyList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.components.galley-list');
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
                ->label(__('general.label'))
                ->helperText(fn () => new HtmlString(__('general.typically_identify_file_format')))
                ->required(),
            Toggle::make('is_remote_url')
                ->label(__('general.galleys_available'))
                ->live()
                ->default(false),
            TextInput::make('remote_url')
                ->label(__('general.remote_url'))
                ->visible(fn (Get $get) => $get('is_remote_url'))
                ->required()
                ->activeUrl()
                ->placeholder('https://example.com/galley.pdf'),
            Select::make('media.type')
                ->label(__('general.type'))
                ->required()
                ->options(
                    fn () => SubmissionFileType::all()->pluck('name', 'id')->toArray()
                )
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label(__('general.name'))
                        ->required(),
                ])
                ->createOptionAction(function (Action $action) {
                    $action->modalWidth('xl')
                        ->color('primary')
                        ->failureNotificationTitle(__('general.problem_creating_file_type'))
                        ->successNotificationTitle(__('general.file_type_created_successfulyy'));
                })
                ->createOptionUsing(function (array $data) {
                    SubmissionFileType::create($data);
                })
                ->visible(fn (Get $get) => ! $get('is_remote_url'))
                ->live(),
            SpatieMediaLibraryFileUpload::make('media.files')
                ->label(__('general.file'))
                ->required()
                ->previewable(false)
                ->downloadable()
                ->reorderable()
                ->disk('private-files')
                ->preserveFilenames()
                ->live()
                ->collection(SubmissionFileCategory::GALLEY_FILES)
                ->visibility('private')
                ->visible(fn (Get $get) => ! $get('is_remote_url'))
                ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, $context, SubmissionGalley $record, Get $get) {
                    if ($context == 'edit') {
                        $component->saveUploadedFiles();
                        UpdateMediaSubmissionGalleyFileAction::run($record, $component->getState(), $get('media.type'));
                        $component->deleteAbandonedFiles();
                    }
                })
                ->afterStateUpdated(function ($state, Set $set) {
                    $set('media.name', pathinfo(SpatieMediaLibraryFileUpload::getClientOriginalName($state), PATHINFO_FILENAME));
                }),
            Checkbox::make('media.is_custom_name')
                ->label(__('general.manually_set_file_name'))
                ->visible(function (Get $get, $context) {
                    return ! $get('is_remote_url') && $context == 'create';
                })
                ->live(),
            TextInput::make('media.name')
                ->label(__('general.file_name'))
                ->required()
                ->visible(function (Get $get, $context) {
                    $isRemoteUrl = $get('is_remote_url');
                    $hasFiles = $get('media.files');
                    $isCustomName = $get('media.is_custom_name', false);

                    return ! $isRemoteUrl && $hasFiles && ($context === 'create' ? $isCustomName : true);
                })
                ->suffix(function (Get $get, $record) {
                    $mediaFile = $get('media.files');

                    if (! $mediaFile) {
                        return null;
                    }
                    $mediaFile = is_array($mediaFile) ? reset($mediaFile) : $mediaFile;
                    $mediaFile = $mediaFile instanceof TemporaryUploadedFile
                        ? SpatieMediaLibraryFileUpload::getClientOriginalName($mediaFile)
                        : $record?->file?->media?->file_name;

                    return $mediaFile ? (pathinfo($mediaFile, PATHINFO_EXTENSION) ?: null) : null;
                }),
        ];
    }

    protected function getMountedGalleyFileUpload(): SpatieMediaLibraryFileUpload
    {
        $schemaName = $this->getMountedActionSchemaName();
        $schema = filled($schemaName) ? $this->getSchema($schemaName) : null;
        $component = $schema?->getComponent('media.files', withHidden: true);

        if ($component instanceof SpatieMediaLibraryFileUpload) {
            return $component;
        }

        $statePath = filled($schema?->getStatePath())
            ? "{$schema->getStatePath()}.media.files"
            : 'media.files';

        throw ValidationException::withMessages([
            $statePath => __('The galley file upload component could not be resolved. Please reload the page and try again.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->reorderable(fn () => $this->viewOnly ? false : 'order_column')
            ->heading(__('general.galleys'))
            ->columns([
                Split::make([
                    TextColumn::make('label')
                        ->label(__('general.label'))
                        ->color('primary')
                        ->action(fn (SubmissionGalley $galley) => $galley?->file?->media)
                        ->openUrlInNewTab(),
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('general.add_galley'))
                    ->modalWidth('2xl')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->successNotificationTitle(__('general.galley_added_succesfully'))
                    ->failureNotificationTitle(__('general.there_was_problem_adding_galley'))
                    ->schema(static::getGalleyFormSchema())
                    ->using(function (array $data) {
                        $componentFile = data_get($data, 'is_remote_url', false)
                            ? null
                            : $this->getMountedGalleyFileUpload();

                        $newGalley = CreateSubmissionGalleyAction::run($this->submission, $data, $componentFile);

                        if ($newGalley instanceof SubmissionGalley) {
                            return $newGalley;
                        }
                    })
                    ->hidden($this->viewOnly),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('2xl')
                    ->successNotificationTitle(__('general.galley_updated_successfully'))
                    ->failureNotificationTitle(__('general.there_was_problem_updating_galley'))
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
