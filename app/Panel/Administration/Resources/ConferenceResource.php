<?php

namespace App\Panel\Administration\Resources;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\Settings;
use App\Models\Conference;
use App\Models\Enums\ConferenceType;
use App\Panel\Administration\Resources\ConferenceResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
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
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
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
                                    ->columnSpanFull()
                                    ->required(),
                                TextInput::make('meta.acronym')
                                    ->unique(column: 'path')
                                    ->rule('alpha_dash')
                                    ->live(onBlur: true),
                                Placeholder::make('path')
                                    ->content(function (Get $get) {
                                        $baseUrl = config('app.url') . '/';
                                        $acronym = $get('meta.acronym') ?? '{acronym}';
                                        return new HtmlString("<span class='text-gray-500'>{$baseUrl}</span>{$acronym}");
                                    }),
                                TextInput::make('meta.theme')
                                    ->placeholder('e.g. Creating a better future with us')
                                    ->helperText("The theme of the conference. This will be used in the conference's branding.")
                                    ->columnSpanFull(),
                                Textarea::make('meta.description')
                                    ->rows(5)
                                    ->autosize()
                                    ->columnSpanFull(),
                            ]),
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('meta.publisher_name'),
                                TextInput::make('meta.publisher_location'),
                                TextInput::make('meta.affiliation'),
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
                            ->hidden(fn($record) => $record)
                            ->options(function () {
                                return Conference::query()
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
                                    'meta.theme' => $getDataConference?->getMeta('theme'),
                                    'meta.description' => $getDataConference?->getMeta('description'),
                                    'meta.publisher_name' => $getDataConference?->getMeta('publisher_name'),
                                    'meta.publisher_location' => $getDataConference?->getMeta('publisher_location'),
                                    'meta.affiliation' => $getDataConference?->getMeta('affiliation'),
                                    'meta.acronym' => $getDataConference?->getMeta('acronym'),
                                    'meta.country' => $getDataConference?->getMeta('country'),
                                ];

                                foreach ($defaults as $key => $previousConferenceValue) {
                                    $fieldUserValue = $get($key);
                                    empty($fieldUserValue) ? $set($key, $previousConferenceValue) : $set($key, $fieldUserValue);
                                }
                            }),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->helperText('A image representation of the conference that can be used in lists of conferences.')
                            ->collection('thumbnail')
                            ->image()
                            ->conversion('thumb'),
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
            ->columns([
                // SpatieMediaLibraryImageColumn::make('logo')
                //     ->collection('logo')
                //     ->conversion('thumb')
                //     ->grow(false),
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('open-conference')
                    ->icon('heroicon-o-link')
                    ->button()
                    ->color('gray')
                    ->url(fn (Conference $record) => route('filament.conference.pages.dashboard', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->button()
                    ->mutateRecordDataUsing(function(Conference $record, array $data){
                        $data['meta'] = $record->getAllMeta()->toArray();

                        return $data;
                    })
                    ->using(fn(Conference $record, array $data) => ConferenceUpdateAction::run($record, $data)),
                Tables\Actions\DeleteAction::make()
                    ->button(),

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
            // 'create' => Pages\CreateConference::route('/create'),
            // 'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
