<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->isProduction()) {
            User::query()->firstOrCreate([
                'name' => 'Admin',
                'email' => 'admin@clubhouse.test',
                'password' => 'password',
                'email_verified_at' => CarbonImmutable::createFromFormat('Y-m-d H:i:s', '2025-01-01 00:00:00'),
            ]);
        }
    }
}
