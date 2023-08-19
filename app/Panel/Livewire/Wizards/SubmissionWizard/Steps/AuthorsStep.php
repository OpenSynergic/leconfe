<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Authors\AuthorCreateAction;
use App\Actions\Authors\AuthorUpdateAction;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Models\Author;
use App\Models\Submission;
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
use Livewire\Component;
use Squire\Models\Country;

class AuthorsStep extends Component implements HasWizardStep, HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

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

        $this->dispatchBrowserEvent('next-wizard-step');
        $this->emit('refreshLivewire');
    }

    protected function getTableQuery(): Builder
    {
        return $this->record->authors()->with('meta')->getQuery();
    }

    protected function getAuthorFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('meta.given_name')
                        ->required(),
                    TextInput::make('meta.family_name'),
                ]),
            TextInput::make('meta.public_name')
                ->label('Preferred Public Name')
                // ->dehydrated(fn ($state) => filled($state))
                ->helperText('Please provide the full name as the author should be identified on the published work.'),
            TextInput::make('email')
                ->required(),
            Select::make('meta.country')
                ->searchable()
                ->required()
                ->optionsLimit(250)
                ->options(fn () => Country::all()->pluck('name', 'id')),
            TextInput::make('meta.affiliation')
                ->label('Affiliation'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Author')
                ->modalWidth('2xl')
                ->form($this->getAuthorFormSchema())
                ->using(fn (array $data) => AuthorCreateAction::run($this->record, $data)),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Split::make([
                Stack::make([
                    TextColumn::make('public_name')
                        ->size('sm')
                        ->wrap()
                        ->searchable(query: function (Builder $query, string $search): Builder {
                            return $query
                                ->whereMeta('public_name', 'like', "%{$search}%")
                                ->orWhere(fn (Builder $query) => $query->whereMeta('family_name', 'like', "%{$search}%"))
                                ->orWhere(fn (Builder $query) => $query->whereMeta('given_name', 'like', "%{$search}%"));
                        })
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
            EditAction::make()
                ->modalWidth('2xl')
                ->mutateRecordDataUsing(function ($data, Author $record) {
                    $data['meta'] = $record->getAllMeta()->toArray();

                    return $data;
                })
                ->form($this->getAuthorFormSchema())
                ->using(fn (array $data, Author $record) => AuthorUpdateAction::run($data, $record)),
            DeleteAction::make(),
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
