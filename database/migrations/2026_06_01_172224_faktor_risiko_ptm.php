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
        Schema::create('faktor_risiko_ptm', function (Blueprint $table) {
            $table->id();

            $table->uuid('skriningID')->unique();
            $table->foreign('skriningID')
                ->references('idSkrining')
                ->on('tabel_kunjungan_ptm')
                ->cascadeOnDelete();
            $table->uuid('pelayananId')->unique();
            // Merokok
            $table->string('merokok', 20)->nullable(); // ya/tidak
            $table->string('status_merokok', 30)->nullable(); // never/current/ex
            $table->unsignedSmallInteger('btg_rokok')->nullable();
            $table->unsignedSmallInteger('lama_rokok')->nullable();
            $table->string('paparan_rokok', 30)->nullable(); // tidak/kadang/setiap_hari

            // Pola makan & aktivitas
            $table->string('gula', 30)->nullable();
            $table->string('garam', 30)->nullable();
            $table->string('minyak', 30)->nullable();
            $table->string('sayur', 30)->nullable();
            $table->string('aktivitas', 30)->nullable();
            $table->string('alkohol', 20)->nullable();

            // Riwayat pribadi
            $table->boolean('r_pribadi_htn')->default(false);
            $table->boolean('r_pribadi_dm')->default(false);
            $table->boolean('r_pribadi_stroke')->default(false);
            $table->boolean('r_pribadi_jantung')->default(false);

            // Riwayat keluarga
            $table->boolean('r_keluarga_htn')->default(false);
            $table->boolean('r_keluarga_dm')->default(false);
            $table->boolean('r_keluarga_stroke')->default(false);
            $table->boolean('r_keluarga_jantung')->default(false);

            // Obat & dukungan perubahan
            $table->text('obat')->nullable();
            $table->string('kesiapan', 30)->nullable(); // tidak_siap/ragu/siap
            $table->string('dukung', 30)->nullable(); // kurang/cukup/baik

            // Hasil scoring
            $table->unsignedTinyInteger('skor_faktor_risiko')->nullable();
            $table->string('kategori_faktor_risiko', 30)->nullable();
            $table->json('detail_faktor_risiko')->nullable();

            $table->timestamps();
            
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktor_risiko_ptm');
    }
};
