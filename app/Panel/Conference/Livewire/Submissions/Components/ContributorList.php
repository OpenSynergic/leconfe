<?php

namespace App\Panel\Conference\Livewire\Submissions\Components;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Participants\ParticipantUpdateAction;
use App\Models\Author;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Panel\Conference\Resources\Conferences\AuthorRoleResource;
use App\Panel\Conference\Resources\Conferences\ParticipantResource;
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
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class ContributorList extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function getQuery(bool $submissionRelated = true): Builder
    {
        return Author::query()
            ->whereSubmissionId($this->submission->getKey())
            ->orderBy('order_column')
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

    public static function getContributorFormSchema(): array
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
                        ->modalWidth('2xl')
                        ->hidden(
                            fn (Model $record): bool => $record->email == auth()->user()->email
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
                                    'participant_id' => $participant->getKey(),
                                ], [
                                    'participant_id' => $participant->getKey(),
                                    'participant_position_id' => $data['position'],
                                ]);

                            return $participant;
                        }),
                    DeleteAction::make()
                        ->using(function (Model $record) {
                            $this->submission->contributors()->where('participant_id', $record->getKey())->delete();
                        })
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
                        ->form(static::getContributorFormSchema())
                        ->using(function (array $data, $record) {
                            $author = Author::whereSubmissionId($record->getKey())->email($data['email'])->first();
                            if (! $author) {
                                // $participant = ParticipantCreateAction::run($data);
                            }
                            dd($author);
                            
                            return $author;
                        }),
                    // Action::make('add_existing')
                    //     ->label('Add from existing')
                    //     ->modalWidth('lg')
                    //     ->form([
                    //         Grid::make()
                    //             ->schema([
                    //                 Select::make('participant_id')
                    //                     ->label('Name')
                    //                     ->options(function () {
                    //                         return $this->getQuery(submissionRelated: false)
                    //                             ->get()
                    //                             ->pluck('fullName', 'id')
                    //                             ->toArray();
                    //                     })
                    //                     ->searchable()
                    //                     ->preload()
                    //                     ->required()
                    //                     ->columnSpanFull(),
                    //                 Select::make('type')
                    //                     ->options(
                    //                         fn () => ParticipantPosition::whereIn('type', ['speaker', 'author'])->pluck('name', 'id')->toArray()
                    //                     )
                    //                     ->searchable()
                    //                     ->preload()
                    //                     ->required()
                    //                     ->columnSpanFull(),
                    //             ]),
                    //     ])
                    //     ->action(function (array $data) {
                    //         $participant = Participant::find($data['participant_id']);
                    //         $this->submission->contributors()->updateOrCreate([
                    //             'participant_id' => $participant->getKey(),
                    //         ], [
                    //             'participant_id' => $participant->getKey(),
                    //             'participant_position_id' => $data['type'],
                    //         ]);

                    //         return $participant;
                    //     }),
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

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.contributor-list');
    }
}
