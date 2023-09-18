<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Authors\AuthorCreateAction;
use App\Actions\Authors\AuthorUpdateAction;
use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Submissions\SubmissionAddAuthorAction;
use App\Actions\Submissions\SubmissionAddParticipant;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Author;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Models\SubmissionParticipant;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Squire\Models\Country;

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

    public function nextStep()
    {
        if ($this->getTableQuery()->count() < 1) {
            return session()->flash('no_authors', 'No authors were added to the submission');
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'for-the-editor',
        ], $this->record);

        $this->dispatch('next-wizard-step');
        $this->dispatch('refreshLivewire');
    }

    protected function getTableQuery(): Builder
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
            ->whereIn(
                'id',
                $this->record->participants()->pluck('participant_id')
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

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Author')
                ->modalWidth('2xl')
                ->modalHeading("Add Author")
                ->form($this->getAuthorFormSchema())
                ->using(function (array $data) {
                    $participant = ParticipantCreateAction::run($data);
                    $positionAuthor = ParticipantPosition::find($data['type']);
                    SubmissionAddParticipant::run($this->record, $participant, $positionAuthor);
                })
        ];
    }

    protected function getTableColumns(): array
    {
        return [
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
