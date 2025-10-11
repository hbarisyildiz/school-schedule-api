<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolRegistrationRequest;
use App\Models\School;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SchoolRegistrationVerification;
use App\Mail\SchoolRegistrationApproved;
use App\Mail\SchoolRegistrationRejected;
use App\Mail\SchoolWelcome;

class SchoolRegistrationController extends Controller
{
    /**
     * Okul kayıt işlemi - Direkt aktif okul oluştur (Public endpoint)
     */
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email|unique:school_registration_requests,email',
            'password' => 'required|string|min:6|confirmed',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        // Otomatik okul kodu üret
        $schoolCode = 'SCH' . strtoupper(Str::random(6));

        DB::beginTransaction();

        try {
            // Direkt okul oluştur - artık pending durumu yok
            $school = School::create([
                'name' => $request->school_name,
                'slug' => Str::slug($request->school_name),
                'code' => $schoolCode,
                'email' => $request->email,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'subscription_plan_id' => 1, // Basic plan
                'subscription_status' => 'active',
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(),
                'is_active' => true
            ]);

            // School Admin rolünü bul
            $schoolAdminRole = Role::where('name', 'school_admin')->first();
            
            if (!$schoolAdminRole) {
                throw new \Exception('School admin rolü bulunamadı');
            }

            // Okul yöneticisi kullanıcısı oluştur
            $schoolAdmin = User::create([
                'name' => $request->school_name . ' Yöneticisi',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'school_id' => $school->id,
                'role_id' => $schoolAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Okul kaydınız başarıyla tamamlandı! Hemen giriş yapabilirsiniz.',
                'school_code' => $schoolCode,
                'school_name' => $school->name,
                'login_email' => $request->email,
                'status' => 'active',
                'subscription_ends_at' => $school->subscription_ends_at->format('d.m.Y')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Okul kaydı oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kayıt talepleri listesi (Super Admin)
     */
    public function index()
    {
        $requests = SchoolRegistrationRequest::with(['city', 'district', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($requests);
    }

    /**
     * Kayıt talebi detayı (Super Admin)
     */
    public function show($id)
    {
        $request = SchoolRegistrationRequest::with(['city', 'district', 'subscriptionPlan'])
            ->findOrFail($id);

        return response()->json($request);
    }

    /**
     * Kayıt talebi sil (Super Admin)
     */
    public function destroy($id)
    {
        $request = SchoolRegistrationRequest::findOrFail($id);
        $request->delete();

        return response()->json(['message' => 'Kayıt talebi silindi']);
    }

    /**
     * Kayıt talebini onayla (Super Admin)
     */
    public function approve($id)
    {
        $registrationRequest = SchoolRegistrationRequest::findOrFail($id);

        if ($registrationRequest->status !== 'verified') {
            return response()->json(['message' => 'Bu kayıt talebi henüz doğrulanmamış'], 400);
        }

        DB::transaction(function () use ($registrationRequest) {
            // Okul oluştur
            $school = School::create([
                'name' => $registrationRequest->school_name,
                'code' => $registrationRequest->school_code,
                'email' => $registrationRequest->email,
                'city_id' => $registrationRequest->city_id,
                'district_id' => $registrationRequest->district_id,
                'subscription_plan_id' => $registrationRequest->subscription_plan_id,
                'subscription_status' => 'active',
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(),
                'status' => 'active'
            ]);

            // Admin kullanıcısı oluştur
            $adminRole = Role::where('name', 'school_admin')->first();
            $adminUser = User::create([
                'name' => 'Okul Yöneticisi',
                'email' => $registrationRequest->email,
                'password' => $registrationRequest->password,
                'role_id' => $adminRole->id,
                'school_id' => $school->id,
                'status' => 'active'
            ]);

            // Kayıt talebini sil
            $registrationRequest->delete();
        });

        return response()->json(['message' => 'Okul kaydı onaylandı ve sistem oluşturuldu']);
    }

    /**
     * Kayıt talebini reddet (Super Admin)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $registrationRequest = SchoolRegistrationRequest::findOrFail($id);
        $registrationRequest->delete();

        return response()->json(['message' => 'Kayıt talebi reddedildi']);
    }
}