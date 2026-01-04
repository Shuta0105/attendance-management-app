<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\RequestController;
use Closure;
use Illuminate\Http\Request;

class SwitchRequestListByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if ($user->role === 'admin') {
            return response(
                app(AdminRequestController::class)->list($request)
            );
        }

        return response(
            app(RequestController::class)->list($request)
        );
    }
}
