<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveAllotment extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_name',
        'month',
        'year',
        'leave_count',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
