<?php

namespace App\Panel\Resources;

use App\Actions\User\UserDeleteAction;
use App\Actions\User\UserUpdateAction;
use App\Models\User;
use App\Panel\Resources\Conferences\ParticipantResource;
use App\Panel\Resources\UserResource\Pages;
use App\Tables\Columns\IndexColumn;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
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
                                    ->dehydrated(fn (?User $record) => !$record)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('password')
                                    ->required(fn (?User $record) => !$record)
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->confirmed(),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->requiredWith('password')
                                    ->password()
                                    ->dehydrated(false),
                                ...ParticipantResource::additionalFormField(),
                            ])
                            ->columns(2),
                        // Forms\Components\Section::make('User Details')
                        //     ->schema([
                        //         Forms\Components\Grid::make(2)
                        //             ->schema([
                        //                 Forms\Components\TextInput::make('meta.phone'),
                        //                 Forms\Components\TextInput::make('meta.orcid_id')
                        //                     ->label('ORCID iD'),
                        //                 Forms\Components\TextInput::make('meta.affiliation'),
                        //             ]),
                        //     ]),

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
                                    ->disabled(fn () => !auth()->user()->can('assignRoles', static::getModel()))
                                    ->relationship('roles', 'name'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IndexColumn::make('no')
                    ->toggleable(),
                SpatieMediaLibraryImageColumn::make('profile')
                    ->collection('profile')
                    ->conversion('avatar')
                    ->width(50)
                    ->height(50)
                    ->extraCellAttributes([
                        'style' => 'width: 1px',
                    ])
                    ->circular()
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('full_name')
                    ->searchable(
                        query: fn ($query, $search) => $query
                            ->where('given_name', 'LIKE', "%{$search}%")
                            ->orWhere('family_name', 'LIKE', "%{$search}%")
                    )
                    ->toggleable(),
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
                        ->modalWidth('full')
                        ->mutateRecordDataUsing(fn ($data, User $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                        ->using(fn (array $data, User $record) => UserUpdateAction::run($data, $record)),
                    Action::make('ban')
                        ->disabled(fn(User $record) => auth()->user()->can('ban', $record))
                        ->label(fn (User $record) => "Disable")
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->modalWidth('xl')
                        ->modalHeading(fn (User $record) => "Disable User : {$record->full_name}")
                        ->form([
                            Textarea::make('comment')
                                ->label('Reason for Disabling User'),
                            Flatpickr::make('expired_at')
                                ->label('Until')
                                // ->native(false)
                                ->minDate(now()->addDay())
                                ->hint('To banned permanently, leave field empty')
                                ->dehydrateStateUsing(fn ($state) => $state ? Carbon::createFromFormat(setting('format.date'), $state) : null),
                        ])
                        ->action(function (array $data, User $record) {
                            $record->ban($data);
                        }),
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
