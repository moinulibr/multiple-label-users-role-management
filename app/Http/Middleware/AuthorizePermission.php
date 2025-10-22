<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class AuthorizePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (Auth::guest()) {
            // 401 Unauthorized for API/AJAX
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // redirect
            return redirect()->route('login');
        }

        // Authorization Check
        if (Gate::denies($permission)) {

            // 403 Forbidden for API/AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have the required permission to perform this action.',
                    'permission_required' => $permission
                ], 403);
            }
            return redirect()->route('dashboard')->with('error', "Access Denied. You lack the permission: {$permission}");
        }

        return $next($request);
    }
}