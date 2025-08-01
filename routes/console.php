<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule auto moderation every 15 minutes
Schedule::command('recipes:auto-moderate')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Auto moderation scheduled task completed successfully');
    })
    ->onFailure(function () {
        Log::error('Auto moderation scheduled task failed');
    });

// Schedule scheduled approvals every 15 minutes
Schedule::command('recipes:process-scheduled-approvals')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Scheduled approvals task completed successfully');
    })
    ->onFailure(function () {
        Log::error('Scheduled approvals task failed');
    });
        

// Schedule daily weather update at 6 AM
Schedule::command('weather:daily-update')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Daily weather update completed successfully');
    })
    ->onFailure(function () {
        Log::error('Daily weather update failed');
    });

// Schedule posts publishing every 5 minutes
Schedule::command('posts:publish-scheduled')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Scheduled posts publishing completed successfully');
    })
    ->onFailure(function () {
        Log::error('Scheduled posts publishing failed');
    });
