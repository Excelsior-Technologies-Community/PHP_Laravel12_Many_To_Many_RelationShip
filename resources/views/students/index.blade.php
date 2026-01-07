@extends('layouts.app')

@section('title', 'Students & Courses')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Students List -->
    <div class="lg:col-span-2">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-users mr-2"></i>Students & Their Courses
                </h2>
                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                    {{ $students->count() }} Students
                </span>
            </div>

            <div class="space-y-6">
                @forelse($students as $student)
                <div class="bg-white rounded-xl p-5 shadow-lg hover-glow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">
                                <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                                {{ $student->name }}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-envelope mr-1"></i> {{ $student->email }}
                            </p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                            {{ $student->courses->count() }} Courses
                        </span>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-book-open text-purple-500 mr-2"></i> Enrolled Courses:
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($student->courses as $course)
                            <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2">
                                <span class="text-gray-800">{{ $course->title }}</span>
                                <form action="{{ route('student.drop', [$student->id, $course->id]) }}" 
                                      method="POST" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 text-sm"
                                            onclick="return confirm('Drop {{ $student->name }} from {{ $course->title }}?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No courses enrolled</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users-slash text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No students found. Generate sample data first!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Courses & Enrollment -->
    <div class="space-y-8">
        <!-- Courses Summary -->
        <div class="glass-card rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-book mr-2"></i>Available Courses
            </h2>
            
            <div class="space-y-4">
                @foreach($courses as $course)
                <div class="bg-white/90 rounded-lg p-4">
                    <h4 class="font-bold text-gray-800">{{ $course->title }}</h4>
                    <p class="text-gray-600 text-sm mt-1">{{ $course->description }}</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-users mr-1"></i> {{ $course->students_count }} enrolled
                        </span>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            ID: {{ $course->id }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Enrollment Form -->
        <div class="glass-card rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-user-plus mr-2"></i>Enroll Student
            </h2>
            
            <form action="{{ route('student.enroll') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-white mb-2">Select Student</label>
                        <select name="student_id" required 
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Choose a student...</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white mb-2">Select Course</label>
                        <select name="course_id" required 
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Choose a course...</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-teal-500 text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-user-graduate mr-2"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Card -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Statistics
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/20 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-white">{{ $students->count() }}</div>
                    <div class="text-white/80 text-sm">Total Students</div>
                </div>
                <div class="bg-white/20 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-white">{{ $courses->count() }}</div>
                    <div class="text-white/80 text-sm">Total Courses</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection