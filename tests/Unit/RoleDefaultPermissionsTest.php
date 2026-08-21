<?php

namespace Tests\Unit;

use App\Models\Enums\UserRole;
use App\Models\Role;
use Tests\TestCase;

class RoleDefaultPermissionsTest extends TestCase
{
    public function test_scheduled_conference_editor_can_login_as_users(): void
    {
        $this->assertContains(
            'User:loginAs',
            Role::getPermissionsForRole(UserRole::ScheduledConferenceEditor->value)
        );
    }

    public function test_scheduled_conference_editor_can_view_but_not_manage_plugins(): void
    {
        $permissions = Role::getPermissionsForRole(UserRole::ScheduledConferenceEditor->value);

        $this->assertContains('Plugin:viewAny', $permissions);
        $this->assertNotContains('Plugin:install', $permissions);
        $this->assertNotContains('PluginGallery:install', $permissions);
        $this->assertNotContains('Plugin:update', $permissions);
        $this->assertNotContains('Plugin:delete', $permissions);
    }
}
