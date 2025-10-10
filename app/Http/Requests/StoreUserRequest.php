<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Sadece müdür ve admin yeni kullanıcı ekleyebilir
        return in_array($user->role->name, ['super_admin', 'school_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'İsim zorunludur.',
            'name.max' => 'İsim en fazla 255 karakterde olmalıdır.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required' => 'Şifre zorunludur.',
            'password.min' => 'Şifre en az 8 karakterde olmalıdır.',
            'password.confirmed' => 'Şifre onayı eşleşmiyor.',
            'role_id.required' => 'Rol seçimi zorunludur.',
            'role_id.exists' => 'Seçilen rol geçerli değil.',
            'phone.max' => 'Telefon numarası en fazla 20 karakterde olmalıdır.',
            'address.max' => 'Adres en fazla 500 karakterde olmalıdır.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            
            // Rolün yetkisi olan kullanıcı tarafından seçilip seçilmediğini kontrol et
            if ($this->role_id) {
                $allowedRoles = $user->role->name === 'super_admin' 
                    ? \App\Models\Role::all()->pluck('id') 
                    : \App\Models\Role::whereIn('name', ['teacher', 'student'])->pluck('id');

                if (!$allowedRoles->contains($this->role_id)) {
                    $validator->errors()->add('role_id', 'Bu rolü oluşturmaya yetkiniz yok.');
                }
            }
        });
    }
}