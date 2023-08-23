<?php

namespace App\Website\Pages;

use App\Http\Middleware\Website\ApplyCurrentConference;
use App\Livewire\Forms\Installation\AccountForm;
use App\Livewire\Forms\Installation\ConferenceForm;
use App\Livewire\Forms\Installation\DatabaseForm;
use App\Livewire\Forms\Installation\UserForm;
use App\Utils\EnvironmentManager;
use App\Utils\PermissionChecker;
use App\Utils\RequirementChecker;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Installation extends Page
{
    protected static string $view = 'website.pages.installation';

    protected static string|array $withoutRouteMiddleware = [
        ApplyCurrentConference::class,
    ];

    public array $folders = [];

    public DatabaseForm $database;

    public AccountForm $account;

    public ConferenceForm $conference;

    public function mount()
    {
        $this->checkPermission();
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

    public function stepDatabase()
    {
        $this->database->validate();
        if(!$this->database->checkConnection()) return false;
        
    }

    public function stepAccount()
    {
        $this->account->validate();

    }

    public function install()
    {
        $this->validateInstallation();
        $this->database->process();
        $this->account->createAccount();
        // try {
        // } catch (\Throwable $th) {
        //     $this->addError('install', $th->getMessage());
        // }
        // throw $th;

        
    }
    
    public function validateInstallation(){
        $this->account->validate();
        $this->database->validate();
        $this->conference->validate();
        if($this->database->checkConnection()) return false;
    } 
}
