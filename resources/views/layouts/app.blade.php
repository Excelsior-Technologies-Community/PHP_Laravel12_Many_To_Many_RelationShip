<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel 12 Many-to-Many Studio')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%);
            min-height: 100vh;
            color: #1f2937;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .glass-nav {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hover-glow:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
            transition: all 0.25s ease;
        }
    </style>
</head>

<body class="antialiased selection:bg-indigo-500 selection:text-white">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Top Navigation -->
        <nav class="glass-nav rounded-2xl p-4 mb-8 text-white shadow-2xl">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fas fa-network-wired text-xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                            <span>LMS Relationship Studio</span>
                            <span class="text-xs uppercase px-2 py-0.5 rounded-full bg-indigo-500/30 border border-indigo-400/40 text-indigo-300 font-semibold">Laravel 12</span>
                        </h1>
                        <p class="text-xs text-indigo-200/70">Many-to-Many Architecture • Students ↔ Courses • Rich Pivot</p>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('students.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 {{ request()->routeIs('students.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-user-graduate"></i>
                        <span>Students</span>
                    </a>
                    <a href="{{ route('courses.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 {{ request()->routeIs('courses.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-book-bookmark"></i>
                        <span>Courses & Waitlist</span>
                    </a>
                    <a href="{{ route('matrix.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 {{ request()->routeIs('matrix.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-border-all"></i>
                        <span>Matrix Studio</span>
                    </a>
                    <a href="{{ route('analytics.index') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 {{ request()->routeIs('analytics.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Analytics</span>
                    </a>
                    <a href="{{ route('enrollment.history') }}"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 {{ request()->routeIs('enrollment.history') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>Audit History</span>
                    </a>
                    <a href="{{ route('create.sample') }}"
                        onclick="return confirm('Reset and re-seed with comprehensive prerequisites, waitlists, and grades?')"
                        class="px-3.5 py-2 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                        <i class="fas fa-rotate"></i>
                        <span>Seed Data</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-4 mb-6 rounded-2xl shadow-lg flex items-center justify-between" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 border border-rose-300 text-rose-800 px-5 py-4 mb-6 rounded-2xl shadow-lg flex items-center justify-between" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-rose-500 text-xl"></i>
                <span class="font-medium text-sm md:text-base">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-14 py-8 border-t border-white/10 text-center text-indigo-200/70 text-sm">
            <div class="flex flex-wrap justify-center items-center gap-6 mb-3">
                <span class="flex items-center gap-1.5"><i class="fas fa-check-circle text-emerald-400"></i> Laravel 12.x</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-layer-group text-blue-400"></i> Custom Pivot Model</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-diagram-project text-purple-400"></i> Course Prerequisites</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-people-line text-amber-400"></i> Smart Waitlist Queue</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-chart-line text-rose-400"></i> Chart.js Analytics</span>
            </div>
            <p class="text-xs text-indigo-300/50">
                Many-to-Many Architecture Showcase • Clean Eloquent Code & Interactive UI
            </p>
        </footer>
    </div>

    <!-- Quick Toast Notification Container for AJAX actions -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const isSuccess = type === 'success';
            toast.className = `pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium transition-all transform translate-y-5 opacity-0 duration-300 ${isSuccess ? 'bg-emerald-600 shadow-emerald-500/30' : 'bg-rose-600 shadow-rose-500/30'}`;
            toast.innerHTML = `<i class="fas ${isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation'} text-lg"></i><span>${message}</span>`;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-5', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
</body>

</html>