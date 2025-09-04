<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\DoctorObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

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
        // Register the User observer for role changes
        User::observe(DoctorObserver::class);

        // Register custom notification channel for appointments
        Notification::extend('custom_appointment', function ($app) {
            return new \App\Channels\AppointmentNotificationChannel();
        });
    }
}
