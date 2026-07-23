<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Components\Files;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Actions\Submissions\CloneSubmissionFilesToReviewRoundAction;
use App\Constants\SubmissionFileCategory;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionReviewRound;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Spatie\MediaLibrary\Support\MediaStream;

class ReviewFiles extends SubmissionFilesTable implements HasActions
{
    use InteractsWithActions;
    protected ?string $category = SubmissionFileCategory::REVIEW_FILES;

    protected string $tableHeading;

    public function __construct()
    {
        $this->tableHeading = __('general.review_files');
    }

    public function mount(Submission $submission, ?int $reviewRoundId = null): void
    {
        $this->submission = $submission;
        $this->reviewRoundId = $reviewRoundId && $submission->reviewRounds()->whereKey($reviewRoundId)->exists()
            ? $reviewRoundId
            : ($submission->activeReviewRound?->getKey() ?? $submission->latestReviewRound?->getKey());
    }

    #[On('peer-review-round-selected')]
    public function onReviewRoundSelected(int $roundId): void
    {
        $this->reviewRoundId = $roundId;
        $this->resetTable();
    }

    public function getSelectedRoundProperty(): ?SubmissionReviewRound
    {
        if (! $this->reviewRoundId) {
            return null;
        }

        return $this->submission->reviewRounds()
            ->whereKey($this->reviewRoundId)
            ->first();
    }

    public function getPreviousReviewRoundProperty(): ?SubmissionReviewRound
    {
        $selectedRound = $this->selectedRound;

        if (! $selectedRound) {
            return null;
        }

        return $this->submission->reviewRounds()
            ->where('round_number', '<', $selectedRound->round_number)
            ->orderByDesc('round_number')
            ->first();
    }

    public function getPreviousRoundFilesProperty(): Collection
    {
        if (! $this->previousReviewRound) {
            return collect();
        }

        return $this->submission->submissionFiles()
            ->with(['media', 'type'])
            ->whereIn('category', [SubmissionFileCategory::REVIEW_FILES, SubmissionFileCategory::REVISION_FILES])
            ->where('review_round_id', $this->previousReviewRound->getKey())
            ->orderBy('id')
            ->get();
    }

    public function headerActions(): array
    {
        return [
            ActionGroup::make([
                $this->selectFilesAction(),
                ...parent::headerActions(),
            ])
                ->button()
                ->color('gray')
                ->label(__('general.actions'))
                ->hidden(fn (): bool => $this->isViewOnly()),
        ];
    }

    public function downloadAllAction(): Action
    {
        return Action::make('download_all')
            ->icon('heroicon-o-arrow-down-tray')
            ->label(__('general.download_all_files'))
            ->color('primary')
            ->hidden(fn (): bool => ! $this->canManageReviewFiles() || ! $this->tableQuery()->exists())
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

    public function uploadAction(): \Filament\Actions\Action|\Filament\Actions\ActionGroup
    {
        return Action::make('upload')
            ->icon('heroicon-o-cloud-arrow-up')
            ->label(__('general.upload_files'))
            ->color('success')
            ->hidden(fn (): bool => $this->isViewOnly())
            ->modalWidth('xl')
            ->schema($this->uploadFormSchema())
            ->successNotificationTitle(__('general.files_added_successfully'))
            ->failureNotificationTitle(__('general.a_problem_adding_files'))
            ->action(
                fn (array $data, Action $action) => $this->handleUploadAction($data, $action)
            );
    }

    protected function shouldFilterByReviewRound(): bool
    {
        return true;
    }

    protected function resolveUploadReviewRoundId(): ?int
    {
        return $this->reviewRoundId;
    }

    protected function isSelectedRoundOpen(): bool
    {
        if (! $this->reviewRoundId) {
            return false;
        }

        $activeRoundId = $this->submission->reviewRounds()
            ->open()
            ->orderByDesc('round_number')
            ->value('id');

        return $activeRoundId && (int) $activeRoundId === $this->reviewRoundId;
    }

    public function isViewOnly(): bool
    {
        return ! $this->canUploadReviewFiles();
    }

    protected function canUploadReviewFiles(): bool
    {
        if (! $this->isSelectedRoundOpen()) {
            return false;
        }

        if ($this->viewOnly) {
            return false;
        }

        $user = auth()->user();

        return $user?->can('actAsEditor', $this->submission)
            || $user?->is($this->submission->user);
    }

    protected function canManageReviewFiles(): bool
    {
        if (! $this->isSelectedRoundOpen()) {
            return false;
        }

        if ($this->viewOnly) {
            return false;
        }

        return auth()->user()?->can('actAsEditor', $this->submission) ?? false;
    }

    protected function canEditSubmissionFile(SubmissionFile $record): bool
    {
        return $this->canManageReviewFiles() && ! $this->submission->isDeclined();
    }

    protected function canDeleteSubmissionFile(SubmissionFile $record): bool
    {
        return $this->canManageReviewFiles() && auth()->user()->can('deleteFile', $record->submission);
    }

    public function selectFilesAction(): Action
    {
        return Action::make('select-files')
            ->label(__('general.select_files'))
            ->icon('heroicon-o-document-duplicate')
            ->color('warning')
            ->modalWidth('xl')
            ->modalHeading(fn () => $this->previousReviewRound
                ? __('general.select_files').' '.__('general.round').' '.$this->previousReviewRound->round_number
                : __('general.select_files'))
            ->hidden(fn (): bool => ! $this->canTakeFromPreviousRound())
            ->schema([
                CheckboxList::make('file_ids')
                    ->label(__('general.files_from_previous_round'))
                    ->options(function () {
                        return $this->previousRoundFiles
                            ->mapWithKeys(fn (SubmissionFile $file) => [
                                $file->getKey() => $file->media?->name ?? 'File #'.$file->getKey(),
                            ])
                            ->toArray();
                    })
                    ->descriptions(function () {
                        return $this->previousRoundFiles
                            ->mapWithKeys(fn (SubmissionFile $file) => [
                                $file->getKey() => $file->type->name.' ('.$file->category.')',
                            ])
                            ->toArray();
                    }),
            ])
            ->successNotificationTitle(__('general.files_added_successfully'))
            ->action(function (array $data, Action $action) {
                $selectedFileIds = collect($data['file_ids'] ?? [])
                    ->filter(fn ($id) => is_numeric($id))
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                $clonedFileIds = CloneSubmissionFilesToReviewRoundAction::run(
                    $this->submission,
                    $this->selectedRound,
                    $selectedFileIds,
                    SubmissionFileCategory::REVIEW_FILES,
                );

                if ($clonedFileIds === []) {
                    $action->failureNotificationTitle(__('general.no_files'));
                    $action->failure();

                    return;
                }

                $this->resetTable();
                $action->success();
            });
    }

    protected function canTakeFromPreviousRound(): bool
    {
        return $this->isSelectedRoundOpen()
            && $this->previousReviewRound !== null
            && $this->previousRoundFiles->isNotEmpty()
            && $this->canManageReviewFiles();
    }
}
