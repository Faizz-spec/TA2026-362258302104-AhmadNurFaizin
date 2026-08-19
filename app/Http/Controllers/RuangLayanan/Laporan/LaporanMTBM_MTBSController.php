<?php

namespace App\Http\Controllers\RuangLayanan\Laporan;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class LaporanMTBM_MTBSController extends Controller
{
    /**
     * Versi ringan/static.
     * Tidak scan semua kolom database, tidak auto-detect tabel baru,
     * tidak pakai Schema::getColumnListing().
     * Hanya tabel fixed dari modul MTBS/MTBM yang memang dipakai controller pelayanan.
     */
    private string $COLL = 'utf8mb4_general_ci';

    // Master puskesmas. Sesuaikan kalau nama tabel/kolom berbeda.
    private string $PUSK_TABLE = 'unit_profiles';
    private string $PUSK_PK = 'unit_id';
    private string $PUSK_NAME = 'nama_unit';

    private ?array $activeSpecsCache = null;

    public function index(Request $request)
    {
        $today = now()->toDateString();

        // Default filter harus sudah nyata di prop Vue, bukan hanya fallback diam-diam di API.
        $filters = [
            'date_from' => $request->filled('date_from') ? (string) $request->get('date_from') : $today,
            'date_to' => $request->filled('date_to') ? (string) $request->get('date_to') : $today,
            'kdPoli' => $request->has('kdPoli') ? (string) $request->get('kdPoli') : '003',
            'keyword' => trim((string) $request->get('keyword', '')),
            'per_page' => (int) $request->get('per_page', 10),
            'puskesmas_id' => $request->get('puskesmas_id', ''),
            'data_scope' => in_array($request->get('data_scope'), ['all', 'module'], true)
                ? (string) $request->get('data_scope')
                : 'all',
        ];

        $puskesmasList = [];

        // Jangan tampilkan pilihan Puskesmas jika loket tidak memiliki kolom relasinya,
        // karena sebelumnya dropdown tetap tampil tetapi filter diam-diam tidak dijalankan.
        if ($this->canJoinPuskesmas()) {
            $puskesmasList = DB::table($this->PUSK_TABLE)
                ->select([
                    DB::raw("`{$this->PUSK_PK}` as id"),
                    DB::raw("`{$this->PUSK_NAME}` as nama"),
                ])
                ->orderBy($this->PUSK_NAME)
                ->get();
        }

        return Inertia::render('Laporan/MTBM_MTBS/LaporanMTBM_MTBS', [
            'filters' => $filters,
            'puskesmasList' => $puskesmasList,
        ]);
    }

    // =========================================================
    // TABEL FIXED YANG DIAMBIL
    // =========================================================
    private function tableSpecs(): array
    {
        return [
            // ================= MTBM =================
            ['module' => 'mtbm', 'table' => 'mtbm_subjective',     'alias' => 'mtbm_s',  'label' => 'MTBM Subjektif',              'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_objective',      'alias' => 'mtbm_o',  'label' => 'MTBM Objektif',               'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_assessment',     'alias' => 'mtbm_a',  'label' => 'MTBM Assessment/Klasifikasi', 'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_planning',       'alias' => 'mtbm_pl', 'label' => 'MTBM Planning',               'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_status_pasien',  'alias' => 'mtbm_st', 'label' => 'MTBM Status Pasien',          'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_diagnosa_medis', 'alias' => 'mtbm_dx', 'label' => 'MTBM Diagnosa Medis',         'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_gizi',            'alias' => 'mtbm_gz', 'label' => 'MTBM Gizi',                   'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_imunisasi',       'alias' => 'mtbm_im', 'label' => 'MTBM Imunisasi',              'latest' => true],
            ['module' => 'mtbm', 'table' => 'mtbm_alergi',          'alias' => 'mtbm_al', 'label' => 'MTBM Alergi',                 'latest' => true],

            // ================= MTBS =================
            // Nama tabel mengikuti controller pelayanan yang sebenarnya.
            ['module' => 'mtbs', 'table' => 'mtbs_subjektif',             'alias' => 'mtbs_s',  'label' => 'MTBS Subjektif',          'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_objektif',              'alias' => 'mtbs_o',  'label' => 'MTBS Objektif',           'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_assessment',            'alias' => 'mtbs_a',  'label' => 'MTBS Assessment',         'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_planning',              'alias' => 'mtbs_pl', 'label' => 'MTBS Planning',           'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_gizi',                  'alias' => 'mtbs_gz', 'label' => 'MTBS Gizi',               'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_statuspasien',          'alias' => 'mtbs_st',     'label' => 'MTBS Status Pasien',          'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_status_pasien',         'alias' => 'mtbs_st2',    'label' => 'MTBS Status Pasien Legacy',   'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_imunisasi_skrining',    'alias' => 'mtbs_im',     'label' => 'MTBS Skrining Imunisasi',     'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_imunisasi',             'alias' => 'mtbs_im_old', 'label' => 'MTBS Imunisasi Legacy',       'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_diagnosa_medis',        'alias' => 'mtbs_dx', 'label' => 'MTBS Diagnosa Medis',     'latest' => true],
            ['module' => 'mtbs', 'table' => 'mtbs_alergi',                'alias' => 'mtbs_al', 'label' => 'MTBS Alergi',             'latest' => true],
        ];
    }

