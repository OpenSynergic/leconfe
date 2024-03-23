<?php

namespace App\Panel\Conference\Livewire\Submissions\Components;

use App\Models\Author;
use App\Models\Submission;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use App\Actions\Authors\AuthorCreateAction;
use App\Actions\Authors\AuthorDeleteAction;
use App\Actions\Authors\AuthorUpdateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Panel\Conference\Resources\Conferences\AuthorRoleResource;
use App\Panel\Conference\Resources\Conferences\ParticipantResource;

class ContributorList extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function getQuery(bool $submissionRelated = true): Builder
    {
        return Author::query()
            ->whereSubmissionId($this->submission->getKey())
            ->with([
                'role',
                'media',
                'meta',
            ])
            // ->when(
            //     $submissionRelated,
            //     fn (Builder $query) => $query->whereIn('id', $this->submission->contributors()->pluck('participant_id'))
            // )
            // ->when(
            //     ! $submissionRelated,
            //     fn (Builder $query) => $query->whereNotIn('id', $this->submission->contributors()->pluck('participant_id'))
            // )
            ->orderBy('order_column');
    }

    public function getContributorFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    SpatieMediaLibraryFileUpload::make('profile')
                        ->label('Profile Picture')
                        ->image()
                        ->key('profile')
                        ->collection('profile')
                        ->conversion('thumb')
                        ->alignCenter()
                        ->columnSpan([
                            'lg' => 2,
                        ]),
                    TextInput::make('given_name')
                        ->required(),
                    TextInput::make('family_name'),
                    TextInput::make('email')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: function (Unique $rule) {
                                return $rule->where('submission_id', $this->submission->getKey());
                            }
                        )
                        ->columnSpan([
                            'lg' => 2,
                        ]),
                    Select::make('author_role_id')
                        ->relationship(
                            name: 'role',
                            titleAttribute: 'name',
                        )
                        ->createOptionForm(fn ($form) => AuthorRoleResource::form($form))
                        ->createOptionAction(
                            fn (FormAction $action) => $action->color('primary')
                                ->modalWidth('xl')
                                ->modalHeading('Create Author Role')
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
            ->heading('Contributors')
            ->query(
                fn (): Builder => $this->getQuery()
            )
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('3xl')
                        ->hidden(
                            fn (Model $record): bool => $record->email == auth()->user()->email
                        )
                        ->mutateRecordDataUsing(function (array $data, Model $record) {
                            $data['meta'] = $record->getAllMeta();
                            return $data;
                        })
                        ->form($this->getContributorFormSchema())
                        ->using(fn (array $data, Author $record) => AuthorUpdateAction::run($data, $record)),
                    DeleteAction::make()
                        ->using(fn (array $data, Model $record) => AuthorDeleteAction::run($record, $data))
                        ->hidden(
                            fn (Model $record): bool => $record->email == auth()->user()->email
                        ),
                ])
                    ->hidden($this->viewOnly),
            ])
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading('Add Contributor')
                        ->successNotificationTitle('Contributor added')
                        ->record($this->submission)
                        ->form($this->getContributorFormSchema())
                        ->using(function (array $data) {
                            $author = Author::whereSubmissionId($this->submission->getKey())->email($data['email'])->first();
                            if (! $author) {
                                $author = AuthorCreateAction::run($this->submission, $data);
                            }
                            
                            return $author;
                        }),
                    Action::make('add_existing')
                        ->label('Add from existing')
                        ->modalWidth('lg')
                        ->form([
                            Grid::make()
                                ->schema([
                                    Select::make('author_id')
                                        ->label('Name')
                                        ->options(function () {
                                            $authors = $this->getQuery()->pluck('email')->toArray();

                                            return Author::query()
                                                ->limit(10)
                                                ->whereNotIn('email', $authors)
                                                ->get()
                                                ->mapWithKeys(fn (Author $author) => [$author->getKey() => static::renderSelectAuthor($author)])
                                                ->toArray();
                                        })
                                        ->getSearchResultsUsing(
                                            function (string $search) {
                                                $authors = $this->getQuery()->pluck('email')->toArray();
        
                                                return Author::query()
                                                    ->with(['media', 'meta'])
                                                    ->whereNotIn('email', $authors)
                                                    ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                        ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                        ->orWhere('email', 'LIKE', "%{$search}%"))
                                                    ->get()
                                                    ->mapWithKeys(fn (Author $author) => [$author->getKey() => static::renderSelectAuthor($author)])
                                                    ->toArray();
                                            }
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->allowHtml()
                                        ->required()
                                        ->columnSpanFull(),
                                    Select::make('author_role_id')
                                        ->options(
                                            fn () => AuthorRoleResource::getEloquentQuery()
                                                ->pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->action(function (array $data) {
                            $author = Author::find($data['author_id']);

                            $newAuthor = $this->submission->authors()->create([
                                ...$author->only(['given_name', 'family_name', 'email']),
                                'author_role_id' => $data['author_role_id'],
                            ]);

                            if ($meta = $author->getAllMeta()->toArray()) {
                                $newAuthor->setManyMeta($meta);
                            }    
                        }),
                ])
                    ->button()
                    ->label('Add Contributor')
                    ->hidden($this->viewOnly),
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
                            fn (Model $record): string => $record->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(! $this->viewOnly),
                    Stack::make([
                        TextColumn::make('fullName')
                            ->formatStateUsing(function (Model $record) {
                                if ($record->email == auth()->user()->email) {
                                    return $record->fullName.' (You)';
                                }

                                return $record->fullName;
                            }),
                        TextColumn::make('affiliation')
                            ->size('xs')
                            ->getStateUsing(
                                fn (Model $record) => $record->getMeta('affiliation')
                            )
                            ->icon('heroicon-o-building-library')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('email')
                            ->size('xs')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])->space(1),
                    TextColumn::make('role.name')
                        ->badge()
                        ->alignEnd(),
                ]),
            ]);
    }

    public static function renderSelectAuthor(Author $author): string
    {
        return view('forms.select-participant', ['participant' => $author])->render();
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.contributor-list');
    }
}
