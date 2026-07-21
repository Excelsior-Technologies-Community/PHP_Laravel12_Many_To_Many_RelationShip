<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display Students
     * Features:
     * - Search
     * - Filter by Course
     * - Pagination
     * - Dashboard Statistics
     */
    public function index(Request $request)
    {
        $query = Student::with('courses');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter By Course
        |--------------------------------------------------------------------------
        */
        if ($request->filled('course')) {

            $courseId = $request->course;

            $query->whereHas('courses', function ($q) use ($courseId) {

                $q->where('courses.id', $courseId);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */
        $students = $query
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Courses
        |--------------------------------------------------------------------------
        */
        $courses = Course::withCount('students')
            ->orderBy('title')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */
        $totalStudents = Student::count();

        $totalCourses = Course::count();

        $totalEnrollments = \DB::table('course_student')->count();

        $studentsWithoutCourse = Student::doesntHave('courses')->count();

        return view('students.index', compact(
            'students',
            'courses',
            'totalStudents',
            'totalCourses',
            'totalEnrollments',
            'studentsWithoutCourse'
        ));
    }

    /**
     * Courses Page
     */
    public function courses(Request $request)
    {
        $query = Course::with('students');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query
            ->orderBy('title')
            ->paginate(6)
            ->withQueryString();

        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalEnrollments = \DB::table('course_student')->count();

        return view('courses.index', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'totalEnrollments'
        ));
    }

    /**
     * Create Sample Data
     */
    public function createSampleData()
    {

        $students = [

            [
                'name'  => 'Mihir Patel',
                'email' => 'mihir@example.com'
            ],

            [
                'name'  => 'Emma Johnson',
                'email' => 'emma@example.com'
            ],

            [
                'name'  => 'Alex Chen',
                'email' => 'alex@example.com'
            ],

            [
                'name'  => 'Sarah Williams',
                'email' => 'sarah@example.com'
            ],

        ];

        foreach ($students as $studentData) {

            Student::firstOrCreate(

                [
                    'email' => $studentData['email']
                ],

                $studentData

            );
        }

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

                'description' => 'Build modern SPA Applications'

            ],

            [

                'title' => 'Database Design',

                'description' => 'Relational Database Design'

            ],

            [

                'title' => 'API Development',

                'description' => 'REST API using Laravel'

            ],

        ];

        foreach ($courses as $courseData) {

            Course::firstOrCreate(

                [
                    'title' => $courseData['title']
                ],

                $courseData

            );
        }

        $allStudents = Student::all();

        $allCourses = Course::all();

        foreach ($allStudents as $student) {

            $randomCourses = $allCourses->random(rand(2, 4));

            foreach ($randomCourses as $course) {

                $student->enroll($course);
            }
        }

        return redirect()
            ->route('students.index')
            ->with('success', 'Sample data created successfully.');
    }

    /**
     * Enroll Student
     */
    public function enroll(Request $request)
    {
        $request->validate([

            'student_id' => 'required|exists:students,id',

            'course_id' => 'required|exists:courses,id',

        ]);

        $student = Student::findOrFail($request->student_id);

        $course = Course::findOrFail($request->course_id);

        if ($student->isEnrolled($course)) {

            return back()->with('error', 'Student already enrolled.');
        }

        $student->enroll($course);

        return back()->with('success', 'Student enrolled successfully.');
    }

    /**
     * Drop Course
     */
    public function drop($studentId, $courseId)
    {
        $student = Student::findOrFail($studentId);

        $course = Course::findOrFail($courseId);

        $student->drop($course);

        return back()->with('success', 'Student dropped successfully.');
    }
}
