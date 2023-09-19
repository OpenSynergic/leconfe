<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Participants\ParticipantUpdateAction;
use App\Actions\Submissions\SubmissionAssignAuthorAction;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Resources\AuthorPositionResource;
use App\Panel\Resources\Conferences\ParticipantResource;
use App\Panel\Resources\Conferences\SpeakerPositionResource;
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
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AuthorsStep extends Component implements HasForms, HasTable, HasWizardStep
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public static function getWizardLabel(): string
    {
        return 'Authors';
    }

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard.steps.authors-step');
    }

    public function table(Table $table)
    {
        return $table
            ->query($this->getTableQuery())
            ->heading("Authors")
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading("Add Author")
                        ->successNotificationTitle("Author added")
                        ->form($this->getAuthorFormSchema())
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
                                            return $this->getTableQuery(submissionRelated: false)
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
                        ->toggleable(),
                    TextColumn::make('fullName')
                        ->description(function (Participant $record): ?string {
                            $description = $this->record->participants()
                                ->where('participant_id', $record->id)
                                ->first()
                                ?->position
                                ?->name;
                            if (auth()->user()->email == $record->email) {
                                return $description . ' (you)';
                            }

                            return $description;
                        })
                        ->size('sm')
                        ->wrap()
                        ->weight('bold'),
                    Stack::make([
                        TextColumn::make('affiliation')
                            ->getStateUsing(fn ($record) => $record->getMeta('affiliation'))
                            ->icon("heroicon-o-building-library")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('email')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                        TextColumn::make('scopus'),
                        TextColumn::make('orcid'),
                        TextColumn::make('google_scholar'),
                    ])
                        ->space(2),
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
                        ->form($this->getAuthorFormSchema())
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
            ])
            ->reorderable('order_column');
    }

    public function nextStep()
    {
        if ($this->getTableQuery()->count()) {
            $this->addError('errors', 'You must add at least one author');
            return;
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'for-the-editor',
        ], $this->record);

        $this->dispatch('next-wizard-step');
        $this->dispatch('refreshLivewire');
    }

    protected function getTableQuery(bool $submissionRelated = true): Builder
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

    protected function getAuthorFormSchema(): array
    {
        return [
            Grid::make()
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

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No authors yet';
    }


    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate($this->getTableRecordsPerPage() == -1 ? $query->count() : $this->getTableRecordsPerPage());
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'order_column';
    }
}
