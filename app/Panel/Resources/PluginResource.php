<?php

namespace App\Panel\Resources;

use App\Facades\Plugin as FacadesPlugin;
use App\Panel\Resources\PluginResource\Pages;
use App\Panel\Resources\PluginResource\RelationManagers;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Settings';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        FileUpload::make('file')
                            ->disk('plugin-upload')
                            ->acceptedFileTypes(['application/zip'])
                            ->preserveFilenames()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
                TextColumn::make('author'),
                ToggleColumn::make('is_active')
                    ->label('Enabled')
                    ->updateStateUsing(function (Plugin $record) {
                        if ($record->is_active) {
                            $record->is_active = false;
                            $record->save();

                            FacadesPlugin::pluginDeactivation($record->path);

                            Notification::make()
                                ->title("{$record->name} is disabled")
                                ->body("Please refresh page to take effect")
                                ->success()
                                ->send();
                        } else {
                            $record->is_active = true;
                            $record->save();

                            FacadesPlugin::pluginActivation($record->path);

                            Notification::make()
                                ->title("{$record->name} is enabled")
                                ->body("Please refresh page to take effect")
                                ->success()
                                ->send();
                        } 
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->action(function (Plugin $record) {
                        FacadesPlugin::pluginUninstall($record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePlugins::route('/'),
        ];
    }    
}
