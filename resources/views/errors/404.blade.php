<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - 404</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(220, 38, 38, 0.3); }
            50% { box-shadow: 0 0 30px rgba(220, 38, 38, 0.5), 0 0 40px rgba(220, 38, 38, 0.3); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-neutral-50 to-neutral-100 dark:from-neutral-900 dark:to-neutral-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Animated 404 Number -->
        <div class="mb-8 float-animation">
            <div class="relative">
                <h1 class="text-9xl md:text-[12rem] font-bold text-transparent bg-gradient-to-br from-red-500 to-red-700 bg-clip-text leading-none">
                    404
                </h1>
                <!-- Glowing backdrop -->
                <div class="absolute inset-0 text-9xl md:text-[12rem] font-bold text-red-500/20 blur-sm -z-10">
                    404
                </div>
            </div>
        </div>

        <!-- Error Icon with Animation -->
        <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-red-100 to-red-200 dark:from-red-700/30 dark:to-red-600/30 rounded-full flex items-center justify-center pulse-glow">
                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                Oops! Page Not Found
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                The page you're looking for seems to have gone missing. It might have been moved, deleted, or you entered the wrong URL.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <!-- Go Back Button -->
            <button onclick="history.back()" 
                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all duration-200 group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </button>

            <!-- Home Button -->
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 dark:from-red-600 dark:to-red-700 dark:hover:from-red-700 dark:hover:to-red-800 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Return Home
            </a>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 p-6 bg-white/50 dark:bg-neutral-800/50 backdrop-blur-sm rounded-xl border border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                Need Help?
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                If you believe this is an error, please contact support or try searching for what you need.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url('/papers') }}" 
                   class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm transition-colors">
                    Browse Papers
                </a>
                <span class="hidden sm:inline text-gray-400">•</span>
                <a href="{{ url('/contact') }}" 
                   class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm transition-colors">
                    Contact Support
                </a>
                <span class="hidden sm:inline text-gray-400">•</span>
                <a href="{{ url('/dashboard') }}" 
                   class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm transition-colors">
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Background decoration -->
    <div class="fixed inset-0 -z-20 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-500/5 rounded-full blur-3xl"></div>
    </div>
</body>
</html>