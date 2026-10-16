@extends('layouts.app')

@section('title', 'Relationship Matrix Studio')

@section('content')

<!-- Header & Explanation -->
<div class="glass-card rounded-2xl p-6 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-border-all text-indigo-600"></i>
                <span>Interactive Relationship Matrix Studio</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Visual Many-to-Many mapping: Click any grid cell to <strong>Attach (Enroll)</strong> or <strong>Detach (Drop)</strong> students in real-time via AJAX without page reloads!
            </p>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-3 text-xs bg-gray-50 border border-gray-200 px-4 py-2.5 rounded-xl font-medium">
            <span class="flex items-center gap-1.5 text-emerald-700">
                <span class="w-4 h-4 rounded bg-emerald-500 text-white flex items-center justify-center text-[10px]"><i class="fas fa-check"></i></span> Active
            </span>
            <span class="flex items-center gap-1.5 text-blue-700">
                <span class="w-4 h-4 rounded bg-blue-500 text-white flex items-center justify-center text-[10px]"><i class="fas fa-check-double"></i></span> Completed
            </span>
            <span class="flex items-center gap-1.5 text-amber-700">
                <span class="w-4 h-4 rounded bg-amber-500 text-white flex items-center justify-center text-[10px]"><i class="fas fa-clock"></i></span> Waitlisted
            </span>
            <span class="flex items-center gap-1.5 text-gray-500">
                <span class="w-4 h-4 rounded border border-gray-300 bg-white"></span> Unassigned
            </span>
        </div>
    </div>
</div>

<!-- Matrix Table Container -->
<div class="glass-card rounded-2xl p-6 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-gray-200">
                    <!-- Top-Left Corner Header -->
                    <th class="p-3 text-left bg-gray-50/80 sticky left-0 z-20 min-w-[200px] rounded-tl-xl">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Students \ Courses
                        </span>
                    </th>

                    <!-- Course Column Headers -->
                    @foreach($courses as $course)
                    <th class="p-3 text-center bg-gray-50/80 min-w-[130px] border-l border-gray-100">
                        <div class="text-xs font-bold text-gray-900 truncate max-w-[140px] mx-auto" title="{{ $course->title }}">
                            {{ $course->title }}
                        </div>
                        <div class="mt-1 flex items-center justify-center gap-1">
                            <span id="course-cap-{{ $course->id }}"
                                class="text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $course->isFull() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $course->enrolledCount() }}/{{ $course->capacity }}
                            </span>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($students as $student)
                @php
                    $enrolledCourseIds = $student->courses->keyBy('id');
                @endphp
                <tr class="hover:bg-indigo-50/30 transition">
                    <!-- Student Row Header -->
                    <td class="p-3 bg-white sticky left-0 z-10 font-medium border-r border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-xs">{{ $student->name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>

                    <!-- Matrix Interactive Cells -->
                    @foreach($courses as $course)
                    @php
                        $enrollment = $enrolledCourseIds->get($course->id);
                        $status = $enrollment ? $enrollment->pivot->status : null;
                        $pos = $enrollment ? $enrollment->pivot->waitlist_position : null;
                    @endphp
                    <td class="p-2.5 text-center border-l border-gray-100">
                        <button type="button"
                            id="cell-{{ $student->id }}-{{ $course->id }}"
                            onclick="toggleMatrixCell({{ $student->id }}, {{ $course->id }}, '{{ addslashes($student->name) }}', '{{ addslashes($course->title) }}')"
                            class="w-10 h-10 mx-auto rounded-xl transition flex items-center justify-center text-sm font-bold shadow-sm relative group
                            @if($status === 'active')
                                bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-500/20
                            @elseif($status === 'completed')
                                bg-blue-500 hover:bg-blue-600 text-white shadow-blue-500/20
                            @elseif($status === 'waitlisted')
                                bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20
                            @else
                                bg-gray-100 hover:bg-indigo-100 text-gray-400 hover:text-indigo-600 border border-gray-200
                            @endif">
                            
                            @if($status === 'active')
                                <i class="fas fa-check"></i>
                            @elseif($status === 'completed')
                                <i class="fas fa-check-double"></i>
                            @elseif($status === 'waitlisted')
                                <span class="text-xs">#{{ $pos ?? 'W' }}</span>
                            @else
                                <i class="fas fa-plus opacity-0 group-hover:opacity-100 text-xs transition"></i>
                            @endif

                            <!-- Tooltip -->
                            <span class="pointer-events-none absolute bottom-full mb-1 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-900 text-white text-[10px] py-1 px-2 rounded-md whitespace-nowrap z-30 shadow-lg">
                                @if($status === 'active') Active (Click to drop)
                                @elseif($status === 'completed') Completed (Click to drop)
                                @elseif($status === 'waitlisted') Waitlisted #{{ $pos }} (Click to drop)
                                @else Click to Enroll
                                @endif
                            </span>
                        </button>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    async function toggleMatrixCell(studentId, courseId, studentName, courseTitle) {
        const btn = document.getElementById(`cell-${studentId}-${courseId}`);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Show spinner / pulse
        btn.classList.add('opacity-50', 'animate-pulse');

        try {
            const res = await fetch('{{ route("matrix.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    student_id: studentId,
                    course_id: courseId
                })
            });

            const data = await res.json();

            btn.classList.remove('opacity-50', 'animate-pulse');

            if (!res.ok || !data.success) {
                showToast(data.message || 'Operation failed', 'error');
                return;
            }

            // Update button visual
            btn.className = 'w-10 h-10 mx-auto rounded-xl transition flex items-center justify-center text-sm font-bold shadow-sm relative group ';

            if (data.action === 'enrolled') {
                btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600', 'text-white', 'shadow-emerald-500/20');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                showToast(data.message, 'success');
            } else if (data.action === 'waitlisted') {
                btn.classList.add('bg-amber-500', 'hover:bg-amber-600', 'text-white', 'shadow-amber-500/20');
                btn.innerHTML = `<span class="text-xs">#${data.waitlist_position}</span>`;
                showToast(data.message, 'success');
            } else {
                // Detached
                btn.classList.add('bg-gray-100', 'hover:bg-indigo-100', 'text-gray-400', 'hover:text-indigo-600', 'border', 'border-gray-200');
                btn.innerHTML = '<i class="fas fa-plus opacity-0 group-hover:opacity-100 text-xs transition"></i>';
                showToast(data.message, 'success');
            }

            // Update course occupancy badge if returned
            const capBadge = document.getElementById(`course-cap-${courseId}`);
            if (capBadge) {
                if (data.course_is_full) {
                    capBadge.className = 'text-[11px] px-2 py-0.5 rounded-full font-semibold bg-rose-100 text-rose-700';
                } else {
                    capBadge.className = 'text-[11px] px-2 py-0.5 rounded-full font-semibold bg-emerald-100 text-emerald-700';
                }
            }
        } catch (err) {
            btn.classList.remove('opacity-50', 'animate-pulse');
            showToast('Network error while toggling enrollment', 'error');
        }
    }
</script>

@endsection
