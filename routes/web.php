<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// Main navigation
Route::get('/', [StudentController::class, 'index'])->name('students.index');
Route::get('/courses', [StudentController::class, 'courses'])->name('courses.index');
Route::get('/analytics', [StudentController::class, 'analytics'])->name('analytics.index');
Route::get('/matrix', [StudentController::class, 'matrix'])->name('matrix.index');
Route::post('/matrix/toggle', [StudentController::class, 'toggleMatrix'])->name('matrix.toggle');

// Sample Data
Route::get('/create-sample-data', [StudentController::class, 'createSampleData'])->name('create.sample');

// Enrollment Operations
Route::post('/enroll', [StudentController::class, 'enroll'])->name('student.enroll');
Route::post('/bulk-enroll', [StudentController::class, 'bulkEnroll'])->name('student.bulkEnroll');
Route::delete('/drop/{student}/{course}', [StudentController::class, 'drop'])->name('student.drop');
Route::post('/students/{student}/transfer', [StudentController::class, 'transferCourse'])->name('student.transfer');

// Rich Pivot & Grade Tracker
Route::post('/enrollments/{enrollment}/update', [StudentController::class, 'updateEnrollment'])->name('enrollments.update');
Route::get('/students/{student}/transcript', [StudentController::class, 'transcript'])->name('students.transcript');

// Waitlist Queue Operations
Route::post('/courses/{course}/waitlist/{student}/promote', [StudentController::class, 'promoteWaitlist'])->name('waitlist.promote');
Route::delete('/courses/{course}/waitlist/{student}/remove', [StudentController::class, 'removeFromWaitlist'])->name('waitlist.remove');

// History & Export
Route::get('/enrollment-history', [StudentController::class, 'history'])->name('enrollment.history');
Route::get('/enrollment-history/export', [StudentController::class, 'exportHistory'])->name('enrollment.export');