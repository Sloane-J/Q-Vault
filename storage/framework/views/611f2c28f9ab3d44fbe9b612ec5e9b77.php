<div class="p-6 bg-white dark:bg-neutral-800 rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
            <?php echo e(__('Department Management')); ?>

        </h2>
        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            <?php echo e(__('Add New Department')); ?>

        </button>
    </div>

    
    <div class="mb-4 flex space-x-4">
        <input 
            wire:model.live="search" 
            type="text" 
            placeholder="<?php echo e(__('Search departments...')); ?>"
            class="flex-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
        />
    </div>

    
    <div class="overflow-x-auto bg-white dark:bg-neutral-800 rounded-lg shadow">
        <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400">
            <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-700 dark:text-neutral-400">
                <tr>
                    <th scope="col" class="px-6 py-3"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="px-6 py-3"><?php echo e(__('Code')); ?></th>
                    <th scope="col" class="px-6 py-3"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="px-6 py-3"><?php echo e(__('Created')); ?></th>
                    <th scope="col" class="px-6 py-3 text-right"><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="<?php echo e($department->id); ?>" class="bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">
                            <?php echo e($department->name); ?>

                        </td>
                        <td class="px-6 py-4">
                            <?php echo e($department->code); ?>

                        </td>
                        <td class="px-6 py-4">
                            <span class="<?php echo e($department->is_active ? 'text-green-600' : 'text-red-600'); ?>">
                                <?php echo e($department->is_active ? __('Active') : __('Inactive')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo e($department->created_at->format('M d, Y')); ?>

                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <button 
                                    wire:click="openEditModal(<?php echo e($department->id); ?>)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="<?php echo e(__('Edit')); ?>"
                                >
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-pencil'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                </button>
                                <button 
                                    wire:click="confirmDelete(<?php echo e($department->id); ?>)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="<?php echo e(__('Delete')); ?>"
                                >
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-neutral-500">
                            <?php echo e(__('No departments found.')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-4">
        <?php echo e($departments->links()); ?>

    </div>

    
    <?php if($showModal): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="relative w-full max-w-md mx-auto my-6">
                <div class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg dark:bg-neutral-800 outline-none focus:outline-none">
                    <div class="flex items-start justify-between p-5 border-b border-solid rounded-t border-neutral-200 dark:border-neutral-700">
                        <h3 class="text-xl font-semibold">
                            <?php echo e($departmentId ? __('Edit Department') : __('Create Department')); ?>

                        </h3>
                        <button 
                            wire:click="$set('showModal', false)"
                            class="float-right p-1 ml-auto text-3xl font-semibold leading-none text-neutral-500 bg-transparent border-0 outline-none opacity-5 focus:outline-none"
                        >
                            <span class="block w-6 h-6 text-2xl text-neutral-500 opacity-5 focus:outline-none">
                                ×
                            </span>
                        </button>
                    </div>
                    
                    <div class="relative flex-auto p-6">
                        <form wire:submit.prevent="saveDepartment">
                            <div class="mb-4">
                                <label for="name" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <?php echo e(__('Department Name')); ?>

                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    id="name"
                                    class="block w-full rounded-md border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                >
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> 
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label for="code" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <?php echo e(__('Department Code')); ?>

                                </label>
                                <input 
                                    type="text" 
                                    wire:model="code"
                                    id="code"
                                    class="block w-full rounded-md border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    required
                                >
                                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> 
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <?php echo e(__('Description')); ?>

                                </label>
                                <textarea 
                                    wire:model="description"
                                    id="description"
                                    rows="3"
                                    class="block w-full rounded-md border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                ></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_active"
                                        class="form-checkbox text-blue-600 rounded dark:bg-neutral-700 dark:border-neutral-600 dark:text-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                                        <?php echo e(__('Active Department')); ?>

                                    </span>
                                </label>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button 
                                    type="button"
                                    wire:click="$set('showModal', false)"
                                    class="px-4 py-2 text-neutral-600 bg-neutral-200 rounded-md hover:bg-neutral-300 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2"
                                >
                                    <?php echo e(__('Cancel')); ?>

                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <?php echo e($departmentId ? __('Update') : __('Create')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="fixed inset-0 z-40 bg-black opacity-25"></div>
    <?php endif; ?>

    
    <?php if($confirmingDeletion): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="relative w-full max-w-md mx-auto my-6">
                <div class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg dark:bg-neutral-800 outline-none focus:outline-none">
                    <div class="flex items-start justify-between p-5 border-b border-solid rounded-t border-neutral-200 dark:border-neutral-700">
                        <h3 class="text-xl font-semibold">
                            <?php echo e(__('Confirm Deletion')); ?>

                        </h3>
                    </div>
                    
                    <div class="relative flex-auto p-6">
                        <p class="text-neutral-600 dark:text-neutral-300">
                            <?php echo e(__('Are you sure you want to delete this department? This action cannot be undone.')); ?>

                        </p>
                        
                        <div class="flex justify-end space-x-4 mt-6">
                            <button 
                                wire:click="$set('confirmingDeletion', false)"
                                class="px-4 py-2 text-neutral-600 bg-neutral-200 rounded-md hover:bg-neutral-300 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2"
                            >
                                <?php echo e(__('Cancel')); ?>

                            </button>
                            <button 
                                wire:click="destroyDepartment"
                                class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                <?php echo e(__('Delete')); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="fixed inset-0 z-40 bg-black opacity-25"></div>
        </div>
    <?php endif; ?>
</div><?php /**PATH /home/user/Q-Vault/resources/views/admin/department.blade.php ENDPATH**/ ?>