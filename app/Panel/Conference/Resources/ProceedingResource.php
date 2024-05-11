<?php

namespace App\Panel\Conference\Resources;

use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use App\Facades\Settings;
use App\Models\Proceeding;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Panel\Conference\Resources\ProceedingResource\Pages;

class ProceedingResource extends Resource
{
    protected static ?string $model = Proceeding::class;

    protected static ?int $navigationSort = 2;

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
                IndexColumn::make('no'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions'),
                TextColumn::make('current')
                    ->hidden(fn(Component $livewire) => $livewire->activeTab === 'future')
                    ->state(fn(Proceeding $record) => $record->published && $record->current ? 'Current' : '')
                    ->badge(),
                TextColumn::make('published_at')
                    ->sortable()
                    ->hidden(fn(Component $livewire) => $livewire->activeTab === 'future')
                    ->date(Settings::get('format_date'))
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProceedings::route('/'),
            'view' => Pages\ViewProceeding::route('/{record}'),
        ];
    }
}
