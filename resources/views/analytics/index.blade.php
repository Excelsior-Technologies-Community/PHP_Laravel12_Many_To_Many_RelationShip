@extends('layouts.app')

@section('title', 'Enrollment Analytics & Capacity Heatmap')

@section('content')

<!-- Header -->
<div class="glass-card rounded-2xl p-6 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-indigo-600"></i>
                <span>Academic Analytics & Capacity Heatmap</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Visual insights into course demand, seat capacity utilization, grade distribution, and student achievements.
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 flex items-center gap-1.5">
                <i class="fas fa-circle text-[8px] text-emerald-500 animate-pulse"></i> Real-time Live Metrics
            </span>
        </div>
    </div>
</div>

<!-- Course Capacity Heatmap Cards -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-fire-flame-curved text-amber-400"></i>
        <span>Course Capacity Heatmap</span>
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($courses as $c)
        @php
            $pct = $c->capacityPercentage();
            $isFull = $c->isFull();
        @endphp
        <div class="glass-card rounded-2xl p-4 hover-glow transition border-t-4 {{ $isFull ? 'border-t-rose-500' : ($pct >= 75 ? 'border-t-amber-500' : 'border-t-emerald-500') }}">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-bold text-gray-900 text-sm truncate flex-1 pr-2" title="{{ $c->title }}">
                    {{ $c->title }}
                </h4>
                <span class="text-xs font-black px-2 py-0.5 rounded-full {{ $isFull ? 'bg-rose-100 text-rose-700' : ($pct >= 75 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                    {{ $pct }}%
                </span>
            </div>

            <div class="text-xs text-gray-500 mb-2 flex justify-between items-center font-medium">
                <span>{{ $c->enrolledCount() }} / {{ $c->capacity }} enrolled</span>
                @if($c->waitlistCount() > 0)
                <span class="text-amber-600 font-bold">
                    <i class="fas fa-clock mr-0.5"></i> {{ $c->waitlistCount() }} waitlisted
                </span>
                @endif
            </div>

            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full {{ $isFull ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                    style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Interactive Chart.js Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart 1: Course Capacity vs Enrolled -->
    <div class="glass-card rounded-2xl p-6 lg:col-span-2">
        <h4 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
            <i class="fas fa-chart-column text-indigo-600"></i>
            <span>Course Occupancy vs Capacity Demand</span>
        </h4>
        <div class="relative h-72">
            <canvas id="courseBarChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Status Breakdown Doughnut -->
    <div class="glass-card rounded-2xl p-6">
        <h4 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
            <i class="fas fa-chart-pie text-purple-600"></i>
            <span>Enrollment Status Distribution</span>
        </h4>
        <div class="relative h-72 flex items-center justify-center">
            <canvas id="statusDoughnutChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart 3: Grade Distribution & Top Leaderboard Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Grade Distribution -->
    <div class="glass-card rounded-2xl p-6">
        <h4 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
            <i class="fas fa-award text-amber-500"></i>
            <span>Academic Letter Grade Distribution</span>
        </h4>
        <div class="relative h-72">
            <canvas id="gradeBarChart"></canvas>
        </div>
    </div>

    <!-- Top Students Leaderboard -->
    <div class="glass-card rounded-2xl p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fas fa-trophy text-amber-500"></i>
                <span>Top Students Academic Leaderboard</span>
            </h4>
            <span class="text-xs text-gray-400">Ranked by completions & GPA</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                        <th class="pb-3">Rank</th>
                        <th class="pb-3">Student</th>
                        <th class="pb-3 text-center">Completed</th>
                        <th class="pb-3 text-center">Active</th>
                        <th class="pb-3 text-center">GPA</th>
                        <th class="pb-3 text-center">Credits</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leaderboard as $index => $lead)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 font-extrabold text-sm text-gray-400">
                            @if($index === 0)
                                <span class="w-6 h-6 rounded-full bg-amber-400 text-amber-950 font-black text-xs flex items-center justify-center shadow">1</span>
                            @elseif($index === 1)
                                <span class="w-6 h-6 rounded-full bg-slate-300 text-slate-800 font-black text-xs flex items-center justify-center shadow">2</span>
                            @elseif($index === 2)
                                <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-black text-xs flex items-center justify-center shadow">3</span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 font-bold text-xs flex items-center justify-center">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="py-3 font-semibold text-gray-900">
                            <div>{{ $lead['name'] }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $lead['email'] }}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $lead['completed_count'] }} Courses
                            </span>
                        </td>
                        <td class="py-3 text-center text-gray-600 font-medium">
                            {{ $lead['active_count'] }}
                        </td>
                        <td class="py-3 text-center font-black text-indigo-600">
                            {{ $lead['gpa'] !== null ? number_format($lead['gpa'], 2) : 'N/A' }}
                        </td>
                        <td class="py-3 text-center text-purple-600 font-bold">
                            {{ $lead['credits'] }}
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('students.transcript', $lead['id']) }}"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition">
                                Transcript
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Course Bar Chart
        const ctxCourse = document.getElementById('courseBarChart').getContext('2d');
        new Chart(ctxCourse, {
            type: 'bar',
            data: {
                labels: {!! json_encode($courseLabels) !!},
                datasets: [
                    {
                        label: 'Active Enrolled',
                        data: {!! json_encode($courseEnrolledData) !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 6
                    },
                    {
                        label: 'Total Capacity',
                        data: {!! json_encode($courseCapacityData) !!},
                        backgroundColor: '#e0e7ff',
                        borderRadius: 6
                    },
                    {
                        label: 'Waitlist Demand',
                        data: {!! json_encode($courseWaitlistData) !!},
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 2 } }
                }
            }
        });

        // 2. Status Doughnut Chart
        const ctxStatus = document.getElementById('statusDoughnutChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Completed', 'Waitlisted', 'Dropped'],
                datasets: [{
                    data: [
                        {{ $statusCounts['active'] }},
                        {{ $statusCounts['completed'] }},
                        {{ $statusCounts['waitlisted'] }},
                        {{ $statusCounts['dropped'] }}
                    ],
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#f43f5e'],
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Grade Bar Chart
        const ctxGrade = document.getElementById('gradeBarChart').getContext('2d');
        new Chart(ctxGrade, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($gradeCounts)) !!},
                datasets: [{
                    label: 'Students Count',
                    data: {!! json_encode(array_values($gradeCounts)) !!},
                    backgroundColor: [
                        '#10b981', '#10b981', '#34d399',
                        '#3b82f6', '#3b82f6', '#60a5fa',
                        '#f59e0b', '#fbbf24', '#f97316', '#ef4444'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>

@endsection
