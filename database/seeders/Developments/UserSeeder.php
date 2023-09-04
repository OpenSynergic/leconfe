<?php

namespace Database\Seeders\Developments;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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

        $user->assignRole(UserRole::Admin->value);

        \App\Models\User::factory(100)->create();
    }
}
