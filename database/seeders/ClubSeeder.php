<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Storage::json('clubs.json');
        foreach ($clubs as $club => $players) {
            Club::query()->create([
                'name' => $club,
            ]);
        }
    }
}
