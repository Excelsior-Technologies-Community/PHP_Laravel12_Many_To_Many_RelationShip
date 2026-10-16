<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Student extends Model
{
    protected $fillable = ['name', 'email'];

    /**
     * All enrolled/associated courses via Enrollment pivot
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->using(Enrollment::class)
            ->withPivot([
                'id',
                'enrolled_at',
                'grade',
                'score',
                'status',
                'attendance_percentage',
                'completed_at',
                'waitlist_position',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Actively enrolled courses
     */
    public function activeCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('status', 'active');
    }

    /**
     * Completed courses
     */
    public function completedCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('status', 'completed');
    }

    /**
     * Waitlisted courses
     */
    public function waitlistedCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('status', 'waitlisted');
    }

    /**
     * Enroll student into course with custom attributes
     */
    public function enroll(Course $course, array $attributes = []): void
    {
        $pivotData = array_merge([
            'enrolled_at' => now(),
            'status' => 'active',
            'attendance_percentage' => 100.00,
        ], $attributes);

        $this->courses()->syncWithoutDetaching([
            $course->id => $pivotData
        ]);
    }

    /**
     * Add student to course waitlist
     */
    public function joinWaitlist(Course $course, ?string $notes = null): int
    {
        $nextPosition = $course->nextWaitlistPosition();

        $this->courses()->syncWithoutDetaching([
            $course->id => [
                'enrolled_at' => now(),
                'status' => 'waitlisted',
                'waitlist_position' => $nextPosition,
                'notes' => $notes,
            ]
        ]);

        return $nextPosition;
    }

    /**
     * Drop a course
     */
    public function drop(Course $course): void
    {
        $this->courses()->detach($course);
    }

    /**
     * Check if currently actively enrolled in a course
     */
    public function isEnrolled(Course $course): bool
    {
        return $this->courses()
            ->where('courses.id', $course->id)
            ->wherePivot('status', 'active')
            ->exists();
    }

    /**
     * Check if student is on waitlist for a course
     */
    public function isWaitlisted(Course $course): bool
    {
        return $this->courses()
            ->where('courses.id', $course->id)
            ->wherePivot('status', 'waitlisted')
            ->exists();
    }

    /**
     * Check if student has completed a course
     */
    public function hasCompletedCourse(Course $course): bool
    {
        return $this->courses()
            ->where('courses.id', $course->id)
            ->wherePivot('status', 'completed')
            ->exists();
    }

    /**
     * Check if student meets all prerequisites for a given course
     */
    public function meetsPrerequisites(Course $course): bool
    {
        $missing = $this->missingPrerequisites($course);
        return $missing->isEmpty();
    }

    /**
     * Returns list of prerequisite courses not yet completed by this student
     */
    public function missingPrerequisites(Course $course): Collection
    {
        $prerequisites = $course->prerequisites;
        if ($prerequisites->isEmpty()) {
            return collect();
        }

        $completedCourseIds = $this->completedCourses()->pluck('courses.id')->toArray();

        return $prerequisites->filter(function (Course $req) use ($completedCourseIds) {
            return !in_array($req->id, $completedCourseIds);
        });
    }

    /**
     * Calculate cumulative GPA on 4.0 scale for completed courses with grades
     */
    public function calculateGpa(): ?float
    {
        $gradedCourses = $this->courses()
            ->wherePivotNotNull('grade')
            ->get();

        if ($gradedCourses->isEmpty()) {
            return null;
        }

        $totalPoints = 0;
        $count = 0;

        foreach ($gradedCourses as $course) {
            $point = $course->pivot->gpaPoint();
            if ($point !== null) {
                $totalPoints += $point;
                $count++;
            }
        }

        return $count > 0 ? round($totalPoints / $count, 2) : null;
    }

    /**
     * Calculate total completed credits (e.g., 3 credits per course)
     */
    public function totalCredits(): int
    {
        return $this->completedCourses()->count() * 3;
    }
}
