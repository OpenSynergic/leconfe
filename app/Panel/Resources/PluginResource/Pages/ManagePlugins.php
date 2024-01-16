<?php

namespace App\Panel\Resources\PluginResource\Pages;

use App\Facades\Plugin;
use App\Panel\Resources\PluginResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManagePlugins extends ManageRecords
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add-plugin')
                ->label('Add new')
                ->modalHeading('Add new Plugin')
                ->form([
                    FileUpload::make('file')
                        ->disk('plugins-tmp')
                        ->acceptedFileTypes(['application/zip'])
                ])
                ->action(function (array $data) {
                    
                    try {
                        Plugin::install(Plugin::getTempDisk()->path($data['file']));
                    } catch (\Throwable $th) {
                        Notification::make('install-failed')
                            ->danger()
                            ->title('Install failed')
                            ->body($th->getMessage())
                            ->send();
                        return;
                    } finally {
                        Plugin::getTempDisk()->delete($data['file']);
                    }
                    

                    Notification::make('install-success')
                        ->title('Install success')
                        ->success()
                        ->body('Plugin installed successfully')
                        ->send();
                })
                ->modalSubmitActionLabel('Submit'),
        ];
    }
}
