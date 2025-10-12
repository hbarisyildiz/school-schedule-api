<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    
    protected $schoolId;
    protected $teacherRoleId;
    protected $rowCount = 0;
    
    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
        $this->teacherRoleId = Role::where('name', 'teacher')->first()->id;
    }
    
    public function getRowCount()
    {
        return $this->rowCount;
    }
    
    /**
     * Excel'den modele çevir
     */
    public function model(array $row)
    {
        $this->rowCount++;
        
        // Kısa ad boşsa otomatik oluştur
        $shortName = $row['kisa_ad'] ?? $this->generateShortName($row['ad_soyad']);
        
        return new User([
            'school_id' => $this->schoolId,
            'role_id' => $this->teacherRoleId,
            'name' => $row['ad_soyad'],
            'short_name' => strtoupper($shortName),
            'branch' => $row['brans'],
            'email' => $row['email'],
            'phone' => $row['telefon'] ?? null,
            'password' => Hash::make('12345678'), // Default şifre, sonra değiştirsinler
            'is_active' => true,
            'email_verified_at' => now()
        ]);
    }
    
    /**
     * Validation kuralları
     */
    public function rules(): array
    {
        return [
            'ad_soyad' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'brans' => 'required|string|max:100',
            'kisa_ad' => 'nullable|string|max:6',
            'telefon' => 'nullable|string|max:20'
        ];
    }
    
    /**
     * Özel hata mesajları
     */
    public function customValidationMessages()
    {
        return [
            'ad_soyad.required' => 'Ad Soyad alanı zorunludur',
            'email.required' => 'Email alanı zorunludur',
            'email.email' => 'Geçerli bir email adresi giriniz',
            'email.unique' => 'Bu email adresi zaten kullanılıyor',
            'brans.required' => 'Branş alanı zorunludur'
        ];
    }
    
    /**
     * Kısa ad oluştur (Ad Soyaddan)
     */
    private function generateShortName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        
        if (count($parts) < 2) {
            return strtoupper(substr($parts[0], 0, 6));
        }
        
        $firstName = substr($parts[0], 0, 4);
        $lastName = substr($parts[count($parts) - 1], 0, 2);
        
        return strtoupper($firstName . $lastName);
    }
}
