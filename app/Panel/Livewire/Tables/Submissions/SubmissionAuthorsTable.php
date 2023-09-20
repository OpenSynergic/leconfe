<?php

namespace App\Panel\Livewire\Tables\Submissions;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Participants\ParticipantUpdateAction;
use App\Actions\Submissions\SubmissionAssignAuthorAction;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Panel\Resources\AuthorPositionResource;
use App\Panel\Resources\Conferences\ParticipantResource;
use App\Panel\Resources\Conferences\SpeakerPositionResource;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Component;

class SubmissionAuthorsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public Submission $record;

    public bool $viewOnly = false;

    public static function getAuthorFormSchema(): array
    {
        return [
            FormGrid::make()
                ->schema([
                    ...ParticipantResource::generalFormField(),
                    Select::make('position')
                        ->relationship(
                            name: 'positions',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', [AuthorPositionResource::$positionType, SpeakerPositionResource::$positionType])
                                ->groupBy('name')
                        )
                        ->preload()
                        ->saveRelationshipsUsing(function (Select $component, Participant $participant, $state) {
                            $participant->positions()->detach($participant->positions);
                            $participant->positions()->attach($state);
                        })
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

    public function getQuery(bool $submissionRelated = false): Builder
    {
        return Participant::query()
            ->orderBy('order_column')
            ->with([
                'positions' => fn ($query) => $query
                    ->where('type', AuthorPositionResource::$positionType),
                'media',
                'meta'
            ])

            ->whereHas(
                'positions',
                fn (Builder $query) => $query->where('type', AuthorPositionResource::$positionType)
            )
            ->when(
                $submissionRelated,
                fn (Builder $query) => $query->whereIn('id', $this->record->participants()->pluck('participant_id'))
            )
            ->when(
                !$submissionRelated,
                fn (Builder $query) => $query->whereNotIn('id', $this->record->participants()->pluck('participant_id'))
            );
    }

    public function table(Table $table, $query = false): Table
    {
        return $table
            ->query(fn (): Builder => $this->getQuery())
            ->heading("Authors")
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading("Add Author")
                        ->successNotificationTitle("Author added")
                        ->form(static::getAuthorFormSchema())
                        ->using(function (array $data) {
                            $participant = Participant::byEmail($data['email']);
                            $participant = $participant ?: ParticipantCreateAction::run($data);
                            $positionAuthor = ParticipantPosition::find($data['position']);
                            SubmissionAssignAuthorAction::run($this->record, $participant, $positionAuthor);
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
                                        ->relationship(
                                            name: 'positions',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', ['author', 'speaker'])
                                                ->groupBy('name')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (array $data) {
                            $participant = Participant::find($data['participant_id']);
                            $position = ParticipantPosition::find($data['type']);
                            SubmissionAssignAuthorAction::run($this->record, $participant, $position);
                            return $participant;
                        })
                ])
                    ->button()
                    ->label("Add author")
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
                        ->defaultImageUrl(function (Participant $record): string {
                            $name = str($record->fullName)
                                ->trim()
                                ->explode(' ')
                                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                                ->join(' ');
                            return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827&font-size=0.33';
                        })
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(!$this->viewOnly),
                    Stack::make([
                        TextColumn::make('fullName'),
                        TextColumn::make('affiliation')
                            ->size("xs")
                            ->getStateUsing(fn ($record) => $record->getMeta('affiliation'))
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
                            return $this->record->participants()
                                ->where('participant_id', $record->id)
                                ->first()
                                ?->position
                                ?->name;
                        })
                        ->badge()
                        ->alignEnd()
                ])
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->hidden(
                            fn (Participant $record): bool => $record->email == auth()->user()->email
                        )
                        ->mutateRecordDataUsing(function ($data, Participant $record) {
                            $data['meta'] = $record->getAllMeta()->toArray();

                            $data['position'] = $this->record->participants()
                                ->where('participant_id', $record->id)
                                ->first()
                                ?->position
                                ?->id;

                            return $data;
                        })
                        ->form(static::getAuthorFormSchema())
                        ->using(function (array $data, Participant $record) {
                            $participant = ParticipantUpdateAction::run($record, $data);
                            SubmissionAssignAuthorAction::run($this->record, $participant, ParticipantPosition::find($data['position']));
                            return $participant;
                        }),
                    DeleteAction::make()
                        ->hidden(
                            fn (Participant $record): bool => $record->email == auth()->user()->email
                        )
                ])
                    ->hidden($this->viewOnly)
            ])
            ->reorderable(function () {
                if ($this->viewOnly) {
                    return false;
                }
                return "order_column";
            });
    }

    public function render()
    {
        return view('panel.livewire.tables.submissions.submission-author-table');
    }
}