<?php

namespace App\Panel\Administration\Pages;

use Filament\Support\Enums\Width;
use Throwable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Livewire;
use App\Facades\Plugin as PluginFacade;
use App\Models\Plugin;
use App\Panel\Administration\Livewire\PluginGalleryTable;
use App\Panel\Administration\Livewire\PluginTable;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class PluginManagement extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected string $view = 'panel.administration.pages.plugin-management';

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): string
    {
        return __('general.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('general.plugin');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('viewAny', Plugin::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload-plugin')
                ->label(__('general.upload_plugin'))
                ->modalHeading(__('general.upload_plugin'))
                ->authorize(fn () => auth()->user()->can('install', Plugin::class))
                ->visible(fn () => auth()->user()->can('install', Plugin::class))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('general.file'))
                        ->disk('plugins-tmp')
                        ->acceptedFileTypes(['application/zip'])
                        ->required(),
                ])
                ->modalWidth(Width::ExtraLarge)
                ->action(function (array $data) {

                    try {
                        PluginFacade::install(PluginFacade::getTempDisk()->path($data['file']));
                    } catch (Throwable $th) {
                        Notification::make()
                            ->danger()
                            ->title(__('general.failed_to_install_plugin'))
                            ->send();
                        Log::error($th);

                        return;
                    } finally {
                        PluginFacade::getTempDisk()->delete($data['file']);
                    }

                    $this->dispatch('refresh-table');

                    Notification::make()
                        ->title(__('general.install_success'))
                        ->success()
                        ->body(__('general.plugin_installed_successfully'))
                        ->send();
                })
                ->modalSubmitActionLabel(__('general.submit')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Installed Plugins')
                            ->schema([
                                Livewire::make(PluginTable::class)
                                    ->key('plugin-table'),
                            ]),
                        Tab::make('Plugin Gallery')
                            ->schema([
                                Livewire::make(PluginGalleryTable::class)
                                    ->key('plugin-gallery-table')
                                    ->lazy(),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
