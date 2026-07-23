<?php

namespace App\Panel\ScheduledConference\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\ManageSubmissions;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\CreateSubmission;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\CompleteSubmission;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\ViewSubmission;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\ReviewSubmissionPage;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\ReviewerInvitationPage;
use App\Constants\ReviewerStatus;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubmissionResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $model = Submission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->getMeta('title') ?? static::getModelLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('general.submissions');
    }

    public static function getModelLabel(): string
    {
        return __('general.submissions');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('viewAny', Submission::class);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'editors',
                'reviews' => fn ($query) => $query->activeAssignments(),
                'reviews as completed_reviews_count' => fn ($query) => $query->submittedForDecision(),
            ])
            ->with([
                'meta',
                'user' => ['meta'],
                'participants',
                'editors',
                'activeReviewRound',
                'latestReviewRound' => fn ($query) => $query
                    ->withCount([
                        'reviews as latest_round_reviews_count',
                        'reviews as latest_round_completed_reviews_count' => fn ($reviewQuery) => $reviewQuery
                            ->whereNotNull('date_completed'),
                    ])
                    ->withAvg([
                        'reviews as latest_round_reviews_avg_score' => fn ($reviewQuery) => $reviewQuery
                            ->whereNotNull('date_completed'),
                    ], 'score'),
            ])
            ->orderBy('updated_at', 'desc');
    }

    public static function getLatestReviewRoundBadgeState(Submission $record): ?string
    {
        if ($record->status !== SubmissionStatus::OnReview) {
            return null;
        }

        $reviewRound = $record->latestReviewRound;

        if (! $reviewRound) {
            return null;
        }

        return filled($reviewRound->name)
            ? $reviewRound->name
            : __('general.round').' '.$reviewRound->round_number;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function (Submission $record) {
                $review = $record->getReviewForUserInActiveRound(auth()->user());
                if ($review) {
                    if ($review->needConfirmation() || $review->status == ReviewerStatus::DECLINED) {
                        return static::getUrl('reviewer-invitation', [
                            'record' => $record->id,
                        ]);
                    } else {
                        return static::getUrl('review', [
                            'record' => $record->id,
                        ]);
                    }
                }

                return static::getUrl('view', [
                    'record' => $record->id,
                    // 'stage' => '-'.str($record->stage->value)->slug('-').'-tab',
                ]);
            })
            ->columns([
                Split::make([
                    TextColumn::make('id')
                        ->grow(false)
                        ->searchable()
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ]),
                    Stack::make([
                        TextColumn::make('title')
                            ->getStateUsing(fn (Submission $record) => $record->getMeta('title'))
                            ->description(function (Submission $record) {
                                $review = $record->getReviewForUserInActiveRound(auth()->user());
                                if ($review) {
                                    return $review->isShowAuthor() ? $record->user->fullName : '';
                                }

                                return $record->user->fullName;
                            })
                            ->wrap()
                            ->searchable(query: function (Builder $query, string $search): Builder {
                                return $query
                                    ->whereMeta('title', 'like', "%{$search}%")
                                    ->orWhereHas('user', fn ($query) => $query->whereMeta('public_name', 'like', "%{$search}%")->orWhere('given_name', 'like', "%{$search}%")->orWhere('family_name', 'like', "%{$search}%"));
                            }),
                        Split::make([
                            TextColumn::make('status')
                                ->grow(false)
                                ->badge()
                                ->getStateUsing(fn (Submission $record) => $record->status?->value),
                            TextColumn::make('latest-review-round')
                                ->grow(false)
                                ->badge()
                                ->color('info')
                                ->getStateUsing(fn (Submission $record) => static::getLatestReviewRoundBadgeState($record)),
                        ])
                            ->extraAttributes([
                                'class' => 'mt-2',
                            ]),
                        // Tables\Columns\TextColumn::make('editorial_also_as_reviewer')
                        //     ->extraAttributes([
                        //         'class' => 'mt-2',
                        //     ])
                        //     ->html()
                        //     ->url('#')
                        //     ->getStateUsing(fn(Submission $record) => view('panel.conference.resources.submission-resource.reviewer-editor', ['record' => $record])),
                    ]),
                    Stack::make([
                        TextColumn::make('editor-assigned-badges')
                            ->badge()
                            ->extraAttributes([
                                'class' => 'mt-2',
                            ])
                            ->extraCellAttributes([
                                'style' => 'width: 1px',
                            ])
                            ->color('warning')
                            ->getStateUsing(function (Submission $record) {
                                $isEditorAssigned = $record->editors_count;

                                if (! $isEditorAssigned && $record->stage != SubmissionStage::Wizard) {
                                    return __('general.no_editor_assigned');
                                }
                            }),
                        TextColumn::make('reviews')
                            ->extraCellAttributes([
                                'style' => 'width: 1px',
                            ])
                            ->getStateUsing(fn (Submission $record) => $record->latestReviewRound && $record->latestReviewRound->latest_round_reviews_count
                                ? view('panel.scheduledConference.components.review-count', [
                                    'reviews_count' => $record->latestReviewRound->latest_round_reviews_count,
                                    'completed_reviews_count' => $record->latestReviewRound->latest_round_completed_reviews_count,
                                ])
                                : ''),
                        TextColumn::make('reviewed')
                            ->badge()
                            ->color('success')
                            ->getStateUsing(function (Submission $record) {
                                $review = $record->getReviewForUserInActiveRound(auth()->user());
                                if (! $review) {
                                    return '';
                                }

                                if ($review->reviewSubmitted()) {
                                    return __('general.reviewed');
                                }
                            }),
                        TextColumn::make('withdrawn-notification')
                            ->badge()
                            ->extraAttributes([
                                'class' => 'mt-2',
                            ])
                            ->color('danger')
                            ->getStateUsing(function (Submission $record) {
                                if (filled($record->withdrawn_reason) && $record->status !== SubmissionStatus::Withdrawn) {
                                    return __('general.pending_withdrawal');
                                }
                            }),
                    ]),
                ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->icon('lineawesome-eye-solid')
                    ->authorize(function (Submission $record) {
                        return auth()->user()->can('view', $record);
                    })
                    ->url(function (Submission $record) {
                        $review = $record->getReviewForUserInActiveRound(auth()->user());
                        if ($review) {
                            if ($review->needConfirmation() || $review->status == ReviewerStatus::DECLINED) {
                                return static::getUrl('reviewer-invitation', [
                                    'record' => $record->id,
                                ]);
                            } else {
                                return static::getUrl('review', [
                                    'record' => $record->id,
                                ]);
                            }
                        }

                        return static::getUrl('view', [
                            'record' => $record->id,
                            // 'stage' => '-'.str($record->stage->value)->slug('-').'-tab',
                        ]);
                    }),
                Action::make('view_as_editor')
                    ->label(__('general.view_as_editor'))
                    ->icon('lineawesome-eye-solid')
                    ->color('warning')
                    ->visible(fn (Submission $record) => $record->isParticipantEditor(auth()->user()) && $record->isReviewer(auth()->user()))
                    ->url(fn (Submission $record) => static::getUrl('view', [
                        'record' => $record->id,
                    ])),
                DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        array_combine(SubmissionStatus::values(), SubmissionStatus::values())
                    )
                    ->searchable(),
                SelectFilter::make('track')
                    ->relationship('track', 'title')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('topic')
                    ->relationship('topics', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubmissions::route('/'),
            'create' => CreateSubmission::route('/create'),
            'complete' => CompleteSubmission::route('/complete/{record}'),
            'view' => ViewSubmission::route('/{record}'),
            'review' => ReviewSubmissionPage::route('/{record}/review'),
            'reviewer-invitation' => ReviewerInvitationPage::route('/{record}/reviewer-invitation'),
        ];
    }
}
