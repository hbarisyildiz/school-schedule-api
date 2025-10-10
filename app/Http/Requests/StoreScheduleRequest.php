<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'classroom_id' => 'required|exists:classes,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'semester' => 'required|in:1,2',
            'academic_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'Ders seçimi zorunludur.',
            'subject_id.exists' => 'Seçilen ders geçerli değil.',
            'teacher_id.required' => 'Öğretmen seçimi zorunludur.',
            'teacher_id.exists' => 'Seçilen öğretmen geçerli değil.',
            'class_id.required' => 'Sınıf seçimi zorunludur.',
            'class_id.exists' => 'Seçilen sınıf geçerli değil.',
            'classroom_id.required' => 'Sınıf (fiziki) seçimi zorunludur.',
            'classroom_id.exists' => 'Seçilen sınıf (fiziki) geçerli değil.',
            'day_of_week.required' => 'Gün seçimi zorunludur.',
            'day_of_week.between' => 'Gün 1-7 arasında olmalıdır.',
            'start_time.required' => 'Başlangıç saati zorunludur.',
            'start_time.date_format' => 'Başlangıç saati formatı geçersiz (HH:MM).',
            'end_time.required' => 'Bitiş saati zorunludur.',
            'end_time.date_format' => 'Bitiş saati formatı geçersiz (HH:MM).',
            'end_time.after' => 'Bitiş saati başlangıç saatinden sonra olmalıdır.',
            'semester.required' => 'Dönem seçimi zorunludur.',
            'semester.in' => 'Dönem 1 veya 2 olmalıdır.',
            'academic_year.required' => 'Akademik yıl zorunludur.',
            'academic_year.regex' => 'Akademik yıl formatı geçersiz (YYYY-YYYY).',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ek özel doğrulama kuralları buraya eklenebilir
            
            // Öğretmenin aynı okulda olup olmadığını kontrol et
            if ($this->teacher_id) {
                $teacher = \App\Models\User::find($this->teacher_id);
                if ($teacher && $teacher->school_id !== auth()->user()->school_id) {
                    $validator->errors()->add('teacher_id', 'Seçilen öğretmen aynı okulda değil.');
                }
            }

            // Sınıfın aynı okulda olup olmadığını kontrol et
            if ($this->class_id) {
                $class = \App\Models\ClassRoom::find($this->class_id);
                if ($class && $class->school_id !== auth()->user()->school_id) {
                    $validator->errors()->add('class_id', 'Seçilen sınıf aynı okulda değil.');
                }
            }

            // Dersin aynı okulda olup olmadığını kontrol et
            if ($this->subject_id) {
                $subject = \App\Models\Subject::find($this->subject_id);
                if ($subject && $subject->school_id !== auth()->user()->school_id) {
                    $validator->errors()->add('subject_id', 'Seçilen ders aynı okulda değil.');
                }
            }
        });
    }
}