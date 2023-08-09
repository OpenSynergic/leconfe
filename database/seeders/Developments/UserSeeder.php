<?php

namespace Database\Seeders\Developments;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    \App\Models\User::factory()->create([
      'given_name' => 'admin',
      'email' => 'admin@admin.com',
      'password' => Hash::make('admin'),
    ]);

    \App\Models\User::factory(100)->create();
  }
}
