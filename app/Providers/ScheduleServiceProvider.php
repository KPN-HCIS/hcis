<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\FetchAndStoreEmployees;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule)
    {
        $schedule->command('fetch:employees')->dailyAt('00:10');
        // $schedule->command('fetch:employees')->everyFiveMinutes();
        $schedule->command('update:employee-access-menu')->dailyAt('00:01');
    }
}