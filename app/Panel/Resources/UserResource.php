<?php

namespace App\Panel\Resources;

use App\Actions\User\UserDeleteAction;
use App\Actions\User\UserUpdateAction;
use App\Models\User;
use App\Panel\Resources\UserResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Squire\Models\Country;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function form(Form $form): Form
    {
        return $form

            ->columns(3)
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('profile')
                                    ->collection('profile')
                                    ->alignCenter()
                                    ->avatar()
                                    ->columnSpan(['lg' => 2]),
                                Forms\Components\TextInput::make('given_name')
                                    ->required(),
                                Forms\Components\TextInput::make('family_name'),
                                Forms\Components\TextInput::make('email')
                                    ->columnSpan(['lg' => 2])
                                    ->disabled(fn (?User $record) => $record)
                                    ->dehydrated(fn (?User $record) => ! $record)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('password')
                                    ->required(fn (?User $record) => ! $record)
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->confirmed(),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->requiredWith('password')
                                    ->password()
                                    ->dehydrated(false),
                                Forms\Components\Select::make('country')
                                    ->searchable()
                                    ->columnSpan(['lg' => 2])
                                    ->options(fn () => Country::all()->pluck('name', 'id'))
                                    ->preload(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('User Details')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('meta.phone'),
                                        Forms\Components\TextInput::make('meta.orcid_id')
                                            ->label('ORCID iD'),
                                        Forms\Components\TextInput::make('meta.affiliation'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (?User $record): ?string => $record?->created_at?->diffForHumans() ?? '-'),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (?User $record): ?string => $record?->updated_at?->diffForHumans() ?? '-'),
                            ]),
                        Forms\Components\Section::make('User Roles')
                            ->schema([
                                Forms\Components\CheckboxList::make('roles')
                                    ->label('')
                                    ->disabled(fn () => ! auth()->user()->can('User:assignRoles'))
                                    ->relationship('roles', 'name'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('given_name')
                    ->size('sm')
                    ->searchable(),
                TextColumn::make('family_name')
                    ->size('sm')
                    ->searchable(),
                TextColumn::make('email')
                    ->size('sm')
                    ->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    Impersonate::make()
                        ->grouped()
                        ->visible(fn ($record) => auth()->user()->can('loginAs', $record))
                        ->label(fn (User $record) => "Login as {$record->given_name}")
                        ->icon('heroicon-m-key')
                        ->color('primary')
                        ->redirectTo('panel'),
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn () => static::formSchemas())
                        ->mutateRecordDataUsing(fn ($data, User $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                        ->using(fn (array $data, User $record) => UserUpdateAction::run($data, $record)),
                    DeleteAction::make()
                        ->using(fn (?array $data, User $record) => UserDeleteAction::run($data, $record)),
                ]),
            ])
            ->queryStringIdentifier('users')
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
