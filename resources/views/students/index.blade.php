@extends('layouts.app')

@section('title', 'Students & Enrollment Management')

@section('content')

<!-- =========================
    Dashboard Metric Cards
========================== -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-user-graduate"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalStudents }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Students</p>
    </div>

    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-book-bookmark"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalCourses }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Courses</p>
    </div>

    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-blue-600">{{ $activeEnrollments }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Active</p>
    </div>

    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-circle-check"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-emerald-600">{{ $completedEnrollments }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Completed</p>
    </div>

    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-people-line"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-amber-600">{{ $waitlistedEnrollments }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Waitlisted</p>
    </div>

    <div class="glass-card rounded-2xl p-5 text-center hover-glow transition">
        <div class="w-10 h-10 mx-auto rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl mb-2">
            <i class="fas fa-ban"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-rose-600">{{ $fullCourses }}</h3>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Full Courses</p>
    </div>
</div>

<!-- =========================
    Search & Filter Toolbar
========================== -->
<div class="glass-card rounded-2xl p-5 mb-8">
    <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="lg:col-span-2">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by student name or email..."
                    class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <select name="course" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>
                    {{ $c->title }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed Only</option>
                <option value="waitlisted" {{ request('status') === 'waitlisted' ? 'selected' : '' }}>Waitlisted Only</option>
                <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped Only</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/20">
                <i class="fas fa-filter"></i> Apply
            </button>
            <a href="{{ route('students.index') }}" class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center">
                <i class="fas fa-rotate-left"></i>
            </a>
        </div>
    </form>
</div>

<!-- =========================
    Main Content Grid
========================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Student Cards Column (2 Cols) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-users-line text-indigo-300"></i>
                <span>Enrolled Students</span>
                <span class="text-xs bg-white/20 text-indigo-100 px-2.5 py-0.5 rounded-full font-medium">{{ $students->total() }}</span>
            </h2>
            <a href="{{ route('matrix.index') }}" class="text-xs font-semibold text-indigo-200 hover:text-white flex items-center gap-1">
                Open Matrix View <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @forelse($students as $student)
        @php
            $gpa = $student->calculateGpa();
            $credits = $student->totalCredits();
        @endphp
        <div class="glass-card rounded-2xl p-6 hover-glow transition border-l-4 border-l-indigo-600">
            <!-- Student Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-indigo-700 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-500/20">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            {{ $student->name }}
                        </h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                            <i class="fas fa-envelope text-gray-400"></i> {{ $student->email }}
                        </p>
                    </div>
                </div>

                <!-- Academic Badges & Transcript Link -->
                <div class="flex items-center gap-2">
                    @if($gpa !== null)
                    <div class="bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 rounded-xl text-xs font-bold flex items-center gap-1">
                        <i class="fas fa-award text-indigo-500"></i>
                        <span>GPA: {{ number_format($gpa, 2) }}</span>
                    </div>
                    @endif
                    <div class="bg-purple-50 border border-purple-200 text-purple-700 px-3 py-1 rounded-xl text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-scroll text-purple-500"></i>
                        <span>{{ $credits }} Credits</span>
                    </div>
                    <a href="{{ route('students.transcript', $student->id) }}"
                        class="bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 text-gray-700 px-3 py-1 rounded-xl text-xs font-semibold transition flex items-center gap-1.5"
                        title="View Full Academic Transcript">
                        <i class="fas fa-file-invoice"></i>
                        <span class="hidden sm:inline">Transcript</span>
                    </a>
                </div>
            </div>

            <!-- Enrolled Courses Section -->
            <div class="mt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-book-open text-indigo-500"></i> Enrolled Courses ({{ $student->courses->count() }})
                    </h4>
                    <span class="text-xs text-gray-400">Includes active, completed & waitlisted</span>
                </div>

                @if($student->courses->count())
                <div class="space-y-3">
                    @foreach($student->courses as $course)
                    @php
                        $pivot = $course->pivot;
                    @endphp
                    <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/80 hover:bg-white hover:border-gray-200 transition flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-gray-900 text-sm">{{ $course->title }}</span>

                                <!-- Status Badge -->
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold border {{ $pivot->statusBadgeClass() }}">
                                    @if($pivot->status === 'waitlisted')
                                        <i class="fas fa-clock mr-1"></i> Waitlist #{{ $pivot->waitlist_position ?? '?' }}
                                    @elseif($pivot->status === 'completed')
                                        <i class="fas fa-check-double mr-1"></i> Completed
                                    @elseif($pivot->status === 'dropped')
                                        <i class="fas fa-ban mr-1"></i> Dropped
                                    @else
                                        <i class="fas fa-circle-dot mr-1"></i> Active
                                    @endif
                                </span>

                                <!-- Grade Badge -->
                                @if($pivot->grade)
                                <span class="px-2 py-0.5 rounded-md text-xs font-extrabold {{ $pivot->gradeBadgeClass() }}">
                                    Grade: {{ $pivot->grade }}
                                </span>
                                @endif
                            </div>

                            <!-- Pivot Metric Details -->
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-1">
                                @if($pivot->score !== null)
                                <span class="flex items-center gap-1 font-medium text-gray-700">
                                    <i class="fas fa-percent text-blue-500"></i> Score: {{ $pivot->score }}%
                                </span>
                                @endif
                                @if($pivot->attendance_percentage !== null)
                                <span class="flex items-center gap-1 font-medium text-gray-700">
                                    <i class="fas fa-user-check text-emerald-500"></i> Attendance: {{ $pivot->attendance_percentage }}%
                                </span>
                                @endif
                                <span class="text-gray-400">
                                    <i class="fas fa-calendar-day mr-1"></i> {{ $pivot->enrolled_at ? $pivot->enrolled_at->format('M d, Y') : 'N/A' }}
                                </span>
                                @if($pivot->notes)
                                <span class="text-gray-500 italic max-w-xs truncate" title="{{ $pivot->notes }}">
                                    "{{ $pivot->notes }}"
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex items-center gap-2 self-end md:self-center">
                            <!-- Edit Grade/Status Button -->
                            <button type="button"
                                onclick="openGradeModal({{ $pivot->id }}, '{{ addslashes($student->name) }}', '{{ addslashes($course->title) }}', '{{ $pivot->status }}', '{{ $pivot->grade }}', '{{ $pivot->score }}', '{{ $pivot->attendance_percentage }}', '{{ addslashes($pivot->notes ?? '') }}')"
                                class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition flex items-center gap-1">
                                <i class="fas fa-pen-to-square"></i>
                                <span>Grade</span>
                            </button>

                            <!-- Drop Course Form -->
                            <form action="{{ route('student.drop', [$student->id, $course->id]) }}" method="POST"
                                onsubmit="return confirm('Drop {{ addslashes($student->name) }} from {{ addslashes($course->title) }}? If waitlist exists, next student will be auto-promoted.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition flex items-center gap-1">
                                    <i class="fas fa-trash-can"></i>
                                    <span>Drop</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-gray-50 rounded-xl p-4 text-center text-gray-500 text-xs">
                    <i class="fas fa-info-circle text-gray-400 mr-1"></i> No courses enrolled yet. Use the sidebar to enroll or assign courses.
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="glass-card rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-2xl mb-4">
                <i class="fas fa-user-slash"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">No Students Found</h3>
            <p class="text-sm text-gray-500 mt-1">Try adjusting your filters or click "Seed Data" to generate sample students.</p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="mt-6">
            {{ $students->links() }}
        </div>
        @endif
    </div>

    <!-- Right Sidebar Controls (Enrollment, Bulk, Transfer) -->
    <div class="space-y-6">

        <!-- 1. Single Student Enrollment Card -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-user-plus text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Enroll Student</h3>
            </div>

            <form action="{{ route('student.enroll') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Student</label>
                    <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Student...</option>
                        @foreach(\App\Models\Student::orderBy('name')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Course</label>
                    <select name="course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Course...</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->title }}
                            ({{ $c->remainingSeats() }} seats left{{ $c->isFull() ? ' • FULL' : '' }})
                            @if($c->prerequisites->isNotEmpty())
                                • Requires: {{ $c->prerequisites->pluck('title')->implode(', ') }}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Regular enrollment, scholarship"
                        class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="allow_waitlist" name="allow_waitlist" value="1" checked
                        class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="allow_waitlist" class="text-xs text-gray-600">
                        Auto-join Waitlist if course capacity is full
                    </label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Enroll Student
                </button>
            </form>
        </div>

        <!-- 2. Bulk Enrollment Studio Card -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <i class="fas fa-layer-group text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Bulk Enrollment</h3>
                </div>
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-semibold">Batch Sync</span>
            </div>

            <form action="{{ route('student.bulkEnroll') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="bulk_type" value="students_to_course">

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Course</label>
                    <select name="course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Target Course...</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->title }} ({{ $c->remainingSeats() }} seats left)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Select Multiple Students</label>
                    <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-xl p-2.5 space-y-1.5 bg-white text-xs">
                        @foreach(\App\Models\Student::orderBy('name')->get() as $s)
                        <label class="flex items-center gap-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="rounded text-purple-600">
                            <span class="font-medium text-gray-800">{{ $s->name }}</span>
                            <span class="text-gray-400">({{ $s->courses->count() }} enrolled)</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Check multiple students to enroll simultaneously.</p>
                </div>

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-md shadow-purple-600/20 flex items-center justify-center gap-2">
                    <i class="fas fa-users"></i> Batch Enroll Students
                </button>
            </form>
        </div>

        <!-- 3. Course Transfer Utility Card -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-right-left text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Transfer Student</h3>
            </div>

            <form id="transferForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Student</label>
                    <select id="transferStudentSelect" required onchange="updateTransferFormAction(this.value)"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Select Student...</option>
                        @foreach(\App\Models\Student::has('courses')->orderBy('name')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">From Course</label>
                    <select name="from_course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Current Course...</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">To Course</label>
                    <select name="to_course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Target Course...</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-md shadow-amber-600/20 flex items-center justify-center gap-2">
                    <i class="fas fa-arrows-spin"></i> Transfer Course
                </button>
            </form>
        </div>

    </div>
</div>

<!-- =========================
    Edit Grade & Status Modal
========================== -->
<div id="gradeModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-indigo-600"></i>
                    <span>Update Enrollment Details</span>
                </h3>
                <p id="modalSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeGradeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="gradeUpdateForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Enrollment Status</label>
                <select id="modalStatus" name="status" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white">
                    <option value="active">Active</option>
                    <option value="completed">Completed (Graduated)</option>
                    <option value="waitlisted">Waitlisted</option>
                    <option value="dropped">Dropped</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Letter Grade</label>
                    <select id="modalGrade" name="grade" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white">
                        <option value="">None</option>
                        <option value="A+">A+ (4.0)</option>
                        <option value="A">A (4.0)</option>
                        <option value="A-">A- (3.7)</option>
                        <option value="B+">B+ (3.3)</option>
                        <option value="B">B (3.0)</option>
                        <option value="B-">B- (2.7)</option>
                        <option value="C+">C+ (2.3)</option>
                        <option value="C">C (2.0)</option>
                        <option value="D">D (1.0)</option>
                        <option value="F">F (0.0)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Score (%)</label>
                    <input type="number" step="0.5" min="0" max="100" id="modalScore" name="score" placeholder="e.g. 92.5"
                        class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Attendance Percentage (%)</label>
                <input type="number" step="0.5" min="0" max="100" id="modalAttendance" name="attendance_percentage" placeholder="e.g. 95.0"
                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Faculty / Performance Notes</label>
                <textarea id="modalNotes" name="notes" rows="2" placeholder="e.g. Strong project work, excellent quiz scores..."
                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-sm bg-white"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeGradeModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl text-sm font-semibold transition shadow-md shadow-indigo-600/20">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateTransferFormAction(studentId) {
        if (!studentId) return;
        document.getElementById('transferForm').action = `/students/${studentId}/transfer`;
    }

    function openGradeModal(enrollmentId, studentName, courseTitle, status, grade, score, attendance, notes) {
        const form = document.getElementById('gradeUpdateForm');
        form.action = `/enrollments/${enrollmentId}/update`;

        document.getElementById('modalSubtitle').textContent = `${studentName} • ${courseTitle}`;
        document.getElementById('modalStatus').value = status || 'active';
        document.getElementById('modalGrade').value = grade || '';
        document.getElementById('modalScore').value = score || '';
        document.getElementById('modalAttendance').value = attendance || '';
        document.getElementById('modalNotes').value = notes || '';

        const modal = document.getElementById('gradeModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeGradeModal() {
        const modal = document.getElementById('gradeModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on click outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('gradeModal');
        if (e.target === modal) {
            closeGradeModal();
        }
    });
</script>

@endsection