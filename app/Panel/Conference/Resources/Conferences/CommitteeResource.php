<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Actions\Committees\CommitteeCreateAction;
use App\Models\Committee;
use App\Panel\Conference\Resources\Conferences\CommitteeResource\Pages;
use App\Panel\Conference\Resources\Traits\CustomizedUrl;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommitteeResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    use CustomizedUrl;

    public static function getNavigationLabel(): string
    {
        return 'Committee';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'committeeRole' => fn ($query) => $query
                    ->where('type', CommitteePositionResource::$positionType),
                'media',
                'meta',
            ])
            ->whereHas(
                'committeeRole',
                fn (Builder $query) => $query
                    ->where('type', CommitteePositionResource::$positionType)
            );
    }

    public static function getModelLabel(): string
    {
        return 'Committee';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...ParticipantResource::generalFormField(),
                Forms\Components\Select::make('committeeRoles')
                    ->label('Position')
                    ->required()
                    ->searchable()
                    // ->multiple()
                    ->relationship(
                        name: 'committeeRoles',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', CommitteePositionResource::$positionType),
                    )
                    ->preload()
                    ->saveRelationshipsUsing(function (Select $component, Model $record, $state) {
                        $record->committeeroles()->detach($record->committeeroles);
                        $record->committeeroles()->attach($state);
                    })
                    ->createOptionForm(fn ($form) => CommitteePositionResource::form($form))
                    ->createOptionAction(
                        fn (FormAction $action) => $action->modalWidth('xl')
                            ->modalHeading('Create Committee Position')
                            ->mutateFormDataUsing(function (array $data): array {
                                $data['type'] = CommitteePositionResource::$positionType;

                                return $data;
                            })
                        // ->form(function (Select $component, Form $form): array|Form|null {
                        //     return CommitteePositionResource::form($form);
                        // })
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
            ->heading('Committee Table')
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->icon('heroicon-o-user-plus'),
                        // ->using(fn (array $data) => CommitteeCreateAction::run($data)),
                    Action::make('add_existing_speaker')
                        ->label('Add Existing')
                        ->icon('heroicon-o-plus')
                        ->modalWidth('xl')
                        ->form([
                            Select::make('committee_id')
                                ->label('Committee')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->allowHtml()
                                ->options(function () {
                                    $committees = static::getEloquentQuery()->pluck('id')->toArray();

                                    return static::getModel()::query()
                                        ->limit(10)
                                        ->whereNotIn('id', $committees)
                                        ->get()
                                        ->mapWithKeys(fn (Committee $committee) => [$committee->getKey() => static::renderSelectCommittee($committee)])
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(
                                    function (string $search) {
                                        $committees = static::getEloquentQuery()->pluck('id')->toArray();

                                        return static::getModel()::query()
                                            ->with(['media', 'meta'])
                                            ->whereNotIn('id', $committees)
                                            ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                ->orWhere('email', 'LIKE', "%{$search}%"))
                                            ->get()
                                            ->mapWithKeys(fn (Committee $committee) => [$committee->getKey() => static::renderSelectCommittee($committee)])
                                            ->toArray();
                                    }
                                ),
                            Select::make('committeeroles')
                                ->required()
                                ->searchable()
                                ->options(
                                    fn () => CommitteePositionResource::getEloquentQuery()
                                        ->pluck('name', 'id')
                                        ->toArray()
                                ),
                        ])
                        ->action(function ($data) {
                            return Committee::find(data_get($data, 'committee_id'))
                                ->committeeroles()
                                ->attach(data_get($data, 'committeeroles'));
                        }),
                ])->button(),
            ])
            ->columns([
                ...ParticipantResource::generalTableColumns(),
            ])
            ->actions([
                ...ParticipantResource::tableActions(CommitteePositionResource::$positionType),
            ])
            ->filters([
                // SelectFilter::make('position')
                //     ->relationship('position', 'name'),
            ]);
    }

    public static function renderSelectCommittee(Committee $committee): string
    {
        return view('forms.select-committee', ['committee' => $committee])->render();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommittee::route('/'),
        ];
    }
}
