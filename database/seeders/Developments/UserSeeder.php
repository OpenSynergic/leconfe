<?php

namespace Database\Seeders\Developments;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Application;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = \App\Models\User::factory()->create([
            'given_name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        $conferences = Conference::all();

        setPermissionsTeamId(Application::CONTEXT_WEBSITE);
        $user->assignRole(UserRole::Admin->value);
        foreach ($conferences as $key => $conference) {
            setPermissionsTeamId($conference->getKey());

            $user->assignRole(UserRole::Admin->value);
            
            $users = \App\Models\User::factory(10)->create();
            $users->each(fn ($user) => $user->assignRole(UserRole::random()->value));
        }

    }
}
