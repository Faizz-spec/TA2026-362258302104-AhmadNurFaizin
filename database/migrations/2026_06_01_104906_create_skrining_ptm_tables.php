<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skrining_ptm', function (Blueprint $table) {
            $table->id();
            $table->string('idpelayanan', 36)->unique();
            $table->unsignedBigInteger('loket_id')->index();
            $table->string('kd_poli', 20)->nullable()->index();
            $table->date('tanggal_skrining')->nullable();
            $table->string('jenis_kunjungan', 50)->nullable();
            $table->string('kd_dokter', 50)->nullable();
            $table->string('dokter')->nullable();
            $table->string('fasyankes')->nullable();
            $table->text('keluhan_utama')->nullable();
            $table->string('doc_num')->nullable()->unique();
            $table->date('comp_date')->nullable();
            $table->string('comp_status', 50)->nullable();
            $table->string('comp_title')->nullable();
            $table->text('catatan_penutup')->nullable();
            $table->timestamps();

            $table->index(['loket_id', 'tanggal_skrining']);
        });

        Schema::create('skrining_ptm_subjektif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->string('merokok', 20)->nullable();
            $table->string('status_merokok', 50)->nullable();
            $table->unsignedSmallInteger('btg_rokok')->nullable();
            $table->unsignedSmallInteger('lama_rokok')->nullable();
            $table->string('paparan_rokok', 50)->nullable();
            $table->string('gula', 50)->nullable();
            $table->string('garam', 50)->nullable();
            $table->string('minyak', 50)->nullable();
            $table->string('sayur', 50)->nullable();
            $table->string('aktivitas', 50)->nullable();
            $table->string('alkohol', 50)->nullable();
            $table->string('r_htn', 50)->nullable();
            $table->string('r_dm', 50)->nullable();
            $table->string('r_stroke', 50)->nullable();
            $table->text('r_pribadi_lain')->nullable();
            $table->text('r_keluarga')->nullable();
            $table->text('obat')->nullable();
            $table->string('kesiapan', 50)->nullable();
            $table->string('dukung', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('skrining_ptm_objektif_metabolik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->decimal('bb', 5, 1)->nullable();
            $table->decimal('tb', 5, 1)->nullable();
            $table->decimal('imt', 5, 1)->nullable();
            $table->string('imt_interp', 50)->nullable();
            $table->decimal('lp', 5, 1)->nullable();
            $table->string('lp_interp', 50)->nullable();
            $table->unsignedSmallInteger('td_s')->nullable();
            $table->unsignedSmallInteger('td_d')->nullable();
            $table->string('td_interp', 100)->nullable();
            $table->unsignedSmallInteger('nadi')->nullable();
            $table->unsignedSmallInteger('napas')->nullable();
            $table->decimal('suhu', 4, 1)->nullable();
            $table->unsignedSmallInteger('gd_puasa')->nullable();
            $table->unsignedSmallInteger('gd_sewaktu')->nullable();
            $table->decimal('hba1c', 4, 1)->nullable();
            $table->unsignedSmallInteger('gd2pp')->nullable();
            $table->string('dm_interp', 50)->nullable();
            $table->decimal('asam_urat', 4, 1)->nullable();
            $table->string('urat_interp', 50)->nullable();
            $table->unsignedSmallInteger('koltot')->nullable();
            $table->string('koltot_i', 50)->nullable();
            $table->unsignedSmallInteger('hdl')->nullable();
            $table->string('hdl_i', 50)->nullable();
            $table->unsignedSmallInteger('ldl')->nullable();
            $table->string('ldl_i', 50)->nullable();
            $table->unsignedSmallInteger('tg')->nullable();
            $table->string('tg_i', 50)->nullable();
            $table->decimal('risiko_lab', 5, 2)->nullable();
            $table->decimal('risiko_nolab', 5, 2)->nullable();
            $table->string('kat_risiko', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('skrining_ptm_objektif_indera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->string('visus_od', 20)->nullable();
            $table->string('pinhole_od', 20)->nullable();
            $table->string('visus_os', 20)->nullable();
            $table->string('pinhole_os', 20)->nullable();
            $table->string('sa_od', 50)->nullable();
            $table->string('rf_od', 50)->nullable();
            $table->string('st_od', 50)->nullable();
            $table->unsignedSmallInteger('gio_od')->nullable();
            $table->string('sa_os', 50)->nullable();
            $table->string('rf_os', 50)->nullable();
            $table->string('st_os', 50)->nullable();
            $table->unsignedSmallInteger('gio_os')->nullable();
            $table->boolean('retino_od')->default(false);
            $table->boolean('retino_os')->default(false);
            $table->boolean('tuli_kanan')->default(false);
            $table->boolean('omsk_kanan')->default(false);
            $table->boolean('serumen_kanan')->default(false);
            $table->boolean('presbi_kanan')->default(false);
            $table->string('bisik_kanan', 50)->nullable();
            $table->boolean('tuli_kiri')->default(false);
            $table->boolean('omsk_kiri')->default(false);
            $table->boolean('serumen_kiri')->default(false);
            $table->boolean('presbi_kiri')->default(false);
            $table->string('bisik_kiri', 50)->nullable();
            $table->string('ppok1', 20)->nullable();
            $table->string('ppok2', 20)->nullable();
            $table->string('ppok3', 20)->nullable();
            $table->string('ppok4', 20)->nullable();
            $table->unsignedTinyInteger('skor_puma')->nullable();
            $table->string('hasil_puma', 50)->nullable();
            $table->boolean('pink_puffer')->default(false);
            $table->boolean('pursed_lips')->default(false);
            $table->boolean('otot_bantu')->default(false);
            $table->boolean('pelebaran_sela')->default(false);
            $table->boolean('barrel_chest')->default(false);
            $table->boolean('hipersonor')->default(false);
            $table->boolean('vesikuler')->default(false);
            $table->boolean('ronki')->default(false);
            $table->boolean('ekspirasi_pjg')->default(false);
            $table->decimal('kv', 5, 1)->nullable();
            $table->decimal('kvp', 5, 1)->nullable();
            $table->decimal('vep1', 5, 1)->nullable();
            $table->timestamps();
        });

        Schema::create('skrining_ptm_objektif_genetik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->string('inspekulo', 50)->nullable();
            $table->string('iva', 50)->nullable();
            $table->string('hpv', 50)->nullable();
            $table->string('sadanis', 50)->nullable();
            $table->string('usg_py', 50)->nullable();
            $table->boolean('krioterapi')->default(false);
            $table->boolean('thermal')->default(false);
            $table->boolean('tca')->default(false);
            $table->boolean('rujuk_serviks')->default(false);
            $table->string('kp1', 50)->nullable();
            $table->string('kp2', 50)->nullable();
            $table->string('kp3', 50)->nullable();
            $table->string('kp4', 50)->nullable();
            $table->string('kp5', 50)->nullable();
            $table->string('kp6', 50)->nullable();
            $table->string('kp7', 50)->nullable();
            $table->string('hasil_kp', 50)->nullable();
            $table->string('kkr1', 50)->nullable();
            $table->string('kkr2', 50)->nullable();
            $table->string('hasil_kkr', 50)->nullable();
            $table->string('colok_dubur', 50)->nullable();
            $table->string('darah_samar', 50)->nullable();
            $table->decimal('hb', 4, 1)->nullable();
            $table->decimal('mcv', 5, 1)->nullable();
            $table->decimal('mch', 5, 1)->nullable();
            $table->decimal('rbc', 5, 2)->nullable();
            $table->decimal('rdw', 5, 1)->nullable();
            $table->string('hasil_thalasemia', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('skrining_ptm_assessment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->string('diagnosis_utama')->nullable();
            $table->string('status_klinis', 50)->nullable();
            $table->text('diagnosis_sekunder')->nullable();
            $table->text('ringkasan_klinis')->nullable();
            $table->text('catatan_assessment')->nullable();
            $table->json('assessment_terpilih')->nullable();
            $table->string('diagnosis_utama_saran')->nullable();
            $table->string('saran_kategori', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('skrining_ptm_status_pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skrining_ptm_id')->constrained('skrining_ptm')->cascadeOnDelete();
            $table->string('kategori_risiko_ptm', 50)->nullable();
            $table->string('status_kasus', 50)->nullable();
            $table->string('keputusan_klinis', 50)->nullable();
            $table->text('catatan_kesimpulan')->nullable();
            $table->string('kondisi_keluar', 50)->nullable();
            $table->string('cara_keluar', 50)->nullable();
            $table->date('jadwal_kontrol')->nullable();
            $table->string('rencana_rujuk', 50)->nullable();
            $table->string('transport', 50)->nullable();
            $table->text('saran_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skrining_ptm_status_pasien');
        Schema::dropIfExists('skrining_ptm_assessment');
        Schema::dropIfExists('skrining_ptm_objektif_genetik');
        Schema::dropIfExists('skrining_ptm_objektif_indera');
        Schema::dropIfExists('skrining_ptm_objektif_metabolik');
        Schema::dropIfExists('skrining_ptm_subjektif');
        Schema::dropIfExists('skrining_ptm');
    }
};
