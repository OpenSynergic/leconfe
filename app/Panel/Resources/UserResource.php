<?php

namespace App\Panel\Resources;

use App\Actions\User\UserDeleteAction;
use App\Actions\User\UserMailAction;
use App\Actions\User\UserUpdateAction;
use App\Models\User;
use App\Panel\Resources\Conferences\ParticipantResource;
use App\Panel\Resources\UserResource\Pages;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->with(['meta', 'media', 'bans']);
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
                                ...ParticipantResource::additionalFormField(),
                            ])
                            ->columns(2),

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

                                Forms\Components\Placeholder::make('disabled_at')
                                    ->visible(fn (?User $record) => $record?->isBanned())
                                    ->label('Disabled at')
                                    ->content(function (?User $record): ?string {
                                        $ban = $record?->bans->first();

                                        return $ban?->created_at?->format(setting('format.date')) ?? '-';
                                    }),
                                Forms\Components\Placeholder::make('disabled_until')
                                    ->visible(fn (?User $record) => $record?->isBanned())
                                    ->label('Disabled until')
                                    ->content(function (?User $record): ?string {
                                        $ban = $record?->bans->first();

                                        return $ban?->expired_at?->format(setting('format.date')) ?? '-';
                                    }),

                            ]),
                        Forms\Components\Section::make('User Roles')
                            ->schema([
                                Forms\Components\CheckboxList::make('roles')
                                    ->label('')
                                    ->disabled(fn () => ! auth()->user()->can('assignRoles', static::getModel()))
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
                Split::make([
                    SpatieMediaLibraryImageColumn::make('profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(function (User $record): string {
                            $name = Str::of(Filament::getUserName($record))
                                ->trim()
                                ->explode(' ')
                                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                                ->join(' ');

                            return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=111827&font-size=0.33';
                        })
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(),
                    Stack::make([
                        TextColumn::make('full_name')
                            ->weight(FontWeight::Medium)
                            ->searchable(
                                query: fn ($query, $search) => $query
                                    ->where('given_name', 'LIKE', "%{$search}%")
                                    ->orWhere('family_name', 'LIKE', "%{$search}%")
                            )
                            ->sortable(
                                query: fn ($query, $direction) => $query
                                    ->orderBy('given_name', $direction)
                                    ->orderBy('family_name', $direction)
                            )
                            ->toggleable(),
                        TextColumn::make('email')
                            ->wrap()
                            ->color('gray')
                            ->searchable()
                            ->size('sm')
                            ->sortable()
                            ->icon('heroicon-m-envelope'),
                        TextColumn::make('affiliation')
                            // ->color(Color::hex('#A6CE39'))
                            ->size('sm')
                            ->wrap()
                            ->color('gray')
                            ->icon('heroicon-s-building-library')
                            ->getStateUsing(fn (User $record) => $record->getMeta('affiliation')),
                        TextColumn::make('disabled')
                            ->getStateUsing(function (User $record) {
                                if (! $record->isBanned()) {
                                    return null;
                                }

                                $ban = $record->bans->filter(function ($ban) {
                                    return $ban->notExpired();
                                })->first();

                                $bannedUntil = $ban->expired_at;

                                return 'Disabled'.($bannedUntil ? " until {$bannedUntil->format(setting('format.date'))}" : '');
                            })
                            ->color('danger')
                            ->badge(),
                    ]),
                    // Stack::make([
                    //     TextColumn::make('phone')
                    //         ->icon('heroicon-m-phone')
                    //         ->getStateUsing(fn (User $record) => $record->getMeta('phone')),
                    //     TextColumn::make('orcid_id')
                    //         ->color(Color::hex('#A6CE39'))
                    //         ->icon('academicon-orcid')
                    //         ->getStateUsing(fn (User $record) => $record->getMeta('orcid_id')),
                    //     TextColumn::make('google_scholar')
                    //         ->color(Color::hex('#4081EC'))
                    //         ->icon('academicon-google-scholar')
                    //         ->getStateUsing(fn (User $record) => $record->getMeta('google_scholar_id')),
                    //     TextColumn::make('scopus_id')
                    //         ->color(Color::hex('#e9711c'))
                    //         ->icon('academicon-scopus-square')
                    //         ->getStateUsing(fn (User $record) => $record->getMeta('scopus_id')),
                    // ])
                ])
                    ->from('md'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth('full')
                    ->mutateRecordDataUsing(fn ($data, User $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                    ->using(fn (array $data, User $record) => UserUpdateAction::run($data, $record)),
                DeleteAction::make()
                    ->using(fn (?array $data, User $record) => UserDeleteAction::run($data, $record)),
                ActionGroup::make([
                    Impersonate::make()
                        ->grouped()
                        ->hidden(fn ($record) => ! auth()->user()->can('loginAs', $record))
                        ->label(fn (User $record) => "Login as {$record->given_name}")
                        ->redirectTo(route('filament.panel.tenant'))
                        ->icon('heroicon-m-key')
                        ->color('primary')
                        ->redirectTo('panel'),
                    Action::make('enable')
                        ->visible(fn (User $record) => auth()->user()->can('enable', $record))
                        ->label(fn (User $record) => 'Enable User')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (User $record) {
                            $record->unban();
                        }),
                    Action::make('disable')
                        ->visible(fn (User $record) => auth()->user()->can('disable', $record))
                        ->label(fn (User $record) => 'Disable')
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
                                ->hint('To disable permanently, leave field empty')
                                ->dehydrateStateUsing(fn ($state) => $state ? Carbon::createFromFormat(setting('format.date'), $state) : null),
                        ])
                        ->action(function (array $data, User $record) {
                            $record->ban($data);
                        }),
                    Action::make('email')
                        ->visible(fn (User $record) => auth()->user()->can('sendEmail', $record))
                        ->label(fn (User $record) => 'Send Email')
                        ->icon('heroicon-o-envelope')
                        ->modalWidth('3xl')
                        ->fillForm(fn ($record) => ['to' => $record->email])
                        ->modalHeading(fn (User $record) => "Send Email to {$record->full_name}")
                        ->form([
                            Grid::make()
                                ->schema([
                                    TextInput::make('subject')
                                        ->label('Subject')
                                        ->required(),
                                    TextInput::make('to')
                                        ->label('To')
                                        ->disabled()
                                        ->required(),
                                ]),
                            TinyEditor::make('message')
                                ->label('Message')
                                ->minHeight(500)
                                ->required(),
                        ])
                        ->action(function (User $record, array $data) {
                            UserMailAction::run($record, ...Arr::only($data, ['subject', 'message']));
                        }),

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
            'view' => Pages\ProfileUser::route('/profile/{record}'),
        ];
    }
}
