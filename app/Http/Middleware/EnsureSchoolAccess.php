<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Süper admin her okula erişebilir
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Kullanıcının okulu olmalı
        if (!$user->school_id) {
            return response()->json([
                'message' => 'Bu işlem için okul ataması gerekli'
            ], 403);
        }
        
        // Okul aktif mi kontrol
        if (!$user->school->is_active) {
            return response()->json([
                'message' => 'Okul hesabı pasif durumda'
            ], 403);
        }
        
        // Abonelik aktif mi kontrol
        if (!$user->school->isSubscriptionActive()) {
            return response()->json([
                'message' => 'Okul aboneliği sona ermiş'
            ], 403);
        }
        
        // Route'da school_id parametresi varsa kontrol et
        $schoolId = $request->route('school_id') ?? $request->input('school_id');
        if ($schoolId && $schoolId != $user->school_id) {
            return response()->json([
                'message' => 'Bu okul verilerine erişim yetkiniz yok'
            ], 403);
        }
        
        return $next($request);
    }
}
