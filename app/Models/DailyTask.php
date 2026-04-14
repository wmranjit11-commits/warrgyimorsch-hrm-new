<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_title',
        'start_date',
        'end_date',
        'priority',
        'status',
        'employee_id',
        'assigned_by',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function followUps()
    {
        return $this->hasMany(TaskFollowUp::class, 'daily_task_id');
    }

    public function getTotalTimeAttribute()
    {
        return $this->followUps->reduce(function ($total, $fu) {
            // Extract numeric part from strings like "5 hours", "2.5 days" etc.
            if (preg_match('/[+-]?([0-9]*[.])?[0-9]+/', $fu->time_taken, $matches)) {
                return $total + (float)$matches[0];
            }
            return $total;
        }, 0);
    }
}
