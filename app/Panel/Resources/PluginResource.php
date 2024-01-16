<?php

namespace App\Panel\Resources;

use App\Facades\Plugin as FacadesPlugin;
use App\Panel\Resources\PluginResource\Pages;
use App\Panel\Resources\PluginResource\RelationManagers;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Settings';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Grid::make(1)
    //                 ->schema([
    //                     FileUpload::make('file')
    //                         ->disk('plugins-tmp')
    //                         ->acceptedFileTypes(['application/zip'])
    //                         ->preserveFilenames()
    //                 ])
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(fn(Plugin $record) => FacadesPlugin::getSetting($record->id, 'enabled') ? FontWeight::SemiBold : FontWeight::Light)
                    ->wrap()
                    ->description(fn(Plugin $record) => $record->description),
                TextColumn::make('author'),
                ToggleColumn::make('enabled')
                    ->label('Enabled')
                    ->getStateUsing(fn(Plugin $record)=> FacadesPlugin::getSetting($record->id, 'enabled'))
                    ->updateStateUsing(function (Plugin $record, $state) {
                        FacadesPlugin::enable($record->id, $state);
                        
                        $record->enabled = $state;

                        return $state;
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->action(function (Plugin $record) {
                        FacadesPlugin::uninstall($record->id);
                    }),
                // TODO : Add actions based on plugin. Currently there's no way to create a dinamically action
                    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePlugins::route('/'),
        ];
    }
}
