<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Log;

class AuthorizePermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        // 🔹 Step 1: Check authentication
        if (Auth::guest()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        //Log::info("middleware parmission - " . $permission);
        //Log::info("middleware parmission within service - " . PermissionService::check($permission));
        
        // 🔹 Step 2: Permission check
        if (!PermissionService::check($permission)) {
            $message = "Access denied: Missing permission [{$permission}]";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'permission_required' => $permission,
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', $message);
        }

        // ✅ Step 3: Continue request
        return $next($request);
    }
}
