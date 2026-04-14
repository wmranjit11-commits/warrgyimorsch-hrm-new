<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'department',
        'description',
        'technology',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    public function getProgressAttribute()
    {
        if ($this->status === 'Completed') {
            return 100;
        }

        if ($this->start_date && $this->end_date) {
            $now = now();
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = \Carbon\Carbon::parse($this->end_date);

            if ($now < $startDate) return 0;
            if ($now > $endDate) return 100;

            $totalSeconds = $startDate->diffInSeconds($endDate);
            $elapsedSeconds = $startDate->diffInSeconds($now);

            return ($totalSeconds > 0) ? round(($elapsedSeconds / $totalSeconds) * 100) : 0;
        }

        return 0;
    }
}
