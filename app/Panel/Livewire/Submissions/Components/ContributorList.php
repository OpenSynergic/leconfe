<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Participants\ParticipantUpdateAction;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Panel\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
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
            ->when(
                $submissionRelated,
                fn (Builder $query) => $query->whereIn('id', $this->submission->contributors()->pluck('participant_id'))
            )
            ->when(
                !$submissionRelated,
                fn (Builder $query) => $query->whereNotIn('id', $this->submission->contributors()->pluck('participant_id'))
            )
            ->orderBy('order_column');
    }

    public static function getContributorFormSchema(): array
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
            ->reorderable(
                fn () => $this->viewOnly ? false : 'order_column'
            )
            ->heading("Contributors")
            ->query(
                fn (): Builder => $this->getQuery()
            )
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->hidden(
                            fn (Participant $record): bool => $record->email == auth()->user()->email
                        )
                        ->mutateRecordDataUsing(function ($data, Participant $record) {
                            $data['meta'] = $record->getAllMeta()->toArray();
                            $contributor = $this->submission->contributors()->where('participant_id', $record->getKey())->first();
                            $data['position'] = $contributor->position->getKey();
                            return $data;
                        })
                        ->form(static::getContributorFormSchema())
                        ->using(function (array $data, Participant $record) {
                            $participant = ParticipantUpdateAction::run($record, $data);
                            $this->submission
                                ->contributors()
                                ->updateOrCreate([
                                    'participant_id' => $participant->getKey()
                                ], [
                                    'participant_id' => $participant->getKey(),
                                    'participant_position_id' => $data['position']
                                ]);
                            return $participant;
                        }),
                    DeleteAction::make()
                        ->using(function (Participant $record) {
                            $this->submission->contributors()->where('participant_id', $record->getKey())->delete();
                        })
                        ->hidden(
                            fn (Participant $record): bool => $record->email == auth()->user()->email
                        )
                ])
                    ->hidden($this->viewOnly)
            ])
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading("Add Contributor")
                        ->successNotificationTitle("Contributor added")
                        ->record($this->submission)
                        ->form(static::getContributorFormSchema())
                        ->using(function (array $data) {
                            $participant = Participant::email($data['email'])->first();
                            if (!$participant) {
                                $participant = ParticipantCreateAction::run($data);
                            }
                            $this->submission
                                ->contributors()
                                ->updateOrCreate([
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
                    ->label("Add Contributor")
                    ->hidden($this->viewOnly)
            ])
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (Participant $record): string => $record->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(!$this->viewOnly),
                    Stack::make([
                        TextColumn::make('fullName')
                            ->formatStateUsing(function (Participant $record) {
                                if ($record->email == auth()->user()->email) {
                                    return $record->fullName . " (You)";
                                }
                                return $record->fullName;
                            }),
                        TextColumn::make('affiliation')
                            ->size("xs")
                            ->getStateUsing(
                                fn (Participant $record) => $record->getMeta('affiliation')
                            )
                            ->icon("heroicon-o-building-library")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('email')
                            ->size("xs")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])->space(1),
                    TextColumn::make("position")
                        ->getStateUsing(function (Participant $record) {
                            /**
                             * Questions:
                             * 1. Is this good way ?
                             */
                            $contributor = $this->submission->contributors()
                                ->where('participant_id', $record->getKey())
                                ->first();
                            return $contributor->position->name;
                        })
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
