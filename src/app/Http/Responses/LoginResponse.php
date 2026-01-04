<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    { 
        $user = $request->user();

        $isFirstLogin = is_null($user->last_login_at);

        if ($user->role === 'user' && $isFirstLogin) {
            $user->sendEmailVerificationNotification();
        }

        $user->update([
            'last_login_at' => now(),
        ]);

        if ($user->role === 'user' && $isFirstLogin) {
            return redirect('/email/verify');
        }

        return redirect()->intended(
            match ($user->role) {
                'admin' => '/admin/attendance/list',
                'user' => '/attendance',
            }
        );
    }
}
