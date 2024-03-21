<?php

namespace App\Panel\Conference\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Serie;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Panel\Conference\Resources\SerieResource\Pages;
use App\Panel\Conference\Resources\SerieResource\RelationManagers;

class SerieResource extends BaseResource
{
    protected static ?string $model = Serie::class;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->description(fn(Serie $record) => $record->description),
                TextColumn::make('path'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