    private function activeTableSpecs(): array
    {
        if ($this->activeSpecsCache !== null) {
            return $this->activeSpecsCache;
        }

        // Ini bukan auto-detect tabel baru. Ini cuma pengaman untuk tabel fixed di atas.
        $this->activeSpecsCache = array_values(array_filter($this->tableSpecs(), function ($s) {
            return Schema::hasTable($s['table']) && Schema::hasColumn($s['table'], 'kunjungan_id');
        }));

        return $this->activeSpecsCache;
    }

    private function aliasExists(string $alias): bool
    {
        foreach ($this->activeTableSpecs() as $s) {
            if ($s['alias'] === $alias) return true;
        }
        return false;
    }

    private function moduleAliases(string $module): array
    {
        return array_values(array_map(
            fn ($s) => $s['alias'],
            array_filter($this->activeTableSpecs(), fn ($s) => $s['module'] === $module)
        ));
    }

    private function allAliases(): array
    {
        return array_values(array_map(fn ($s) => $s['alias'], $this->activeTableSpecs()));
    }

    private function moduleCondition(string $module): string
    {
        $aliases = $this->moduleAliases($module);

        if ($aliases === []) {
            return '0 = 1';
        }

        return '(' . implode(' OR ', array_map(
            fn (string $alias) => "{$alias}.`kunjungan_id` IS NOT NULL",
            $aliases
        )) . ')';
    }

    private function joinedTableMeta(): array
    {
        return array_map(function ($s) {
            return [
                'module' => $s['module'],
                'table' => $s['table'],
                'alias' => $s['alias'],
                'prefix' => $s['alias'] . '__',
                'label' => $s['label'],
            ];
        }, $this->activeTableSpecs());
    }

