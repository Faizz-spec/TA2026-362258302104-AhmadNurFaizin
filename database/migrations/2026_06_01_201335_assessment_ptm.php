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
        Schema::create('assessment_ptm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();

            $table->json('masalah_hasil_skrining')->nullable();
            $table->json('ringkasan_temuan')->nullable();

            $table->string('diagnosis_utama')->nullable();
            $table->string('diagnosis_utama_saran')->nullable();
            $table->string('status_klinis', 50)->nullable();
            $table->text('catatan_diagnosis')->nullable();

            $table->string('kategori_risiko', 50)->nullable();
            $table->text('ringkasan_klinis')->nullable();
            $table->text('catatan_assessment')->nullable();

            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_ptm');
        //
    }
};
