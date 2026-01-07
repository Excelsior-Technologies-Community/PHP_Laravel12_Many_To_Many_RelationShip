<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = ['name', 'email'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
                    ->withPivot('enrolled_at')
                    ->withTimestamps();
    }

    // Enroll in a course
    public function enroll(Course $course): void
    {
        $this->courses()->syncWithoutDetaching($course);
    }

    // Drop a course
    public function drop(Course $course): void
    {
        $this->courses()->detach($course);
    }

    // Check if enrolled in a course
    public function isEnrolled(Course $course): bool
    {
        return $this->courses()->where('course_id', $course->id)->exists();
    }
}