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
     * Okul kayıt talebi oluştur (Public endpoint)
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

        $registrationRequest = SchoolRegistrationRequest::create([
            'school_name' => $request->school_name,
            'school_code' => $schoolCode,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'subscription_plan_id' => 1, // Basic plan
            'status' => 'verified', // Email doğrulama yok, direkt onaylı
            'email_verified_at' => now()
        ]);

        return response()->json([
            'message' => 'Okul kaydınız başarıyla oluşturuldu!',
            'school_code' => $schoolCode,
            'status' => 'verified',
            'next_step' => 'Sistemimize giriş yapabilirsiniz.'
        ], 201);
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