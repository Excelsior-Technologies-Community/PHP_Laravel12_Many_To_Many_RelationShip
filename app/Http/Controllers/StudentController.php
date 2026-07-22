<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        /*
    |--------------------------------------------------------------------------
    | Full Courses
    |--------------------------------------------------------------------------
    */
        $fullCourses = Course::get()
            ->filter(function ($course) {

                return $course->isFull();
            })
            ->count();

        return view('students.index', compact(
            'students',
            'courses',
            'totalStudents',
            'totalCourses',
            'totalEnrollments',
            'studentsWithoutCourse',
            'fullCourses'
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
                'description' => 'Master Laravel with advanced techniques',
                'capacity' => 20,
            ],

            [
                'title' => 'PHP Fundamentals',
                'description' => 'Learn PHP from scratch',
                'capacity' => 35,
            ],

            [
                'title' => 'Vue.js with Laravel',
                'description' => 'Build modern SPA Applications',
                'capacity' => 25,
            ],

            [
                'title' => 'Database Design',
                'description' => 'Relational Database Design',
                'capacity' => 15,
            ],

            [
                'title' => 'API Development',
                'description' => 'REST API using Laravel',
                'capacity' => 30,
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

        if ($course->isFull()) {
            return back()->with('error', 'This course has reached its maximum capacity.');
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

    /**
     * Enrollment History
     *
     * Shows all student-course enrollments
     * with search, date filter and pagination
     */
    public function history(Request $request)
    {

        $query = DB::table('course_student')
            ->join(
                'students',
                'course_student.student_id',
                '=',
                'students.id'
            )
            ->join(
                'courses',
                'course_student.course_id',
                '=',
                'courses.id'
            )
            ->select(
                'students.name as student_name',
                'students.email',
                'courses.title as course_title',
                'courses.description',
                'course_student.enrolled_at'
            );


        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = $request->search;


            $query->where(function ($q) use ($search) {

                $q->where(
                    'students.name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'students.email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'courses.title',
                        'like',
                        "%{$search}%"
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date')) {

            $query->whereDate(
                'course_student.enrolled_at',
                $request->date
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $enrollments = $query
            ->orderBy(
                'course_student.enrolled_at',
                'desc'
            )
            ->paginate(10)
            ->withQueryString();



        /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

        $totalEnrollments = DB::table('course_student')
            ->count();


        $todayEnrollments = DB::table('course_student')
            ->whereDate(
                'enrolled_at',
                now()->toDateString()
            )
            ->count();


        $uniqueStudents = DB::table('course_student')
            ->distinct('student_id')
            ->count('student_id');


        $uniqueCourses = DB::table('course_student')
            ->distinct('course_id')
            ->count('course_id');



        return view(
            'history.index',
            compact(
                'enrollments',
                'totalEnrollments',
                'todayEnrollments',
                'uniqueStudents',
                'uniqueCourses'
            )
        );
    }

    /**
     * Export Enrollment History CSV
     */
    public function exportHistory()
    {

        $enrollments = DB::table('course_student')

            ->join(
                'students',
                'course_student.student_id',
                '=',
                'students.id'
            )

            ->join(
                'courses',
                'course_student.course_id',
                '=',
                'courses.id'
            )

            ->select(
                'students.name as student_name',
                'students.email',
                'courses.title as course_title',
                'course_student.enrolled_at'
            )

            ->orderBy(
                'course_student.enrolled_at',
                'desc'
            )

            ->get();


        $response = new StreamedResponse(function () use ($enrollments) {

            $handle = fopen('php://output', 'w');

            // CSV Header

            fputcsv($handle, [

                'Student Name',
                'Email',
                'Course',
                'Enrollment Date'

            ]);


            foreach ($enrollments as $item) {


                fputcsv($handle, [

                    $item->student_name,

                    $item->email,

                    $item->course_title,

                    $item->enrolled_at

                ]);
            }

            fclose($handle);
        });

        $response->headers->set(
            'Content-Type',
            'text/csv'
        );

        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=enrollment-history.csv'
        );


        return $response;
    }
}
