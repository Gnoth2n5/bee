<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule auto moderation every 2 minutes (for testing)
Schedule::command('recipes:auto-moderate')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Auto moderation scheduled task completed successfully');
    })
    ->onFailure(function () {
        Log::error('Auto moderation scheduled task failed');
    });

// Schedule scheduled approvals every 2 minutes (for testing)
Schedule::command('recipes:process-scheduled-approvals')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Scheduled approvals task completed successfully');
    })
    ->onFailure(function () {
        Log::error('Scheduled approvals task failed');
    });