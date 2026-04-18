<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CelebrationController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentMonth = $today->month;

        $employees = Employee::all();

        $celebrations = [];

        foreach ($employees as $emp) {
            if ($emp->date_of_birth) {
                $dob = Carbon::parse($emp->date_of_birth);
                $thisYearBirth = $dob->copy()->year($today->year);

                // If birthday already passed this year, look at next year
                if ($thisYearBirth->lt($today)) {
                    $thisYearBirth->addYear();
                }

                $celebrations[] = [
                    'employee' => $emp,
                    'type' => 'Birthday',
                    'date' => $thisYearBirth,
                    'original_date' => $dob,
                    'icon' => 'feather-gift',
                    'color' => 'primary'
                ];
            }

            if ($emp->date_of_joining) {
                $doj = Carbon::parse($emp->date_of_joining);
                $thisYearAnniv = $doj->copy()->year($today->year);

                // If anniversary already passed this year, look at next year
                if ($thisYearAnniv->lt($today)) {
                    $thisYearAnniv->addYear();
                }

                // Only count as anniversary if joined in a previous year
                if ($doj->year < $today->year) {
                    $years = $thisYearAnniv->year - $doj->year;
                    $celebrations[] = [
                        'employee' => $emp,
                        'type' => 'Work Anniversary',
                        'date' => $thisYearAnniv,
                        'original_date' => $doj,
                        'years' => $years,
                        'icon' => 'feather-award',
                        'color' => 'success'
                    ];
                }
            }
        }

        // Sort by date (closest first)
        usort($celebrations, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        // Filter: Only show celebrations for the next 30 days
        $celebrations = collect($celebrations)->filter(function($item) use ($today) {
            return $today->diffInDays($item['date']) <= 30;
        });

        return view('celebrations.index', compact('celebrations', 'today'));
    }
}
