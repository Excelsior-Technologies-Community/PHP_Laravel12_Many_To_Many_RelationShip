@extends('layouts.app')

@section('title', 'Students & Courses')

@section('content')

<!-- =========================
    Dashboard Statistics
========================== -->

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">

    <div class="glass-card rounded-2xl p-6 text-center">

        <div class="text-4xl text-white mb-3">
            <i class="fas fa-user-graduate"></i>
        </div>

        <h2 class="text-4xl font-bold text-white">
            {{ $totalStudents }}
        </h2>

        <p class="text-white mt-2">
            Total Students
        </p>

    </div>

    <div class="glass-card rounded-2xl p-6 text-center">

        <div class="text-4xl text-white mb-3">
            <i class="fas fa-book"></i>
        </div>

        <h2 class="text-4xl font-bold text-white">
            {{ $totalCourses }}
        </h2>

        <p class="text-white mt-2">
            Total Courses
        </p>

    </div>

    <div class="glass-card rounded-2xl p-6 text-center">

        <div class="text-4xl text-white mb-3">
            <i class="fas fa-link"></i>
        </div>

        <h2 class="text-4xl font-bold text-white">
            {{ $totalEnrollments }}
        </h2>

        <p class="text-white mt-2">
            Total Enrollments
        </p>

    </div>

    <div class="glass-card rounded-2xl p-6 text-center">

        <div class="text-4xl text-white mb-3">
            <i class="fas fa-user-times"></i>
        </div>

        <h2 class="text-4xl font-bold text-white">
            {{ $studentsWithoutCourse }}
        </h2>

        <p class="text-white mt-2">
            Students Without Course
        </p>

    </div>

    <div class="glass-card rounded-2xl p-6 text-center">

        <div class="text-4xl text-white mb-3">

            <i class="fas fa-lock"></i>

        </div>


        <h2 class="text-4xl font-bold text-white">

            {{ $fullCourses }}

        </h2>


        <p class="text-white mt-2">

            Full Courses

        </p>

    </div>

</div>

<!-- =========================
      Search + Filter
========================== -->

<div class="glass-card rounded-2xl p-6 mb-8">

    <form method="GET"
        action="{{ route('students.index') }}">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search student name or email..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500">

            </div>

            <div>

                <select
                    name="course"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3">

                    <option value="">All Courses</option>

                    @foreach($courses as $course)

                    <option
                        value="{{ $course->id }}"
                        {{ request('course') == $course->id ? 'selected' : '' }}>

                        {{ $course->title }}

                    </option>

                    @endforeach

                </select>

            </div>

            <div>

                <button
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-3">

                    <i class="fas fa-search mr-2"></i>

                    Search

                </button>

            </div>

            <div>

                <a href="{{ route('students.index') }}"
                    class="block w-full text-center bg-gray-500 hover:bg-gray-600 text-white rounded-lg py-3">

                    <i class="fas fa-sync-alt mr-2"></i>

                    Reset

                </a>

            </div>

        </div>

    </form>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- ==========================
        Student List
