<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class FixDesignations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:designations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up duplicate designations and updates employee records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            // First, find all designations that contain '(' in their names
            $oldDesignations = Designation::where('name', 'LIKE', '%(%')->get();

            $this->info("Found " . $oldDesignations->count() . " old designations with parentheses.");

            foreach ($oldDesignations as $old) {
                // E.g. "Software Engineer / Developer" from "Software Engineer / Developer (SDE)" ? Wait. No!
                // Original was: "Frontend Developer (React / Next.js)"
                // We seeded new one: "Frontend Developer"
                $newName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $old->name));
                
                // Find if a new designation without parenthesis exists
                $newDesignation = Designation::where('name', $newName)->first();

                if ($newDesignation) {
                    // Update any employees referencing the old designation string to use the new one
                    $employeesUpdated = Employee::where('designation', $old->name)->update(['designation' => $newName]);
                    $this->info("Updated {$employeesUpdated} employees from '{$old->name}' to '{$newName}'.");
                    
                    // Delete the old designation since we have the new one
                    $old->delete();
                } else {
                    $this->info("Could not find matching new designation for '{$newName}'");
                }
            }

            DB::commit();
            $this->info('Designations cleanup completed perfectly!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed: ' . $e->getMessage());
        }
    }
}
