<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Kimlik doğrulama gerekli'
            ], 401);
        }
        
        $userPermissions = $user->role->permissions ?? [];
        
        // Süper admin tüm izinlere sahip
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Gerekli izinlerden en az birinin olması yeterli (OR mantığı)
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }
        
        if (!$hasPermission) {
            return response()->json([
                'message' => 'Bu işlem için gerekli izniniz bulunmuyor',
                'required_permissions' => $permissions,
                'user_permissions' => $userPermissions
            ], 403);
        }
        
        return $next($request);
    }
}
