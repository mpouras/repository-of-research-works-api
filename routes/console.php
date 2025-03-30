<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('logs:remove', function() {
    if (PHP_OS_FAMILY === 'Windows') {
        // Windows-specific command to delete log files
        exec('del /f /q ' . base_path('*.log'));
        exec('del /f /q ' . storage_path('logs\\*.log'));
    } else {
        // Unix/Linux/Mac specific command to delete log files
        exec('rm -f ' . storage_path('logs/*.log'));
        exec('rm -f ' . base_path('*.log'));
    }
    $this->comment('Logs have been removed!');
})->describe('Remove log files');
