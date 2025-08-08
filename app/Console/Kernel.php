<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Command untuk update transaksi terlambat - jalan setiap hari
        $schedule->command('transaksi:update-late')->daily();
        
        // Contoh scheduling lainnya:
        
        // Jalan setiap jam
        // $schedule->command('emails:send')->hourly();
        
        // Jalan setiap hari jam 1 pagi
        // $schedule->command('backup:database')->dailyAt('01:00');
        
        // Jalan setiap minggu hari Senin jam 8 pagi
        // $schedule->command('reports:weekly')->weeklyOn(1, '8:00');
        
        // Jalan setiap bulan tanggal 1 jam 12 siang
        // $schedule->command('invoices:monthly')->monthlyOn(1, '12:00');
        
        // Jalan setiap 5 menit
        // $schedule->command('queue:work')->everyFiveMinutes();
        
        // Conditional scheduling - hanya jalan di environment production
        // $schedule->command('backup:run')
        //          ->daily()
        //          ->when(function () {
        //              return app()->environment('production');
        //          });
        
        // Scheduling dengan output ke log file
        // $schedule->command('transaksi:update-late')
        //          ->daily()
        //          ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        // Scheduling dengan email notification jika gagal
        // $schedule->command('transaksi:update-late')
        //          ->daily()
        //          ->emailOutputOnFailure('admin@example.com');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}