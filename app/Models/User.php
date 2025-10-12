<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'branch',
        'email',
        'password',
        'school_id',
        'role_id',
        'tc_no',
        'phone',
        'avatar',
        'birth_date',
        'gender',
        'teacher_data',
        'student_data',
        'is_active',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'teacher_data' => 'array',
            'student_data' => 'array',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime'
        ];
    }

    /**
     * Okul ilişkisi
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Rol ilişkisi
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Öğretmen olduğu sınıflar
     */
    public function teachingClasses()
    {
        return $this->hasMany(ClassRoom::class, 'class_teacher_id');
    }

    /**
     * Ders programları (öğretmen olarak)
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Rol kontrol metodları
     */
    public function isSuperAdmin(): bool
    {
        return $this->role->name === 'super_admin';
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role->name === 'school_admin';
    }

    public function isTeacher(): bool
    {
        return $this->role->name === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role->name === 'student';
    }

    /**
     * Aktif kullanıcılar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Okul bazlı filtreleme
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
