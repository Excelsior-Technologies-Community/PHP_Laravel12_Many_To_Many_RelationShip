<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'capacity'];

    /**
     * All students mapped to this course via Enrollment pivot
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_student')
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
     * Actively enrolled students
     */
    public function activeStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    /**
     * Completed students
     */
    public function completedStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'completed');
    }

    /**
     * Waitlisted students ordered by queue position
     */
    public function waitlistedStudents(): BelongsToMany
    {
        return $this->students()
            ->wherePivot('status', 'waitlisted')
            ->orderByPivot('waitlist_position', 'asc');
    }

    /**
     * Prerequisites required before taking this course
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_course_id'
        )->withTimestamps();
    }

    /**
     * Courses that require this course as a prerequisite
     */
    public function dependentCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'prerequisite_course_id',
            'course_id'
        )->withTimestamps();
    }

    /**
     * Number of actively enrolled students
     */
    public function enrolledCount(): int
    {
        return $this->activeStudents()->count();
    }

    public function activeCount(): int
    {
        return $this->activeStudents()->count();
    }

    public function waitlistCount(): int
    {
        return $this->waitlistedStudents()->count();
    }

    /**
     * Remaining seats calculation
     */
    public function remainingSeats(): int
    {
        return max(0, $this->capacity - $this->enrolledCount());
    }

    /**
     * Whether course has reached full active capacity
     */
    public function isFull(): bool
    {
        return $this->enrolledCount() >= $this->capacity;
    }

    /**
     * Capacity utilization percentage (0 - 100%)
     */
    public function capacityPercentage(): float
    {
        if ($this->capacity <= 0) {
            return 100.0;
        }

        $percentage = ($this->enrolledCount() / $this->capacity) * 100;
        return round(min(100, $percentage), 1);
    }

    /**
     * Capacity badge color CSS helper
     */
    public function capacityColorClass(): string
    {
        $pct = $this->capacityPercentage();
        if ($pct >= 100) {
            return 'bg-red-500 text-white';
        }
        if ($pct >= 75) {
            return 'bg-amber-500 text-white';
        }
        return 'bg-emerald-500 text-white';
    }

    /**
     * Next waitlist queue number
     */
    public function nextWaitlistPosition(): int
    {
        $max = DB::table('course_student')
            ->where('course_id', $this->id)
            ->where('status', 'waitlisted')
            ->max('waitlist_position');

        return ($max ?? 0) + 1;
    }

    /**
     * Promote the next waitlisted student to active enrollment
     */
    public function promoteNextWaitlistStudent(): ?Student
    {
        // Check if there is capacity to promote
        if ($this->isFull()) {
            return null;
        }

        $nextWaitlisted = $this->waitlistedStudents()->first();
        if (!$nextWaitlisted) {
            return null;
        }

        // Promote student to active
        DB::table('course_student')
            ->where('id', $nextWaitlisted->pivot->id)
            ->update([
                'status' => 'active',
                'enrolled_at' => now(),
                'waitlist_position' => null,
                'updated_at' => now(),
            ]);

        // Re-sequence remaining waitlisted students
        $remainingWaitlist = DB::table('course_student')
            ->where('course_id', $this->id)
            ->where('status', 'waitlisted')
            ->orderBy('waitlist_position', 'asc')
            ->get();

        $pos = 1;
        foreach ($remainingWaitlist as $item) {
            DB::table('course_student')
                ->where('id', $item->id)
                ->update(['waitlist_position' => $pos++]);
        }

        return $nextWaitlisted;
    }
}
