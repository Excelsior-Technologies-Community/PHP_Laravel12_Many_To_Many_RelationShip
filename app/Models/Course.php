<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = ['title', 'description'];

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
}