=========================== -->

    <div class="lg:col-span-2">

        <div class="glass-card rounded-2xl p-6">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-2xl font-bold text-white">

                    <i class="fas fa-users mr-2"></i>

                    Students

                </h2>

                <span
                    class="bg-blue-500 px-4 py-2 rounded-full text-white">

                    {{ $students->total() }}

                    Students

                </span>

            </div>

            <div class="space-y-6">

                @forelse($students as $student)

                <div class="bg-white rounded-xl shadow-lg p-5 hover-glow">

                    <div class="flex justify-between items-start">

                        <div>

                            <h3 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                                {{ $student->name }}
                            </h3>

                            <p class="text-gray-500 mt-2">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $student->email }}
                            </p>

                        </div>

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                            {{ $student->courses->count() }} Courses
                        </span>

                    </div>

                    <hr class="my-4">

                    <h4 class="font-semibold text-gray-700 mb-3">
                        <i class="fas fa-book-open text-purple-500 mr-2"></i>
                        Enrolled Courses
                    </h4>

                    @if($student->courses->count())

                    <div class="flex flex-wrap gap-2">

                        @foreach($student->courses as $course)

                        <div class="flex items-center bg-gray-100 rounded-lg px-3 py-2">

                            <span class="text-gray-700">
                                {{ $course->title }}
                            </span>

                            <form
                                action="{{ route('student.drop', [$student->id, $course->id]) }}"
                                method="POST"
                                class="ml-2">

                                @csrf
                                @method('DELETE')

                                <button
                                    onclick="return confirm('Are you sure?')"
                                    class="text-red-500 hover:text-red-700">

                                    <i class="fas fa-times-circle"></i>

                                </button>

                            </form>

                        </div>

                        @endforeach

                    </div>

                    @else

                    <div class="bg-yellow-100 text-yellow-800 rounded-lg p-3">

                        This student has not enrolled in any course.

                    </div>

                    @endif

                </div>

                @empty

                <div class="bg-white rounded-xl p-8 text-center shadow">

                    <i class="fas fa-users-slash text-6xl text-gray-400 mb-4"></i>

                    <h3 class="text-2xl font-bold text-gray-700">
                        No Students Found
                    </h3>

                    <p class="text-gray-500 mt-2">
                        Try another search or create sample data.
                    </p>

                </div>

                @endforelse

            </div>

            @if($students->hasPages())

            <div class="mt-8">

                {{ $students->links() }}

            </div>

            @endif

        </div>

    </div>

    <!-- Right Sidebar -->

    <div class="space-y-8">

        <!-- Available Courses -->

        <div class="glass-card rounded-2xl p-6">

            <h2 class="text-2xl font-bold text-white mb-6">

                <i class="fas fa-book mr-2"></i>

                Available Courses

            </h2>

            <div class="space-y-4">

                @foreach($courses as $course)

                <div class="bg-white rounded-lg p-4 shadow hover:shadow-lg transition">


                    <h4 class="font-bold text-gray-800">

                        {{ $course->title }}

                    </h4>


                    <p class="text-gray-600 text-sm mt-2">

                        {{ $course->description }}

                    </p>



                    <div class="mt-4 space-y-3">


                        <div class="flex justify-between">

                            <span class="text-gray-600">

                                <i class="fas fa-chair mr-1 text-blue-500"></i>

                                Capacity

                            </span>


                            <span class="font-semibold">

                                {{ $course->capacity }}

                            </span>

                        </div>


                        
                        <div class="flex justify-between">

                            <span class="text-gray-600">

                                <i class="fas fa-users mr-1 text-green-500"></i>

                                Enrolled

                            </span>


                            <span class="font-semibold">

                                {{ $course->students_count }}

                            </span>

                        </div>



                        <div class="flex justify-between">

                            <span class="text-gray-600">

                                <i class="fas fa-user-plus mr-1 text-purple-500"></i>

                                Remaining

                            </span>


                            <span class="font-semibold">

                                {{ $course->remainingSeats() }}

                            </span>

                        </div>


                    </div>


                    @if($course->isFull())


                    <div class="mt-4">

                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

                            <i class="fas fa-lock mr-1"></i>

                            Course Full

                        </span>

                    </div>


                    @else


                    <div class="mt-4">

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

                            <i class="fas fa-check-circle mr-1"></i>

                            Seats Available

                        </span>

                    </div>


                    @endif



                </div>

                @endforeach

            </div>

        </div>

        <!-- Enroll Student -->

        <div class="glass-card rounded-2xl p-6">

            <h2 class="text-2xl font-bold text-white mb-6">

                <i class="fas fa-user-plus mr-2"></i>

                Enroll Student

            </h2>

            <form action="{{ route('student.enroll') }}" method="POST">

                @csrf

                <div class="space-y-4">

                    <div>

                        <label class="block text-white mb-2">

                            Student

                        </label>

                        <select
                            name="student_id"
                            class="w-full rounded-lg px-4 py-2 border"
                            required>

                            <option value="">Select Student</option>

                            @foreach($students as $student)

                            <option value="{{ $student->id }}">

                                {{ $student->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label class="block text-white mb-2">

                            Course

                        </label>

                        <select
                            name="course_id"
                            class="w-full rounded-lg px-4 py-2 border"
                            required>

                            <option value="">Select Course</option>

                            @foreach($courses as $course)

                            <option
                                value="{{ $course->id }}"
                                {{ $course->isFull() ? 'disabled' : '' }}>

                                {{ $course->title }}

                                {{ $course->isFull() ? '(Course Full)' : '' }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="space-y-3">

                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg">

                            <i class="fas fa-user-graduate mr-2"></i>

                            Enroll Student

                        </button>

                        <a href="{{ route('enrollment.history') }}"
                            class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg">

                            <i class="fas fa-history mr-2"></i>

                            View Enrollment History

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection