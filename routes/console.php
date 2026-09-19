<?php

use Illuminate\Support\Facades\Schedule;

// Shared hosting has no queue daemon: cPanel cron runs schedule:run every minute
// and this drains the database queue within that minute.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5);

Schedule::call(fn () => cache()->forever('ops.last_schedule_run', now()->toDateTimeString()))
    ->everyMinute()
    ->name('heartbeat');
