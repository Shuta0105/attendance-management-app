<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_show_correctly_when_out_of_work()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_display_show_correctly_when_in_work()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        // Attendance::factory()->create([
        //     'user_id' => $user->id,
        //     'work_date' => now()->toDateString(),
        //     'clock_in_at' => now(),
        // ]);

        $this->post('/attendance/clock-in');

        $response = $this->get('/attendance/clock-in');
        $response->assertSee('出勤中');
    }
}
