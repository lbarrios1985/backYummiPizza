<?php

namespace App\Http\Middleware;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Closure;

class ForceGuestUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::getRoutes()->match($request);
        $current_route = $route->getName();
        
        if ($current_route == 'order' && !auth('api')->user()) {
            # Force pseudo-guest User registration
            $new_user = factory(User::class, 1)->create()[0];

            Auth::attempt(['email' => $new_user->email, 'password' => $new_user->email]);
            $user = Auth::user();
            $token = $user->createToken('Yummi_Pizza');
            $request->headers->set('authorization', 'Bearer '.$token->accessToken);
            $request->headers->set('guest-created', 'True');
        }
        return $next($request);
    }
}
