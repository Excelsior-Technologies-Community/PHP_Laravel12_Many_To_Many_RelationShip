@extends('layouts.app')

@section('title', 'Academic Transcript - ' . $student->name)

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Action Toolbar (hidden on print) -->
    <div class="flex justify-between items-center print:hidden">
        <a href="{{ route('students.index') }}" class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white text-sm font-semibold transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition shadow-lg shadow-indigo-600/30 flex items-center gap-2">
            <i class="fas fa-print"></i> Print / Save as PDF
        </button>
    </div>

    <!-- Official Transcript Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-gray-100 text-gray-800">

        <!-- University / System Header -->
        <div class="border-b-2 border-indigo-600 pb-6 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <div class="flex items-center gap-2.5 text-indigo-700 font-black text-2xl tracking-tight">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                    <span>Laravel Academy of Technology</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-semibold">Official Student Academic Transcript</p>
            </div>
            <div class="text-right text-xs text-gray-500">
                <div>Document ID: <strong>TRANS-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Issued: <strong>{{ now()->format('F d, Y') }}</strong></div>
            </div>
        </div>

        <!-- Student Profile Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100/60">
            <div>
                <span class="text-[11px] font-bold uppercase text-gray-400 block">Student Name</span>
                <span class="text-base font-extrabold text-gray-900">{{ $student->name }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase text-gray-400 block">Email Address</span>
                <span class="text-sm font-medium text-gray-700">{{ $student->email }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase text-gray-400 block">Cumulative GPA</span>
                <span class="text-xl font-black text-indigo-600">
                    {{ $gpa !== null ? number_format($gpa, 2) : 'N/A' }}
                    <span class="text-xs font-normal text-gray-400">/ 4.0</span>
                </span>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase text-gray-400 block">Completed Credits</span>
                <span class="text-xl font-black text-purple-600">
                    {{ $totalCredits }}
                    <span class="text-xs font-normal text-gray-400">Credits</span>
                </span>
            </div>
        </div>

        <!-- Academic Standing Banner -->
        <div class="mb-8 p-4 rounded-xl flex items-center justify-between
            @if($gpa >= 3.8) bg-emerald-50 border border-emerald-200 text-emerald-800
            @elseif($gpa >= 3.0) bg-blue-50 border border-blue-200 text-blue-800
            @else bg-gray-50 border border-gray-200 text-gray-800
            @endif">
            <div class="flex items-center gap-3">
                <i class="fas @if($gpa >= 3.8) fa-award text-emerald-600 text-2xl @else fa-check-circle text-blue-600 text-2xl @endif"></i>
                <div>
                    <h4 class="font-extrabold text-sm">
                        @if($gpa >= 3.8) Dean's Honors List (Excellence Standing)
                        @elseif($gpa >= 3.0) Good Academic Standing
                        @else Academic Progress In-Review
                        @endif
                    </h4>
                    <p class="text-xs opacity-80">Candidate has enrolled in {{ $student->courses->count() }} courses across semesters.</p>
                </div>
            </div>
            <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-white shadow-sm">
                Verified
            </span>
        </div>

        <!-- Coursework Performance Table -->
        <div class="mb-8">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-1.5">
                <i class="fas fa-list-check text-indigo-600"></i> Coursework & Performance Record
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-200 text-[11px] font-bold uppercase text-gray-500 bg-gray-50/80">
                            <th class="py-3 px-3">Course Title</th>
                            <th class="py-3 px-2 text-center">Status</th>
                            <th class="py-3 px-2 text-center">Grade</th>
                            <th class="py-3 px-2 text-center">Score</th>
                            <th class="py-3 px-2 text-center">Attendance</th>
                            <th class="py-3 px-2 text-center">Credits</th>
                            <th class="py-3 px-3 text-right">Completion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($student->courses as $course)
                        @php
                            $pivot = $course->pivot;
                        @endphp
                        <tr class="hover:bg-gray-50/60">
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-gray-900">{{ $course->title }}</div>
                                @if($pivot->notes)
                                <div class="text-xs text-gray-500 italic mt-0.5">Remarks: "{{ $pivot->notes }}"</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold border {{ $pivot->statusBadgeClass() }}">
                                    {{ ucfirst($pivot->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-2 text-center">
                                @if($pivot->grade)
                                <span class="px-2 py-0.5 rounded font-extrabold text-xs {{ $pivot->gradeBadgeClass() }}">
                                    {{ $pivot->grade }}
                                </span>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-2 text-center font-semibold text-gray-700 text-xs">
                                {{ $pivot->score !== null ? $pivot->score . '%' : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-2 text-center text-xs text-gray-700">
                                {{ $pivot->attendance_percentage !== null ? $pivot->attendance_percentage . '%' : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-2 text-center font-bold text-gray-800 text-xs">
                                {{ $pivot->status === 'completed' ? '3.0' : '0.0' }}
                            </td>
                            <td class="py-3.5 px-3 text-right text-xs text-gray-500 font-medium">
                                {{ $pivot->completed_at ? $pivot->completed_at->format('M d, Y') : ($pivot->enrolled_at ? $pivot->enrolled_at->format('M Y') : 'In Progress') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                No course enrollments recorded for this student.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transcript Footer & Signatures -->
        <div class="border-t border-gray-200 pt-8 mt-12 grid grid-cols-2 gap-8 text-xs text-gray-500">
            <div>
                <p class="font-bold text-gray-700 mb-1">Grading Scale Reference:</p>
                <p>A+ / A (4.0) • A- (3.7) • B+ (3.3) • B (3.0) • B- (2.7) • C+ (2.3) • C (2.0) • D (1.0) • F (0.0)</p>
            </div>
            <div class="text-right">
                <div class="inline-block border-b border-gray-400 w-48 mb-1"></div>
                <div class="font-bold text-gray-800">Office of Academic Registrar</div>
                <div class="text-gray-400">Verified Electronic Credential</div>
            </div>
        </div>

    </div>
</div>

@endsection
