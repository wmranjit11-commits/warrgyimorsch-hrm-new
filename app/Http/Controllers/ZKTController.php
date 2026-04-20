<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\AttendanceService;

class ZKTController extends Controller
{

    public function syncAttendance(AttendanceService $service)
    {
        $zk = new ZKTeco('192.168.29.101');
        // dd($zk);
        if (!$zk->connect()) {
            return "❌ Device Connection Failed";
        }

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
    //     $zk = new ZKTeco('192.168.29.150', 4370, 0, 10);
    //     $connect = $zk->connect();
    //    if (!$connect) {
    //         dd("❌ Connection still failed");
    //     }

    //     dd("✅ Connected successfully");
        
    //     $zk->disableDevice();

    //     $attendances = $zk->getAttendance();
    //     dd($attendances);
    //     $formatted = [];

    //     foreach ($attendances as $att) {

    //         // store raw logs
    //         \DB::table('attendance_logs')->updateOrInsert(
    //             [
    //                 'device_uid' => $att['uid'],
    //                 'timestamp'  => $att['timestamp'],
    //             ],
    //             [
    //                 'user_id' => $att['id'],
    //                 'created_at' => now()
    //             ]
    //         );

    //         $formatted[] = [
    //             'employee_code' => $att['id'], // replace with mapping later
    //             'timestamp'     => $att['timestamp'],
    //         ];
    //     }

    //     $service->processPunches($formatted);

    //     $zk->enableDevice();

    //     return "✅ Attendance Synced Successfully";
    // }

   public function syncAttendance(AttendanceService $service)
    {
        $zk = new ZKTeco('192.168.29.150', 4370, 0, 10);

        if (!$zk->connect()) {
            return "❌ Device Connection Failed";
        }

        try {
            $zk->disableDevice();

            $attendances = $zk->getAttendance();

            if (empty($attendances)) {
                return "⚠️ No attendance records found";
            }

            $formatted = [];

            foreach ($attendances as $att) {
                $formatted[] = [
                    'employee_code' => $att['id'] ?? $att['uid'],
                    'timestamp'     => $att['timestamp'],
                ];
            }

            $service->processPunches($formatted);

            return "✅ Attendance Synced Successfully";

        } catch (\Exception $e) {
            return "❌ Error: " . $e->getMessage();
        } finally {
            $zk->enableDevice();
        }
    }

}