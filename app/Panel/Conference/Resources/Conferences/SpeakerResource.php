<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Actions\Speakers\SpeakerCreateAction;
use App\Models\Speaker;
use App\Models\SpeakerPosition;
use App\Panel\Conference\Resources\Conferences\SpeakerResource\Pages;
use App\Panel\Conference\Resources\Traits\CustomizedUrl;
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

    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    use CustomizedUrl;

    public static function getNavigationLabel(): string
    {
        return 'Speakers';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'role' => fn ($query) => $query
                    ->ofType(SpeakerPositionResource::$positionType),
                'media',
                'meta',
            ])
            ->whereHas(
                'role',
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
                Forms\Components\Select::make('speaker_role_id')
                    ->label('Role')
                    ->required()
                    ->searchable()
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->ofType(SpeakerPositionResource::$positionType),
                    )
                    ->preload()
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
                        ->using(fn (array $data) => SpeakerCreateAction::run($data)),
                    Action::make('add_existing_speaker')
                        ->label('Add Existing')
                        ->icon('heroicon-o-plus')
                        ->modalWidth('xl')
                        ->form([
                            Select::make('speaker_id')
                                ->label('Speaker')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->allowHtml()
                                ->options(function () {
                                    $speakers = static::getEloquentQuery()->pluck('id')->toArray();

                                    return Speaker::query()
                                        ->limit(10)
                                        ->whereNotIn('id', $speakers)
                                        ->get()
                                        ->mapWithKeys(fn (Speaker $speaker) => [$speaker->getKey() => static::renderSelectSpeaker($speaker)])
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(
                                    function (string $search) {
                                        $speakers = static::getEloquentQuery()->pluck('id')->toArray();

                                        return Speaker::query()
                                            ->with(['media', 'meta'])
                                            ->whereNotIn('id', $speakers)
                                            ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                ->orWhere('email', 'LIKE', "%{$search}%"))
                                            ->get()
                                            ->mapWithKeys(fn (Speaker $speaker) => [$speaker->getKey() => static::renderSelectSpeaker($speaker)])
                                            ->toArray();
                                    }
                                ),
                            Select::make('positions')
                                ->required()
                                ->searchable()
                                ->options(fn () => SpeakerPosition::query()
                                    ->where('type', SpeakerPositionResource::$positionType)
                                    ->pluck('name', 'id')
                                    ->toArray()),
                        ])
                        ->action(function ($data) {
                            return Speaker::find(data_get($data, 'speaker_id'))
                                ->positions()
                                ->attach(data_get($data, 'positions'));
                        }),
                ])->button(),
            ])
            ->columns([
                ...ParticipantResource::generalTableColumns(),
            ])
            ->actions([
                // ...ParticipantResource::tableActions(SpeakerPositionResource::$positionType),
            ])
            ->filters([
                // SelectFilter::make('position')
                //     ->relationship('position', 'name'),
            ]);
    }

    public static function renderSelectSpeaker(Speaker $speaker): string
    {
        return view('forms.select-speaker', ['speaker' => $speaker])->render();
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
