<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'status' => '承認待ち',
            'requested_at' => now(),
        ];
    }
}
