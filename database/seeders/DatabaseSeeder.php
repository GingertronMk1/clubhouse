<?php

namespace Database\Seeders;

use App\Models\User;
use App\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@clubhouse.test',
            'password' => 1234,
            'permissions' => [Permission::SUPERADMIN],
        ]);

        $this->call([
            SportSeeder::class,
            LocationSeeder::class,
        ]);
    }
}
