<?php

namespace App\Website\Pages;

use App\Events\AppInstalled;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Http\Middleware\SetupDefaultData;
use App\Livewire\Forms\InstallationForm;
use App\Utils\EnvironmentManager;
use App\Utils\PermissionChecker;
use Illuminate\Support\Str;
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
        $this->form->db_name = 'conference_db_'.Str::random(3);

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
        $this->form->checkDatabaseConnection();
    }

    public function install()
    {

        if (! $this->validateInstallation()) {
            return;
        }

        $this->form->updateConfig();

        app(EnvironmentManager::class)->installation();

        $this->form->process();

        AppInstalled::dispatch();

        // create empty file on storage path
        touch(storage_path('installed'));

        return redirect('/');
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
