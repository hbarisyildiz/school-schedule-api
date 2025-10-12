<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Traits\ApiResponseTrait;
use App\Imports\TeachersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use ApiResponseTrait;
    /**
     * Okulun kullanıcılarını listele
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'school'])
            ->where('school_id', auth()->user()->school_id);

        // Rol filtresi
        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Arama
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Durum filtresi
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate(15);

        return response()->json($users);
    }

    /**
     * Yeni kullanıcı oluştur (Sadece müdür ve admin)
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = auth()->user();

            $newUser = User::create([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'branch' => $request->branch,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'school_id' => $user->school_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true
            ]);

            return $this->createdResponse(
                $newUser->load(['role', 'school']),
                'Kullanıcı başarıyla oluşturuldu'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Kullanıcı detaylarını göster
     */
    public function show(string $id)
    {
        $user = User::with(['role', 'school', 'createdBy'])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        return response()->json([
            'user' => $user,
            'permissions' => $user->role->permissions ?? [],
            'statistics' => [
                'created_at' => $user->created_at->format('d.m.Y H:i'),
                'last_login' => $user->last_login_at?->format('d.m.Y H:i'),
                'total_schedules' => $user->schedules()->count() ?? 0
            ]
        ]);
    }

    /**
     * Kullanıcı bilgilerini güncelle
     */
    public function update(Request $request, string $id)
    {
        $currentUser = auth()->user();
        $user = User::where('school_id', $currentUser->school_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'string|max:255',
            'short_name' => 'nullable|string|max:6',
            'branch' => 'nullable|string|max:100',
            'email' => ['email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'exists:roles,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Sadece kendi bilgilerini veya yetkisi olanlar değiştirebilir
        if ($user->id !== $currentUser->id && !in_array($currentUser->role->name, ['super_admin', 'school_admin'])) {
            return response()->json([
                'message' => 'Bu kullanıcıyı güncelleme yetkiniz yok'
            ], 403);
        }

        $user->update($request->only([
            'name', 'short_name', 'branch', 'email', 'role_id', 'phone', 'address'
        ]));

        return response()->json([
            'message' => 'Kullanıcı bilgileri güncellendi',
            'user' => $user->fresh()->load(['role', 'school'])
        ]);
    }

    /**
     * Kullanıcı şifresini güncelle
     */
    public function updatePassword(Request $request, string $id)
    {
        $currentUser = auth()->user();
        $user = User::where('school_id', $currentUser->school_id)->findOrFail($id);

        $request->validate([
            'current_password' => $user->id === $currentUser->id ? 'required' : 'nullable',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Kendi şifresini değiştiriyorsa mevcut şifre kontrolü
        if ($user->id === $currentUser->id) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Mevcut şifre yanlış'
                ], 422);
            }
        } elseif (!in_array($currentUser->role->name, ['super_admin', 'school_admin'])) {
            return response()->json([
                'message' => 'Bu kullanıcının şifresini değiştirme yetkiniz yok'
            ], 403);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Şifre başarıyla güncellendi'
        ]);
    }

    /**
     * Kullanıcıyı aktif/pasif yap
     */
    public function toggleStatus(string $id)
    {
        $currentUser = auth()->user();
        $user = User::where('school_id', $currentUser->school_id)->findOrFail($id);

        // Sadece admin/müdür durum değiştirebilir
        if (!in_array($currentUser->role->name, ['super_admin', 'school_admin'])) {
            return response()->json([
                'message' => 'Bu işlem için yetkiniz yok'
            ], 403);
        }

        // Kendi durumunu değiştiremez
        if ($user->id === $currentUser->id) {
            return response()->json([
                'message' => 'Kendi durumunuzu değiştiremezsiniz'
            ], 422);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => $user->is_active ? 'Kullanıcı aktifleştirildi' : 'Kullanıcı pasifleştirildi',
            'user' => $user
        ]);
    }

    /**
     * Kullanıcıyı sil
     */
    public function destroy(string $id)
    {
        $currentUser = auth()->user();
        $user = User::where('school_id', $currentUser->school_id)->findOrFail($id);

        // Sadece super admin silebilir
        if ($currentUser->role->name !== 'super_admin') {
            return response()->json([
                'message' => 'Bu işlem için yetkiniz yok'
            ], 403);
        }

        // Kendi hesabını silemez
        if ($user->id === $currentUser->id) {
            return response()->json([
                'message' => 'Kendi hesabınızı silemezsiniz'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Kullanıcı silindi'
        ]);
    }

    /**
     * Rolleri listele
     */
    public function getRoles()
    {
        $currentUser = auth()->user();
        
        $roles = $currentUser->role->name === 'super_admin' 
            ? Role::all()
            : Role::whereIn('name', ['teacher', 'student'])->get();

        return response()->json($roles);
    }

    /**
     * Profil bilgilerini güncelle
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'message' => 'Profil güncellendi',
            'user' => $user->fresh()->load(['role', 'school'])
        ]);
    }
    
    /**
     * Excel'den toplu öğretmen yükleme
     */
    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);
        
        try {
            $import = new TeachersImport(auth()->user()->school_id);
            Excel::import($import, $request->file('file'));
            
            $failures = $import->failures();
            $successCount = $import->getRowCount() - count($failures);
            
            if (count($failures) > 0) {
                return response()->json([
                    'message' => "{$successCount} öğretmen başarıyla eklendi, " . count($failures) . " satırda hata var",
                    'success_count' => $successCount,
                    'error_count' => count($failures),
                    'errors' => $failures->map(function($failure) {
                        return [
                            'row' => $failure->row(),
                            'attribute' => $failure->attribute(),
                            'errors' => $failure->errors(),
                            'values' => $failure->values()
                        ];
                    })
                ], 206); // 206 = Partial Content
            }
            
            return response()->json([
                'message' => "Tüm öğretmenler başarıyla eklendi! ({$successCount} öğretmen)",
                'success_count' => $successCount
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Excel yüklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