    // =========================================================
    // KOLOM FIXED YANG DIAMBIL
    // =========================================================
    private function columnsForExport(): array
    {
        return [
            // ================= MTBM =================
            'mtbm_subjective' => [
                'id','kunjungan_id','keluhan_utama','lama_sakit_hari','bisa_minum_menyusu','muntah_semua','kejang',
                'batuk_lama_hari','diare_lama_hari','darah_diare','demam_lama_hari','demam_tiap_hari',
                'pernah_malaria','minum_obat_malaria','campak_3_bulan','nyeri_telinga','cairan_telinga',
                'riwayat_imunisasi','riwayat_asi_makan','keluhan_lain','created_by','updated_by','created_at','updated_at',
            ],
            'mtbm_objective' => [
                'id','kunjungan_id','kesadaran','kejang_saat_ini','tarikan_dinding_dada_umum','stridor','sianosis','nadi_status',
                'spo2','rr','tarikan_dinding_dada','wheezing','mata_cekung','haus_minum_lahap','turgor_kulit','suhu',
                'kaku_kuduk','ruam_campak','dengue_perdarahan','dengue_nyeri_perut','dengue_muntah_terus',
                'bb','tb_pb','lila','edema','status_bb_tb','nyeri_tekan_belakang_telinga','nanah_keluar_telinga',
                'lama_nanah_hari','tampak_pucat','hb','status_ringkas','created_by','updated_by','created_at','updated_at',
            ],
            'mtbm_assessment' => [
                'id','kunjungan_id','pasien_id','klas_infeksi','klas_ikterus','klas_diare','klas_menyusu_bb',
                'klasifikasi_global','status_kegawatan','catatan_assessment','generated_from','generated_at',
                'created_by','updated_by','created_at','updated_at',
            ],
            'mtbm_planning' => [
                'id','kunjungan_id','klasifikasi_global','keputusan','tindakan_items','resep_items','rekomendasi_obat',
                'catatan_planning','konseling_edukasi','kontrol_ulang','rujuk_alasan',
                'created_by','updated_by','created_at','updated_at',
            ],
            'mtbm_status_pasien' => [
                'id','kunjungan_id','status_pulang','tenaga_medis','asal_poli','poli_tujuan','poli_internal','ppk_rujukan',
                'nama_poli','nama_dokter','spesialis','catatan','keterangan','tgl_rencana_berkunjung',
                'created_by','updated_by','created_at','updated_at',
            ],
            'mtbm_diagnosa_medis' => [
                'id','kunjungan_id','pasien_id','diagnosa_id','kdDiag','nmDiag','kasus','keterangan','poli','created_by','created_at','updated_at',
            ],
            'mtbm_gizi' => [
                'id','kunjungan_id','umur_hari','bb','pb','lila','zscore','edema','klasifikasi','tindakan','catatan','created_at','updated_at',
            ],
            'mtbm_imunisasi' => [
                'id','kunjungan_id','pasien_id','jenis_imunisasi','nama_imunisasi','vaksin','status_imunisasi','catatan','created_at','updated_at',
            ],
            'mtbm_alergi' => [
                'id','kunjungan_id','pasien_id','alergi_makanan','alergi_obat','keterangan_alergi','created_by','created_at','updated_at',
            ],

            // ================= MTBS =================
            'mtbs_subjektif' => [
                'id','kunjungan_id','jenis_kunjungan','umur_tahun','umur_bulan','jenis_kelamin','keluhan_utama','keluhan_lain',
                'batuk_lama_hari','napas_cepat','mengi','diare_lama_hari','darah_tinja','demam_lama_hari','demam_tiap_hari',
                'riwayat_malaria','riwayat_campak','nyeri_telinga','cairan_telinga','telinga_lama_hari',
                'riwayat_imunisasi','vitamin_a','riwayat_asi','riwayat_penyakit','hiv_ibu','anamnesis_khusus','created_at','updated_at',
            ],
            'mtbs_objektif' => [
                'id','kunjungan_id','tanda_bahaya','saga_penampilan','saga_napas','saga_sirkulasi','rr','suhu','spo2',
                'bb','tb','lila','lk','pemeriksaan_khusus','status_saga','created_at','updated_at',
            ],
            'mtbs_assessment' => [
                'id','kunjungan_id','pasien_id','batuk','diare','demam','gizi','anemia','klasifikasi_global','status_kegawatan','created_at','updated_at',
            ],
            'mtbs_planning' => [
                'id','kunjungan_id','tindakan_segera','pengobatan','edukasi','catatan_edukasi','kunjungan_ulang_hari','created_by','created_at','updated_at',
            ],
            'mtbs_gizi' => [
                'id','kunjungan_id','umur_bulan','bb','tb','lila','zscore','edema','komplikasi_medis','lemah_menyusu',
                'bb_tidak_naik','syok','diare','klasifikasi','tindakan','catatan','created_at','updated_at',
            ],
            'mtbs_statuspasien' => [
                'id','kunjungan_id','asal_poli','status_pulang','poli_internal_tujuan','tenaga_medis','ppk_rujukan',
                'nama_poli','nama_dokter','spesialis','catatan','tgl_rencana_berkunjung',
                'mulai_melayani','selesai_melayani','created_by','created_at','updated_at',
            ],
            'mtbs_status_pasien' => [
                'id','kunjungan_id','asal_poli','status_pulang','poli_tujuan','poli_internal','tenaga_medis','ppk_rujukan',
                'nama_poli','nama_dokter','spesialis','catatan','keterangan','tgl_rencana_berkunjung',
                'created_by','updated_by','created_at','updated_at',
            ],
            'mtbs_imunisasi_skrining' => [
                'id','kunjungan_id','pasien_id','umur_bulan_total','sumber_verifikasi','vaksin_tercatat','vaksin_belum',
                'status_imunisasi','kondisi_anak','tindak_lanjut','program_pcv','program_je','catatan',
                'created_by','updated_by','created_at','updated_at',
            ],
            'mtbs_imunisasi' => [
                'id','kunjungan_id','tanggal_imunisasi','jenis_imunisasi','nama_imunisasi','imunisasi','vaksin','status_imunisasi','catatan','created_at','updated_at',
            ],
            'mtbs_diagnosa_medis' => [
                'id','kunjungan_id','pasien_id','diagnosa_id','kdDiag','nmDiag','kasus','keterangan','poli','created_by','created_at','updated_at',
            ],
            'mtbs_alergi' => [
                'id','kunjungan_id','pasien_id','alergi_makanan','alergi_obat','keterangan_alergi','created_by','created_at','updated_at',
            ],
        ];
    }

