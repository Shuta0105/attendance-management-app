<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_send_after_register()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_verify_button_link_to_email_verification_site()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification) use ($user) {

                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->email),
                    ]
                );

                $response = $this->get($verificationUrl);

                $response->assertStatus(302);
                return true;
            }
        );
    }

    public function test_verified_user_is_redirected_to_attendance_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/attendance');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
