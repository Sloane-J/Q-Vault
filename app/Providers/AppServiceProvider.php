<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire; // Add this import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire components in the boot method instead
        Livewire::component('admin.paper-manager', \App\Livewire\Admin\PaperManager::class);
    }
}