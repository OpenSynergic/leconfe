<?php

namespace App\Panel\Conference\Resources;

use App\Panel\Conference\Resources\ProceedingResource\Pages;
use App\Panel\Conference\Resources\ProceedingResource\RelationManagers;
use App\Models\Proceeding;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProceedingResource extends Resource
{
    protected static ?string $model = Proceeding::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Fieldset::make('Identification')
                    ->columns(3)
                    ->schema([
                        TextInput::make('volume')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('number')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('year')
                            ->numeric()
                            ->minValue(0),
                    ]),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->nullable()
                    ->autosize(),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->collection('cover')
                    ->imageResizeUpscale(false)
                    ->image()
                    ->conversion('thumb')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('cover')
                        ->collection('cover')
                        ->conversion('avatar')
                        ->grow(false),
                    TextColumn::make('title'),
                    TextColumn::make('current')
                        ->state(fn(Proceeding $record) => $record->published && $record->current ? 'Current' : '')
                        ->badge(),
                ]),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth('xl'),
                    Tables\Actions\Action::make('preview')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\Action::make('publish')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-up-tray')
                        ->hidden(fn (Proceeding $record) => $record->published)
                        ->action(fn (Proceeding $record) => $record->publish()),
                    Tables\Actions\Action::make('unpublish')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-down-tray')
                        ->hidden(fn (Proceeding $record) => !$record->published)
                        ->action(fn (Proceeding $record) => $record->unpublish()),
                    Tables\Actions\Action::make('set_as_current')
                        ->requiresConfirmation()
                        ->visible(fn (Proceeding $record) => $record->published && !$record->current)
                        ->action(fn (Proceeding $record) => $record->setAsCurrent()),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ManageProceedings::route('/'),
        ];
    }
}
