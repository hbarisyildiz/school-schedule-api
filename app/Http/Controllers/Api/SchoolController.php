<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Tüm okulları listele (Sadece süper admin)
     */
    public function index(Request $request)
    {
        $schools = School::with(['subscriptionPlan', 'city', 'district'])
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                $query->where('subscription_status', $status);
            })
            ->paginate(15);

        return response()->json($schools);
    }

    /**
     * Yeni okul oluştur (Süper admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id'
        ]);

        $school = School::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'code' => 'SCH' . rand(1000, 9999),
            'email' => $request->email,
            'phone' => $request->phone,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'website' => $request->website,
            'subscription_plan_id' => $request->subscription_plan_id,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addMonth(),
            'subscription_status' => 'active',
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Okul başarıyla oluşturuldu',
            'school' => $school->load(['subscriptionPlan', 'city', 'district'])
        ], 201);
    }

    /**
     * Okul detaylarını göster
     */
    public function show(string $id)
    {
        $school = School::with(['subscriptionPlan', 'users.role'])
            ->findOrFail($id);

        return response()->json([
            'school' => $school,
            'statistics' => [
                'teachers' => $school->current_teachers,
                'students' => $school->current_students,
                'classes' => $school->current_classes,
                'subscription_days_left' => $school->subscription_ends_at->diffInDays(now())
            ]
        ]);
    }

    /**
     * Okul bilgilerini güncelle
     */
    public function update(Request $request, string $id)
    {
        $school = School::findOrFail($id);
        
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:schools,email,' . $school->id,
            'phone' => 'string',
            'city_id' => 'exists:cities,id',
            'district_id' => 'exists:districts,id',
        ]);

        $school->update($request->only([
            'name', 'email', 'phone', 'city_id', 'district_id', 'website'
        ]));

        return response()->json([
            'message' => 'Okul bilgileri güncellendi',
            'school' => $school->fresh()->load(['subscriptionPlan', 'city', 'district'])
        ]);
    }

    /**
     * Okulun aboneliğini güncelle (Süper admin)
     */
    public function updateSubscription(Request $request, string $id)
    {
        $school = School::findOrFail($id);
        
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'subscription_ends_at' => 'required|date|after:today',
            'subscription_status' => 'required|in:active,expired,suspended,cancelled'
        ]);

        $school->update([
            'subscription_plan_id' => $request->subscription_plan_id,
            'subscription_ends_at' => $request->subscription_ends_at,
            'subscription_status' => $request->subscription_status
        ]);

        return response()->json([
            'message' => 'Abonelik güncellendi',
            'school' => $school->fresh()->load('subscriptionPlan')
        ]);
    }

    /**
     * Okulu pasifleştir/aktifleştir
     */
    public function toggleStatus(string $id)
    {
        $school = School::findOrFail($id);
        $school->update(['is_active' => !$school->is_active]);

        return response()->json([
            'message' => $school->is_active ? 'Okul aktifleştirildi' : 'Okul pasifleştirildi',
            'school' => $school
        ]);
    }

    /**
     * Abonelik planlarını listele
     */
    public function getSubscriptionPlans()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return response()->json($plans);
    }

    /**
     * Şehirleri listele
     */
    public function getCities()
    {
        $cities = \App\Models\City::orderBy('name')->get();
        
        return response()->json($cities);
    }

    /**
     * Belirli şehre ait ilçeleri listele
     */
    public function getDistricts($cityId)
    {
        $districts = \App\Models\District::where('city_id', $cityId)
            ->orderBy('name')
            ->get();
            
        return response()->json($districts);
    }
}
