<?php

namespace Database\Seeders\Productions;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Actions\Roles\RoleAssignDefaultPermissions;
use App\Models\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (UserRole::array() as $role) {
            Role::updateOrCreate(['name' => $role, 'conference_id' => null]);
        }

        // RoleAssignDefaultPermissions::run();
    }
}
