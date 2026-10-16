@extends('layouts.app')

@section('title', 'Courses & Waitlist Management')

@section('content')

<!-- Header Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass-card rounded-2xl p-6 text-center hover-glow transition">
        <div class="w-12 h-12 mx-auto rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl mb-3">
            <i class="fas fa-book-bookmark"></i>
        </div>
        <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalCourses }}</h3>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Total Academic Courses</p>
    </div>

    <div class="glass-card rounded-2xl p-6 text-center hover-glow transition">
        <div class="w-12 h-12 mx-auto rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mb-3">
            <i class="fas fa-user-graduate"></i>
        </div>
        <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalStudents }}</h3>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Enrolled Students</p>
    </div>

    <div class="glass-card rounded-2xl p-6 text-center hover-glow transition">
        <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mb-3">
            <i class="fas fa-network-wired"></i>
        </div>
        <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalEnrollments }}</h3>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Total Relationships (Active + History)</p>
    </div>
</div>

<!-- Search & Actions Bar -->
<div class="glass-card rounded-2xl p-5 mb-8">
    <form method="GET" action="{{ route('courses.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search course title or description..."
                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20">
            <i class="fas fa-search"></i> Search
        </button>
        <a href="{{ route('courses.index') }}" class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 rounded-xl text-sm transition flex items-center justify-center">
            <i class="fas fa-rotate-left"></i>
        </a>
    </form>
</div>

<!-- Courses Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($courses as $course)
    @php
        $activeCount = $course->enrolledCount();
        $capacityPct = $course->capacityPercentage();
        $waitlist = $course->waitlistedStudents;
        $prereqs = $course->prerequisites;
        $dependents = $course->dependentCourses;
    @endphp

    <div class="glass-card rounded-2xl overflow-hidden hover-glow transition flex flex-col justify-between border-t-4 {{ $course->isFull() ? 'border-t-rose-500' : 'border-t-indigo-600' }}">
        
        <!-- Course Header -->
        <div class="p-6">
            <div class="flex justify-between items-start gap-2 mb-2">
                <h3 class="text-xl font-bold text-gray-900 leading-tight">
                    {{ $course->title }}
                </h3>
                @if($course->isFull())
                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-extrabold flex items-center gap-1">
                    <i class="fas fa-lock"></i> FULL
                </span>
                @else
                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-extrabold flex items-center gap-1">
                    <i class="fas fa-check"></i> {{ $course->remainingSeats() }} Open
                </span>
                @endif
            </div>

            <p class="text-xs text-gray-500 mb-4 line-clamp-2">
                {{ $course->description }}
            </p>

            <!-- Capacity Progress Bar -->
            <div class="mb-5 bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex justify-between items-center text-xs mb-1.5 font-semibold">
                    <span class="text-gray-600 flex items-center gap-1">
                        <i class="fas fa-chair text-indigo-500"></i> Seat Occupancy
                    </span>
                    <span class="text-gray-800">
                        {{ $activeCount }} / {{ $course->capacity }} seats ({{ $capacityPct }}%)
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $course->isFull() ? 'bg-rose-500' : ($capacityPct >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ $capacityPct }}%"></div>
                </div>
            </div>

            <!-- Prerequisites Flow (Self-referential Many-to-Many) -->
            <div class="mb-5 space-y-2">
                @if($prereqs->isNotEmpty())
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 flex items-center gap-1 mb-1">
                        <i class="fas fa-key text-amber-500"></i> Required Prerequisites:
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($prereqs as $p)
                        <span class="bg-amber-50 border border-amber-200 text-amber-800 px-2 py-0.5 rounded-md text-xs font-semibold">
                            {{ $p->title }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($dependents->isNotEmpty())
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-1 mb-1">
                        <i class="fas fa-arrow-right-from-bracket text-purple-500"></i> Prerequisite For:
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($dependents as $dep)
                        <span class="bg-purple-50 border border-purple-200 text-purple-800 px-2 py-0.5 rounded-md text-xs font-semibold">
                            {{ $dep->title }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Waitlist Section (if any waitlisted) -->
            @if($waitlist->isNotEmpty())
            <div class="mb-5 bg-amber-50/70 border border-amber-200 rounded-xl p-3.5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1">
                        <i class="fas fa-people-line text-amber-600"></i> Waitlist Queue ({{ $waitlist->count() }})
                    </span>
                    <span class="text-xs text-amber-600 font-semibold">Auto-promotes on drop</span>
                </div>

                <div class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                    @foreach($waitlist as $wStudent)
                    <div class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-amber-200 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-500 text-white font-bold text-xs flex items-center justify-center">
                                #{{ $wStudent->pivot->waitlist_position ?? $loop->iteration }}
                            </span>
                            <span class="font-medium text-gray-800">{{ $wStudent->name }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            @if(!$course->isFull())
                            <form action="{{ route('waitlist.promote', [$course->id, $wStudent->id]) }}" method="POST">
                                @csrf
                                <button type="submit" title="Promote to active enrollment"
                                    class="text-emerald-600 hover:text-emerald-800 font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-xs">
                                    Promote
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('waitlist.remove', [$course->id, $wStudent->id]) }}" method="POST"
                                onsubmit="return confirm('Remove {{ addslashes($wStudent->name) }} from waitlist?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Remove from queue"
                                    class="text-rose-500 hover:text-rose-700 font-bold px-1 py-0.5">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Active Students List -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2 flex items-center gap-1">
                    <i class="fas fa-users text-indigo-500"></i> Enrolled Students ({{ $course->activeStudents->count() }})
                </h4>
                
                @if($course->activeStudents->isNotEmpty())
                <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1">
                    @foreach($course->activeStudents as $aStudent)
                    <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 p-2 rounded-lg text-xs transition">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-circle text-gray-400 text-sm"></i>
                            <div>
                                <span class="font-medium text-gray-900">{{ $aStudent->name }}</span>
                                @if($aStudent->pivot->grade)
                                <span class="ml-1 text-xs font-bold text-indigo-600">[{{ $aStudent->pivot->grade }}]</span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('student.drop', [$aStudent->id, $course->id]) }}" method="POST"
                            onsubmit="return confirm('Drop {{ addslashes($aStudent->name) }} from {{ addslashes($course->title) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:text-rose-600 text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 italic">No active students enrolled yet.</p>
                @endif
            </div>
        </div>

        <!-- Course Footer Action -->
        <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex justify-between items-center text-xs">
            <span class="text-gray-500">
                Created: {{ $course->created_at ? $course->created_at->format('M Y') : 'N/A' }}
            </span>
            <a href="{{ route('students.index', ['course' => $course->id]) }}"
                class="font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Filter Students <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-3 glass-card rounded-2xl p-12 text-center">
        <i class="fas fa-book-open text-4xl text-gray-300 mb-3"></i>
        <h3 class="text-lg font-bold text-gray-700">No Courses Found</h3>
        <p class="text-sm text-gray-400 mt-1">Try another search keyword.</p>
    </div>
    @endforelse
</div>

@if($courses->hasPages())
<div class="mt-8">
    {{ $courses->links() }}
</div>
@endif

@endsection