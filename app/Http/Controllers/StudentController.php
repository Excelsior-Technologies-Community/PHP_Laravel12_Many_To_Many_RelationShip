<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Show all students with their courses
    public function index()
    {
        $students = Student::with('courses')
                    ->orderBy('name')
                    ->get();
        
        $courses = Course::withCount('students')
                    ->orderBy('title')
                    ->get();
        
        return view('students.index', compact('students', 'courses'));
    }

    // Show all courses with their students
    public function courses()
    {
        $courses = Course::with('students')
                    ->orderBy('title')
                    ->get();
        
        return view('courses.index', compact('courses'));
    }

    // Create sample data
    public function createSampleData()
    {
        // Create students
        $students = [
            ['name' => 'Mihir Patel', 'email' => 'mihir@example.com'],
            ['name' => 'Emma Johnson', 'email' => 'emma@example.com'],
            ['name' => 'Alex Chen', 'email' => 'alex@example.com'],
            ['name' => 'Sarah Williams', 'email' => 'sarah@example.com'],
        ];

        foreach ($students as $studentData) {
            Student::firstOrCreate(
                ['email' => $studentData['email']],
                $studentData
            );
        }

        // Create courses
        $courses = [
            [
                'title' => 'Laravel Advanced',
                'description' => 'Master Laravel with advanced techniques'
            ],
            [
                'title' => 'PHP Fundamentals',
                'description' => 'Learn PHP from scratch'
            ],
            [
                'title' => 'Vue.js with Laravel',
                'description' => 'Build modern SPAs with Vue and Laravel'
            ],
            [
                'title' => 'Database Design',
                'description' => 'Learn relational database design principles'
            ],
            [
                'title' => 'API Development',
                'description' => 'Build RESTful APIs with Laravel'
            ],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );
        }

        // Enroll students in random courses
        $allStudents = Student::all();
        $allCourses = Course::all();

        foreach ($allStudents as $student) {
            // Each student enrolls in 2-4 random courses
            $randomCourses = $allCourses->random(rand(2, 4));
            foreach ($randomCourses as $course) {
                $student->enroll($course);
            }
        }

        return redirect()->route('students.index')
            ->with('success', 'Sample data created successfully!');
    }

    // Enroll a student in a course
    public function enroll(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        if ($student->isEnrolled($course)) {
            return back()->with('error', 'Student is already enrolled in this course!');
        }

        $student->enroll($course);

        return back()->with('success', 'Student enrolled successfully!');
    }

    // Drop a student from a course
    public function drop($studentId, $courseId)
    {
        $student = Student::findOrFail($studentId);
        $course = Course::findOrFail($courseId);

        $student->drop($course);

        return back()->with('success', 'Student dropped from course successfully!');
    }
}