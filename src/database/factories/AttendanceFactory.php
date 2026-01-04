<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $date = Carbon::parse(
            $this->faker->dateTimeBetween('2025-12-01', '2026-03-01')
        );

        $clockIn = $date->copy()->setTime(rand(8, 10), rand(0, 59));
        $clockOut = $clockIn->copy()->addHours(rand(6, 8));

        return [
            'work_date' => $date->toDateString(),
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($attendance) {
            $breakCount = rand(0, 3);

            if ($breakCount === 0) {
                return;
            }

            $start = $attendance->clock_in_at->copy()->addHours(2);

            for ($i = 0; $i < $breakCount; $i++) {
                $duration = [15, 30][rand(0, 1)];
                $end = $start->copy()->addMinutes($duration);

                if ($end->gte($attendance->clock_out_at)) {
                    break;
                }

                BreakTime::factory()->create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $start,
                    'break_end_at' => $end,
                ]);

                $start->addHours(1);
            }
        });
    }
}
