<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use ApiResponseTrait;

    /**
     * Dersleri listele
     */
    public function index()
    {
        try {
            $subjects = Subject::where('school_id', auth()->user()->school_id)
                ->orderBy('name', 'asc')
                ->get();

            return $this->successResponse($subjects, 'Dersler listelendi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Yeni ders oluştur
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ], [
                'name.required' => 'Ders adı zorunludur',
            ]);

            $subject = Subject::create([
                'school_id' => auth()->user()->school_id,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            return $this->createdResponse($subject, 'Ders başarıyla oluşturuldu');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Ders detayını göster
     */
    public function show(string $id)
    {
        try {
            $subject = Subject::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            return $this->successResponse($subject, 'Ders detayı');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Dersi güncelle
     */
    public function update(Request $request, string $id)
    {
        try {
            $subject = Subject::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $subject->update($request->only([
                'name', 'description'
            ]));

            return $this->updatedResponse($subject, 'Ders başarıyla güncellendi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Dersi sil
     */
    public function destroy(string $id)
    {
        try {
            $subject = Subject::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            // Ders programında kullanılıyor mu kontrol et
            if ($subject->schedules()->count() > 0) {
                return $this->errorResponse(
                    'Bu ders, ders programında kullanıldığı için silinemez',
                    422
                );
            }

            $subject->delete();

            return $this->deletedResponse('Ders başarıyla silindi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Ders durumunu değiştir
     */
    public function toggleStatus(string $id)
    {
        try {
            $subject = Subject::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            $subject->update(['is_active' => !$subject->is_active]);

            $message = $subject->is_active ? 'Ders aktifleştirildi' : 'Ders pasifleştirildi';

            return $this->successResponse($subject, $message);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
