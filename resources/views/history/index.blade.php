@extends('layouts.app')

@section('title', 'Enrollment History')


@section('content')


<div class="space-y-8">


    <!-- ==========================
        Statistics Cards
    =========================== -->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">


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
                <i class="fas fa-calendar-day"></i>
            </div>

            <h2 class="text-4xl font-bold text-white">
                {{ $todayEnrollments }}
            </h2>

            <p class="text-white mt-2">
                Today's Enrollment
            </p>

        </div>


        <div class="glass-card rounded-2xl p-6 text-center">

            <div class="text-4xl text-white mb-3">
                <i class="fas fa-user-graduate"></i>
            </div>

            <h2 class="text-4xl font-bold text-white">
                {{ $uniqueStudents }}
            </h2>

            <p class="text-white mt-2">
                Active Students
            </p>

        </div>


        <div class="glass-card rounded-2xl p-6 text-center">

            <div class="text-4xl text-white mb-3">
                <i class="fas fa-book"></i>
            </div>

            <h2 class="text-4xl font-bold text-white">
                {{ $uniqueCourses }}
            </h2>

            <p class="text-white mt-2">
                Active Courses
            </p>

        </div>


    </div>


    <!-- ==========================
        Search & Filter
    =========================== -->


    <div class="glass-card rounded-2xl p-6">


        <form method="GET"
            action="{{ route('enrollment.history') }}">


            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search student or course..."
                        class="w-full rounded-lg border px-4 py-3">


                </div>


                <div>

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full rounded-lg border px-4 py-3">

                </div>


                <div>

                    <button
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-3">

                        <i class="fas fa-search mr-2"></i>

                        Search

                    </button>

                </div>


                <div>

                    <a href="{{ route('enrollment.history') }}"
                        class="block text-center w-full bg-gray-600 hover:bg-gray-700 text-white rounded-lg py-3">


                        <i class="fas fa-sync mr-2"></i>

                        Reset


                    </a>

                </div>


            </div>


        </form>


    </div>


    <!-- ==========================
        History Table
    =========================== -->


    <div class="glass-card rounded-2xl p-6">


        <div class="flex justify-between items-center mb-6">

            <h2 class="text-2xl font-bold text-white">

                <i class="fas fa-history mr-2"></i>

                Enrollment History

            </h2>

            <div class="flex items-center gap-3">

                <span class="bg-blue-500 text-white px-4 py-2 rounded-full">

                    {{ $enrollments->total() }} Records

                </span>

                <a href="{{ route('enrollment.export') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">

                    <i class="fas fa-file-csv mr-2"></i>

                    Export CSV

                </a>

            </div>

        </div>


        <div class="overflow-x-auto">


            <table class="w-full bg-white rounded-xl overflow-hidden">


                <thead class="bg-gray-100">


                    <tr>


                        <th class="px-6 py-4 text-left">
                            Student
                        </th>


                        <th class="px-6 py-4 text-left">
                            Email
                        </th>


                        <th class="px-6 py-4 text-left">
                            Course
                        </th>


                        <th class="px-6 py-4 text-left">
                            Enrolled Date
                        </th>


                    </tr>


                </thead>


                <tbody>


                    @forelse($enrollments as $item)


                    <tr class="border-b hover:bg-gray-50">


                        <td class="px-6 py-4">


                            <div class="font-semibold text-gray-800">

                                <i class="fas fa-user-graduate text-blue-500 mr-2"></i>

                                {{ $item->student_name }}

                            </div>


                        </td>


                        <td class="px-6 py-4 text-gray-600">


                            {{ $item->email }}


                        </td>


                        <td class="px-6 py-4">


                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">


                                <i class="fas fa-book mr-1"></i>

                                {{ $item->course_title }}


                            </span>


                        </td>


                        <td class="px-6 py-4 text-gray-600">


                            @if($item->enrolled_at)

                            {{ \Carbon\Carbon::parse($item->enrolled_at)->format('d M Y') }}

                            @else

                            N/A

                            @endif



                        </td>


                    </tr>


                    @empty


                    <tr>

                        <td colspan="4"
                            class="text-center py-10">


                            <i class="fas fa-history text-5xl text-gray-400"></i>


                            <h3 class="text-xl font-bold text-gray-700 mt-4">

                                No Enrollment Found

                            </h3>


                            <p class="text-gray-500">

                                Try changing your search filter.

                            </p>


                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Pagination -->

        @if($enrollments->hasPages())


        <div class="mt-8">


            {{ $enrollments->links() }}


        </div>


        @endif

    </div>

</div>


@endsection