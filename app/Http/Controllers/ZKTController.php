<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\PyAttendanceService;
use Illuminate\Support\Facades\DB;
class ZKTController extends Controller
{



public function syncAttendance(PyAttendanceService $service)
{
    $pythonScript = "C:\\xampp\\htdocs\\python\\zk_attendance.py";

    $output = shell_exec("python $pythonScript");

    $records = json_decode($output, true);
    // dd($records);
    if (!$records) {
        return response()->json([
            'success' => false,
            'message' => 'No data received from biometric machine',
            'raw' => $output
        ]);
    }

    if (isset($records['error'])) {
        return response()->json([
            'success' => false,
            'message' => $records['error']
        ]);
    }

    foreach ($records as $att) {

        DB::table('attendance_logs')->updateOrInsert(
            [
                'device_uid' => $att['uid'],
                'timestamp'  => $att['timestamp'],
            ],
            [
                'user_id'    => $att['user_id'],
                'status'     => $att['status'],
                'punch'      => $att['punch'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    $formatted = collect($records)->map(function ($att) {
        return [
            'employee_code' => $att['user_id'],
            'timestamp'     => $att['timestamp'],
        ];
    })->toArray();

    $service->processPunches($formatted);

    return response()->json([
        'success' => true,
        'total_records' => count($records),
        'message' => 'Attendance Synced Successfully'
    ]);
}

}