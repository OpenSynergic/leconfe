<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Actions\Committees\CommitteeCreateAction;
use App\Actions\Committees\CommitteeDeleteAction;
use App\Actions\Committees\CommitteeUpdateAction;
use App\Panel\Conference\Resources\Conferences\ParticipantResource;
use App\Models\Committee;
use App\Models\CommitteeRole;
use App\Panel\Conference\Resources\Conferences\CommitteeResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommitteeResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Committee';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'role',
                'media',
                'meta',
            ])
            ->whereHas(
                'role'
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
                Forms\Components\Select::make('committee_role_id')
                    ->label('Role')
                    ->required()
                    ->searchable()
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                    )
                    ->preload()
                    ->createOptionForm(fn ($form) => CommitteeRoleResource::form($form))
                    ->createOptionAction(
                        fn (FormAction $action) => $action->color('primary')
                            ->modalWidth('xl')
                            ->modalHeading('Create Committee Role')
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
                        ->icon('heroicon-o-user-plus')
                        ->using(fn (array $data) => CommitteeCreateAction::run($data)),
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
                                    $committees = static::getEloquentQuery()->pluck('email')->toArray();

                                    return static::getModel()::query()
                                        ->limit(10)
                                        ->whereNotIn('email', $committees)
                                        ->get()
                                        ->mapWithKeys(fn (Committee $committee) => [$committee->getKey() => static::renderSelectCommittee($committee)])
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(
                                    function (string $search) {
                                        $committees = static::getEloquentQuery()->pluck('email')->toArray();

                                        return static::getModel()::query()
                                            ->with(['media', 'meta'])
                                            ->whereNotIn('email', $committees)
                                            ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                ->orWhere('email', 'LIKE', "%{$search}%"))
                                            ->get()
                                            ->mapWithKeys(fn (Committee $committee) => [$committee->getKey() => static::renderSelectCommittee($committee)])
                                            ->toArray();
                                    }
                                ),
                            Select::make('committee_role_id')
                                ->required()
                                ->searchable()
                                ->options(
                                    fn () => CommitteeRoleResource::getEloquentQuery()
                                        ->pluck('name', 'id')
                                        ->toArray()
                                ),
                        ])
                        ->action(function ($data) {
                            $committee = static::getModel()::find($data['committee_id']);

                            $newCommittee = Committee::create([
                                ...$committee->only(['given_name', 'family_name', 'email']),
                                'committee_role_id' => $data['committee_role_id'],
                            ]);

                            if ($meta = $committee->getAllMeta()->toArray()) {
                                $newCommittee->setManyMeta($meta);
                            }           
                        }),
                ])->button(),
            ])
            ->columns([
                ...ParticipantResource::generalTableColumns(),
            ])
            ->actions([
                ...ParticipantResource::tableActions(CommitteeUpdateAction::class, CommitteeDeleteAction::class),
            ])
            ->filters([
                // SelectFilter::make('role')
                //     ->relationship('role', 'name'),
            ]);
    }

    public static function renderSelectCommittee(Committee $committee): string
    {
        return view('forms.select-participant', ['participant' => $committee])->render();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommittee::route('/'),
        ];
    }
}
