<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Q-Vault - Your trusted source for past exam papers">

        <title>Q-Vault | Past Exam Paper Management System</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.png" type="image/png>
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700|space-grotesk:400,500,600,700" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f0f9ff',
                                100: '#e0f2fe', 
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                            },
                            gray: {
                                50: '#f8fafc',
                                100: '#f1f5f9',
                                200: '#e2e8f0',
                                300: '#cbd5e1',
                                400: '#94a3b8',
                                500: '#64748b',
                                600: '#475569',
                                700: '#334155',
                                800: '#1e293b',
                                900: '#0f172a',
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            display: ['Space Grotesk', 'sans-serif'],
                        },
                        animation: {
                            'fade-in': 'fadeIn 0.5s ease-out',
                            'slide-up': 'slideUp 0.5s ease-out',
                            'slide-down': 'slideDown 0.5s ease-out',
                            'slide-in-right': 'slideInRight 0.5s ease-out',
                            'float': 'float 6s ease-in-out infinite',
                        },
                        keyframes: {
                            fadeIn: {
                                '0%': { opacity: '0' },
                                '100%': { opacity: '1' },
                            },
                            slideUp: {
                                '0%': { transform: 'translateY(20px)', opacity: '0' },
                                '100%': { transform: 'translateY(0)', opacity: '1' },
                            },
                            slideDown: {
                                '0%': { transform: 'translateY(-20px)', opacity: '0' },
                                '100%': { transform: 'translateY(0)', opacity: '1' },
                            },
                            slideInRight: {
                                '0%': { transform: 'translateX(-20px)', opacity: '0' },
                                '100%': { transform: 'translateX(0)', opacity: '1' },
                            },
                            float: {
                                '0%, 100%': { transform: 'translateY(0px)' },
                                '50%': { transform: 'translateY(-20px)' },
                            }
                        }
                    }
                }
            }
        </script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            .hero-gradient {
                background: linear-gradient(135deg, #498dd6 0%, #154a91 50%, #042142 100%);
            }
                
            .floating-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.8);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            
            .glass-effect {
                backdrop-filter: blur(20px);
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen" x-data="{ isLoaded: false }" x-init="setTimeout(() => isLoaded = true, 100)">
        
        <div class="relative overflow-hidden" :class="{ 'opacity-0': !isLoaded, 'animate-fade-in': isLoaded }">
            <!-- Navigation Bar -->
            <header class="relative z-20 w-full py-4 px-6 md:px-8" x-data="{ scrolled: false }" @scroll.window="scrolled = window.pageYOffset > 20">
                <div class="max-w-6xl mx-auto">
                    <div class="flex items-center justify-between p-4 rounded-2xl transition-all duration-300" 
                         :class="{ 'bg-white/80 backdrop-blur-md shadow-lg': scrolled }">
                        <!-- Logo -->
                        <div class="flex items-center group">
                            <div class="bg-primary-500 p-2 rounded-xl group-hover:bg-primary-600 transition-all duration-300 shadow-lg">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L4 6V12C4 15.31 7.12 19.43 12 22C16.88 19.43 20 15.31 20 12V6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <span class="ml-3 text-2xl font-bold font-display text-gray-900">Q-Vault</span>
                        </div>
                        
                        <!-- Auth Navigation -->
                        @if (Route::has('login'))
                            <nav class="flex items-center gap-3">
                                @auth
                                    <a href="{{ url('/dashboard') }}" 
                                       class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-all duration-300 rounded-xl hover:bg-gray-100">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-all duration-300 rounded-xl hover:bg-gray-100">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" 
                                           class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                                            Get Started
                                        </a>
                                    @endif
                                @endauth
                            </nav>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <main class="relative hero-gradient min-h-screen">
                <!-- Floating Elements -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-20 right-20 w-32 h-32 bg-primary-200 rounded-full opacity-20 animate-float"></div>
                    <div class="absolute top-40 left-16 w-24 h-24 bg-primary-300 rounded-full opacity-30 animate-float" style="animation-delay: 1s;"></div>
                    <div class="absolute bottom-32 right-32 w-20 h-20 bg-primary-400 rounded-full opacity-25 animate-float" style="animation-delay: 2s;"></div>
                </div>

                <div class="relative z-10 max-w-6xl mx-auto px-6 md:px-8 pt-12 pb-20">
                    <div class="grid md:grid-cols-2 gap-12 items-center min-h-[80vh]">
                        <!-- Hero Content -->
                        <div class="text-center md:text-left space-y-8" x-show="isLoaded" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="space-y-6">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-display leading-tight text-gray-100">
                                    Papers that help you 
                                    <span class="text-primary-500">stay focused</span>
                                </h1>
                                <p class="text-lg md:text-xl text-gray-300 max-w-lg leading-relaxed">
                                    Your comprehensive solution for accessing, managing, and studying past exam papers. Streamline your preparation and achieve better results.
                                </p>
                            </div>
                            
                            <!-- Email Input Section -->
                            <div class="flex flex-col sm:flex-row gap-3 max-w-md">
                                <div class="flex-1">
                                    <input type="email" 
                                           placeholder="Your email address"
                                           class="w-full px-4 py-3 text-gray-800 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 shadow-sm">
                                </div>
                                @auth
                                    <a href="{{ url('/dashboard') }}" 
                                       class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl whitespace-nowrap">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" 
                                       class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl whitespace-nowrap">
                                        Get Started
                                    </a>
                                @endauth
                            </div>
                        </div>
                        
                        <!-- Hero Visual -->
                        <div class="relative" x-show="isLoaded" x-transition:enter="transition duration-700 delay-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <!-- Main Card -->
                            <div class="relative">
                                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-200">
                                    <!-- Profile Section -->
                                    <div class="flex items-center mb-6">
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-800">Student Dashboard</h3>
                                            <p class="text-sm text-gray-500">Access your papers</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Paper Preview -->
                                    <div class="space-y-4 mb-6">
                                        <div class="aspect-[4/3] bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 relative">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="absolute top-3 right-3 bg-primary-100 text-primary-700 px-2 py-1 rounded-lg text-xs font-medium">
                                                PDF
                                            </div>
                                        </div>
                                        
                                        <!-- Paper Info -->
                                        <div class="space-y-3">
                                            <div class="h-4 bg-gray-200 rounded-full w-3/4"></div>
                                            <div class="h-3 bg-gray-100 rounded-full"></div>
                                            <div class="h-3 bg-gray-100 rounded-full w-5/6"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <button class="flex-1 bg-primary-500 text-white py-2.5 rounded-xl font-medium hover:bg-primary-600 transition-colors duration-300">
                                            Download
                                        </button>
                                        <button class="px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Floating Mini Cards -->
                                <div class="absolute -top-4 -right-4 floating-card rounded-2xl p-4 shadow-lg animate-float">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Verified</span>
                                    </div>
                                </div>
                                
                                <div class="absolute -bottom-6 -left-6 floating-card rounded-2xl p-4 shadow-lg animate-float" style="animation-delay: 1s;">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Recent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Featured Papers Section -->
                    <div class="mt-20" x-show="isLoaded">
                        <div class="text-center mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-100 mb-4">Featured Papers</h2>
                            <p class="text-gray-400 max-w-2xl mx-auto">Discover the most popular and recently added exam papers across all departments</p>
                        </div>
                        
                        <div class="grid md:grid-cols-3 gap-6">
                            <!-- Paper Card 1 -->
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group"
                                 x-transition:enter="transition duration-500 delay-100" 
                                 x-transition:enter-start="opacity-0 transform translate-y-4" 
                                 x-transition:enter-end="opacity-100 transform translate-y-0">
                                <div class="aspect-[4/3] bg-gradient-to-br from-purple-100 to-purple-200 relative overflow-hidden">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-white/90 text-purple-700 px-2 py-1 rounded-lg text-xs font-medium">
                                        2023
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-primary-600 transition-colors duration-300">Computer Science Final</h3>
                                    <p class="text-sm text-gray-500 mb-4">Department of Computing • Level 300</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">1,250 downloads</span>
                                        <button class="text-primary-500 hover:text-primary-600 transition-colors duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Paper Card 2 -->
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group"
                                 x-transition:enter="transition duration-500 delay-200" 
                                 x-transition:enter-start="opacity-0 transform translate-y-4" 
                                 x-transition:enter-end="opacity-100 transform translate-y-0">
                                <div class="aspect-[4/3] bg-gradient-to-br from-green-100 to-green-200 relative overflow-hidden">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-white/90 text-green-700 px-2 py-1 rounded-lg text-xs font-medium">
                                        2024
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-primary-600 transition-colors duration-300">Mathematics Exam</h3>
                                    <p class="text-sm text-gray-500 mb-4">Department of Mathematics • Level 200</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">890 downloads</span>
                                        <button class="text-primary-500 hover:text-primary-600 transition-colors duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Paper Card 3 -->
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group"
                                 x-transition:enter="transition duration-500 delay-300" 
                                 x-transition:enter-start="opacity-0 transform translate-y-4" 
                                 x-transition:enter-end="opacity-100 transform translate-y-0">
                                <div class="aspect-[4/3] bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-white/90 text-blue-700 px-2 py-1 rounded-lg text-xs font-medium">
                                        2023
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-primary-600 transition-colors duration-300">Engineering Mechanics</h3>
                                    <p class="text-sm text-gray-500 mb-4">Department of Engineering • Level 400</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">1,520 downloads</span>
                                        <button class="text-primary-500 hover:text-primary-600 transition-colors duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Loading animation -->
        <div class="fixed inset-0 bg-gray-50 z-50 flex items-center justify-center transition-opacity duration-500"
             :class="{ 'opacity-100': !isLoaded, 'opacity-0 pointer-events-none': isLoaded }">
            <div class="flex items-center space-x-2">
                <svg class="animate-spin h-8 w-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-primary-600 font-medium">Loading Q-Vault...</span>
            </div>
        </div>
    </body>
</html>