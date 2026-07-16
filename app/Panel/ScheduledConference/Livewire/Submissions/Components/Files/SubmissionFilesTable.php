<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Components\Files;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Throwable;
use Filament\Actions\DeleteAction;
use App\Actions\SubmissionFiles\UploadSubmissionFileAction;
use App\Classes\Log;
use App\Facades\Setting;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\MediaStream;

abstract class SubmissionFilesTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public ?int $reviewRoundId = null;

    protected ?string $category = null;

    protected string $tableHeading = 'Files';

    protected string $tableDescription = '';

    public ?array $uploadFilesData = [];

    public function isViewOnly(): bool
    {
        return $this->viewOnly;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    protected function shouldFilterByReviewRound(): bool
    {
        return false;
    }

    protected function resolveUploadReviewRoundId(): ?int
    {
        return null;
    }

    public function tableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->wrap(),
            TextColumn::make('media.original_file_name')
                ->wrap()
                ->label(__('general.filename'))
                ->color('primary')
                ->action(function (SubmissionFile $record) {
                    $name = implode('-', [
                        $record->getKey(),
                        str_replace(['/', '\\'], '-', $record->user->full_name),
                        Str::limit(basename($record->media->name), 100, ''),
                    ]);

                    return response()->download($record->media->getPath(), $name.'.'.$record->media->extension);
                })
                ->description(fn (SubmissionFile $record) => $record->type->name),
            TextColumn::make('created_at')
                ->label(__('general.uploaded_at'))
                ->formatStateUsing(fn ($state) => $state?->format(Setting::get('format_date').' '.Setting::get('format_time')))
                ->sortable(),
        ];
    }

    public function downloadAllAction(): \Filament\Actions\Action
    {
        return Action::make('download_all')
            ->icon('iconpark-download-o')
            ->label(__('general.download_all_files'))
            ->button()
            ->hidden(fn (Table $table): bool => ! $table->getQuery()->exists() || $this->isViewOnly())
            ->color('gray')
            ->action(function (Action $action) {
                $mediaIds = $this->tableQuery()->pluck('media_id');
                $files = $this->submission->media()
                    ->whereIn('id', $mediaIds)
                    ->get();
                if ($files->count()) {
                    $name = implode('-', [
                        $this->submission->getKey(),
                        'files',
                    ]);

                    return MediaStream::create($name.'.zip')->addMedia($files);
                }
                $action->failureNotificationTitle(__('general.nothing_to_download'));
                $action->failure();
            });
    }

    public function uploadFormSchema(): array
    {
        return [
            Select::make('type')
                ->label(__('general.type'))
                ->required()
                ->options(fn () => $this->submissionFileTypeOptions())
                ->searchable(),
            SpatieMediaLibraryFileUpload::make('files')
                ->required()
                ->previewable(false)
                ->downloadable()
                ->reorderable()
                ->disk('private-files')
                ->collection($this->category)
                ->visibility('private')
                ->model(fn () => $this->submission)
                ->saveRelationshipsUsing(function (SpatieMediaLibraryFileUpload $component) {
                    $component->saveUploadedFiles();

                    $this->uploadFilesData[] = $component->getState();
                }),
        ];
    }

    public function handleUploadAction(array $data, Action $action): void
    {
        $getUuids = [];
        foreach ($this->uploadFilesData as $val) {
            if (is_array($val)) {
                $getUuids = array_merge($getUuids, array_values($val));
            } elseif (is_string($val)) {
                $getUuids[] = $val;
            }
        }
        $files = $this->submission->media()->whereCollectionName($this->category)->whereIn('uuid', $getUuids)->get();
        foreach ($files as $file) {
            $submissionFile = UploadSubmissionFileAction::run(
                $this->submission,
                $file,
                $this->category,
                $this->resolveSubmissionFileType($data['type']),
                $this->resolveUploadReviewRoundId(),
            );

            Log::make(
                name: 'submission',
                subject: $this->submission,
                description: __('general.submission_file_uploaded_activity', [
                    'id' => $submissionFile->getKey(),
                    'name' => $file->original_file_name,
                    'category' => $this->category,
                ]),
                event: 'submission-file-upload',
            )
                ->by(auth()->user())
                ->save();
        }

        $this->uploadFilesData = [];

        $action->success();
        $this->dispatch('refreshLivewire');
    }

    public function uploadAction(): \Filament\Actions\Action
    {
        return Action::make('upload')
            ->icon('iconpark-upload')
            ->label(__('general.upload_files'))
            ->outlined()
            ->hidden(fn (): bool => $this->isViewOnly())
            ->modalWidth('xl')
            ->schema(
                $this->uploadFormSchema()
            )
            ->successNotificationTitle(__('general.files_added_successfully'))
            ->failureNotificationTitle(__('general.a_problem_adding_files'))
            ->action(
                fn (array $data, Action $action) => $this->handleUploadAction($data, $action)
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
            Action::make('rename')
                ->icon('iconpark-edit')
                ->label(__('general.edit'))
                ->modalWidth('md')
                ->modalHeading(__('general.edit_files'))
                ->hidden(fn (SubmissionFile $record): bool => ! $this->canEditSubmissionFile($record))
                ->successNotificationTitle(__('general.file_updated_successfully'))
                ->mountUsing(function (SubmissionFile $record, Schema $schema) {
                    $schema->fill([
                        'name' => $record->media->name,
                        'type' => $record->submission_file_type_id,
                    ]);
                })
                ->action(function (SubmissionFile $record, array $data, Action $action) {
                    try {
                        DB::beginTransaction();

                        $type = $this->resolveSubmissionFileType($data['type']);
                        $oldTypeName = $record->type->name;
                        $media = $record->media;
                        $oldName = $media->original_file_name;
                        $newName = (string) $data['name'];

                        $media->name = $newName;
                        $renamedFileName = $media->original_file_name;
                        $record->submission_file_type_id = $type->getKey();

                        $log = Log::make(
                            name: 'submission',
                            subject: $this->submission,
                            description: __('general.submission_file_updated_activity', [
                                'id' => $record->getKey(),
                                'oldName' => $oldName,
                                'newName' => $renamedFileName,
                                'oldType' => $oldTypeName,
                                'newType' => $type->name,
                                'category' => $this->category,
                            ]),
                            event: 'submission-file-update',
                        )
                            ->by(auth()->user());

                        $media->save();
                        $record->save();
                        $log->save();

                        DB::commit();
                    } catch (Throwable $th) {
                        DB::rollBack();
                        throw $th;
                    }
                    $action->success();
                    $this->dispatch('refreshLivewire');
                })
                ->modalSubmitActionLabel(__('general.save'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('general.new_filename'))
                        ->formatStateUsing(function (SubmissionFile $record) {
                            return $record->media->name;
                        })
                        ->suffix(function (SubmissionFile $record) {
                            return '.'.$record->media->extension;
                        }),
                    Select::make('type')
                        ->label(__('general.type'))
                        ->required()
                        ->options(fn () => $this->submissionFileTypeOptions())
                        ->searchable(),
                ]),
            DeleteAction::make()
                ->authorize(fn (SubmissionFile $record): bool => $this->canDeleteSubmissionFile($record))
                ->using(function (SubmissionFile $record) {
                    try {
                        DB::beginTransaction();

                        $record->delete();

                        Log::make(
                            name: 'submission',
                            subject: $this->submission,
                            description: __('general.submission_file_deleted_activity', [
                                'id' => $record->getKey(),
                                'name' => $record->media->file_name,
                            ]),
                            event: 'submission-file-deleted',
                        )
                            ->by(auth()->user())
                            ->save();

                        DB::commit();
                    } catch (Throwable $th) {
                        DB::rollBack();

                        throw $th;
                    }

                    $this->dispatch('refreshLivewire');
                })
                ->hidden(fn (SubmissionFile $record): bool => ! $this->canDeleteSubmissionFile($record)),
        ];
    }

    protected function canEditSubmissionFile(SubmissionFile $record): bool
    {
        return ! $this->isViewOnly() && ! $this->submission->isDeclined();
    }

    protected function canDeleteSubmissionFile(SubmissionFile $record): bool
    {
        return ! $this->isViewOnly() && auth()->user()->can('deleteFile', $record->submission);
    }

    public function tableQuery(): Builder
    {
        $query = $this->submission
            ->submissionFiles()
            ->with(['media', 'submission'])
            ->where('category', $this->category)
            ->getQuery();

        if ($this->shouldFilterByReviewRound()) {
            $query->where('review_round_id', $this->reviewRoundId ?: 0);
        }

        return $query;
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
            ->emptyStateHeading(__('general.no_files'))
            ->query($this->tableQuery())
            ->columns($this->tableColumns())
            ->headerActions($this->headerActions())
            ->recordActions($this->tableActions())
            ->toolbarActions($this->bulkActions());
    }

    public function bulkActions(): array
    {
        return [];
    }

    protected function submissionFileTypeOptions(): array
    {
        return $this->submissionFileTypeQuery()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function resolveSubmissionFileType(int|string $id): SubmissionFileType
    {
        return $this->submissionFileTypeQuery()
            ->whereKey($id)
            ->firstOrFail();
    }

    protected function submissionFileTypeQuery()
    {
        return SubmissionFileType::withoutGlobalScopes()
            ->where('scheduled_conference_id', $this->submission->scheduled_conference_id);
    }

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.components.files.media-file-table');
    }
}
