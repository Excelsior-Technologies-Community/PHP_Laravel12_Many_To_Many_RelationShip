@extends('layouts.app')

@section('title', 'Enrollment History & Audit Log')

@section('content')

<div class="space-y-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
            <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-2">
                <i class="fas fa-network-wired"></i>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalEnrollments }}</h3>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Total Enrollments</p>
        </div>

        <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
            <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-2">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 class="text-3xl font-extrabold text-emerald-600">{{ $todayEnrollments }}</h3>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Today's Enrollments</p>
        </div>

        <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
            <div class="w-10 h-10 mx-auto rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl mb-2">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3 class="text-3xl font-extrabold text-purple-600">{{ $uniqueStudents }}</h3>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Unique Students</p>
        </div>

        <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
            <div class="w-10 h-10 mx-auto rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl mb-2">
                <i class="fas fa-book-bookmark"></i>
            </div>
            <h3 class="text-3xl font-extrabold text-amber-600">{{ $uniqueCourses }}</h3>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Active Courses</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card rounded-2xl p-5">
        <form method="GET" action="{{ route('enrollment.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by student, email, or course..."
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="waitlisted" {{ request('status') === 'waitlisted' ? 'selected' : '' }}>Waitlisted</option>
                    <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>

            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/20">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="{{ route('enrollment.history') }}" class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- History Audit Table -->
    <div class="glass-card rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-indigo-600"></i>
                    <span>Enrollment Audit Log</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Chronological record of student-course relationships and grades</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full">
                    {{ $enrollments->total() }} Records
                </span>
                <a href="{{ route('enrollment.export') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-md shadow-emerald-600/20">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold uppercase text-gray-500 bg-gray-50/70">
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Grade</th>
                        <th class="py-3 px-3 text-center">Score</th>
                        <th class="py-3 px-3 text-center">Attendance</th>
                        <th class="py-3 px-4 text-right">Enrolled Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($enrollments as $item)
                    <tr class="hover:bg-indigo-50/20 transition">
                        <td class="py-3.5 px-4 font-semibold text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-graduate text-indigo-500 text-xs"></i>
                                <div>
                                    <div>{{ $item->student_name }}</div>
                                    <div class="text-[11px] text-gray-400 font-normal">{{ $item->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-800">
                            {{ $item->course_title }}
                        </td>
                        <td class="py-3.5 px-3 text-center">
                            @if($item->status === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Completed</span>
                            @elseif($item->status === 'waitlisted')
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Waitlist #{{ $item->waitlist_position }}</span>
                            @elseif($item->status === 'dropped')
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">Dropped</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">Active</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-center">
                            @if($item->grade)
                                <span class="px-2 py-0.5 rounded text-xs font-black bg-indigo-500 text-white">{{ $item->grade }}</span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-center font-medium text-gray-700 text-xs">
                            {{ $item->score !== null ? $item->score . '%' : '-' }}
                        </td>
                        <td class="py-3.5 px-3 text-center text-gray-700 text-xs">
                            {{ $item->attendance_percentage !== null ? $item->attendance_percentage . '%' : '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($item->enrolled_at)->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            No enrollment history records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
        <div class="mt-6">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>
</div>

@endsection