<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Panel\Resources\Conferences\AuthorPositionResource;
use App\Panel\Resources\Conferences\ParticipantResource;
use App\Panel\Resources\Conferences\SpeakerPositionResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ContributorList extends \Livewire\Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function mount(Submission $submission)
    {
    }

    public function getQuery(bool $submissionRelated = true): Builder
    {
        return Participant::query()
            ->orderBy('order_column')
            ->with([
                'positions' => fn ($query) => $query
                    ->whereIn('type', ['author', 'speaker']),
                'media',
                'meta'
            ])

            ->whereHas(
                'positions',
                fn (Builder $query) => $query->whereIn('type', ['author', 'speaker']),
            )
            ->when(
                $submissionRelated,
                fn (Builder $query) => $query->whereIn('id', $this->submission->participants()->pluck('participant_id'))
            )
            ->when(
                !$submissionRelated,
                fn (Builder $query) => $query->whereNotIn('id', $this->submission->contributors()->pluck('participant_id'))
            );
    }

    public static function getAuthorFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    ...ParticipantResource::generalFormField(),
                    Select::make('position')
                        ->options(
                            fn () => ParticipantPosition::whereIn('type', ['speaker', 'author'])->pluck('name', 'id')->toArray()
                        )
                        ->preload()
                        ->required()
                        ->columnSpanFull()
                        ->searchable(),
                    ...ParticipantResource::additionalFormField(),
                ])
                ->columnSpan([
                    'lg' => 2,
                ]),
        ];
    }
    public function table(Table $table): Table
    {
        return $table
            ->heading("Contributors")
            ->query(
                fn (): Builder => $this->submission->contributors()->with('position')->getQuery()
            )
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading("Add Author")
                        ->successNotificationTitle("Author added")
                        ->record($this->submission)
                        ->form(static::getAuthorFormSchema())
                        ->using(function (array $data) {
                            $participant = Participant::email($data['email'])->first();
                            $this->submission->contributors()->updateOrCreate([
                                'participant_id' => $participant->getKey(),
                            ], [
                                'participant_id' => $participant->getKey(),
                                'participant_position_id' => $data['position']
                            ]);
                            return $participant;
                        }),
                    Action::make('add_existing')
                        ->label("Add from existing")
                        ->modalWidth("lg")
                        ->form([
                            Grid::make()
                                ->schema([
                                    Select::make('participant_id')
                                        ->label("Name")
                                        ->options(function () {
                                            return $this->getQuery(submissionRelated: false)
                                                ->get()
                                                ->pluck('fullName', 'id')
                                                ->toArray();
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                    Select::make('type')
                                        ->options(
                                            fn () => ParticipantPosition::whereIn('type', ['speaker', 'author'])->pluck('name', 'id')->toArray()
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (array $data) {
                            $participant = Participant::find($data['participant_id']);
                            $this->submission->contributors()->updateOrCreate([
                                'participant_id' => $participant->getKey()
                            ], [
                                'participant_id' => $participant->getKey(),
                                'participant_position_id' => $data['type']
                            ]);
                            return $participant;
                        })
                ])
                    ->button()
                    ->label("Add author")
                    ->hidden($this->viewOnly)
            ])
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('participant.profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (SubmissionContributor $record): string => $record->participant->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(!$this->viewOnly),
                    Stack::make([
                        TextColumn::make('participant.fullName')
                            ->formatStateUsing(function (SubmissionContributor $record) {
                                if ($record->participant->email == auth()->user()->email) {
                                    return $record->participant->fullName . " (You)";
                                }
                                return $record->participant->fullName;
                            }),
                        TextColumn::make('affiliation')
                            ->size("xs")
                            ->getStateUsing(
                                fn (SubmissionContributor $record) => $record->participant->getMeta('affiliation')
                            )
                            ->icon("heroicon-o-building-library")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('participant.email')
                            ->size("xs")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])->space(1),
                    TextColumn::make("position.name")
                        ->badge()
                        ->alignEnd()
                ])
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.contributor-list');
    }
}
