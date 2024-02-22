<?php

namespace App\Panel\Resources\Conferences;

use App\Actions\Participants\ParticipantCreateAction;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Panel\Resources\Conferences\SpeakerResource\Pages;
use App\Panel\Resources\Conferences\SpeakerResource\Widgets;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
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
                'meta',
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
                ...ParticipantResource::generalFormField(),
                Forms\Components\Select::make('positions')
                    ->required()
                    ->searchable()
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
                ...ParticipantResource::additionalFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
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
                ...ParticipantResource::generalTableColumns(),
            ])
            ->actions([
                ...ParticipantResource::tableActions(SpeakerPositionResource::$positionType),
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
