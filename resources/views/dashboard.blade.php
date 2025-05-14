<x-layouts.app :title="__('Dashboard')">
    @if(auth()->user()->isAdmin())
            
    <div class="flex flex-col gap-6 mt-6 w-full h-full rounded-xl admin-dashboard-section">
        <!-- Stats Grid -->
        <div class="grid gap-6 md:grid-cols-3 w-full">
            <!-- Total Users -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class=" text-lg font-semibold mb-1">Total Students</h3>
                <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
            </div>
            
    
            <!-- Total Papers -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Total Papers</h3>
                <p class="text-3xl font-bold">{{ \App\Models\Paper::count() }}</p>
            </div>
    
            <!-- Departments -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Departments</h3>
                <p class="text-3xl font-bold">{{ \App\Models\Department::count() }}</p>
            </div>
        </div>
    
        <!-- Placeholder Section -->
        
            <livewire:student-table/>   
       
    </div>
    

    @elseif(auth()->user()->isStudent())
        <div class="student-dashboard-section">
            <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Available Papers</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Paper::where('student_type_id', auth()->user()->student_type_id)->count() }}</p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Recent Downloads</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Download::where('user_id', auth()->id())->count() }}</p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Your Department</h3>
                    <p class="text-lg">{{ auth()->user()->department->name ?? 'Not Assigned' }}</p>
                </div>
            </div>
            <livewire:student.download-history />
        </div>
    @endif

   
</x-layouts.app>