<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'capacity'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    // Get enrolled students count
    public function enrolledCount(): int
    {
        return $this->students()->count();
    }

    /**
     * Current enrolled students.
     */
    public function enrolledStudentsCount()
    {
        return $this->students()->count();
    }

    /**
     * Remaining seats.
     */
    public function remainingSeats()
    {
        return max(0, $this->capacity - $this->students()->count());
    }

    /**
     * Check whether course is full.
     */
    public function isFull()
    {
        return $this->students()->count() >= $this->capacity;
    }

    public function enrollmentHistory()
    {
        return $this->belongsToMany(
            Student::class
        )
            ->withPivot(
                'enrolled_at'
            )
            ->withTimestamps();
    }
}
