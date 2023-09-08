<?php

namespace App\Panel\Resources\Conferences;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Participants\ParticipantUpdateAction;
use App\Models\Participants\Participant;
use App\Models\Participants\ParticipantPosition;
use App\Models\Participants\Speaker;
use App\Models\Scopes\ConferenceScope;
use App\Panel\Resources\Conferences\SpeakerResource\Pages;
use App\Panel\Resources\Conferences\SpeakerResource\Widgets;
use App\Tables\Columns\IndexColumn;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SpeakerResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Speakers';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'positions' => fn ($query) => $query
                    ->ofType(SpeakerPositionResource::$positionType),
                'media',
                'meta'
            ])
            ->whereHas(
                'positions',
                fn (Builder $query) => $query
                    ->ofType(SpeakerPositionResource::$positionType)
            );
    }

    public static function getModelLabel(): string
    {
        return 'Speaker';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TODO : Add search field to select existing speaker. Search across all conferences
                Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                    ->image()
                    ->key('photo')
                    ->collection('photo')
                    ->conversion('thumb')
                    ->alignCenter()
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                Forms\Components\TextInput::make('given_name')
                    ->required(),
                Forms\Components\TextInput::make('family_name'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                Forms\Components\Select::make('positions')
                    ->required()
                    ->searchable()
                    ->multiple()
                    ->relationship(
                        name: 'positions',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->ofType(SpeakerPositionResource::$positionType),
                    )
                    ->preload()
                    ->saveRelationshipsUsing(function (Select $component, Model $record, $state) {
                        $record->positions()->detach($record->positions);
                        $record->positions()->attach($state);
                    })
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionAction(
                        fn (FormAction $action) => $action->modalWidth('xl')
                            ->modalHeading('Create Speaker Position')
                            ->mutateFormDataUsing(function (array $data): array {
                                $data['type'] = SpeakerPositionResource::$positionType;

                                return $data;
                            })
                            ->form(function (Select $component, Form $form): array|Form|null {
                                return SpeakerPositionResource::form($form);
                            })
                    )
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                Forms\Components\Toggle::make('meta.confirmed'),
                Forms\Components\Fieldset::make('Detail')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('meta.phone'),
                                Forms\Components\TextInput::make('meta.orcid_id')
                                    ->label('ORCID iD'),
                                Forms\Components\TextInput::make('meta.affiliation'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->recordUrl(null)
            // ->recordAction(null)
            ->reorderable('order_column')
            // Disable because grouping with reorderable active is acting weird
            // ->groups([
            //     Group::make('position.name')
            //         ->label('Position'),
            // ])
            ->heading('Speakers Table')
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->icon('heroicon-o-user-plus')
                        ->using(fn (array $data) => ParticipantCreateAction::run($data)),
                    Action::make('add_existing_speaker')
                        ->label('Add Existing')
                        ->icon('heroicon-o-plus')
                        ->modalWidth('xl')
                        ->form([
                            Select::make('participant_id')
                                ->label('Speaker')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->allowHtml()
                                ->options(function () {
                                    $participants = static::getEloquentQuery()->pluck('id')->toArray();

                                    return Participant::query()
                                        ->limit(10)
                                        ->whereNotIn('id', $participants)
                                        ->get()
                                        ->mapWithKeys(fn (Participant $participant) => [$participant->getKey() => static::renderSelectParticipant($participant)])
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(
                                    function (string $search) {
                                        $participants = static::getEloquentQuery()->pluck('id')->toArray();

                                        return Participant::query()
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
                                ->required()
                                ->searchable()
                                ->multiple()
                                ->options(fn () => ParticipantPosition::query()
                                    ->where('type', SpeakerPositionResource::$positionType)
                                    ->pluck('name', 'id')
                                    ->toArray()),
                        ])
                        ->action(function ($data) {
                            return Participant::find(data_get($data, 'participant_id'))
                                ->positions()
                                ->attach(data_get($data, 'positions'));
                        }),
                ])->button(),
            ])
            ->columns([
                IndexColumn::make('no')
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->conversion('avatar')
                    ->width(50)
                    ->height(50)
                    ->extraCellAttributes([
                        'style' => 'width: 1px',
                    ])
                    ->circular()
                    ->toggleable(),
                TextColumn::make('given_name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('family_name'),
                TextColumn::make('positions.name')
                    ->badge()
                    ->limitList(3)
                    ->listWithLineBreaks(),
                ToggleColumn::make('confirmed')
                    ->label('Confirmed')
                    ->updateStateUsing(fn (bool $state, Participant $record) => $record->setMeta('confirmed', $state))
                    ->getStateUsing(fn (Participant $record) => $record->getMeta('confirmed') ?? false),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->mutateRecordDataUsing(function (array $data, Participant $record) {
                            $data['meta'] = $record->getAllMeta();

                            return $data;
                        })
                        ->using(fn (array $data, Model $record) => ParticipantUpdateAction::run($record, $data)),
                    DeleteAction::make()
                        ->using(
                            function (Participant $record) {
                                $positions =    $record
                                    ->positions()
                                    ->where('type', SpeakerPositionResource::$positionType)
                                    ->get();
                                return $record->positions()->detach($positions);
                            }
                        ),
                ]),
            ])
            ->filters([
                // SelectFilter::make('position')
                //     ->relationship('position', 'name'),
            ]);
    }

    public static function renderSelectParticipant(Participant $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSpeakers::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // Widgets\SpeakerOverview::make(),
        ];
    }
}
