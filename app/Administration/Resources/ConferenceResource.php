<?php

namespace App\Administration\Resources;

use App\Actions\Conferences\ConferenceSetCurrentAction;
use App\Administration\Resources\ConferenceResource\Pages;
use App\Models\Conference;
use App\Models\Enums\ConferenceType;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                                    ->required(),
                                TextInput::make('path')
                                    ->rule('alpha_dash')
                                    ->required(),
                                Flatpickr::make('meta.date_held')
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),
                                Textarea::make('meta.description')
                                    ->autosize()
                                    ->rows(6)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),
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
                        Section::make()
                            ->columns(1)
                            ->schema([
                                Checkbox::make('current')
                                    ->label('Set this conference as the currently active one'),
                            ]),
                    ]),
                Section::make()
                    ->columnSpan([
                        'sm' => 1,
                    ])
                    ->schema([
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
                Section::make('Banner')
                    ->columnSpan([
                        'sm' => 2,
                    ])
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('banner')
                            ->collection('banner')
                            ->label('')
                            ->image()
                            ->reorderable()
                            ->multiple()
                            ->conversion('thumb'),
                    ]),
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
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->grow(false),
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
                    Tables\Actions\Action::make('set_as_current')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->hidden(fn (Conference $record) => $record->isCurrent())
                        ->successNotificationTitle(fn () => 'Current conference is changed')
                        ->visible(fn (Conference $record) => auth()->user()->can('setAsCurrent', $record))
                        ->action(function ($record, Tables\Actions\Action $action) {
                            try {
                                ConferenceSetCurrentAction::run($record);
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
