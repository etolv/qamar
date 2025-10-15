<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserableMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$userables): Response
    {
        $user = Auth::user();
        if (!in_array(strtolower(class_basename($user->type)), $userables)) {
            abort(403, _t("You don't have permission to access this page"));
        }
        // if (!$user->type instanceof Customer && !$user->type->is_verified) {
        //     abort(403, _t("Your account is under review"));
        // }
        return $next($request);
    }
}
