<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\AttendanceService;

class ZKTController extends Controller
{
    public function syncAttendance(AttendanceService $service)
    {
        $ip = '192.168.29.201';

        $zk = new ZKTeco($ip);

        if (!$zk->connect()) {
            return "❌ Device Connection Failed";
        }

        $zk->disableDevice();

        $attendances = $zk->getAttendance();

        // 🔥 STEP 1: Format data (VERY IMPORTANT)
        $formatted = [];

        foreach ($attendances as $att) {

            $formatted[] = [
                'employee_code' => $att['id'], // ⚠️ TEMP (fix mapping later)
                'timestamp'     => $att['timestamp'],
            ];
        }

        // 🔥 STEP 2: Send to your existing logic
        $service->processPunches($formatted);

        $zk->enableDevice();

        return "✅ Attendance Synced Successfully";
    }
}