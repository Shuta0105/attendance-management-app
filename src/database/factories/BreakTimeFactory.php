<?php

namespace Database\Factories;

use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        return [
            'break_start_at' => now(),
            'break_end_at' => now()->addMinutes(30),
        ];
    }
}
