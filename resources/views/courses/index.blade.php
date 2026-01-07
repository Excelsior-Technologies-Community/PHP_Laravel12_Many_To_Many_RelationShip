@extends('layouts.app')

@section('title', 'Courses & Students')

@section('content')
<div class="glass-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-white">
            <i class="fas fa-book mr-2"></i>Courses & Enrolled Students
        </h2>
        <span class="bg-purple-500 text-white px-4 py-2 rounded-full">
            {{ $courses->count() }} Courses Available
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
        <div class="bg-white rounded-xl p-5 shadow-lg hover-glow">
            <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-800">{{ $course->title }}</h3>
                <p class="text-gray-600 mt-2 text-sm">{{ $course->description }}</p>
                
                <div class="flex items-center justify-between mt-4">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-users mr-1"></i> {{ $course->students->count() }} Students
                    </span>
                    <span class="text-gray-500 text-sm">
                        ID: {{ $course->id }}
                    </span>
                </div>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-user-graduate text-green-500 mr-2"></i> Enrolled Students:
                </h4>
                
                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                    @forelse($course->students as $student)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                            <span class="text-gray-800">{{ $student->name }}</span>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $student->email }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm text-center py-2">No students enrolled yet</p>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-3 text-center py-12">
            <i class="fas fa-book-open text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl text-gray-600 mb-2">No Courses Available</h3>
            <p class="text-gray-500">Generate sample data to get started!</p>
            <a href="{{ route('create.sample') }}" 
               class="inline-block mt-4 px-6 py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg font-semibold hover:opacity-90 transition">
                <i class="fas fa-magic mr-2"></i> Generate Sample Data
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection