<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class StudentCourseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('course_prerequisites')->truncate();
        DB::table('course_student')->truncate();
        Course::truncate();
        Student::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Students
        $studentRecords = [
            ['name' => 'Mihir Patel', 'email' => 'mihir@example.com'],
            ['name' => 'Emma Johnson', 'email' => 'emma@example.com'],
            ['name' => 'Alex Chen', 'email' => 'alex@example.com'],
            ['name' => 'Sarah Williams', 'email' => 'sarah@example.com'],
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Lisa Ray', 'email' => 'lisa@example.com'],
            ['name' => 'Rahul Sharma', 'email' => 'rahul@example.com'],
            ['name' => 'Priya Patel', 'email' => 'priya@example.com'],
        ];

        $students = [];
        foreach ($studentRecords as $rec) {
            $students[] = Student::create($rec);
        }

        // 2. Create Courses with capacities
        $coursesData = [
            'php' => [
                'title' => 'PHP Fundamentals',
                'description' => 'Learn PHP syntax, OOP, arrays, and standard libraries from scratch',
                'capacity' => 10,
            ],
            'db' => [
                'title' => 'Database Design',
                'description' => 'Relational database schema normalization, indexing, and SQL queries',
                'capacity' => 8,
            ],
            'js' => [
                'title' => 'JavaScript ES6+',
                'description' => 'Modern JavaScript features, async/await, DOM manipulation, and modules',
                'capacity' => 10,
            ],
            'laravel' => [
                'title' => 'Laravel Advanced',
                'description' => 'Master Laravel 12 architecture, service providers, pipelines, and Eloquent',
                'capacity' => 4, // Intentionally tight to show waitlists
            ],
            'vue' => [
                'title' => 'Vue.js with Laravel',
                'description' => 'Build modern reactive Single Page Applications with Inertia/Vue and Laravel',
                'capacity' => 5,
            ],
            'api' => [
                'title' => 'API Development',
                'description' => 'Build secure, scalable RESTful APIs with Sanctum, caching, and rate limiting',
                'capacity' => 6,
            ],
            'testing' => [
                'title' => 'Testing in Laravel',
                'description' => 'Automated test suite creation with PHPUnit and Pest testing framework',
                'capacity' => 6,
            ],
        ];

        $courses = [];
        foreach ($coursesData as $key => $data) {
            $courses[$key] = Course::create($data);
        }

        // 3. Set up Course Prerequisites (Self-referential Many-to-Many)
        // Laravel Advanced requires PHP Fundamentals & Database Design
        $courses['laravel']->prerequisites()->sync([
            $courses['php']->id,
            $courses['db']->id,
        ]);

        // Vue.js with Laravel requires JavaScript ES6+
        $courses['vue']->prerequisites()->sync([
            $courses['js']->id,
        ]);

        // API Development requires PHP Fundamentals & Database Design
        $courses['api']->prerequisites()->sync([
            $courses['php']->id,
            $courses['db']->id,
        ]);

        // Testing in Laravel requires Laravel Advanced
        $courses['testing']->prerequisites()->sync([
            $courses['laravel']->id,
        ]);

        // 4. Populate Enrollments with Rich Pivot Attributes
        // Mihir Patel: Completed PHP & DB with top grades, active in Laravel
        $students[0]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(120),
            'status' => 'completed',
            'grade' => 'A+',
            'score' => 97.5,
            'attendance_percentage' => 98.0,
            'completed_at' => now()->subDays(60),
            'notes' => 'Outstanding coursework and project submission',
        ]);
        $students[0]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(100),
            'status' => 'completed',
            'grade' => 'A',
            'score' => 93.0,
            'attendance_percentage' => 95.0,
            'completed_at' => now()->subDays(40),
            'notes' => 'Exceptional schema design and indexing optimization',
        ]);
        $students[0]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(20),
            'status' => 'active',
            'grade' => 'A',
            'score' => 91.0,
            'attendance_percentage' => 100.0,
            'notes' => 'Active participant in architecture workshops',
        ]);

        // Emma Johnson: Completed JS, active in Vue.js & PHP
        $students[1]->courses()->attach($courses['js']->id, [
            'enrolled_at' => now()->subDays(90),
            'status' => 'completed',
            'grade' => 'A',
            'score' => 92.0,
            'attendance_percentage' => 96.0,
            'completed_at' => now()->subDays(30),
        ]);
        $students[1]->courses()->attach($courses['vue']->id, [
            'enrolled_at' => now()->subDays(25),
            'status' => 'active',
            'grade' => 'A-',
            'score' => 88.5,
            'attendance_percentage' => 94.0,
        ]);
        $students[1]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(45),
            'status' => 'active',
            'grade' => 'B+',
            'score' => 86.0,
            'attendance_percentage' => 90.0,
        ]);

        // Alex Chen: Completed PHP & DB, active in Laravel & API
        $students[2]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(110),
            'status' => 'completed',
            'grade' => 'A',
            'score' => 94.0,
            'attendance_percentage' => 97.0,
            'completed_at' => now()->subDays(50),
        ]);
        $students[2]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(90),
            'status' => 'completed',
            'grade' => 'B+',
            'score' => 87.5,
            'attendance_percentage' => 91.0,
            'completed_at' => now()->subDays(35),
        ]);
        $students[2]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(18),
            'status' => 'active',
            'grade' => 'B+',
            'score' => 85.0,
            'attendance_percentage' => 95.0,
        ]);
        $students[2]->courses()->attach($courses['api']->id, [
            'enrolled_at' => now()->subDays(15),
            'status' => 'active',
            'grade' => 'A-',
            'score' => 89.0,
            'attendance_percentage' => 93.0,
        ]);

        // Sarah Williams: Active in PHP, DB, Laravel
        $students[3]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(60),
            'status' => 'completed',
            'grade' => 'B',
            'score' => 82.0,
            'attendance_percentage' => 88.0,
            'completed_at' => now()->subDays(15),
        ]);
        $students[3]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(55),
            'status' => 'completed',
            'grade' => 'A-',
            'score' => 88.0,
            'attendance_percentage' => 92.0,
            'completed_at' => now()->subDays(10),
        ]);
        $students[3]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(10),
            'status' => 'active',
            'grade' => 'A',
            'score' => 92.0,
            'attendance_percentage' => 100.0,
        ]);

        // John Doe: Enrolled in Laravel (capacity reaches 4/4!)
        $students[4]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(70),
            'status' => 'completed',
            'grade' => 'A',
            'score' => 90.0,
            'attendance_percentage' => 94.0,
            'completed_at' => now()->subDays(20),
        ]);
        $students[4]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(65),
            'status' => 'completed',
            'grade' => 'B',
            'score' => 83.0,
            'attendance_percentage' => 89.0,
            'completed_at' => now()->subDays(18),
        ]);
        $students[4]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(5),
            'status' => 'active',
            'grade' => 'B',
            'score' => 81.0,
            'attendance_percentage' => 90.0,
        ]);

        // Lisa Ray: Joined waitlist for Laravel Advanced (because capacity 4 is full)
        $students[5]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(80),
            'status' => 'completed',
            'grade' => 'A+',
            'score' => 98.0,
            'attendance_percentage' => 99.0,
            'completed_at' => now()->subDays(25),
        ]);
        $students[5]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(75),
            'status' => 'completed',
            'grade' => 'A',
            'score' => 95.0,
            'attendance_percentage' => 96.0,
            'completed_at' => now()->subDays(20),
        ]);
        $students[5]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(3),
            'status' => 'waitlisted',
            'waitlist_position' => 1,
            'notes' => 'High-priority waitlist: completed all prerequisites with A+',
        ]);

        // Rahul Sharma: Waitlisted #2 for Laravel Advanced
        $students[6]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(60),
            'status' => 'completed',
            'grade' => 'B+',
            'score' => 86.0,
            'attendance_percentage' => 91.0,
            'completed_at' => now()->subDays(12),
        ]);
        $students[6]->courses()->attach($courses['db']->id, [
            'enrolled_at' => now()->subDays(58),
            'status' => 'completed',
            'grade' => 'A-',
            'score' => 89.0,
            'attendance_percentage' => 93.0,
            'completed_at' => now()->subDays(10),
        ]);
        $students[6]->courses()->attach($courses['laravel']->id, [
            'enrolled_at' => now()->subDays(1),
            'status' => 'waitlisted',
            'waitlist_position' => 2,
            'notes' => 'Waitlist #2',
        ]);

        // Priya Patel: Dropped from a course & active in others
        $students[7]->courses()->attach($courses['js']->id, [
            'enrolled_at' => now()->subDays(40),
            'status' => 'active',
            'grade' => 'B+',
            'score' => 87.0,
            'attendance_percentage' => 92.0,
        ]);
        $students[7]->courses()->attach($courses['php']->id, [
            'enrolled_at' => now()->subDays(50),
            'status' => 'dropped',
            'notes' => 'Dropped due to scheduling conflict',
        ]);
    }
}