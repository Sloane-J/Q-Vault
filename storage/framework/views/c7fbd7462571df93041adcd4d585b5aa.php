<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Dashboard'))]); ?>
    <?php if(auth()->user()->isAdmin()): ?>
            
    <div class="flex flex-col gap-6 mt-6 w-full h-full rounded-xl admin-dashboard-section">
        <!-- Stats Grid -->
        <div class="grid gap-6 md:grid-cols-3 w-full">
            <!-- Total Users -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Total Students</h3>
                <p class="text-3xl font-bold"><?php echo e(\App\Models\User::count()); ?></p>
            </div>
            
    
            <!-- Total Papers -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Total Papers</h3>
                <p class="text-3xl font-bold"><?php echo e(\App\Models\Paper::count()); ?></p>
            </div>
    
            <!-- Departments -->
            <div class="flex flex-col justify-center items-start p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Departments</h3>
                <p class="text-3xl font-bold"><?php echo e(\App\Models\Department::count()); ?></p>
            </div>
        </div>
    
        <!-- Placeholder Section -->
        <div class="relative flex-1 h-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <?php if (isset($component)) { $__componentOriginal1e4630c5daeca7ac226f30794c203a2d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1e4630c5daeca7ac226f30794c203a2d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.placeholder-pattern','data' => ['class' => 'absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('placeholder-pattern'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1e4630c5daeca7ac226f30794c203a2d)): ?>
<?php $attributes = $__attributesOriginal1e4630c5daeca7ac226f30794c203a2d; ?>
<?php unset($__attributesOriginal1e4630c5daeca7ac226f30794c203a2d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1e4630c5daeca7ac226f30794c203a2d)): ?>
<?php $component = $__componentOriginal1e4630c5daeca7ac226f30794c203a2d; ?>
<?php unset($__componentOriginal1e4630c5daeca7ac226f30794c203a2d); ?>
<?php endif; ?>
        </div>
    </div>
    

    <?php elseif(auth()->user()->isStudent()): ?>
        <div class="student-dashboard-section">
            <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Available Papers</h3>
                    <p class="text-2xl font-bold"><?php echo e(\App\Models\Paper::where('student_type_id', auth()->user()->student_type_id)->count()); ?></p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Recent Downloads</h3>
                    <p class="text-2xl font-bold"><?php echo e(\App\Models\Download::where('user_id', auth()->id())->count()); ?></p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Your Department</h3>
                    <p class="text-lg"><?php echo e(auth()->user()->department->name ?? 'Not Assigned'); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

   
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /home/user/Q-Vault/resources/views/dashboard.blade.php ENDPATH**/ ?>