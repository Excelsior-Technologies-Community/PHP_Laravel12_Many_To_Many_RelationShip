<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;

class ManyToManyRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_pivot_enrollment_with_grades_and_gpa_calculation(): void
    {
        $student = Student::create(['name' => 'Test Student', 'email' => 'test@example.com']);
        $course1 = Course::create(['title' => 'Course 1', 'description' => 'Desc 1', 'capacity' => 10]);
        $course2 = Course::create(['title' => 'Course 2', 'description' => 'Desc 2', 'capacity' => 10]);

        $student->enroll($course1, [
            'status' => 'completed',
            'grade' => 'A+',
            'score' => 98.0,
            'completed_at' => now(),
        ]);

        $student->enroll($course2, [
            'status' => 'completed',
            'grade' => 'B',
            'score' => 84.0,
            'completed_at' => now(),
        ]);

        $this->assertEquals(2, $student->courses()->count());
        $this->assertEquals(2, $student->completedCourses()->count());

        // A+ = 4.0, B = 3.0 -> Average GPA = 3.5
        $this->assertEquals(3.5, $student->calculateGpa());
        $this->assertEquals(6, $student->totalCredits());

        // Test updating enrollment via route
        $enrollment = Enrollment::where('student_id', $student->id)->where('course_id', $course2->id)->first();
        $response = $this->post(route('enrollments.update', $enrollment->id), [
            'grade' => 'A',
            'score' => 92.0,
            'status' => 'completed',
            'attendance_percentage' => 95.0,
            'notes' => 'Grade revised',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('A', $enrollment->fresh()->grade);
        $this->assertEquals(4.0, $student->calculateGpa()); // (A+ = 4.0, A = 4.0) -> Average GPA = 4.0
    }

    public function test_course_prerequisites_blocks_unqualified_student_and_allows_eligible_student(): void
    {
        $phpCourse = Course::create(['title' => 'PHP Fundamentals', 'description' => 'Basics', 'capacity' => 10]);
        $laravelCourse = Course::create(['title' => 'Laravel Advanced', 'description' => 'Advanced', 'capacity' => 10]);

        // Set PHP as prerequisite for Laravel
        $laravelCourse->prerequisites()->attach($phpCourse->id);
        $this->assertTrue($laravelCourse->prerequisites->contains($phpCourse->id));

        $student = Student::create(['name' => 'Alice Green', 'email' => 'alice@example.com']);

        // Student tries to enroll in Laravel without completing PHP -> Must fail
        $response = $this->post(route('student.enroll'), [
            'student_id' => $student->id,
            'course_id' => $laravelCourse->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertFalse($student->isEnrolled($laravelCourse));

        // Now student completes PHP
        $student->enroll($phpCourse, [
            'status' => 'completed',
            'grade' => 'A',
            'completed_at' => now(),
        ]);
        $this->assertTrue($student->hasCompletedCourse($phpCourse));
        $this->assertTrue($student->meetsPrerequisites($laravelCourse));

        // Now student can enroll in Laravel
        $response2 = $this->post(route('student.enroll'), [
            'student_id' => $student->id,
            'course_id' => $laravelCourse->id,
        ]);

        $response2->assertSessionHas('success');
        $this->assertTrue($student->isEnrolled($laravelCourse));
    }

    public function test_course_capacity_waitlist_and_auto_promotion_on_drop(): void
    {
        $course = Course::create(['title' => 'Tight Capacity Course', 'description' => 'Cap 1', 'capacity' => 1]);
        $student1 = Student::create(['name' => 'Student One', 'email' => 's1@example.com']);
        $student2 = Student::create(['name' => 'Student Two', 'email' => 's2@example.com']);

        // Student 1 enrolls and fills capacity
        $student1->enroll($course);
        $this->assertTrue($course->isFull());

        // Student 2 tries to enroll -> placed on waitlist
        $response = $this->post(route('student.enroll'), [
            'student_id' => $student2->id,
            'course_id' => $course->id,
            'allow_waitlist' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue($student2->isWaitlisted($course));
        $this->assertEquals(1, $course->waitlistCount());

        // Student 1 drops -> Student 2 should be automatically promoted to active!
        $dropResponse = $this->delete(route('student.drop', [$student1->id, $course->id]));
        $dropResponse->assertSessionHas('success');

        $this->assertFalse($student1->isEnrolled($course));
        $this->assertTrue($student2->isEnrolled($course));
        $this->assertFalse($student2->isWaitlisted($course));
        $this->assertEquals(0, $course->waitlistCount());
    }

    public function test_interactive_matrix_toggle_via_ajax(): void
    {
        $student = Student::create(['name' => 'Matrix Tester', 'email' => 'matrix@example.com']);
        $course = Course::create(['title' => 'Matrix 101', 'description' => 'Desc', 'capacity' => 5]);

        // Toggle on (Enroll)
        $response = $this->postJson(route('matrix.toggle'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'enrolled',
                'status' => 'active',
            ]);
        $this->assertTrue($student->isEnrolled($course));

        // Toggle off (Drop)
        $response2 = $this->postJson(route('matrix.toggle'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'detached',
            ]);
        $this->assertFalse($student->isEnrolled($course));
    }

    public function test_bulk_enrollment_and_course_transfer(): void
    {
        $s1 = Student::create(['name' => 'Bulk Student 1', 'email' => 'b1@example.com']);
        $s2 = Student::create(['name' => 'Bulk Student 2', 'email' => 'b2@example.com']);
        $courseA = Course::create(['title' => 'Course Alpha', 'description' => 'A', 'capacity' => 5]);
        $courseB = Course::create(['title' => 'Course Beta', 'description' => 'B', 'capacity' => 5]);

        // Bulk enroll both into Course Alpha
        $response = $this->post(route('student.bulkEnroll'), [
            'bulk_type' => 'students_to_course',
            'course_id' => $courseA->id,
            'student_ids' => [$s1->id, $s2->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue($s1->isEnrolled($courseA));
        $this->assertTrue($s2->isEnrolled($courseA));

        // Transfer s1 from Alpha to Beta
        $transferResponse = $this->post(route('student.transfer', $s1->id), [
            'from_course_id' => $courseA->id,
            'to_course_id' => $courseB->id,
        ]);

        $transferResponse->assertSessionHas('success');
        $this->assertFalse($s1->isEnrolled($courseA));
        $this->assertTrue($s1->isEnrolled($courseB));
    }

    public function test_analytics_matrix_and_transcript_pages_load_successfully(): void
    {
        $student = Student::create(['name' => 'Eva Stone', 'email' => 'eva@example.com']);
        $course = Course::create(['title' => 'Modern PHP', 'description' => 'PHP 8.4', 'capacity' => 10]);
        $student->enroll($course, ['status' => 'completed', 'grade' => 'A', 'score' => 95.0]);

        $this->get(route('students.index'))->assertStatus(200);
        $this->get(route('courses.index'))->assertStatus(200);
        $this->get(route('matrix.index'))->assertStatus(200);
        $this->get(route('analytics.index'))->assertStatus(200);
        $this->get(route('enrollment.history'))->assertStatus(200);
        $this->get(route('enrollment.export'))->assertStatus(200);
        $this->get(route('students.transcript', $student->id))->assertStatus(200);
    }
}
