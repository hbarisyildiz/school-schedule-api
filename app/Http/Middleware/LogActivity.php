<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Sadece authenticated request'leri logla
        if (auth()->check()) {
            // POST, PUT, DELETE method'ları logla (GET hari)
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $this->logActivity($request, $response);
            }
        }
        
        return $response;
    }
    
    /**
     * Aktiviteyi logla
     */
    private function logActivity(Request $request, Response $response)
    {
        try {
            // Route bilgilerinden entity çıkar
            $routeName = $request->route()?->getName();
            $action = $this->determineAction($request->method(), $routeName);
            $entityInfo = $this->extractEntityInfo($request, $routeName);
            
            ActivityLog::create([
                'school_id' => auth()->user()->school_id,
                'user_id' => auth()->id(),
                'action' => $action,
                'entity_type' => $entityInfo['type'],
                'entity_id' => $entityInfo['id'],
                'description' => $this->generateDescription($action, $entityInfo, $request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        } catch (\Exception $e) {
            // Logging hatası uygulamayı durdurmamalı
            \Log::error('Activity logging failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Action type'ı belirle
     */
    private function determineAction(string $method, ?string $routeName): string
    {
        $actions = [
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete'
        ];
        
        // Özel aksiyonlar
        if (str_contains($routeName, 'login')) return 'login';
        if (str_contains($routeName, 'logout')) return 'logout';
        if (str_contains($routeName, 'toggle')) return 'toggle_status';
        
        return $actions[$method] ?? 'action';
    }
    
    /**
     * Entity bilgilerini çıkar
     */
    private function extractEntityInfo(Request $request, ?string $routeName): array
    {
        $uri = $request->path();
        
        // API URL'den entity type çıkar
        if (preg_match('/api\/([\w-]+)/', $uri, $matches)) {
            $entityType = $matches[1];
            
            // ID varsa al
            $entityId = $request->route('id') 
                ?? $request->route('school') 
                ?? $request->route('user')
                ?? $request->route('schedule')
                ?? $request->route('subject')
                ?? null;
            
            return [
                'type' => $entityType,
                'id' => $entityId
            ];
        }
        
        return ['type' => null, 'id' => null];
    }
    
    /**
     * Açıklama oluştur
     */
    private function generateDescription(string $action, array $entityInfo, Request $request): string
    {
        $user = auth()->user()->name;
        $entity = $entityInfo['type'] ?? 'item';
        
        $descriptions = [
            'create' => "{$user} yeni {$entity} oluşturdu",
            'update' => "{$user} {$entity} güncelledi",
            'delete' => "{$user} {$entity} sildi",
            'login' => "{$user} sisteme giriş yaptı",
            'logout' => "{$user} sistemden çıkış yaptı",
            'toggle_status' => "{$user} {$entity} durumunu değiştirdi"
        ];
        
        return $descriptions[$action] ?? "{$user} {$action} işlemi yaptı";
    }
}
