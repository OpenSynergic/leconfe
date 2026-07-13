<?php

namespace Rahmanramsi\LivewirePageGroup;

use Illuminate\Routing\Router;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use Rahmanramsi\LivewirePageGroup\Http\Middleware\SetUpPageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\HomePage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewirePageGroupServiceProvider extends PackageServiceProvider
{
    /*
    * This class is a Package Service Provider
    *
    * More info: https://github.com/spatie/laravel-package-tools
    */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-page-group')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void
    {
        $this->app->scoped('livewire-page-group', function (): LivewirePageGroupManager {
            return new LivewirePageGroupManager;
        });

        app(Router::class)->aliasMiddleware('pagegroup', SetUpPageGroup::class);
    }

    public function packageBooted()
    {
        Livewire::addPersistentMiddleware([
            SetUpPageGroup::class,
        ]);

        $componentRegistry = app(ComponentRegistry::class);

        Livewire::component($componentRegistry->getName(HomePage::class), HomePage::class);
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            Commands\MakePageCommand::class,
        ];
    }
}
