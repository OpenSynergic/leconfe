<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Submissions\SubmissionAddParticipant;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
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

    protected function getTableHeading(): string | Htmlable | null
    {
        return "Authors";
    }

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
                        ->form($this->getAuthorFormSchema())
                        ->using(function (array $data) {
                            $participant = ParticipantCreateAction::run($data);
                            $positionAuthor = ParticipantPosition::find($data['type']);
                            SubmissionAddParticipant::run($this->record, $participant, $positionAuthor);
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
                                        ->noSearchResultsMessage('No authors found')
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
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (array $data) {
                            dd($data);
                        })
                ])
                    ->button()
                    ->label("Add author")
            ])
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('fullName')
                            ->size('sm')
                            ->wrap()
                            // ->searchable(query: function (Builder $query, string $search): Builder {
                            //     return $query
                            //         ->whereMeta('public_name', 'like', "%{$search}%")
                            //         ->orWhere(fn (Builder $query) => $query->whereMeta('family_name', 'like', "%{$search}%"))
                            //         ->orWhere(fn (Builder $query) => $query->whereMeta('given_name', 'like', "%{$search}%"));
                            // })
                            ->weight('bold'),
                        TextColumn::make('affiliation')
                            ->getStateUsing(fn ($record) => $record->getMeta('affiliation'))
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                    ]),
                    TextColumn::make('email')
                        ->extraAttributes([
                            'class' => 'text-xs',
                        ])
                        ->color('gray')
                        ->icon('heroicon-o-envelope')
                        ->alignStart(),
                ])->from('lg'),
            ]);
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
        return Participant::query()->with([
            'positions' => fn ($query) => $query
                ->where('type', 'author'),
            'media',
            'meta'
        ])
            ->whereHas(
                'positions',
                fn (Builder $query) => $query->where('type', 'author')
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
                    Select::make('type')
                        ->relationship(
                            name: 'positions',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', ['author', 'speaker'])
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
                ])->columnSpan([
                    'lg' => 2,
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // EditAction::make()
            //     ->modalWidth('2xl')
            //     ->mutateRecordDataUsing(function ($data, Author $record) {
            //         $data['meta'] = $record->getAllMeta()->toArray();

            //         return $data;
            //     })
            //     ->form($this->getAuthorFormSchema())
            //     ->using(fn (array $data, Author $record) => AuthorUpdateAction::run($data, $record)),
            // DeleteAction::make(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No authors yet';
    }

    // public function isTableLoadingDeferred(): bool
    // {
    //     return true;
    // }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate($this->getTableRecordsPerPage() == -1 ? $query->count() : $this->getTableRecordsPerPage());
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'order_column';
    }
}
