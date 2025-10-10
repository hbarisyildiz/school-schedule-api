<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Kimlik doğrulama gerekli'
            ], 401);
        }
        
        // Kullanıcının rolü gerekli roller arasında mı?
        $userRole = $user->role->name;
        
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Bu işlem için yetkiniz bulunmuyor',
                'required_roles' => $roles,
                'user_role' => $userRole
            ], 403);
        }
        
        return $next($request);
    }
}
