<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Many-to-Many')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-glow:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="text-gray-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <nav class="glass-card rounded-xl p-4 mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-white">
                    <i class="fas fa-graduation-cap mr-2"></i>Student Course System
                </h1>
                <div class="space-x-4">
                    <a href="{{ route('students.index') }}" 
                       class="px-4 py-2 rounded-lg bg-white/20 text-white hover:bg-white/30 transition">
                        <i class="fas fa-users mr-2"></i>Students
                    </a>
                    <a href="{{ route('courses.index') }}" 
                       class="px-4 py-2 rounded-lg bg-white/20 text-white hover:bg-white/30 transition">
                        <i class="fas fa-book mr-2"></i>Courses
                    </a>
                    <a href="{{ route('create.sample') }}" 
                       class="px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition">
                        <i class="fas fa-magic mr-2"></i>Generate Sample Data
                    </a>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-12 text-center text-white/70">
            <p class="text-sm">
                Laravel 12 Many-to-Many Relationship Demo • 
                Students ↔ Courses • 
                Created with <i class="fas fa-heart text-red-400"></i>
            </p>
            <p class="text-xs mt-2">
                Each student can enroll in multiple courses • Each course can have multiple students
            </p>
        </footer>
    </div>

    <script>
        // Simple animation for cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover-glow');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.classList.add('shadow-xl');
                });
                card.addEventListener('mouseleave', () => {
                    card.classList.remove('shadow-xl');
                });
            });
        });
    </script>
</body>
</html>