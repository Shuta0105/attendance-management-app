<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_attendances_display_in_a_day()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $user1 = User::factory()->create(['name' => 'ユーザーA']);
        $user2 = User::factory()->create(['name' => 'ユーザーB']);

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->setTime(9, 0),
            'clock_out_at' => now()->setTime(16, 0),
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->setTime(10, 0),
            'clock_out_at' => now()->setTime(17, 0),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list');
        $response->assertSee('ユーザーA');
        $response->assertSee('ユーザーB');
    }

    public function test_current_date_displayed_in_attendance_list()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('renderDate()');
    }

    public function test_attendances_shows_in_last_day()
    {
        $now = Carbon::create(2026, 2, 5);
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $previous = now()->copy()->subDay();

        $year = $previous->format('Y');
        $month = $previous->format('m');
        $date = $previous->format('d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $previous->toDateString(),
            'clock_in_at' => $previous->copy()->setTime(9, 0),
            'clock_out_at' => $previous->copy()->setTime(18, 0),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get("/admin/attendance/list?date={$year}-{$month}-{$date}");

        $response->assertSee($user->name);
    }

    public function test_attendances_shows_in_next_day()
    {
        $now = Carbon::create(2026, 2, 5);
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $previous = now()->copy()->addDay();

        $year = $previous->format('Y');
        $month = $previous->format('m');
        $date = $previous->format('d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $previous->toDateString(),
            'clock_in_at' => $previous->copy()->setTime(9, 0),
            'clock_out_at' => $previous->copy()->setTime(18, 0),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get("/admin/attendance/list?date={$year}-{$month}-{$date}");

        $response->assertSee($user->name);
    }
}
