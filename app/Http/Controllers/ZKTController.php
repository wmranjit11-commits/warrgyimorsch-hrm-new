<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\AttendanceService;

class ZKTController extends Controller
{

    public function syncAttendance(AttendanceService $service)
    {
        $zk = new \Rats\Zkteco\Lib\ZKTeco('192.168.29.201', 4370, 5);
        $connect = $zk->connect();

        if (!$connect) {
            return "❌ Connect failed even after network OK";
        }
        // return "✅ Connected";
        
        $zk->disableDevice();

        $attendances = $zk->getAttendance();
        dd($attendances);
        $formatted = [];

        foreach ($attendances as $att) {

            // store raw logs
            \DB::table('attendance_logs')->updateOrInsert(
                [
                    'device_uid' => $att['uid'],
                    'timestamp'  => $att['timestamp'],
                ],
                [
                    'user_id' => $att['id'],
                    'created_at' => now()
                ]
            );

            $formatted[] = [
                'employee_code' => $att['id'], // replace with mapping later
                'timestamp'     => $att['timestamp'],
            ];
        }

        $service->processPunches($formatted);

        $zk->enableDevice();

        return "✅ Attendance Synced Successfully";
    }

    // public function syncAttendance(AttendanceService $service)
    // {
    //     $ip = '192.168.1.201';

    //     $zk = new ZKTeco($ip);

    //     if (!$zk->connect()) {
    //         return "❌ Device Connection Failed";
    //     }

    //     $zk->disableDevice();

    //     $attendances = $zk->getAttendance();
    //     dd($attendances);
    //     // 🔥 STEP 1: Format data (VERY IMPORTANT)
    //     $formatted = [];

    //     foreach ($attendances as $att) {

    //         $formatted[] = [
    //             'employee_code' => $att['id'], // ⚠️ TEMP (fix mapping later)
    //             'timestamp'     => $att['timestamp'],
    //         ];
    //     }

    //     // 🔥 STEP 2: Send to your existing logic
    //     $service->processPunches($formatted);

    //     $zk->enableDevice();

    //     return "✅ Attendance Synced Successfully";
    // }

}