    private function columnsForList(): array
    {
        return [
            'mtbm_subjective' => ['kunjungan_id','keluhan_utama','keluhan_lain','riwayat_imunisasi','riwayat_asi_makan'],
            'mtbm_objective' => ['kunjungan_id','rr','suhu','spo2','bb','tb_pb','lila','status_ringkas'],
            'mtbm_assessment' => ['kunjungan_id','klas_infeksi','klas_ikterus','klas_diare','klas_menyusu_bb','klasifikasi_global','status_kegawatan','catatan_assessment'],
            'mtbm_planning' => ['kunjungan_id','klasifikasi_global','keputusan','tindakan_items','resep_items','rekomendasi_obat','catatan_planning','kontrol_ulang','rujuk_alasan'],
            'mtbm_status_pasien' => ['kunjungan_id','status_pulang','tenaga_medis','poli_tujuan','poli_internal','nama_poli','nama_dokter','catatan','tgl_rencana_berkunjung'],
            'mtbm_diagnosa_medis' => ['kunjungan_id','kdDiag','nmDiag','kasus','keterangan'],
            'mtbm_gizi' => ['kunjungan_id','bb','pb','lila','zscore','klasifikasi'],
            'mtbm_imunisasi' => ['kunjungan_id','jenis_imunisasi','nama_imunisasi','vaksin','status_imunisasi','catatan'],
            'mtbm_alergi' => ['kunjungan_id','alergi_makanan','alergi_obat','keterangan_alergi'],

            'mtbs_subjektif' => ['kunjungan_id','jenis_kunjungan','umur_tahun','umur_bulan','jenis_kelamin','keluhan_utama','keluhan_lain','riwayat_imunisasi','vitamin_a','riwayat_asi'],
            'mtbs_objektif' => ['kunjungan_id','rr','suhu','spo2','bb','tb','lila','lk','status_saga'],
            'mtbs_assessment' => ['kunjungan_id','batuk','diare','demam','gizi','anemia','klasifikasi_global','status_kegawatan'],
            'mtbs_planning' => ['kunjungan_id','tindakan_segera','pengobatan','edukasi','catatan_edukasi','kunjungan_ulang_hari'],
            'mtbs_gizi' => ['kunjungan_id','bb','tb','lila','zscore','edema','klasifikasi','tindakan','catatan'],
            'mtbs_statuspasien' => ['kunjungan_id','status_pulang','tenaga_medis','poli_internal_tujuan','nama_poli','nama_dokter','catatan','tgl_rencana_berkunjung','mulai_melayani','selesai_melayani'],
            'mtbs_status_pasien' => ['kunjungan_id','status_pulang','tenaga_medis','poli_tujuan','poli_internal','nama_poli','nama_dokter','catatan','keterangan','tgl_rencana_berkunjung'],
            'mtbs_imunisasi_skrining' => ['kunjungan_id','umur_bulan_total','sumber_verifikasi','vaksin_tercatat','vaksin_belum','status_imunisasi','kondisi_anak','tindak_lanjut','catatan'],
            'mtbs_imunisasi' => ['kunjungan_id','tanggal_imunisasi','jenis_imunisasi','nama_imunisasi','imunisasi','vaksin','status_imunisasi','catatan'],
            'mtbs_diagnosa_medis' => ['kunjungan_id','kdDiag','nmDiag','kasus','keterangan'],
            'mtbs_alergi' => ['kunjungan_id','alergi_makanan','alergi_obat','keterangan_alergi'],
        ];
    }

    // =========================================================
    // HELPER JOIN / SELECT
    // =========================================================
    private function canReadPuskesmasMaster(): bool
    {
        return Schema::hasTable($this->PUSK_TABLE)
            && Schema::hasColumn($this->PUSK_TABLE, $this->PUSK_PK)
            && Schema::hasColumn($this->PUSK_TABLE, $this->PUSK_NAME);
    }

