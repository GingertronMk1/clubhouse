<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating admin user');
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@clubhouse.test',
            'password' => Hash::make('1234'),
            'bio' => 'The admin of admins, king of kings',
            'superadmin' => true,
        ]);
        $this->command->info('Admin user created, creating registered users');
        User::factory(10)->create();
        $this->command->info('Registered users created, creating non-registered users');
        User::factory(50)->nonRegisteredUser()->create();
        $this->command->info('Non-registered users created');
    }
}
