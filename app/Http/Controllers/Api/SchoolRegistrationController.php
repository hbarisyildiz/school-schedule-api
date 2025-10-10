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
        
        // Temel plan ata (ID: 1)
        $basicPlan = \App\Models\SubscriptionPlan::where('slug', 'basic')->first();

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
        ]);

        // Hoş geldiniz maili gönder
        try {
            // Geçici School modeli oluştur mail için
            $tempSchool = new School();
            $tempSchool->school_name = $request->school_name;
            $tempSchool->email = $request->email;
            $tempSchool->school_code = $schoolCode;
            
            Mail::to($request->email)->send(new SchoolWelcome($tempSchool, $request->password));
        } catch (\Exception $e) {
            \Log::error('Welcome email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Kayıt başarılı! Hoş geldiniz. Giriş yapabilirsiniz.',
            'school_code' => $schoolCode,
            'message_detail' => 'Email adresinize hoş geldiniz mesajı gönderildi.'
        ], 201);
    }

    /**
     * Email doğrulama (Public endpoint)
     * GET /api/verify-school-email/{token} veya POST /api/verify-school-email (body: {token: "..."})
     */
    public function verifyEmail(Request $request, $token = null)
    {
        $verificationToken = $token ?? $request->input('token');
        
        if (!$verificationToken) {
            return response()->json(['message' => 'Doğrulama token\'ı gereklidir'], 400);
        }

        $registrationRequest = SchoolRegistrationRequest::where('verification_token', $verificationToken)->first();

        if (!$registrationRequest) {
            return response()->json(['message' => 'Geçersiz doğrulama kodu'], 404);
        }

        if ($registrationRequest->isVerified()) {
            return response()->json(['message' => 'Email adresi zaten doğrulanmış'], 400);
        }

        $registrationRequest->update([
            'email_verified_at' => now(),
            'verification_token' => null
        ]);

        return response()->json([
            'message' => 'Email adresiniz başarıyla doğrulandı. Talebiniz inceleme sürecine alındı.'
        ]);
    }

    /**
     * Kayıt taleplerini listele (Super Admin)
     */
    public function index(Request $request)
    {
        $requests = SchoolRegistrationRequest::with(['city', 'district', 'subscriptionPlan', 'approvedBy'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                $query->where('school_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('principal_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($requests);
    }

    /**
     * Kayıt talebini detaylı görüntüle (Super Admin)
     */
    public function show($id)
    {
        $request = SchoolRegistrationRequest::with(['city', 'district', 'subscriptionPlan', 'approvedBy', 'school'])
            ->findOrFail($id);

        return response()->json($request);
    }

    /**
     * Kayıt talebini onayla ve okul oluştur (Super Admin)
     */
    public function approve($id)
    {
        $registrationRequest = SchoolRegistrationRequest::findOrFail($id);

        // Email doğrulama artık gerekli değil

        if (!$registrationRequest->isPending()) {
            return response()->json(['message' => 'Bu talep zaten işlenmiş'], 400);
        }

        DB::beginTransaction();
        try {
            // Okul oluştur
            $school = School::create([
                'name' => $registrationRequest->school_name,
                'slug' => Str::slug($registrationRequest->school_name),
                'code' => $registrationRequest->school_code,
                'email' => $registrationRequest->email,
                'city_id' => $registrationRequest->city_id,
                'district_id' => $registrationRequest->district_id,
                'subscription_plan_id' => $registrationRequest->subscription_plan_id,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addMonth(),
                'subscription_status' => 'active',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'registration_request_id' => $registrationRequest->id
            ]);

            // School Admin kullanıcısı oluştur
            $schoolAdminRole = Role::where('name', 'school_admin')->first();
            $schoolAdmin = User::create([
                'name' => $registrationRequest->school_name . ' Admin',
                'email' => $registrationRequest->email,
                'password' => $registrationRequest->password, // Kullanıcının girdiği şifre
                'school_id' => $school->id,
                'role_id' => $schoolAdminRole->id,
                'is_active' => true
            ]);

            // Registration request'i onayla
            $registrationRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            DB::commit();

            // Onay maili gönder
            try {
                Mail::to($registrationRequest->email)->send(new SchoolRegistrationApproved($school, $schoolAdmin, 'school123'));
            } catch (\Exception $e) {
                \Log::error('Approval email failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Okul kaydı onaylandı ve sistem hesabı oluşturuldu',
                'school' => $school->load(['subscriptionPlan', 'city', 'district']),
                'admin_user' => [
                    'name' => $schoolAdmin->name,
                    'email' => $schoolAdmin->email
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Okul oluşturulurken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Kayıt talebini reddet (Super Admin)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $registrationRequest = SchoolRegistrationRequest::findOrFail($id);

        if (!$registrationRequest->isPending()) {
            return response()->json(['message' => 'Bu talep zaten işlenmiş'], 400);
        }

        $registrationRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Red maili gönder
        try {
            Mail::to($registrationRequest->email)->send(new SchoolRegistrationRejected($registrationRequest));
        } catch (\Exception $e) {
            \Log::error('Rejection email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Okul kaydı reddedildi',
            'request' => $registrationRequest
        ]);
    }

    /**
     * Kayıt talebini sil (Super Admin)
     */
    public function destroy($id)
    {
        $registrationRequest = SchoolRegistrationRequest::findOrFail($id);
        
        if ($registrationRequest->isApproved() && $registrationRequest->school) {
            return response()->json(['message' => 'Onaylanmış ve okul oluşturulmuş talep silinemez'], 400);
        }

        $registrationRequest->delete();

        return response()->json(['message' => 'Kayıt talebi silindi']);
    }
}
