<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Q-Vault - Your trusted source for past exam papers">

        <title>Q-Vault | Past Exam Paper Management System</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:200,300,400,500,600,700|space-grotesk:400,500,600,700" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            dark: {
                                900: '#000000',
                                800: '#0A0A0A',
                                700: '#141414',
                                600: '#1A1A1A',
                                500: '#222222',
                                400: '#2A2A2A',
                            },
                            accent: {
                                500: '#3B82F6', // Blue
                                400: '#60A5FA',
                            }
                        },
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                            display: ['Space Grotesk', 'sans-serif'],
                        },
                        animation: {
                            'fade-in': 'fadeIn 0.5s ease-out',
                            'slide-up': 'slideUp 0.5s ease-out',
                            'slide-down': 'slideDown 0.5s ease-out',
                            'slide-in-right': 'slideInRight 0.5s ease-out',
                            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
                        }
                    }
                }
            }
        </script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            .bg-grid {
                background-size: 40px 40px;
                background-image: 
                    linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            }
            
            .glow {
                box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
            }
            
            .hover-glow:hover {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
            }
            
            /* Custom gradient overlay */
            .gradient-mask {
                -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
                mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
            }
        </style>
    </head>
    <body class="bg-dark-900 text-white min-h-screen" x-data="{ isLoaded: false }" x-init="setTimeout(() => isLoaded = true, 100)">
        <!-- Background grid pattern -->
        <div class="fixed inset-0 bg-grid opacity-20"></div>
        
        <!-- Background gradient -->
        <div class="fixed inset-0 bg-gradient-to-br from-dark-900 via-dark-800 to-dark-700 opacity-80"></div>
        
        <!-- Ambient glow effects -->
        <div class="fixed top-1/4 left-1/4 w-96 h-96 bg-accent-500/10 rounded-full filter blur-3xl animate-pulse-slow"></div>
        <div class="fixed bottom-1/3 right-1/4 w-80 h-80 bg-indigo-500/5 rounded-full filter blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        
        <div class="relative z-10" :class="{ 'opacity-0': !isLoaded, 'animate-fade-in': isLoaded }">
            <!-- Navigation Bar -->
            <header class="w-full py-6 px-6 md:px-8 lg:px-12" x-data="{ scrolled: false }" @scroll.window="scrolled = window.pageYOffset > 20">
                <div class="max-w-7xl mx-auto flex items-center justify-between" :class="{ 'py-2 bg-dark-800/80 backdrop-blur-sm rounded-xl shadow-lg': scrolled }" 
                     x-transition:enter="transition duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100">
                    <!-- Logo -->
                    <div class="flex items-center group">
                        <div class="bg-dark-700 p-2 rounded-md group-hover:bg-dark-600 transition-all duration-300">
                            <svg class="h-8 w-8 text-accent-500 group-hover:text-accent-400 transition-colors duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L4 6V12C4 15.31 7.12 19.43 12 22C16.88 19.43 20 15.31 20 12V6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-2xl font-bold font-display text-white group-hover:text-accent-400 transition-colors duration-300">Q-Vault</span>
                    </div>
                    
                    <!-- Auth Navigation -->
                    <?php if(Route::has('login')): ?>
                        <nav class="flex items-center gap-4">
                            <?php if(auth()->guard()->check()): ?>
                                <a href="<?php echo e(url('/dashboard')); ?>" 
                                   class="px-5 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300 relative overflow-hidden group">
                                    <span class="relative z-10">Dashboard</span>
                                    <span class="absolute inset-x-0 bottom-0 h-0.5 bg-accent-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" 
                                   class="px-5 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300 relative overflow-hidden group">
                                    <span class="relative z-10">Log in</span>
                                    <span class="absolute inset-x-0 bottom-0 h-0.5 bg-accent-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                                </a>
                                <?php if(Route::has('register')): ?>
                                    <a href="<?php echo e(route('register')); ?>" 
                                       class="px-5 py-2 bg-dark-700 hover:bg-dark-600 border border-dark-500 hover:border-accent-500 text-white rounded-md hover-glow text-sm font-medium transition-all duration-300">
                                        Register
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Hero Section -->
            <main class="relative">
                <div class="max-w-7xl mx-auto px-6 md:px-8 lg:px-12 py-12 md:py-20 lg:py-28">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <!-- Hero Content -->
                        <div class="text-center md:text-left" x-show="isLoaded" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-display leading-tight mb-6">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-gray-100 to-gray-300">Unlock Academic</span> 
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-accent-400 to-accent-500 animate-pulse-slow">Excellence</span>
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-gray-100 to-gray-300"> with Past Papers</span>
                            </h1>
                            <p class="text-lg md:text-xl text-gray-400 mb-8 max-w-xl">
                                Your comprehensive solution for accessing, managing, and studying past exam papers. Streamline your preparation and achieve better results.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                                <?php if(auth()->guard()->check()): ?>
                                    <a href="<?php echo e(url('/dashboard')); ?>" 
                                       class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden rounded-md bg-accent-500 text-white font-medium transition duration-300 ease-out border-2 border-accent-500 hover:border-accent-400 shadow-md hover:shadow-accent-500/40">
                                        <span class="absolute inset-0 flex items-center justify-center w-full h-full text-white duration-300 translate-y-full group-hover:translate-y-0 ease">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </span>
                                        <span class="relative block text-white transition-all duration-300 group-hover:translate-y-[-200%]">Go to Dashboard</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('login')); ?>" 
                                       class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden rounded-md bg-accent-500 text-white font-medium transition duration-300 ease-out border-2 border-accent-500 hover:border-accent-400 shadow-md hover:shadow-accent-500/40">
                                        <span class="absolute inset-0 flex items-center justify-center w-full h-full text-white duration-300 translate-y-full group-hover:translate-y-0 ease">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </span>
                                        <span class="relative block text-white transition-all duration-300 group-hover:translate-y-[-200%]">Get Started</span>
                                    </a>
                                    <?php if(Route::has('register')): ?>
                                        <a href="<?php echo e(route('register')); ?>" 
                                           class="relative inline-flex items-center justify-center px-8 py-3 overflow-hidden rounded-md bg-transparent text-accent-400 font-medium transition duration-300 ease-out border-2 border-accent-500 hover:bg-dark-700 group">
                                            <span class="relative">Register Now</span>
                                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent-400 transition-all duration-500 group-hover:w-full"></span>
                                            <span class="absolute right-0 top-0 w-0 h-0.5 bg-accent-400 transition-all duration-500 group-hover:w-full"></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Hero Image -->
                        <div class="relative hidden md:block" x-show="isLoaded" x-transition:enter="transition duration-700 delay-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="absolute inset-0 bg-gradient-to-tr from-accent-500/10 to-indigo-500/10 rounded-2xl transform -rotate-6"></div>
                            <div class="relative bg-dark-700 p-4 rounded-2xl border border-dark-500 shadow-xl transition-all duration-300 hover:shadow-accent-500/20">
                                <div class="aspect-[4/3] bg-dark-600 rounded-md overflow-hidden border border-dark-500">
                                    <svg class="w-full h-full text-dark-400" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor" viewBox="0 0 640 512">
                                        <path d="M480 80C480 35.82 515.8 0 560 0C604.2 0 640 35.82 640 80C640 124.2 604.2 160 560 160C515.8 160 480 124.2 480 80zM0 456.1C0 445.6 2.964 435.3 8.551 426.4L225.3 81.01C231.9 70.42 243.5 64 256 64C268.5 64 280.1 70.42 286.8 81.01L412.7 281.7L460.9 202.7C464.1 196.1 472.2 192 480 192C487.8 192 495 196.1 499.1 202.7L631.1 419.1C636.9 428.6 640 439.7 640 450.9C640 484.6 612.6 512 578.9 512H55.91C25.03 512 0 486.1 0 456.1L0 456.1z" />
                                    </svg>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <div class="h-4 bg-dark-500 rounded-full w-3/4"></div>
                                    <div class="h-4 bg-dark-500 rounded-full"></div>
                                    <div class="h-4 bg-dark-500 rounded-full w-5/6"></div>
                                </div>
                                <div class="mt-4 flex justify-between">
                                    <div class="h-8 bg-accent-500/20 rounded-md w-24"></div>
                                    <div class="h-8 bg-dark-500 rounded-md w-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Key Features Badges -->
                    <div class="mt-16 md:mt-24 grid grid-cols-2 sm:grid-cols-4 gap-4" x-show="isLoaded">
                        <div class="feature-badge px-4 py-3 bg-dark-800/80 backdrop-blur-sm rounded-xl border border-dark-600 transition-all duration-300 hover:border-accent-500/50 hover:bg-dark-700/80 group"
                             x-show="isLoaded" 
                             x-transition:enter="transition duration-500 delay-100" 
                             x-transition:enter-start="opacity-0 transform translate-y-4" 
                             x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="flex items-center">
                                <span class="flex-shrink-0 bg-accent-500/20 group-hover:bg-accent-500/30 p-2 rounded-md transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.415l2.261-2.261A4 4 0 1011 5z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-200 group-hover:text-white transition-colors duration-300">Easy Search & Filters</span>
                            </div>
                        </div>
                        <div class="feature-badge px-4 py-3 bg-dark-800/80 backdrop-blur-sm rounded-xl border border-dark-600 transition-all duration-300 hover:border-accent-500/50 hover:bg-dark-700/80 group"
                             x-show="isLoaded" 
                             x-transition:enter="transition duration-500 delay-200" 
                             x-transition:enter-start="opacity-0 transform translate-y-4" 
                             x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="flex items-center">
                                <span class="flex-shrink-0 bg-accent-500/20 group-hover:bg-accent-500/30 p-2 rounded-md transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                    </svg>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-200 group-hover:text-white transition-colors duration-300">Multiple Departments</span>
                            </div>
                        </div>
                        <div class="feature-badge px-4 py-3 bg-dark-800/80 backdrop-blur-sm rounded-xl border border-dark-600 transition-all duration-300 hover:border-accent-500/50 hover:bg-dark-700/80 group"
                             x-show="isLoaded" 
                             x-transition:enter="transition duration-500 delay-300" 
                             x-transition:enter-start="opacity-0 transform translate-y-4" 
                             x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="flex items-center">
                                <span class="flex-shrink-0 bg-accent-500/20 group-hover:bg-accent-500/30 p-2 rounded-md transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-200 group-hover:text-white transition-colors duration-300">Version Control</span>
                            </div>
                        </div>
                        <div class="feature-badge px-4 py-3 bg-dark-800/80 backdrop-blur-sm rounded-xl border border-dark-600 transition-all duration-300 hover:border-accent-500/50 hover:bg-dark-700/80 group"
                             x-show="isLoaded" 
                             x-transition:enter="transition duration-500 delay-400" 
                             x-transition:enter-start="opacity-0 transform translate-y-4" 
                             x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="flex items-center">
                                <span class="flex-shrink-0 bg-accent-500/20 group-hover:bg-accent-500/30 p-2 rounded-md transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                    </svg>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-200 group-hover:text-white transition-colors duration-300">PDF Preview</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Loading animation -->
        <div class="fixed inset-0 bg-dark-900 z-50 flex items-center justify-center transition-opacity duration-500"
             :class="{ 'opacity-100': !isLoaded, 'opacity-0 pointer-events-none': isLoaded }">
            <svg class="animate-spin h-12 w-12 text-accent-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </body>
</html><?php /**PATH /home/user/Q-Vault/resources/views/welcome.blade.php ENDPATH**/ ?>