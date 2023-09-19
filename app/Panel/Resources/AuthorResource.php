<?php

namespace App\Panel\Resources;

use App\Actions\Participants\ParticipantCreateAction;
use App\Models\Participant;
use App\Panel\Resources\AuthorResource\Pages;
use App\Panel\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuthorResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Author';
    }

    public static function getModelLabel(): string
    {
        return 'Author';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'positions' => fn ($query) => $query
                    ->whereType(AuthorPositionResource::$positionType),
                'media',
                'meta'
            ])
            ->whereHas(
                'positions',
                fn (Builder $query) => $query->whereType(AuthorPositionResource::$positionType)
            );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...ParticipantResource::generalFormField(),
                Select::make('type')
                    ->relationship(
                        name: 'positions',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereType(
                            AuthorPositionResource::$positionType
                        )
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')

            ->heading("Author table")
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->icon('heroicon-o-user-plus')
                        ->using(fn (array $data) => ParticipantCreateAction::run($data)),
                    Action::make('add_existing')
                        ->label("Add Existing")
                        ->icon('heroicon-o-plus')
                        ->modalWidth("xl")
                        ->form([
                            Select::make('participant_id')
                                ->label("Author")
                                ->required()
                                ->preload()
                                ->searchable()
                                ->allowHtml()
                                ->options(function () {
                                    $participants = static::getEloquentQuery()
                                        ->pluck('id')
                                        ->toArray();

                                    return static::getModel()::query()
                                        ->limit(10)
                                        ->whereNotIn('id', $participants)
                                        ->get()
                                        ->mapWithKeys(fn (Participant $participant) => [$participant->getKey() => static::renderSelectParticipant($participant)])
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(
                                    function (string $search) {
                                        $participants = static::getEloquentQuery()->pluck('id')->toArray();

                                        return static::getModel()::query()
                                            ->with(['media', 'meta'])
                                            ->whereNotIn('id', $participants)
                                            ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                ->orWhere('email', 'LIKE', "%{$search}%"))
                                            ->get()
                                            ->mapWithKeys(fn (Participant $participant) => [$participant->getKey() => static::renderSelectParticipant($participant)])
                                            ->toArray();
                                    }
                                ),
                            Select::make('positions')
                                ->relationship(
                                    name: 'positions',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->whereType('author')
                                )
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            return Participant::find(data_get($data, 'participant_id'))
                                ->positions()
                                ->attach(data_get($data, 'positions'));
                        })
                ])->button(),
            ])
            ->columns([
                ...ParticipantResource::generalTableColumns(),

            ])
            ->actions([
                ...ParticipantResource::tableActions(AuthorPositionResource::$positionType),
            ]);
    }

    public static function renderSelectParticipant(Participant $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuthors::route('/'),
        ];
    }
}
