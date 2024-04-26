<?php

namespace App\Panel\Conference\Resources;

use App\Facades\Settings;
use App\Models\Serie;
use App\Panel\Conference\Resources\SerieResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SerieResource extends Resource
{
    protected static ?string $model = Serie::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->autofocus()
                            ->autocomplete()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('path', Str::slug($state)))
                            ->placeholder('Enter the title of the serie'),
                        TextInput::make('path')
                            ->label('Path')
                            ->rule('alpha_dash')
                            ->required()
                            ->placeholder('Enter the path of the serie'),
                    ]),
                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter the description of the serie')
                    ->rows(5)
                    ->autosize(),
                TextInput::make('issn')
                    ->label('ISSN')
                    ->placeholder('Enter the ISSN of the serie'),
                Grid::make()
                    ->schema([
                        DatePicker::make('date_start')
                            ->label('Start Date')
                            ->placeholder('Enter the start date of the serie')
                            ->requiredWith('date_end'),
                        DatePicker::make('date_end')
                            ->label('End Date')
                            ->afterOrEqual('date_start')
                            ->requiredWith('date_start')
                            ->placeholder('Enter the end date of the serie'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Serie $record) => route('filament.series.pages.dashboard', ['serie' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->wrapHeader(),
                TextColumn::make('date_start')
                    ->date(Settings::get('format_date')),
                TextColumn::make('date_end')
                    ->date(Settings::get('format_date')),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->hidden(fn (Serie $record) => $record->trashed()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Move To Trash')
                        ->modalHeading('Move To Trash')
                        ->hidden(fn (Serie $record) => $record->active || $record->trashed() )
                        ->successNotificationTitle('Serie moved to trash'),
                ]),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSeries::route('/'),
        ];
    }
}
