<?php

namespace App\Website\Pages;

use App\Events\AppInstalled;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Http\Middleware\SetupDefaultData;
use App\Livewire\Forms\InstallationForm;
use App\Utils\Installer;
use App\Utils\PermissionChecker;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Installation extends Page
{
    protected static string $view = 'website.pages.installation';

    protected static string|array $withoutRouteMiddleware = [
        SetupDefaultData::class,
        IdentifyCurrentConference::class,
    ];

    public array $folders = [];

    public InstallationForm $form;

    public function mount()
    {
        if (app()->isInstalled()) {
            return redirect('/');
        }

        if (file_exists(base_path('.env'))) {
            copy(base_path('.env'), base_path('.env.backup'));
            unlink(base_path('.env'));

            return redirect(static::getSlug());
        }

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
        return 'website.components.layouts.base';
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
        if($this->form->checkDatabaseConnection()){
            session()->flash('success', 'Successfully Connected');
        }
    }

    public function install()
    {
        if (!$this->validateInstallation()) {
            return;
        }

        $installer = new Installer($this->form->all());
        $installer->run();

        return redirect('/');
    }

    public function validateInstallation(): bool
    {
        $this->form->validate();

        if (!$this->form->checkDatabaseConnection()) {
            return false;
        }

        if (!$this->form->createDatabase()) {
            return false;
        }

        return true;
    }
}
