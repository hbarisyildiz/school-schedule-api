<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SchoolRegistrationRequest extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'school_name',
        'school_code', 
        'email',
        'phone',
        'city_id',
        'district_id',
        'address',
        'principal_name',
        'principal_phone',
        'principal_email',
        'subscription_plan_id',
        'status',
        'verification_token',
        'email_verified_at',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'rejected_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Accessor for email template compatibility
    public function getEmailVerificationTokenAttribute()
    {
        return $this->verification_token;
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function school()
    {
        return $this->hasOne(School::class, 'registration_request_id');
    }

    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
