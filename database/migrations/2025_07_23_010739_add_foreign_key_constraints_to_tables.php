<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membersihkan data yang tidak konsisten sebelum menambah foreign key
        
        // 1. Hapus mobil yang user_id nya tidak ada di tabel users
        DB::table('mobils')
            ->whereNotIn('user_id', function($query) {
                $query->select('id')->from('users');
            })
            ->delete();

        // 2. Hapus transaksi yang user_id nya tidak ada di tabel users
        DB::table('transaksis')
            ->whereNotIn('user_id', function($query) {
                $query->select('id')->from('users');
            })
            ->delete();

        // 3. Hapus transaksi yang mobil_id nya tidak ada di tabel mobils
        DB::table('transaksis')
            ->whereNotIn('mobil_id', function($query) {
                $query->select('id')->from('mobils');
            })
            ->delete();

        // 4. Set NULL untuk user_id yang kosong (jika ingin mempertahankan data)
        // DB::table('mobils')->whereNull('user_id')->delete();
        // DB::table('transaksis')->whereNull('user_id')->delete();
        // DB::table('transaksis')->whereNull('mobil_id')->delete();

        // Setelah data bersih, tambahkan foreign key constraints
        Schema::table('mobils', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('mobil_id')
                  ->references('id')
                  ->on('mobils')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mobil_id']);
        });
    }
};