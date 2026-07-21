@extends('layouts.app')

@section('title', 'Courses & Students')

@section('content')

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="glass-card rounded-2xl p-6 text-center">
        <div class="text-4xl text-white mb-2">
            <i class="fas fa-book"></i>
        </div>
        <div class="text-4xl font-bold text-white">
            {{ $totalCourses }}
        </div>
        <div class="text-white mt-2">
            Total Courses
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 text-center">
        <div class="text-4xl text-white mb-2">
            <i class="fas fa-users"></i>
        </div>
        <div class="text-4xl font-bold text-white">
            {{ $totalStudents }}
        </div>
        <div class="text-white mt-2">
            Total Students
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 text-center">
        <div class="text-4xl text-white mb-2">
            <i class="fas fa-link"></i>
        </div>
        <div class="text-4xl font-bold text-white">
            {{ $totalEnrollments }}
        </div>
        <div class="text-white mt-2">
            Total Enrollments
        </div>
    </div>

</div>

<!-- Search -->
<div class="glass-card rounded-2xl p-6 mb-8">

    <form method="GET" action="{{ route('courses.index') }}">

        <div class="flex gap-4">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search course title or description..."
                class="flex-1 rounded-lg border px-4 py-3">

            <button class="bg-blue-600 text-white px-6 rounded-lg">
                <i class="fas fa-search mr-2"></i>
                Search
            </button>

            <a href="{{ route('courses.index') }}"
                class="bg-gray-600 text-white px-6 rounded-lg flex items-center">
                Reset
            </a>

        </div>

    </form>

</div>

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

        <div class="bg-white rounded-xl shadow-lg hover-glow overflow-hidden">

            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-5">

                <div class="flex justify-between items-center">

                    <h3 class="text-xl font-bold text-white">

                        <i class="fas fa-book-open mr-2"></i>

                        {{ $course->title }}

                    </h3>

                    <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-xs font-bold">

                        ID : {{ $course->id }}

                    </span>

                </div>

            </div>

            <div class="p-5">

                <p class="text-gray-600 mb-5">

                    {{ $course->description }}

                </p>

                <div class="grid grid-cols-2 gap-4 mb-5">

                    <div class="bg-blue-50 rounded-lg p-4 text-center">

                        <div class="text-2xl font-bold text-blue-700">

                            {{ $course->students->count() }}

                        </div>

                        <div class="text-gray-600 text-sm">

                            Students

                        </div>

                    </div>

                    <div class="bg-green-50 rounded-lg p-4 text-center">

                        <div class="text-2xl font-bold text-green-700">

                            {{ $course->id }}

                        </div>

                        <div class="text-gray-600 text-sm">

                            Course ID

                        </div>

                    </div>

                </div>

                <h4 class="font-semibold text-gray-700 mb-3">

                    <i class="fas fa-users text-green-500 mr-2"></i>

                    Enrolled Students

                </h4>

                @if($course->students->count())

                <div class="space-y-2 max-h-48 overflow-y-auto">

                    @foreach($course->students as $student)

                    <div class="flex justify-between items-center bg-gray-100 rounded-lg px-3 py-2">

                        <div>

                            <div class="font-semibold text-gray-700">

                                {{ $student->name }}

                            </div>

                            <div class="text-xs text-gray-500">

                                {{ $student->email }}

                            </div>

                        </div>

                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">

                            Student

                        </span>

                    </div>

                    @endforeach

                </div>

                @else

                <div class="bg-yellow-100 text-yellow-700 rounded-lg p-3 text-center">

                    No students enrolled yet.

                </div>

                @endif

            </div>

        </div>
        @empty

        <div class="col-span-3">

            <div class="bg-white rounded-xl shadow-lg p-10 text-center">

                <i class="fas fa-book-open text-6xl text-gray-400 mb-4"></i>

                <h2 class="text-2xl font-bold text-gray-700 mb-2">

                    No Courses Found

                </h2>

                <p class="text-gray-500">

                    No course matches your search.

                </p>

                <a href="{{ route('courses.index') }}"
                    class="inline-block mt-5 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                    <i class="fas fa-sync-alt mr-2"></i>

                    Reset Search

                </a>

            </div>

        </div>

        @endforelse

    </div>

    @if($courses->hasPages())

    <div class="mt-10 flex justify-center">

        {{ $courses->links() }}

    </div>

    @endif

</div>

@endsection