<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabel_kunjungan_ptm', function (Blueprint $table) {
            $table->id();
            $table->uuid('idSkrining')->unique();
            $table->uuid('idPelayanan')->unique();
            $table->uuid('idLoket')->unique();
            $table->string('nikPasien', 20);
            $table->datetime('tanggal_skrining');
            $table->string('dokter', 50);
            $table->string('fasyankes', 50);
            $table->string('jenis_kunjungan', 50);
            $table->string('keluhan_utama', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_kunjungan_ptm');
    }
};
