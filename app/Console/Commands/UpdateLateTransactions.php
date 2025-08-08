<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use Carbon\Carbon;

class UpdateLateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksi:update-late';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status transaksi yang terlambat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update status transaksi terlambat...');
        
        // Ambil transaksi yang terlambat
        $lateTransactions = Transaksi::lateTransactions()->get();
        
        if ($lateTransactions->isEmpty()) {
            $this->info('Tidak ada transaksi yang terlambat.');
            return;
        }
        
        $updatedCount = 0;
        
        foreach ($lateTransactions as $transaction) {
            $transaction->update([
                'status' => Transaksi::STATUS_TERLAMBAT
            ]);
            $updatedCount++;
            
            $this->info("Transaksi ID {$transaction->id} - {$transaction->nama} diupdate ke status Terlambat");
        }
        
        $this->info("Total {$updatedCount} transaksi diupdate ke status Terlambat.");
    }
}