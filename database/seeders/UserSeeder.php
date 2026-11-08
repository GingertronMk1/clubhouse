<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::query()->firstOrNew(
            [
                'email' => 'admin@clubhouse.test',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(12345),
            ],
        );
        $superadmin->superadmin = true;
        $superadmin->save();
        $firstNames = ['Alice', 'Amelia', 'Benjamin', 'Charlotte', 'Daniel', 'Emily', 'Ethan', 'Grace', 'Harry', 'Isabella', 'Jack', 'James', 'Lily', 'Oliver', 'Sophie', 'Thomas'];
        $lastNames = ['Anderson', 'Baker', 'Brown', 'Clark', 'Davies', 'Evans', 'Green', 'Hall', 'Harris', 'Jones', 'King', 'Lewis', 'Martin', 'Smith', 'Taylor', 'Wilson'];
        $password = Hash::make('password');

        foreach ($firstNames as $firstName) {
            foreach ($lastNames as $lastName) {
                $email = Str::lower("{$firstName}.{$lastName}@clubhouse.test");

                User::withTrashed()->firstOrCreate(['email' => $email], [
                    'name' => "{$firstName} {$lastName}",
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
            }
        }
    }
}
