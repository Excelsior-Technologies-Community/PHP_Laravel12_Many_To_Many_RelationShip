<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Pivot
{
    protected $table = 'course_student';

    public $incrementing = true;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrolled_at',
        'grade',
        'score',
        'status',
        'attendance_percentage',
        'completed_at',
        'waitlist_position',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'float',
        'attendance_percentage' => 'float',
        'waitlist_position' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isWaitlisted(): bool
    {
        return $this->status === 'waitlisted';
    }

    public function isDropped(): bool
    {
        return $this->status === 'dropped';
    }

    /**
     * Convert letter grade to GPA 4.0 scale point
     */
    public function gpaPoint(): ?float
    {
        if (!$this->grade) {
            return null;
        }

        return match (strtoupper(trim($this->grade))) {
            'A+' => 4.0,
            'A'  => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B'  => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C'  => 2.0,
            'D'  => 1.0,
            'F'  => 0.0,
            default => null,
        };
    }

    /**
     * UI Status Badge classes
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'completed'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'waitlisted' => 'bg-amber-100 text-amber-800 border-amber-200',
            'dropped'    => 'bg-rose-100 text-rose-800 border-rose-200',
            default      => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    /**
     * Grade Badge classes
     */
    public function gradeBadgeClass(): string
    {
        if (!$this->grade) {
            return 'bg-gray-100 text-gray-500 border-gray-200';
        }

        $letter = strtoupper(substr(trim($this->grade), 0, 1));
        return match ($letter) {
            'A' => 'bg-emerald-500 text-white shadow-sm',
            'B' => 'bg-blue-500 text-white shadow-sm',
            'C' => 'bg-amber-500 text-white shadow-sm',
            'D' => 'bg-orange-500 text-white shadow-sm',
            'F' => 'bg-red-500 text-white shadow-sm',
            default => 'bg-gray-500 text-white shadow-sm',
        };
    }
}
