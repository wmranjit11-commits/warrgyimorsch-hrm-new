<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'role',
        'department',
        'designation',
        'date_of_joining',
        'date_of_birth',
        'gender',
        'employee_type',
        'username',
        'password',
        'aadhaar_number',
        'pan_number',
        'address',
        'time_in',
        'time_out',
        'leave',
        'photo',
        'pf',
        'pf_number',
        'esi',
        'esi_number',
        'insurance',
        'insurance_provider',
        'insurance_policy_number',
        'bank_name',
        'account_number',
        'ifsc_code',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'other_allowance',
    ];

    protected $appends = ['photo_url'];

    public function leaveAllotments()
    {
        return $this->hasMany(LeaveAllotment::class);
    }

    /**
     * Get the full URL for the employee's photo.
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) return null;
        
        // Handle direct public uploads
        if (str_starts_with($this->photo, 'uploads/')) {
            return asset($this->photo);
        }
        
        // Handle storage-based uploads
        return asset('storage/' . $this->photo);
    }
}
