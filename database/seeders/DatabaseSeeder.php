<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $defaultPlayers = ["Sam", "Ludi", "Ben", "Loic", "Nico", "Joris", "Mamad", "Mo", "Fab"];
        foreach($defaultPlayers as $player) {
            User::factory()->create(['name' => $player]);
        }
    }
}
