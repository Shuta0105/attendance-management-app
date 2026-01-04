<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use App\Http\Requests\LoginRequest as CustomLoginRequest;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginRequest::class, CustomLoginRequest::class);
        $this->app->bind(LoginResponse::class, CustomLoginResponse::class);
        $this->app->bind(RegisterResponse::class, CustomRegisterResponse::class);
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if ($request->login_type === 'admin' && $user->role !== 'admin') {
                return null;
            }

            if ($request->login_type === 'user' && $user->role !== 'user') {
                return null;
            }

            return $user;
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
