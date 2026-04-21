<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\AttendanceService;

class ZKTController extends Controller
{

   
 public function syncAttendance()
    {
         $zk = new \Rats\Zkteco\Lib\ZKTeco('192.168.29.150', 4370, 0, 10);

        // ✅ Step 1: CONNECT FIRST
        if (!$zk->connect()) {
            return "❌ Connection Failed";
        }

        try {
            // ✅ Step 2: Now safe to call device methods
            $zk->disableDevice();
            dd($zk->deviceName());
            // Test communication
            $version = $zk->version();
            dd("Connected OK", $version);

            // Then get attendance
            $attendance = $zk->getAttendance();
            dd($attendance);

        } catch (\Exception $e) {
            dd("ERROR: " . $e->getMessage());
        }

        $zk->enableDevice();
        $zk->disconnect();
    }
}