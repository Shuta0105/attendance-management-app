<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakTime;
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
        User::factory()->admin()->count(5)->create();
        User::factory()
            ->count(10)
            ->has(Attendance::factory()->count(10))
            ->create();
    }
}
