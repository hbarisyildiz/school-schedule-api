<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Kullanıcı girişi
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Giriş bilgileri hatalı.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Hesabınız pasif durumda.'],
            ]);
        }

        // Okul abonelik kontrol
        if ($user->school && !$user->school->isSubscriptionActive()) {
            throw ValidationException::withMessages([
                'email' => ['Okul aboneliği sona ermiş.'],
            ]);
        }

        // Token oluştur
        $token = $user->createToken('api-token')->plainTextToken;

        // Son giriş zamanını güncelle
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'message' => 'Giriş başarılı',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'display_name' => $user->role->display_name
                ],
                'school' => $user->school ? [
                    'id' => $user->school->id,
                    'name' => $user->school->name
                ] : null,
                'permissions' => $user->role->permissions
            ]
        ]);
    }

    /**
     * Okul kayıt (yeni okul + admin kullanıcı)
     */
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_email' => 'required|email|unique:schools,email',
            'school_phone' => 'required|string',
            'school_address' => 'required|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8|confirmed',
            'subscription_plan_id' => 'required|exists:subscription_plans,id'
        ]);

        try {
            // Okul oluştur
            $school = School::create([
                'name' => $request->school_name,
                'slug' => \Str::slug($request->school_name),
                'code' => 'SCH' . rand(1000, 9999),
                'email' => $request->school_email,
                'phone' => $request->school_phone,
                'address' => $request->school_address,
                'subscription_plan_id' => $request->subscription_plan_id,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(), // 1 ay deneme
                'subscription_status' => 'active',
                'is_active' => true
            ]);

            // Okul admin kullanıcısı oluştur
            $adminRole = Role::where('name', 'school_admin')->first();
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'school_id' => $school->id,
                'role_id' => $adminRole->id,
                'is_active' => true,
                'status' => 'active'
            ]);

            // Token oluştur
            $token = $admin->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Kayıt başarılı',
                'school' => $school,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => $admin->role->display_name,
                    'school' => $school->name,
                    'permissions' => $admin->role->permissions
                ],
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kayıt sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kullanıcı bilgilerini getir
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('role', 'school');
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'display_name' => $user->role->display_name
            ],
            'school' => $user->school ? [
                'id' => $user->school->id,
                'name' => $user->school->name
            ] : null,
            'permissions' => $user->role->permissions,
            'last_login_at' => $user->last_login_at
        ]);
    }

    /**
     * Çıkış yap
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Çıkış başarılı'
        ]);
    }

    /**
     * Şifre sıfırlama (basit versiyon)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // TODO: Mail gönderme işlemi
        
        return response()->json([
            'message' => 'Şifre sıfırlama bağlantısı email adresinize gönderildi'
        ]);
    }
}