    private function canJoinPuskesmas(): bool
    {
        return Schema::hasColumn('simpus_loket', 'puskId') && $this->canReadPuskesmasMaster();
    }

    private function detectGenderColumn(): ?string
    {
        foreach (['JENIS_KLMIN', 'JENIS_KELAMIN', 'jenis_kelamin', 'jk', 'JK', 'GENDER', 'gender'] as $col) {
            if (Schema::hasColumn('simpus_pasien', $col)) return $col;
        }
        return null;
    }

    private function castCollate(string $expr): \Illuminate\Database\Query\Expression
    {
        return DB::raw("CAST({$expr} AS CHAR) COLLATE {$this->COLL}");
    }

    private function latestSubquery(string $table, string $alias): string
    {
        if (Schema::hasColumn($table, 'id')) {
            return "(
                select t1.*
                from `{$table}` t1
                join (
                    select `kunjungan_id`, max(`id`) as max_id
                    from `{$table}`
                    group by `kunjungan_id`
                ) t2 on t2.`kunjungan_id` = t1.`kunjungan_id` and t2.`max_id` = t1.`id`
            ) as {$alias}";
        }

        return "`{$table}` as {$alias}";
    }

    private function joinKunjunganTable($q, array $spec): void
    {
        $table = $spec['table'];
        $alias = $spec['alias'];

        $joinTarget = !empty($spec['latest'])
            ? DB::raw($this->latestSubquery($table, $alias))
            : DB::raw("`{$table}` as {$alias}");

        $q->leftJoin(
            $joinTarget,
            $this->castCollate("{$alias}.`kunjungan_id`"),
            '=',
            $this->castCollate('pel.`idpelayanan`')
        );
    }

    private function baseQuery()
    {
        $q = DB::table('simpus_pelayanan as pel')
            ->join(
                'simpus_loket as l',
                $this->castCollate('pel.`loketId`'),
                '=',
                $this->castCollate('l.`idLoket`')
            )
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli');

        if ($this->canJoinPuskesmas()) {
            $q->leftJoin($this->PUSK_TABLE . ' as up', 'up.' . $this->PUSK_PK, '=', 'l.puskId');
        }

        // Wilayah dipakai juga di controller MTBS/MTBM, tapi ini static ringan.
        if (Schema::hasTable('setup_kel')) {
            $q->leftJoin('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            });
        }
        if (Schema::hasTable('setup_kec')) {
            $q->leftJoin('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            });
        }
        if (Schema::hasTable('setup_kab')) {
            $q->leftJoin('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            });
        }
        if (Schema::hasTable('setup_prop')) {
            $q->leftJoin('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP');
        }

        foreach ($this->activeTableSpecs() as $spec) {
            $this->joinKunjunganTable($q, $spec);
        }

        return $q;
    }

    private function addSelectIfColumn(array &$select, string $table, string $alias, string $column, string $as): void
    {
        if (Schema::hasColumn($table, $column)) {
            $select[] = DB::raw("{$alias}.`{$column}` as `{$as}`");
        } else {
            $select[] = DB::raw("NULL as `{$as}`");
        }
    }

    private function baseSelect(): array
    {
        $select = [
            'pel.idpelayanan as kunjungan_id',
            'pel.tglPelayanan',
            'pel.sudahDilayani',
            'l.idLoket',
            'l.kdPoli',
            'l.tglKunjungan',
            'p.ID as pasien_id',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'poli.nmPoli',
        ];

        $this->addSelectIfColumn($select, 'simpus_loket', 'l', 'puskId', 'puskId');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'JENIS_KLMIN', 'JENIS_KLMIN');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'JENIS_KELAMIN', 'JENIS_KELAMIN');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'JK', 'JK');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'TGL_LHR', 'TGL_LHR');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'AGAMA', 'AGAMA');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'alamat', 'alamat');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'no_rt', 'no_rt');
        $this->addSelectIfColumn($select, 'simpus_pasien', 'p', 'no_rw', 'no_rw');

        if ($this->canJoinPuskesmas()) {
            $select[] = DB::raw("up.`{$this->PUSK_NAME}` as puskesmas_nama");
            $select[] = DB::raw("up.`{$this->PUSK_PK}` as puskesmas_id");
        } else {
            $select[] = DB::raw('NULL as puskesmas_nama');
            $select[] = DB::raw('NULL as puskesmas_id');
        }

        $select[] = Schema::hasTable('setup_kel') ? DB::raw('kel.nama_kel as nama_kel') : DB::raw('NULL as nama_kel');
        $select[] = Schema::hasTable('setup_kec') ? DB::raw('kec.nama_kec as nama_kec') : DB::raw('NULL as nama_kec');
        $select[] = Schema::hasTable('setup_kab') ? DB::raw('kab.nama_kab as nama_kab') : DB::raw('NULL as nama_kab');
        $select[] = Schema::hasTable('setup_prop') ? DB::raw('prop.nama_prop as nama_prop') : DB::raw('NULL as nama_prop');

        $select[] = DB::raw('CASE WHEN ' . $this->moduleCondition('mtbs') . ' THEN 1 ELSE 0 END as has_mtbs_data');
        $select[] = DB::raw('CASE WHEN ' . $this->moduleCondition('mtbm') . ' THEN 1 ELSE 0 END as has_mtbm_data');

        return $select;
    }

    private function addStaticColumns($q, array $columnMap): void
    {
        $activeByTable = [];
        foreach ($this->activeTableSpecs() as $spec) {
            $activeByTable[$spec['table']] = $spec['alias'];
        }

        $added = [];
        foreach ($columnMap as $table => $columns) {
            if (!isset($activeByTable[$table])) continue;

            $alias = $activeByTable[$table];
            foreach ($columns as $column) {
                $key = "{$alias}__{$column}";
                if (isset($added[$key])) continue;
                if (!Schema::hasColumn($table, $column)) continue;

                $q->addSelect(DB::raw("{$alias}.`{$column}` as `{$key}`"));
                $added[$key] = true;
            }
        }
    }

    // =========================================================
    // FILTER
    // =========================================================
    private function applyFilters($q, Request $request): array
    {
        $kdPoli = trim((string) $request->get('kdPoli', '003'));
        $keyword = trim((string) $request->get('keyword', ''));
        $puskesmasId = $request->get('puskesmas_id');
        $dataScope = strtolower(trim((string) $request->get('data_scope', 'all')));
        if (!in_array($dataScope, ['all', 'module'], true)) {
            $dataScope = 'all';
        }

        $today = now()->toDateString();
        $dateFrom = trim((string) $request->get('date_from', ''));
        $dateTo = trim((string) $request->get('date_to', ''));

        // Default benar-benar hari ini. Jika hanya satu sisi diisi, jadikan filter satu hari.
        if ($dateFrom === '' && $dateTo === '') {
            $dateFrom = $today;
            $dateTo = $today;
        } elseif ($dateFrom === '') {
            $dateFrom = $dateTo;
        } elseif ($dateTo === '') {
            $dateTo = $dateFrom;
        }

        // Lindungi endpoint dari tanggal invalid yang dikirim langsung lewat URL/API.
        try {
            $from = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $dateTo)->startOfDay();
        } catch (\Throwable $e) {
            $from = now()->startOfDay();
            $to = now()->startOfDay();
        }

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        $dateFrom = $from->toDateString();
        $dateTo = $to->toDateString();

        if ($kdPoli !== '') {
            $q->where('l.kdPoli', $kdPoli);
        }

        /*
         * Dasar periode laporan adalah tanggal kunjungan pasien (simpus_loket.tglKunjungan),
         * bukan tglPelayanan. tglPelayanan dapat NULL atau berubah saat planning disimpan,
         * sehingga kunjungan lama bisa hilang dari laporan walaupun rentang tanggal sudah benar.
         * Jika data lama tidak memiliki tglKunjungan, baru fallback ke tglPelayanan.
         */
        $nextDay = $to->copy()->addDay();

        $q->where(function ($dateQuery) use ($from, $nextDay) {
            $dateQuery
                ->where(function ($byVisitDate) use ($from, $nextDay) {
                    $byVisitDate->whereNotNull('l.tglKunjungan')
                        ->where('l.tglKunjungan', '>=', $from)
                        ->where('l.tglKunjungan', '<', $nextDay);
                })
                ->orWhere(function ($byServiceDate) use ($from, $nextDay) {
                    $byServiceDate->whereNull('l.tglKunjungan')
                        ->where('pel.tglPelayanan', '>=', $from)
                        ->where('pel.tglPelayanan', '<', $nextDay);
                });
        });

        if ($puskesmasId !== null && $puskesmasId !== '' && $this->canJoinPuskesmas()) {
            $q->where('l.puskId', $puskesmasId);
        }

        if ($keyword !== '') {
            $q->where(function ($w) use ($keyword) {
                $w->where('p.NO_MR', 'like', "%{$keyword}%")
                    ->orWhere('p.NAMA_LGKP', 'like', "%{$keyword}%")
                    ->orWhere('p.NIK', 'like', "%{$keyword}%");
            });
        }

        // Default 'all' menampilkan semua kunjungan KIA pada poli/tanggal terpilih.
        // Pilih 'module' jika hanya ingin kunjungan yang sudah memiliki data MTBS/MTBM.
        if ($dataScope === 'module') {
            $this->whereAnyAliasHasKunjungan($q, $this->allAliases());
        }

        return [$dateFrom, $dateTo, $kdPoli, $puskesmasId, $dataScope];
    }

    private function whereAnyAliasHasKunjungan($q, array $aliases): void
    {
        $aliases = array_values(array_filter($aliases));
        if (count($aliases) === 0) return;

        $q->where(function ($w) use ($aliases) {
            foreach ($aliases as $i => $alias) {
                if ($i === 0) {
                    $w->whereNotNull("{$alias}.kunjungan_id");
                } else {
                    $w->orWhereNotNull("{$alias}.kunjungan_id");
                }
            }
        });
    }

    private function puskesmasName($puskesmasId): ?string
    {
        if ($puskesmasId === null || $puskesmasId === '' || !$this->canReadPuskesmasMaster()) {
            return null;
        }

        return DB::table($this->PUSK_TABLE)
            ->where($this->PUSK_PK, $puskesmasId)
            ->value($this->PUSK_NAME);
    }

    private function buildAggregat($qAgg, ?string $genderCol): array
    {
        $selects = [DB::raw('COUNT(DISTINCT pel.idpelayanan) as total')];

        $allCondition = $this->moduleCondition('mtbs') . ' OR ' . $this->moduleCondition('mtbm');
        $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN {$allCondition} THEN pel.idpelayanan END) as module_total");
        $selects[] = DB::raw('COUNT(DISTINCT CASE WHEN pel.sudahDilayani = 1 THEN pel.idpelayanan END) as served_total');

        if ($genderCol) {
            $lakiVals = [1, '1', 'L', 'l', 'LK', 'lk', 'LAKI', 'Laki-laki', 'LAKI-LAKI', 'Laki Laki'];
            $perVals = [2, '2', 'P', 'p', 'PR', 'pr', 'PEREMPUAN', 'Perempuan'];

            $quote = fn ($v) => "'" . str_replace("'", "''", (string) $v) . "'";
            $lakiIn = implode(',', array_map($quote, $lakiVals));
            $perIn = implode(',', array_map($quote, $perVals));

            $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN CAST(p.`{$genderCol}` AS CHAR) IN ({$lakiIn}) THEN pel.idpelayanan END) as laki_laki");
            $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN CAST(p.`{$genderCol}` AS CHAR) IN ({$perIn}) THEN pel.idpelayanan END) as perempuan");
        } else {
            $selects[] = DB::raw('0 as laki_laki');
            $selects[] = DB::raw('0 as perempuan');
        }

        $metricAliases = [
            'mtbs_total' => $this->moduleAliases('mtbs'),
            'mtbm_total' => $this->moduleAliases('mtbm'),
            'mtbs_subjektif' => ['mtbs_s'],
            'mtbs_objektif' => ['mtbs_o'],
            'mtbs_assessment' => ['mtbs_a'],
            'mtbs_planning' => ['mtbs_pl'],
            'mtbs_gizi' => ['mtbs_gz'],
            'mtbs_status_pasien' => ['mtbs_st', 'mtbs_st2'],
            'mtbs_imunisasi' => ['mtbs_im', 'mtbs_im_old'],
            'mtbs_diagnosa_medis' => ['mtbs_dx'],
            'mtbs_alergi' => ['mtbs_al'],
            'mtbm_subjektif' => ['mtbm_s'],
            'mtbm_objektif' => ['mtbm_o'],
            'mtbm_assessment' => ['mtbm_a'],
            'mtbm_planning' => ['mtbm_pl'],
            'mtbm_status_pasien' => ['mtbm_st'],
            'mtbm_diagnosa_medis' => ['mtbm_dx'],
        ];

        foreach ($metricAliases as $metric => $aliases) {
            $aliases = array_values(array_filter($aliases, fn ($a) => $this->aliasExists($a)));
            if (count($aliases) === 0) {
                $selects[] = DB::raw("0 as `{$metric}`");
                continue;
            }

            $conditions = array_map(fn ($a) => "{$a}.`kunjungan_id` IS NOT NULL", $aliases);
            $where = implode(' OR ', $conditions);
            $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN {$where} THEN pel.idpelayanan END) as `{$metric}`");
        }

        $row = (clone $qAgg)->select($selects)->first();

        $out = [
            'laki_laki' => (int) ($row->laki_laki ?? 0),
            'perempuan' => (int) ($row->perempuan ?? 0),
            'total' => (int) ($row->total ?? 0),
            'module_total' => (int) ($row->module_total ?? 0),
            'served_total' => (int) ($row->served_total ?? 0),
            'gender_col_used' => $genderCol,
        ];

        foreach (array_keys($metricAliases) as $metric) {
            $out[$metric] = (int) ($row->{$metric} ?? 0);
        }

        $out['without_module_total'] = max(0, $out['total'] - $out['module_total']);

        return $out;
    }

    // =========================================================
    // API DATA
    // =========================================================
    public function data(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);
            if ($perPage <= 0) $perPage = 10;
            if ($perPage > 100) $perPage = 100;
            if ($page <= 0) $page = 1;

            $genderCol = $this->detectGenderColumn();

            $q = $this->baseQuery();
            [$dateFrom, $dateTo, $kdPoli, $puskesmasId, $dataScope] = $this->applyFilters($q, $request);

            $q->select($this->baseSelect());
            $this->addStaticColumns($q, $this->columnsForList());

            $qAgg = $this->baseQuery();
            $this->applyFilters($qAgg, $request);
            $aggregat = $this->buildAggregat($qAgg, $genderCol);

            $rows = $q->orderByDesc('l.tglKunjungan')
                ->orderByDesc('pel.tglPelayanan')
                ->orderByDesc('pel.idpelayanan')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'aggregat' => $aggregat,
                    'rows' => $rows,
                    'joined_tables' => $this->joinedTableMeta(),
                    'debug' => [
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'kdPoli' => $kdPoli,
                        'puskesmas_id' => $puskesmasId,
                        'puskesmas_nama' => $this->puskesmasName($puskesmasId),
                        'collation_forced' => $this->COLL,
                        'mode' => 'static_fixed_tables_fixed_columns',
                        'data_scope' => $dataScope,
                        'active_tables' => array_column($this->joinedTableMeta(), 'table'),
                        'date_basis' => 'l.tglKunjungan; fallback pel.tglPelayanan bila tanggal kunjungan NULL',
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'ERROR',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function exportAggregat(Request $request)
    {
        try {
            $genderCol = $this->detectGenderColumn();

            $qAgg = $this->baseQuery();
            [$dateFrom, $dateTo, $kdPoli, $puskesmasId, $dataScope] = $this->applyFilters($qAgg, $request);
            $aggregat = $this->buildAggregat($qAgg, $genderCol);

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'info' => [
                        'kdPoli' => $kdPoli,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'gender_col_used' => $genderCol,
                        'puskesmas_id' => $puskesmasId,
                        'puskesmas_nama' => $this->puskesmasName($puskesmasId),
                        'joined_tables' => $this->joinedTableMeta(),
                        'data_scope' => $dataScope,
                    ],
                    'aggregat' => $aggregat,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'ERROR',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function exportDetail(Request $request)
    {
        try {
            $q = $this->baseQuery();
            [$dateFrom, $dateTo, $kdPoli, $puskesmasId, $dataScope] = $this->applyFilters($q, $request);

            $q->select($this->baseSelect());
            $this->addStaticColumns($q, $this->columnsForExport());

            $limit = (int) $request->get('limit', 50000);
            if ($limit <= 0) $limit = 50000;
            if ($limit > 100000) $limit = 100000;

            $rows = $q->orderByDesc('l.tglKunjungan')
                ->orderByDesc('pel.tglPelayanan')
                ->orderByDesc('pel.idpelayanan')
                ->limit($limit)
                ->get();

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'info' => [
                        'kdPoli' => $kdPoli,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'limit' => $limit,
                        'puskesmas_id' => $puskesmasId,
                        'puskesmas_nama' => $this->puskesmasName($puskesmasId),
                        'joined_tables' => $this->joinedTableMeta(),
                        'data_scope' => $dataScope,
                    ],
                    'rows' => $rows,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'ERROR',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}
