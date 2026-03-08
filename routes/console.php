<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
| Laporan harian gudang dikirim ke Telegram setiap pukul 08:00 WIB.
| Berisi: PO jatuh tempo H-3 & stok kritis (≤ 10).
|
| Untuk menjalankan scheduler, tambahkan cron job (Linux) atau Task Scheduler (Windows):
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
| Atau untuk test manual:
|   php artisan app:send-daily-gudang-report
*/
Schedule::command('app:send-daily-gudang-report')->dailyAt('08:00');
