<?php

namespace App\Frontend\Website\Pages;

use App\Facades\MetaTag;
use App\Utils\Installer;
use Livewire\Attributes\Title;
use App\Utils\PermissionChecker;
use Illuminate\Support\Facades\App;
use App\Livewire\Forms\InstallationForm;
use App\Http\Middleware\SetupDefaultData;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Jackiedo\Timezonelist\Facades\Timezonelist;

class Installation extends Page
{
    protected static string $view = 'frontend.website.pages.installation';

    protected static string|array $withoutRouteMiddleware = [
        SetupDefaultData::class,
    ];

    public array $folders = [];

    public InstallationForm $form;

    public bool $installationSuccessful = false;

    public function mount()
    {
        if (App::isInstalled()) {
            return redirect('/');
        }
        
        MetaTag::add('robots', 'noindex, nofollow');

        $this->checkPermission();
    }

    protected function getViewData(): array
    {
        return [
            'groupedTimezone' => Timezonelist::toArray(false),
        ];
    }

    public static function getLayout(): string
    {
        return 'frontend.website.components.layouts.base';
    }

    public function checkPermission()
    {
        $permissionChecker = app(PermissionChecker::class);

        $this->folders = $permissionChecker->checkFolders([
            'storage/framework/' => 'storage/framework/',
            'storage/logs/' => 'storage/logs/',
            'storage/app/public/' => 'storage/app/public/',
            'bootstrap/cache/' => 'bootstrap/cache/',
        ]);
    }

    public function testConnection()
    {
        if ($this->form->checkDatabaseConnection()) {
            session()->flash('testConnection', true);
        }
    }

    public function install()
    {
        if (! $this->validateInstallation()) {
            return;
        }

        try {
            $installer = new Installer($this->form->all());
            $installer->run();
    
            return redirect('/');
        } catch (\Throwable $th) {
            $this->form->addError('error', $th->getMessage());
        }

    }

    public function validateInstallation(): bool
    {
        $this->form->validate();

        if (! $this->form->checkDatabaseConnection()) {
            return false;
        }

        if (! $this->form->createDatabase()) {
            return false;
        }

        return true;
    }
}
