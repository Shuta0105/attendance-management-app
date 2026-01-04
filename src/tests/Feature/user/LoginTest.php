<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_requires_email()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    public function test_user_login_requires_password()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    public function test_login_fails_with_wrong_information()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '123@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }
}
