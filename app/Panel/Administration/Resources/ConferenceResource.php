<?php

namespace App\Panel\Administration\Resources;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\Setting;
use App\Models\Conference;
use App\Models\Enums\SerieType;
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
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                Grid::make()
                    ->schema([
                        TextInput::make('meta.acronym')
                            ->helperText('The acronym of the conference series'),
                        TextInput::make('meta.issn')
                            ->label('ISSN')
                            ->helperText('The ISSN of the conference series'),
                    ]),
                TextInput::make('path')
                    ->label('Path')
                    ->helperText('The path of the conference. This will be used in the URL of the conference.')
                    ->required()
                    ->rule('alpha_dash')
                    ->unique(ignoreRecord: true)
                    ->prefix(config('app.url') . '/'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->columns([
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
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->button()
                    ->mutateRecordDataUsing(function (Conference $record, array $data) {
                        $data['meta'] = $record->getAllMeta()->toArray();

                        return $data;
                    })
                    ->using(fn (Conference $record, array $data) => ConferenceUpdateAction::run($record, $data)),
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
