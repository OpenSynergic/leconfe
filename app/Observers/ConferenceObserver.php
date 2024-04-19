<?php

namespace App\Observers;

use App\Actions\Authors\AuthorRolePopulateDefaultDataAction;
use App\Actions\Committees\CommitteeRolePopulateDefaultDataAction;
use App\Actions\Speakers\SpeakerRolePopulateDefaultDataAction;
use App\Actions\Roles\RolePopulateAction;
use App\Actions\Roles\RolePopulateConferenceAction;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\Role;

class ConferenceObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Conference "created" event.
     */
    public function created(Conference $conference): void
    {
        CommitteeRolePopulateDefaultDataAction::run($conference);
        SpeakerRolePopulateDefaultDataAction::run($conference);
        AuthorRolePopulateDefaultDataAction::run($conference);

        $primaryNavigationMenu = NavigationMenu::create([
            'name' => 'Primary Navigation Menu',
            'handle' => 'primary-navigation-menu',
            'conference_id' => $conference->getKey(),
        ]);

        $userNavigationMenu = NavigationMenu::create([
            'name' => 'User Navigation Menu',
            'handle' => 'user-navigation-menu',
            'conference_id' => $conference->getKey(),
        ]);

        NavigationMenuItem::insert([
            [
                'navigation_menu_id' => $primaryNavigationMenu->getKey(),
                'label' => 'Home',
                'type' => 'home',
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $primaryNavigationMenu->getKey(),
                'label' => 'About',
                'type' => 'about',
                'order_column' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $primaryNavigationMenu->getKey(),
                'label' => 'Announcements',
                'type' => 'announcements',
                'order_column' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $primaryNavigationMenu->getKey(),
                'label' => 'Contact Us',
                'type' => 'contact-us',
                'order_column' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $primaryNavigationMenu->getKey(),
                'label' => 'Proceedings',
                'type' => 'proceedings',
                'order_column' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $userNavigationMenu->getKey(),
                'label' => 'Login',
                'type' => 'login',
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $userNavigationMenu->getKey(),
                'label' => 'Register',
                'type' => 'register',
                'order_column' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $usernameNavigation = NavigationMenuItem::create([
            'navigation_menu_id' => $userNavigationMenu->getKey(),
            'label' => '{$username}',
            'type' => 'dashboard',
            'order_column' => 3,
        ]);

        NavigationMenuItem::insert([
            [
                'navigation_menu_id' => $userNavigationMenu->getKey(),
                'parent_id' => $usernameNavigation->getKey(),
                'label' => 'Dashboard',
                'type' => 'dashboard',
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $userNavigationMenu->getKey(),
                'parent_id' => $usernameNavigation->getKey(),
                'label' => 'Profile',
                'type' => 'profile',
                'order_column' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'navigation_menu_id' => $userNavigationMenu->getKey(),
                'parent_id' => $usernameNavigation->getKey(),
                'label' => 'Logout',
                'type' => 'logout',
                'order_column' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        RolePopulateConferenceAction::run($conference);

        $conference->setMeta('page_footer', view('frontend.examples.footer')->render());
        $conference->setMeta('workflow.payment.supported_currencies', ['usd']);
        $conference->save();

        if(auth()->user()){
           $session_team_id = getPermissionsTeamId();
           // set actual new team_id to package instance
           setPermissionsTeamId($conference);
           // get the admin user and assign roles/permissions on new conference
           auth()->user()->assignRole(UserRole::Admin->name);
           // restore session team_id to package instance using temporary value stored above
           setPermissionsTeamId($session_team_id);
        }

    }

    /**
     * Handle the Conference "updated" event.
     */
    public function updated(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "deleted" event.
     */
    public function deleted(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "deleted" event.
     */
    public function deleting(Conference $conference): void
    {
        if ($conference->getKey() == Conference::active()?->getKey()) {
            throw new \Exception('Conference cannot be deleted because it is currently active');
        }
    }

    /**
     * Handle the Conference "restored" event.
     */
    public function restored(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "force deleted" event.
     */
    public function forceDeleted(Conference $conference): void
    {
        //
    }
}
