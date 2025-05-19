<div class="flex flex-col p-8 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-md space-y-8">

    <!-- Header + Stats -->
    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Student Management</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 w-full md:w-auto">
            <div class="p-5 rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-500/10 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-400">Total Students</h3>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100"><?php echo e($students->total()); ?></p>
            </div>
            <div class="p-5 rounded-xl border border-pink-200 dark:border-pink-700 bg-pink-50 dark:bg-pink-500/10 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-400">Active Today</h3>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100"><?php echo e($activeTodayCount); ?></p>
            </div>
            <div class="p-5 rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-500/10 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-400">New This Week</h3>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100"><?php echo e($newThisWeekCount); ?></p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="relative flex-1">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search students..."
                class="w-full pl-10 py-2.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:outline-none transition"
            />
        </div>

        <div>
            <select
                wire:model.live="perPage"
                class="w-full sm:w-auto py-2.5 px-4 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:outline-none transition"
            >
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800 text-left text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 cursor-pointer" wire:click="sortBy('name')">
                        <div class="flex items-center gap-1">
                            Name
                            <!--[if BLOCK]><![endif]--><?php if($sortField === 'name'): ?>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor"><path d="<?php echo e($sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </th>
                    <th class="px-6 py-4 cursor-pointer" wire:click="sortBy('email')">
                        <div class="flex items-center gap-1">
                            Email
                            <!--[if BLOCK]><![endif]--><?php if($sortField === 'email'): ?>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor"><path d="<?php echo e($sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </th>
                    <th class="px-6 py-4">Last Activity</th>
                    <th class="px-6 py-4">IP Address</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-300 font-semibold">
                                    <?php echo e($student->initials()); ?>

                                </div>
                                <div>
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100"><?php echo e($student->name); ?></div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">Joined <?php echo e($student->created_at->diffForHumans()); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-800 dark:text-neutral-100"><?php echo e($student->email); ?></td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400"><?php echo e($this->getLastActivity($student->id)); ?></td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400"><?php echo e($this->getLastIpAddress($student->id)); ?></td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center space-x-2">
                                <button wire:click="editStudent(<?php echo e($student->id); ?>)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete(<?php echo e($student->id); ?>)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                            No students found matching your search.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-6">
        <?php echo e($students->links()); ?>

    </div>
</div><?php /**PATH /home/user/Q-Vault/resources/views/livewire/student-table.blade.php ENDPATH**/ ?>