<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\Designation;
use App\Models\RoleMaster;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Manager', 'slug' => 'manager'],
            ['name' => 'HR Executive', 'slug' => 'hr_executive'],
            ['name' => 'HR Intern', 'slug' => 'hr_intern'],
            ['name' => 'Team Leader', 'slug' => 'team_leader'],
            ['name' => 'Employee', 'slug' => 'employee'],
            ['name' => 'Business Operation Head', 'slug' => 'business_operation_head'],
            ['name' => 'HR Marketing', 'slug' => 'hr_marketing'],
        ];

        foreach ($roles as $role) {
            RoleMaster::updateOrCreate(['slug' => $role['slug']], $role);
        }

        // 2. Departments
        $departments = [
            ['name' => 'Administration', 'short_name' => 'Admin'],
            ['name' => 'Business Development', 'short_name' => 'BD'],
            ['name' => 'HR Department', 'short_name' => 'HR'],
            ['name' => 'Web Development', 'short_name' => 'WD'],
            ['name' => 'Digital Marketing', 'short_name' => 'DM'],
            ['name' => 'Web & Graphics Design', 'short_name' => 'WGD'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['name' => $dept['name']], $dept);
        }

        // 3. Designations
        $designations = [
            // Management
            ['name' => 'Chief Executive Officer', 'short_name' => 'CEO'],
            ['name' => 'Chief Finance Officer', 'short_name' => 'CFO'],
            ['name' => 'Chief Technology Officer', 'short_name' => 'CTO'],
            ['name' => 'Project Manager', 'short_name' => 'PM'],
            ['name' => 'Team Lead / Tech Lead', 'short_name' => 'TL'],
            // Development
            ['name' => 'Software Engineer / Developer', 'short_name' => 'SDE'],
            ['name' => 'Frontend Developer', 'short_name' => 'React / Next.js'],
            ['name' => 'Backend Developer', 'short_name' => 'Laravel / Node.js'],
            ['name' => 'Full Stack Developer', 'short_name' => 'FSD'],
            ['name' => 'Mobile App Developer', 'short_name' => 'Flutter / Android / iOS'],
            ['name' => 'Web Developer Intern', 'short_name' => 'Intern'],
            // Specialized
            ['name' => 'DevOps Engineer', 'short_name' => 'DevOps'],
            ['name' => 'Cloud Engineer', 'short_name' => 'AWS / Azure / GCP'],
            ['name' => 'Data Science Engineer', 'short_name' => 'DSE'],
            ['name' => 'AI / Machine Learning Engineer', 'short_name' => 'AI/ML'],
            // Testing
            ['name' => 'QA Engineer / Tester', 'short_name' => 'QA'],
            ['name' => 'Automation Test Engineer', 'short_name' => 'QA Automation'],
            // Design
            ['name' => 'UI/UX Designer', 'short_name' => 'UI/UX'],
            ['name' => 'Graphic Designer', 'short_name' => 'GD'],
            ['name' => 'Social Media Executive', 'short_name' => 'SME'],
            // Support
            ['name' => 'System Administrator', 'short_name' => 'SysAdmin'],
            ['name' => 'IT Support Engineer', 'short_name' => 'IT Support'],
            // Business
            ['name' => 'Business Development Manager', 'short_name' => 'BDM'],
            ['name' => 'Sales Executive', 'short_name' => 'SE'],
            ['name' => 'Digital Marketing Executive', 'short_name' => 'DME'],
            ['name' => 'SEO Executive', 'short_name' => 'SEO'],
            ['name' => 'SEO Intern', 'short_name' => 'SEO Intern'],
            // HR
            ['name' => 'HR Manager', 'short_name' => 'HRM'],
            ['name' => 'HR Executive', 'short_name' => 'HR'],
            ['name' => 'HR Intern', 'short_name' => 'HR Intern'],
        ];

        foreach ($designations as $desg) {
            Designation::updateOrCreate(['name' => $desg['name']], $desg);
        }
    }
}
