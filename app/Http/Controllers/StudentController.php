<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    /**
     * Display Students Dashboard
     */
    public function index(Request $request)
    {
        $query = Student::with(['courses.prerequisites']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $courseId = $request->course;
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('courses', function ($q) use ($status) {
                $q->where('course_student.status', $status);
            });
        }

        $students = $query
            ->orderBy('name')
            ->paginate(6)
            ->withQueryString();

        $courses = Course::with(['prerequisites', 'students'])->orderBy('title')->get();

        // Dashboard Statistics
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $totalEnrollments = DB::table('course_student')->count();
        $activeEnrollments = DB::table('course_student')->where('status', 'active')->count();
        $completedEnrollments = DB::table('course_student')->where('status', 'completed')->count();
        $waitlistedEnrollments = DB::table('course_student')->where('status', 'waitlisted')->count();
        $studentsWithoutCourse = Student::doesntHave('courses')->count();
        $fullCourses = $courses->filter(fn($c) => $c->isFull())->count();

        return view('students.index', compact(
            'students',
            'courses',
            'totalStudents',
            'totalCourses',
            'totalEnrollments',
            'activeEnrollments',
            'completedEnrollments',
            'waitlistedEnrollments',
            'studentsWithoutCourse',
            'fullCourses'
        ));
    }

    /**
     * Courses Page with Prerequisites and Waitlists
     */
    public function courses(Request $request)
    {
        $query = Course::with(['students', 'prerequisites', 'dependentCourses']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('title')->paginate(6)->withQueryString();

        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalEnrollments = DB::table('course_student')->count();
        $allStudents = Student::orderBy('name')->get();
        $allCourses = Course::orderBy('title')->get();

        return view('courses.index', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'totalEnrollments',
            'allStudents',
            'allCourses'
        ));
    }

    /**
     * Interactive Relationship Matrix (Grid View)
     */
    public function matrix(Request $request)
    {
        $students = Student::with('courses')->orderBy('name')->get();
        $courses = Course::with(['prerequisites', 'students'])->orderBy('title')->get();

        return view('matrix.index', compact('students', 'courses'));
    }

    /**
     * AJAX Toggle for Matrix Cell
     */
    public function toggleMatrix(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $course = Course::with('prerequisites')->findOrFail($request->course_id);

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            // Already enrolled -> detach / remove
            $student->drop($course);
            $promoted = $course->promoteNextWaitlistStudent();

            return response()->json([
                'success' => true,
                'action' => 'detached',
                'status' => null,
                'message' => "Removed {$student->name} from {$course->title}." . ($promoted ? " Auto-promoted {$promoted->name} from waitlist!" : ""),
                'course_remaining' => $course->remainingSeats(),
                'course_is_full' => $course->isFull(),
            ]);
        }

        // Not enrolled -> Check prerequisites
        if (!$student->meetsPrerequisites($course)) {
            $missing = $student->missingPrerequisites($course)->pluck('title')->implode(', ');
            return response()->json([
                'success' => false,
                'action' => 'prerequisite_failed',
                'message' => "Cannot enroll {$student->name}! Missing required prerequisites: {$missing}",
            ], 422);
        }

        // Check capacity -> if full, join waitlist
        if ($course->isFull()) {
            $position = $student->joinWaitlist($course, 'Joined via Matrix Studio');
            return response()->json([
                'success' => true,
                'action' => 'waitlisted',
                'status' => 'waitlisted',
                'waitlist_position' => $position,
                'message' => "Course is full! {$student->name} placed on Waitlist at #{$position}.",
                'course_remaining' => 0,
                'course_is_full' => true,
            ]);
        }

        // Active enrollment
        $student->enroll($course);

        return response()->json([
            'success' => true,
            'action' => 'enrolled',
            'status' => 'active',
            'message' => "Successfully enrolled {$student->name} in {$course->title}.",
            'course_remaining' => $course->remainingSeats(),
            'course_is_full' => $course->isFull(),
        ]);
    }

    /**
     * Graphical Analytics Dashboard
     */
    public function analytics()
    {
        $courses = Course::with(['students'])->orderBy('title')->get();
        $students = Student::with(['courses'])->get();

        // Enrollment Status Breakdown
        $statusCounts = [
            'active' => DB::table('course_student')->where('status', 'active')->count(),
            'completed' => DB::table('course_student')->where('status', 'completed')->count(),
            'waitlisted' => DB::table('course_student')->where('status', 'waitlisted')->count(),
            'dropped' => DB::table('course_student')->where('status', 'dropped')->count(),
        ];

        // Grade Distribution
        $grades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'F'];
        $gradeCounts = [];
        foreach ($grades as $g) {
            $gradeCounts[$g] = DB::table('course_student')->where('grade', $g)->count();
        }

        // Course Popularity & Capacity
        $courseLabels = [];
        $courseEnrolledData = [];
        $courseCapacityData = [];
        $courseWaitlistData = [];

        foreach ($courses as $c) {
            $courseLabels[] = $c->title;
            $courseEnrolledData[] = $c->enrolledCount();
            $courseCapacityData[] = $c->capacity;
            $courseWaitlistData[] = $c->waitlistCount();
        }

        // Leaderboard: Top Students by Completed Courses & GPA
        $leaderboard = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
                'completed_count' => $s->completedCourses()->count(),
                'active_count' => $s->activeCourses()->count(),
                'gpa' => $s->calculateGpa(),
                'credits' => $s->totalCredits(),
            ];
        })->sortByDesc('completed_count')->values()->take(5);

        return view('analytics.index', compact(
            'courses',
            'statusCounts',
            'gradeCounts',
            'courseLabels',
            'courseEnrolledData',
            'courseCapacityData',
            'courseWaitlistData',
            'leaderboard'
        ));
    }

    /**
     * Enroll Student (with Prerequisite & Waitlist support)
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'force_waitlist' => 'nullable|boolean',
        ]);

        $student = Student::findOrFail($request->student_id);
        $course = Course::with('prerequisites')->findOrFail($request->course_id);

        if ($student->isEnrolled($course)) {
            return back()->with('error', "{$student->name} is already actively enrolled in {$course->title}.");
        }

        if ($student->isWaitlisted($course)) {
            return back()->with('error', "{$student->name} is already on the waitlist for {$course->title}.");
        }

        // Check prerequisites
        if (!$student->meetsPrerequisites($course)) {
            $missing = $student->missingPrerequisites($course)->pluck('title')->implode(', ');
            return back()->with('error', "Prerequisite check failed! {$student->name} must complete: [{$missing}] before taking {$course->title}.");
        }

        // Check capacity
        if ($course->isFull()) {
            if ($request->boolean('allow_waitlist', true)) {
                $pos = $student->joinWaitlist($course, $request->input('notes', 'Auto added to waitlist'));
                return back()->with('success', "Course capacity is full! {$student->name} was placed on the Waitlist at queue position #{$pos}.");
            }
            return back()->with('error', "This course has reached its maximum capacity of {$course->capacity} seats.");
        }

        $student->enroll($course, [
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', "{$student->name} enrolled successfully in {$course->title}.");
    }

    /**
     * Bulk Enrollment
     */
    public function bulkEnroll(Request $request)
    {
        $type = $request->input('bulk_type', 'students_to_course');

        if ($type === 'students_to_course') {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,id',
            ]);

            $course = Course::with('prerequisites')->findOrFail($request->course_id);
            $enrolledCount = 0;
            $waitlistedCount = 0;
            $skipped = [];

            foreach ($request->student_ids as $sid) {
                $student = Student::findOrFail($sid);

                if ($student->isEnrolled($course) || $student->isWaitlisted($course)) {
                    continue;
                }

                if (!$student->meetsPrerequisites($course)) {
                    $missing = $student->missingPrerequisites($course)->pluck('title')->implode(', ');
                    $skipped[] = "{$student->name} (missing: {$missing})";
                    continue;
                }

                if ($course->isFull()) {
                    $student->joinWaitlist($course, 'Bulk enrollment');
                    $waitlistedCount++;
                } else {
                    $student->enroll($course);
                    $enrolledCount++;
                }
            }

            $msg = "Bulk enrollment completed: {$enrolledCount} enrolled, {$waitlistedCount} waitlisted.";
            if (!empty($skipped)) {
                $msg .= " Skipped due to prerequisites: " . implode('; ', $skipped);
            }

            return back()->with('success', $msg);
        } else {
            // One student to multiple courses
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_ids' => 'required|array|min:1',
                'course_ids.*' => 'exists:courses,id',
            ]);

            $student = Student::findOrFail($request->student_id);
            $enrolledCount = 0;
            $waitlistedCount = 0;
            $skipped = [];

            foreach ($request->course_ids as $cid) {
                $course = Course::with('prerequisites')->findOrFail($cid);

                if ($student->isEnrolled($course) || $student->isWaitlisted($course)) {
                    continue;
                }

                if (!$student->meetsPrerequisites($course)) {
                    $missing = $student->missingPrerequisites($course)->pluck('title')->implode(', ');
                    $skipped[] = "{$course->title} (missing: {$missing})";
                    continue;
                }

                if ($course->isFull()) {
                    $student->joinWaitlist($course, 'Bulk enrollment');
                    $waitlistedCount++;
                } else {
                    $student->enroll($course);
                    $enrolledCount++;
                }
            }

            $msg = "Assigned {$enrolledCount} courses to {$student->name} ({$waitlistedCount} waitlisted).";
            if (!empty($skipped)) {
                $msg .= " Prerequisites not met for: " . implode(', ', $skipped);
            }

            return back()->with('success', $msg);
        }
    }

    /**
     * Transfer Student from one course to another
     */
    public function transferCourse(Request $request, Student $student)
    {
        $request->validate([
            'from_course_id' => 'required|exists:courses,id',
            'to_course_id' => 'required|exists:courses,id|different:from_course_id',
        ]);

        $fromCourse = Course::findOrFail($request->from_course_id);
        $toCourse = Course::with('prerequisites')->findOrFail($request->to_course_id);

        if (!$student->meetsPrerequisites($toCourse)) {
            $missing = $student->missingPrerequisites($toCourse)->pluck('title')->implode(', ');
            return back()->with('error', "Transfer failed! {$student->name} does not meet prerequisites for {$toCourse->title}: [{$missing}].");
        }

        DB::transaction(function () use ($student, $fromCourse, $toCourse) {
            // Drop from previous course
            $student->drop($fromCourse);
            $fromCourse->promoteNextWaitlistStudent();

            // Enroll or waitlist in target course
            if ($toCourse->isFull()) {
                $student->joinWaitlist($toCourse, 'Transferred from ' . $fromCourse->title);
            } else {
                $student->enroll($toCourse, [
                    'notes' => 'Transferred from ' . $fromCourse->title,
                ]);
            }
        });

        $msg = "Successfully transferred {$student->name} from {$fromCourse->title} to {$toCourse->title}!";
        if ($toCourse->isFull()) {
            $msg .= " (Placed on Waitlist due to capacity)";
        }

        return back()->with('success', $msg);
    }

    /**
     * Drop Course with Auto-Promotion of Waitlisted Student
     */
    public function drop($studentId, $courseId)
    {
        $student = Student::findOrFail($studentId);
        $course = Course::findOrFail($courseId);

        $wasActive = $student->isEnrolled($course);
        $student->drop($course);

        $msg = "{$student->name} removed from {$course->title}.";

        // Auto-promote next student on waitlist if an active seat was freed
        if ($wasActive) {
            $promotedStudent = $course->promoteNextWaitlistStudent();
            if ($promotedStudent) {
                $msg .= " 🎉 Auto-promoted {$promotedStudent->name} from the waitlist into {$course->title}!";
            }
        }

        return back()->with('success', $msg);
    }

    /**
     * Update Pivot Enrollment Attributes (Grade, Status, Score, Attendance)
     */
    public function updateEnrollment(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'grade' => 'nullable|string|max:5',
            'score' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,completed,dropped,waitlisted',
            'attendance_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validated['status'] === 'completed' && $enrollment->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['waitlist_position'] = null;
        }

        if ($validated['status'] === 'active' && $enrollment->status === 'waitlisted') {
            $validated['waitlist_position'] = null;
        }

        $enrollment->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Enrollment details updated successfully!',
                'enrollment' => $enrollment->fresh(),
            ]);
        }

        return back()->with('success', 'Enrollment details updated successfully!');
    }

    /**
     * Student Academic Transcript (Report Card)
     */
    public function transcript(Student $student)
    {
        $student->load(['courses.prerequisites']);
        $gpa = $student->calculateGpa();
        $totalCredits = $student->totalCredits();

        return view('students.transcript', compact('student', 'gpa', 'totalCredits'));
    }

    /**
     * Manual Waitlist Promotion by Admin
     */
    public function promoteWaitlist(Course $course, Student $student)
    {
        if ($course->isFull()) {
            return back()->with('error', "Cannot promote: Course {$course->title} is currently at max capacity ({$course->capacity}).");
        }

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->where('status', 'waitlisted')
            ->firstOrFail();

        $enrollment->update([
            'status' => 'active',
            'enrolled_at' => now(),
            'waitlist_position' => null,
        ]);

        // Re-number remaining waitlist
        $remaining = Enrollment::where('course_id', $course->id)
            ->where('status', 'waitlisted')
            ->orderBy('waitlist_position')
            ->get();

        $pos = 1;
        foreach ($remaining as $r) {
            $r->update(['waitlist_position' => $pos++]);
        }

        return back()->with('success', "{$student->name} has been promoted to Active Enrollment in {$course->title}!");
    }

    /**
     * Remove from Waitlist
     */
    public function removeFromWaitlist(Course $course, Student $student)
    {
        Enrollment::where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->where('status', 'waitlisted')
            ->delete();

        // Re-number remaining waitlist
        $remaining = Enrollment::where('course_id', $course->id)
            ->where('status', 'waitlisted')
            ->orderBy('waitlist_position')
            ->get();

        $pos = 1;
        foreach ($remaining as $r) {
            $r->update(['waitlist_position' => $pos++]);
        }

        return back()->with('success', "{$student->name} removed from {$course->title} waitlist.");
    }

    /**
     * Create Sample Data
     */
    public function createSampleData()
    {
        $seeder = new \Database\Seeders\StudentCourseSeeder();
        $seeder->run();

        return redirect()
            ->route('students.index')
            ->with('success', 'Comprehensive sample data with prerequisites, waitlists, grades, and analytics created successfully!');
    }

    /**
     * Enrollment History
     */
    public function history(Request $request)
    {
        $query = DB::table('course_student')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->select(
                'course_student.id as enrollment_id',
                'students.id as student_id',
                'students.name as student_name',
                'students.email',
                'courses.id as course_id',
                'courses.title as course_title',
                'courses.description',
                'course_student.enrolled_at',
                'course_student.grade',
                'course_student.score',
                'course_student.status',
                'course_student.attendance_percentage',
                'course_student.waitlist_position'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                    ->orWhere('students.email', 'like', "%{$search}%")
                    ->orWhere('courses.title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('course_student.status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('course_student.enrolled_at', $request->date);
        }

        $enrollments = $query->orderBy('course_student.enrolled_at', 'desc')->paginate(10)->withQueryString();

        $totalEnrollments = DB::table('course_student')->count();
        $todayEnrollments = DB::table('course_student')->whereDate('enrolled_at', now()->toDateString())->count();
        $uniqueStudents = DB::table('course_student')->distinct('student_id')->count('student_id');
        $uniqueCourses = DB::table('course_student')->distinct('course_id')->count('course_id');

        return view('history.index', compact(
            'enrollments',
            'totalEnrollments',
            'todayEnrollments',
            'uniqueStudents',
            'uniqueCourses'
        ));
    }

    /**
     * Export Enrollment History CSV
     */
    public function exportHistory()
    {
        $enrollments = DB::table('course_student')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->select(
                'students.name as student_name',
                'students.email',
                'courses.title as course_title',
                'course_student.status',
                'course_student.grade',
                'course_student.score',
                'course_student.attendance_percentage',
                'course_student.enrolled_at',
                'course_student.completed_at'
            )
            ->orderBy('course_student.enrolled_at', 'desc')
            ->get();

        $response = new StreamedResponse(function () use ($enrollments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student Name',
                'Email',
                'Course',
                'Status',
                'Grade',
                'Score (%)',
                'Attendance (%)',
                'Enrollment Date',
                'Completion Date',
            ]);

            foreach ($enrollments as $item) {
                fputcsv($handle, [
                    $item->student_name,
                    $item->email,
                    $item->course_title,
                    ucfirst($item->status),
                    $item->grade ?? 'N/A',
                    $item->score ?? 'N/A',
                    $item->attendance_percentage ? $item->attendance_percentage . '%' : 'N/A',
                    $item->enrolled_at,
                    $item->completed_at ?? 'N/A',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=enrollment-history.csv');

        return $response;
    }
}
