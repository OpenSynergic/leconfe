<?php

namespace App\Panel\Resources\PluginResource\Pages;

use App\Facades\Plugin;
use App\Models\Plugin as PluginModel;
use App\Panel\Resources\PluginResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\ManageRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManagePlugins extends ManageRecords
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scan_plugins')
                ->link()
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    Plugin::scanPlugins();
                }),
            Actions\CreateAction::make()
                ->modalSubmitActionLabel('Add')
                ->using(function (array $data) {
                    Plugin::pluginInstall($data['file']);
                })
                ->createAnother(false),
        ];
    }
}
