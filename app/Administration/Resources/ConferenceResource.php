<?php

namespace App\Administration\Resources;

use App\Actions\Conferences\ConferenceSetActiveAction;
use App\Administration\Resources\ConferenceResource\Pages;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
use App\Tables\Columns\IndexColumn;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Squire\Models\Country;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->columnSpan([
                        'sm' => 2,
                        'lg' => 2,
                    ])
                    ->schema([
                        Section::make()
                            ->columns([
                                'sm' => 2,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('path', Str::slug($state)))
                                    ->columnSpanFull(),
                                TextInput::make('path')
                                    ->rule('alpha_dash')
                                    ->required(),
                                TextInput::make('meta.location'),
                                Flatpickr::make('start_date'),
                                Flatpickr::make('end_date')
                                    ->rule('after:start_at'),
                                Textarea::make('meta.description')
                                    ->rows(5)
                                    ->autosize()
                                    ->columnSpanFull(),
                            ]),
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('meta.publisher_name'),
                                TextInput::make('meta.affiliation'),
                                TextInput::make('meta.abbreviation'),
                                Select::make('meta.country')
                                    ->searchable()
                                    ->options(Country::pluck('name', 'id'))
                                    ->optionsLimit(250),
                            ]),
                    ]),
                Section::make()
                    ->columnSpan([
                        'sm' => 1,
                    ])
                    ->schema([
                        Select::make('conference_id')
                            ->label('Previous Conference')
                            ->options(function () {
                                return Conference::query()
                                    ->where('status', ConferenceStatus::Archived)
                                    ->latest('created_at')
                                    ->take(5)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->helperText('Fill the data from previous conference')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $getDataConference = Conference::find($state);

                                $defaults = [
                                    'name' => $getDataConference?->name,
                                    'path' => $getDataConference?->path,
                                    'type' => $getDataConference?->type,
                                    'start_date' => $getDataConference?->start_date,
                                    'end_date' => $getDataConference?->end_date,
                                    'meta.location' => $getDataConference?->getMeta('location'),
                                    'meta.description' => $getDataConference?->getMeta('description'),
                                    'meta.publisher_name' => $getDataConference?->getMeta('publisher_name'),
                                    'meta.affiliation' => $getDataConference?->getMeta('affiliation'),
                                    'meta.abbreviation' => $getDataConference?->getMeta('abbreviation'),
                                    'meta.country' => $getDataConference?->getMeta('country'),
                                ];

                                foreach ($defaults as $key => $previousConferenceValue) {
                                    $fieldUserValue = $get($key);
                                    empty($fieldUserValue) ? $set($key, $previousConferenceValue) : $set($key, $fieldUserValue);
                                }
                            })
                            ->hidden(fn () => Conference::where('status', ConferenceStatus::Archived->value)->doesntExist()),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->helperText('A image representation of the conference that can be used in lists of conferences.')
                            ->collection('thumbnail')
                            ->image()
                            ->conversion('thumb'),
                        Radio::make('type')
                            ->required()
                            ->options(ConferenceType::array()),
                    ]),
                // Section::make('Banner')
                //     ->columnSpan([
                //         'sm' => 2,
                //     ])
                //     ->schema([
                //         SpatieMediaLibraryFileUpload::make('banner')
                //             ->collection('banner')
                //             ->label('')
                //             ->image()
                //             ->reorderable()
                //             ->multiple()
                //             ->conversion('thumb'),
                //     ]),
            ])
            ->columns([
                'sm' => 3,
                'lg' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (Conference $record): ?string => route('filament.panel.pages.dashboard', $record))
            ->columns([
                // SpatieMediaLibraryImageColumn::make('logo')
                //     ->collection('logo')
                //     ->conversion('thumb')
                //     ->grow(false),
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('set_as_active')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->hidden(fn (Conference $record) => $record->isActive())
                        ->successNotificationTitle(fn () => 'Active conference is changed')
                        ->visible(fn (Conference $record) => auth()->user()->can('setAsActive', $record))
                        ->action(function ($record, Tables\Actions\Action $action) {
                            try {
                                ConferenceSetActiveAction::run($record);
                            } catch (\Throwable $th) {
                                $action->failure();

                                return;
                            }

                            $action->success();
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
