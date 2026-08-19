<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Carbon\Carbon;

class LaboratoriumController extends Controller
{
    /** =========================
     *  Konfigurasi & Utilities
     *  ========================= */
    private string $paramTable = 'parameter_uji'; // ganti ke 'loniccode.parameter_uji' jika beda DB

    /** Normalisasi string (kode, id, dsb.) */
    private function norm(string $s): string
    {
        return trim($s);
    }

    /**
     * Normalisasi / resolve loketId:
     * - Jika ada persis di simpus_loket / simpus_permohonan_lab → pakai apa adanya.
     * - Jika tidak ada, ambil digit dari param (mis. "LKT01" → "01"), pad-left 3 → "001",
     *   lalu bentuk "LOK001". Jika ada di loket/permohonan → pakai itu.
     * - Kalau tetap tidak ketemu, kembalikan param awal.
     */
    private function resolveLoketId(string $raw): string
    {
        $in = trim($raw);

        $existsLoket = DB::table('simpus_loket')->whereRaw('TRIM(idLoket)=?', [$in])->exists();
        if ($existsLoket) return $in;

        $existsPerm = DB::table('simpus_permohonan_lab')->whereRaw('TRIM(loketId)=?', [$in])->exists();
        if ($existsPerm) return $in;

        if (preg_match('/(\d+)/', strtoupper($in), $m)) {
            $digits = str_pad($m[1], 3, '0', STR_PAD_LEFT);
            $canon  = 'LOK' . $digits;

            $ok = DB::table('simpus_loket')->whereRaw('TRIM(idLoket)=?', [$canon])->exists()
               || DB::table('simpus_permohonan_lab')->whereRaw('TRIM(loketId)=?', [$canon])->exists();

            if ($ok) return $canon;
        }

        return $in;
    }
/** Pastikan parent di simpus_loket ada untuk FK */
/** Pastikan parent di simpus_loket ada (versi robust, tanpa asumsi kolom) */
/** Pastikan parent di simpus_loket ada (robust, isi kolom NOT NULL tanpa default) */
private function ensureLoketParentExists(string $loketId, string $permohonanId): void
{
    $loketId = trim($loketId);

    if (DB::table('simpus_loket')->whereRaw('TRIM(idLoket)=?', [$loketId])->exists()) return;

    // ---- Ambil data permohonan (kolom yang ada saja) ----
    $plCols = Schema::getColumnListing('simpus_permohonan_lab');
    $sel    = [];
    foreach (['loketId','pasienId','tglDibuat'] as $c) if (in_array($c, $plCols)) $sel[] = $c;

    $p = DB::table('simpus_permohonan_lab')
        ->whereRaw('TRIM(idPermohonan)=?', [trim($permohonanId)])
        ->first($sel);

    // Kalau permohonan menyimpan loketId berbeda, pakai yang itu
    if ($p && property_exists($p, 'loketId') && trim((string)$p->loketId) !== '' && trim((string)$p->loketId) !== $loketId) {
        $loketId = trim((string)$p->loketId);
        if (DB::table('simpus_loket')->whereRaw('TRIM(idLoket)=?', [$loketId])->exists()) return;
    }

    // ---- Susun payload dasar sesuai kolom yang ada ----
    $loketCols = Schema::getColumnListing('simpus_loket');
    $row = ['idLoket' => $loketId];

    if (in_array('pasienId', $loketCols)) $row['pasienId'] = $p->pasienId ?? null;
    if (in_array('kdPoli', $loketCols))  $row['kdPoli']    = 999; // fallback aman
    if (in_array('tglKunjungan', $loketCols)) {
        $row['tglKunjungan'] = ($p && isset($p->tglDibuat) && $p->tglDibuat)
            ? \Carbon\Carbon::parse($p->tglDibuat)->toDateString()
            : now()->toDateString();
    }
    if (in_array('createdDate', $loketCols))  $row['createdDate']  = now();
    if (in_array('modifiedDate', $loketCols)) $row['modifiedDate'] = now();

    // ---- Lengkapi kolom NOT NULL tanpa default (termasuk kdKegiatan) ----
    $row = $this->applyLoketRequiredDefaults($row);

    DB::table('simpus_loket')->insert($row);
}

/**
 * Isi kolom-kolom NOT NULL tanpa default di simpus_loket yang belum terisi pada $row.
 * - Untuk kdKegiatan: coba ambil nilai contoh dari baris lain (paling akhir). Jika tidak ada, pakai 0.
 * - Untuk kolom numerik: 0. Tanggal: today/now. String: ''.
 */
private function applyLoketRequiredDefaults(array $row): array
{
    $meta = DB::table('information_schema.COLUMNS')
        ->select('COLUMN_NAME','IS_NULLABLE','DATA_TYPE','COLUMN_DEFAULT','EXTRA')
        ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
        ->where('TABLE_NAME', 'simpus_loket')
        ->get();

    // helper ambil contoh nilai suatu kolom dari baris existing (biar cocok FK/enum kalau ada)
    $getSample = function(string $col) {
        return DB::table('simpus_loket')->orderByDesc('createdDate')->value($col)
            ?? DB::table('simpus_loket')->value($col);
    };

    foreach ($meta as $c) {
        $name = $c->COLUMN_NAME;

        // skip kalau sudah diisi / bisa NULL / ada default / auto_increment
        if (array_key_exists($name, $row)) continue;
        if ($c->IS_NULLABLE === 'YES') continue;
        if ($c->COLUMN_DEFAULT !== null) continue;
        if (str_contains((string)$c->EXTRA, 'auto_increment')) continue;

        // penanganan khusus beberapa kolom yang sering muncul
        if ($name === 'kdKegiatan') {
            $sample = $getSample('kdKegiatan');
            $row[$name] = $sample ?? 0;
            continue;
        }
        if ($name === 'status') {
            $row[$name] = 0;
            continue;
        }

        // fallback generik berdasar tipe data
        $type = strtolower($c->DATA_TYPE);
        if (in_array($type, ['int','tinyint','smallint','mediumint','bigint','decimal','numeric','float','double'])) {
            $row[$name] = 0;
        } elseif (in_array($type, ['date'])) {
            $row[$name] = now()->toDateString();
        } elseif (in_array($type, ['datetime','timestamp'])) {
            $row[$name] = now();
        } else {
            // char/varchar/text/enum/set dll → coba sample dulu biar aman, kalau kosong baru ''
            $sample = $getSample($name);
            $row[$name] = $sample ?? '';
        }
    }

    return $row;
}



    /** Ambil nama poli dari loket (opsional helper) */
    private function getPoliNameByLoket(string $loketId): ?string
    {
        return DB::table('simpus_loket as l')
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
            ->whereRaw('TRIM(l.idLoket)=?', [$loketId])
            ->value('poli.nmPoli');
    }

    /** =========================
     *  INDEX
     *  ========================= */
public function index(Request $request)
{
    // =========================
    // 1) Data untuk header & dropdown
    // =========================
    $DataUnit = DB::table('data_master_unit_detail as d')
        ->join('data_master_unit as k', 'k.id_kategori', '=', 'd.id_kategori')
        ->select('d.id_unit','d.id_kategori','d.nama_unit','d.no_kec','d.no_kel','k.kategori')
        ->groupBy('d.id_unit','d.id_kategori','d.nama_unit','d.no_kec','d.no_kel','k.kategori')
        ->orderBy('k.kategori')->orderBy('d.nama_unit')
        ->get();

    $FilterKategori = DB::table('data_master_unit')
        ->select('id_kategori','kategori')
        ->orderBy('kategori')->get();

    $FilterUnits = DB::table('data_master_unit_detail as d')
        ->join('data_master_unit as k', 'k.id_kategori', '=', 'd.id_kategori')
        ->select('k.id_kategori','k.kategori','d.id_unit','d.nama_unit')
        ->distinct()->orderBy('k.kategori')->orderBy('d.nama_unit')
        ->get();

    // =========================
    // 2) Query utama: permohonan lab (+loket/pasien/poli)
    // =========================
    $q = DB::table('simpus_permohonan_lab as lo')
        ->leftJoin('simpus_loket as l', 'l.idLoket', '=', 'lo.loketId')
        ->leftJoin('simpus_pasien as p', 'p.ID', '=', 'l.pasienId')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli');

    // Map unit via subquery dari l.unitId -> data_master_unit_detail.id_unit
    // (pakai CAST + TRIM supaya angka ' 1 ' tetap nyambung ke id_unit=1)
    $subIdKategori = "(SELECT d1.id_kategori FROM data_master_unit_detail d1 WHERE d1.id_unit = CAST(TRIM(l.unitId) AS SIGNED) LIMIT 1)";
    $subNamaUnit   = "(SELECT d2.nama_unit   FROM data_master_unit_detail d2 WHERE d2.id_unit = CAST(TRIM(l.unitId) AS SIGNED) LIMIT 1)";
    $subKategori   = "(SELECT k.kategori     FROM data_master_unit k WHERE k.id_kategori = {$subIdKategori} LIMIT 1)";

    $Rows = $q->addSelect(
            // identitas & waktu
            DB::raw('COALESCE(l.idLoket, lo.loketId) as idLoket'),
            DB::raw('COALESCE(p.NO_MR, lo.pasienId) as NO_MR'),
            DB::raw('COALESCE(p.NAMA_LGKP, "") as NAMA_LGKP'),
            DB::raw('COALESCE(p.ALAMAT, "") as alamat'),
            DB::raw('COALESCE(p.NO_RT, "") as no_rt'),
            DB::raw('COALESCE(p.NO_RW, "") as no_rw'),
            DB::raw('COALESCE(poli.nmPoli, lo.nmPoli) as nmPoli'),
            DB::raw('COALESCE(l.tglKunjungan, lo.tglDibuat) as tglKunjungan'),
DB::raw("lo.statusLayanan as statusLayanan"), // 🔥 kirim angka mentah ke FE
DB::raw("CASE 
            WHEN lo.statusLayanan = '1' THEN 'Selesai'
            WHEN lo.statusLayanan = '2' THEN 'Proses'
            ELSE 'Belum dilayani'
         END as status_text"),               // 🔥 kirim teks juga kalau mau

// kolom unit untuk filter FE
DB::raw('l.unitId as loket_unit_id'),
            DB::raw("CAST(TRIM(l.unitId) AS SIGNED) as unit_id"),
            DB::raw("$subNamaUnit   as nama_unit"),
            DB::raw("$subIdKategori as id_kategori"),
            DB::raw("$subKategori   as kategori"),
            // penanda berhasil terpetakan
            DB::raw("CASE WHEN ($subNamaUnit) IS NULL THEN 0 ELSE 1 END as unit_mapped")
        )
        ->orderByDesc(DB::raw('COALESCE(l.tglKunjungan, lo.tglDibuat)'))
        ->limit(200)
        ->get();

    // =========================
    // 3) DEBUG (selalu ada nilainya)
    // =========================
    $totalPermohonan = (int) DB::table('simpus_permohonan_lab')->count();
    $joinLoketCount  = (int) DB::table('simpus_permohonan_lab as lo')
                        ->leftJoin('simpus_loket as l', 'l.idLoket', '=', 'lo.loketId')
                        ->whereNotNull('l.idLoket')->count();
    $joinUnitIdCount = (int) DB::table('simpus_permohonan_lab as lo')
                        ->leftJoin('simpus_loket as l', 'l.idLoket', '=', 'lo.loketId')
                        ->whereNotNull('l.unitId')->count();

    $exampleNoLoket = DB::table('simpus_permohonan_lab as lo')
        ->leftJoin('simpus_loket as l', 'l.idLoket', '=', 'lo.loketId')
        ->whereNull('l.idLoket')
        ->select('lo.idPermohonan','lo.loketId','lo.tglDibuat')
        ->orderByDesc('lo.tglDibuat')->limit(5)->get();

    $exampleNoUnit = DB::table('simpus_permohonan_lab as lo')
        ->leftJoin('simpus_loket as l', 'l.idLoket', '=', 'lo.loketId')
        ->whereNotNull('l.idLoket')->whereNull('l.unitId')
        ->select('lo.idPermohonan','lo.loketId','l.idLoket','l.unitId')
        ->orderByDesc('lo.tglDibuat')->limit(5)->get();

    $Debug = [
        'rows'                 => $Rows->count(),
        'sample'               => $Rows->take(3)->values(),    // biar terlihat di FE
        'perm_lab_total'       => $totalPermohonan,
        'join_loket_rows'      => $joinLoketCount,
        'join_with_unitId'     => $joinUnitIdCount,
        'unit_mapped_true'     => $Rows->where('unit_mapped', 1)->count(),
        'unit_mapped_false'    => $Rows->where('unit_mapped', 0)->count(),
        'example_no_loket'     => $exampleNoLoket,
        'example_no_unitId'    => $exampleNoUnit,
        'loket_has_unitId_col' => Schema::hasColumn('simpus_loket','unitId'),
        'note' => 'Jika rows=0 → kemungkinan lo.loketId tidak match ke l.idLoket (cek example_no_loket). Jika unit_mapped_false>0 → isi l.unitId belum sesuai id_unit.',
    ];

    // =========================
    // 4) Return ke Inertia (Debug DIKIRIM)
    // =========================
    return Inertia::render('Ruang_Layanan/Laborat/index', [
        'DataUnit'        => $DataUnit,
        'DataPasien'      => $Rows,
        'FilterKategori'  => $FilterKategori,
        'FilterUnits'     => $FilterUnits,
        'Debug'           => $Debug,
    ]);
}





    /** =========================
     *  PEMERIKSAAN (DETAIL)
     *  ========================= */
public function pemeriksaan(Request $request, $loketId)
{
    $paramLoketId    = trim($loketId);
    $resolvedLoketId = $this->resolveLoketId($paramLoketId);

    // 1) Identitas: LOKET → PASIEN → POLI
    $DataPasien = DB::table('simpus_loket as l')
        ->leftJoin('simpus_pasien as p', 'p.ID', '=', 'l.pasienId')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->leftJoin('setup_kel as kel', function ($join) {
            $join->on('kel.NO_KEL', '=', 'p.NO_KEL')
                 ->on('kel.NO_KEC', '=', 'p.NO_KEC')
                 ->on('kel.NO_KAB', '=', 'p.NO_KAB')
                 ->on('kel.NO_PROP', '=', 'p.NO_PROP');
        })
        ->leftJoin('setup_kec as kec', function ($join) {
            $join->on('kec.NO_KEC', '=', 'p.NO_KEC')
                 ->on('kec.NO_KAB', '=', 'p.NO_KAB')
                 ->on('kec.NO_PROP', '=', 'p.NO_PROP');
        })
        ->leftJoin('setup_kab as kab', function ($join) {
            $join->on('kab.NO_KAB', '=', 'p.NO_KAB')
                 ->on('kab.NO_PROP', '=', 'p.NO_PROP');
        })
        ->leftJoin('setup_prop as prop', 'prop.NO_PROP', '=', 'p.NO_PROP')
        ->whereRaw('TRIM(l.idLoket) = ?', [$resolvedLoketId])
        ->select(
            'l.idLoket',
            'l.tglKunjungan',
            'l.umur', 'l.umur_bulan', 'l.umur_hari',
            DB::raw('p.NO_MR as NO_MR'),
            DB::raw('p.NAMA_LGKP as NAMA_LGKP'),
            DB::raw('p.NIK as NIK'),
            DB::raw('p.JENIS_KLMIN as jenis_klmin'),
            DB::raw('p.ALAMAT as alamat'),
            DB::raw('p.NO_RT as no_rt'),
            DB::raw('p.NO_RW as no_rw'),
            DB::raw('kel.nama_kel as nama_kel'),
            DB::raw('kec.nama_kec as nama_kec'),
            DB::raw('kab.nama_kab as nama_kab'),
            DB::raw('prop.nama_prop as nama_prop'),
            'poli.nmPoli',
            DB::raw("CASE 
                WHEN p.TGL_LHR IN ('0000-00-00','0000-00-00 00:00:00','') THEN NULL
                ELSE p.TGL_LHR 
            END as TGL_LHR")
        )
        ->first();

    // 2) PERMOHONAN TERBARU
    $DataPermohonan = DB::table('simpus_permohonan_lab')
        ->whereRaw('TRIM(loketId) = ?', [$resolvedLoketId])
        ->orderByDesc('tglDibuat')
        ->select(
            DB::raw('idPermohonan as order_id'),
            DB::raw('idPermohonan as idPermohonan'),
            'loketId',
            DB::raw('pasienId as pasien_id'),
            DB::raw('tglDibuat as tgl_dibuat'),
            DB::raw('tenagaMedisPengirim as tenaga_medis_perujuk'),
            DB::raw('tenagaMedisPenerima as tenaga_medis_pemeriksa'),
            DB::raw('nmPoli as poli'),
            DB::raw('alasanDirujuk as alasan'),
            DB::raw('hasilLabLuarFaskes as hasil_lab_luar_faskes'),
            DB::raw("CASE 
                        WHEN statusLayanan = '1' THEN 'Selesai'
                        WHEN statusLayanan = '2' THEN 'Proses'
                        ELSE 'Belum dilayani'
                     END as status"),
            DB::raw('statusLayanan as status_layanan'),
            DB::raw('sampleDiambil as sample_diambil_at'),
            DB::raw('sampleSelesai as sample_selesai_at')
        )
        ->first();

    // 3) Fallback identitas dari permohonan (kalau LOKET tidak ada)
    if (!$DataPasien && $DataPermohonan && $DataPermohonan->pasien_id) {
        $p = DB::table('simpus_pasien as p')
            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('kel.NO_KEL', '=', 'p.NO_KEL')
                     ->on('kel.NO_KEC', '=', 'p.NO_KEC')
                     ->on('kel.NO_KAB', '=', 'p.NO_KAB')
                     ->on('kel.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('kec.NO_KEC', '=', 'p.NO_KEC')
                     ->on('kec.NO_KAB', '=', 'p.NO_KAB')
                     ->on('kec.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('setup_kab as kab', function ($join) {
                $join->on('kab.NO_KAB', '=', 'p.NO_KAB')
                     ->on('kab.NO_PROP', '=', 'p.NO_PROP');
            })
            ->leftJoin('setup_prop as prop', 'prop.NO_PROP', '=', 'p.NO_PROP')
            ->where('p.ID', $DataPermohonan->pasien_id)
            ->select(
                DB::raw("'{$resolvedLoketId}' as idLoket"),
                DB::raw('NULL as umur'),
                DB::raw('NULL as umur_bulan'),
                DB::raw('NULL as umur_hari'),
                DB::raw('p.NO_MR as NO_MR'),
                DB::raw('p.NAMA_LGKP as NAMA_LGKP'),
                DB::raw('p.NIK as NIK'),
                DB::raw('p.JENIS_KLMIN as jenis_klmin'),
                DB::raw('p.ALAMAT as alamat'),
                DB::raw('p.NO_RT as no_rt'),
                DB::raw('p.NO_RW as no_rw'),
                DB::raw('kel.nama_kel as nama_kel'),
                DB::raw('kec.nama_kec as nama_kec'),
                DB::raw('kab.nama_kab as nama_kab'),
                DB::raw('prop.nama_prop as nama_prop'),
                DB::raw('NULL as nmPoli'),
                DB::raw('CAST(NULL AS DATE) as tglKunjungan')
            )
            ->first();

        if ($p) {
            $p->tglKunjungan = $DataPermohonan->tgl_dibuat
                ? Carbon::parse($DataPermohonan->tgl_dibuat)->toDateString()
                : null;
        }
        $DataPasien = $p;
    }

    // 4) OBJECTIVE: ambil by loketId, fallback by pasien terdekat waktu
    $DataObjective = DB::table('simpus_anamnesa as a')
        ->whereRaw('TRIM(a.loketId) = ?', [$resolvedLoketId])
        ->orderByDesc('a.createdDate')
        ->select(
            'a.keadaanUmum','a.kdSadar','a.tinggiBadan','a.beratBadan',
            'a.lingkarPerut','a.lingkarTangan',
            'a.sistole','a.diastole','a.respRate','a.heartRate','a.suhu',
            'a.createdDate','a.loketId'
        )
        ->first();

    if (!$DataObjective) {
        $pasienId = DB::table('simpus_loket')->whereRaw('TRIM(idLoket)=?', [$resolvedLoketId])->value('pasienId');
        if (!$pasienId && $DataPermohonan) {
            $pasienId = $DataPermohonan->pasien_id ?: DB::table('simpus_loket')
                ->whereRaw('TRIM(idLoket)=?', [$DataPermohonan->loketId])
                ->value('pasienId');
        }

        if ($pasienId) {
            $ref    = $DataPasien?->tglKunjungan ?: $DataPermohonan?->tgl_dibuat;
            $target = $ref ? Carbon::parse($ref)->endOfDay() : Carbon::now();

            $DataObjective = DB::table('simpus_anamnesa as a')
                ->join('simpus_loket as l2', 'l2.idLoket', '=', 'a.loketId')
                ->where('l2.pasienId', $pasienId)
                ->select(
                    'a.keadaanUmum','a.kdSadar','a.tinggiBadan','a.beratBadan',
                    'a.lingkarPerut','a.lingkarTangan',
                    'a.sistole','a.diastole','a.respRate','a.heartRate','a.suhu',
                    'a.createdDate','a.loketId'
                )
                ->orderByRaw('CASE WHEN a.loketId = ? THEN 0 ELSE 1 END', [$resolvedLoketId])
                ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, a.createdDate, ?)) ASC', [$target])
                ->orderByDesc('a.createdDate')
                ->first();
        }
    }

    // 5) DETAIL PEMERIKSAAN — AMBIL META DARI parameter_uji SAJA (tanpa join ke master)
    $DataPemeriksaan = collect();
    if ($DataPermohonan && $DataPermohonan->order_id) {

        $puSub = DB::table($this->paramTable . ' as pu0')
    ->leftJoin('master_kategori as k', 'k.id_kategori', '=', 'pu0.id_kategori')
    ->selectRaw("
        {$this->sqlNorm('pu0.kode_parameter')}    AS kode,
        MAX(pu0.satuan)                           AS satuan,
        MAX(NULLIF(pu0.nilai_normal,''))          AS nilai_normal,
        MAX(NULLIF(pu0.nilai_kritis,''))          AS nilai_kritis,
        MAX(pu0.id_kategori)                      AS id_kategori,
        MAX(k.nama_kategori)                      AS kategori
    ")
    ->whereNotNull('pu0.kode_parameter')
    ->groupBy(DB::raw($this->sqlNorm('pu0.kode_parameter')));


$DataPemeriksaan = DB::table('simpus_tindakan as t')
    ->leftJoinSub($puSub, 'pu', function ($j) {
        $j->on('pu.kode', '=', DB::raw($this->sqlNorm('t.kdTindakan')));
    })
    ->whereRaw('TRIM(t.loketId) = ?', [$this->norm($resolvedLoketId)])
    ->whereRaw('TRIM(t.permohonanId) = ?', [$this->norm($DataPermohonan->order_id)])
    ->selectRaw("
                        t.idTindakan                                        AS detail_id,
                        t.kdTindakan                                        AS kode,
                        COALESCE(NULLIF(t.nmTindakanInd,''), t.nmTindakan)  AS nama_pemeriksaan,
                        t.nilaiLab                                          AS nilai_lab,
                        pu.satuan                                           AS satuan,
                        pu.nilai_normal                                     AS nilai_normal,
                        pu.nilai_kritis                                     AS nilai_kritis,
                        CONCAT(
                            'Nilai Normal : ', COALESCE(pu.nilai_normal, '-'),
                            '\nNilai Kritis : ', COALESCE(pu.nilai_kritis, '-')
                        ) AS nilai_normal_kritis,
                        /* Tambahan untuk FE */
                        pu.id_kategori                                      AS id_kategori,
                        COALESCE(pu.kategori, 'Tanpa kategori')             AS kategori
                    ")

            ->orderBy('t.idTindakan')
            ->get();
    }

    $TenagaMedis = DB::table('master_dokter')
        ->select('kdDokter as id', 'nmDokter as nama', DB::raw('NULL as profesi'))
        ->orderBy('nmDokter')
        ->get();
    $source = $request->query('from', 'laborat'); // default: laborat

    return Inertia::render('Ruang_Layanan/Laborat/pemeriksaan', [
        'DataPasien'      => $DataPasien,
        'DataPermohonan'  => $DataPermohonan,
        'DataPemeriksaan' => $DataPemeriksaan,
        'DataObjective'   => $DataObjective,
        'TenagaMedis'     => $TenagaMedis,
        'Source'          => $source,

    ]);
}


    /** =========================
     *  HEADER PERMOHONAN: Waktu & Status
     *  ========================= */
    public function setWaktuSample(Request $request)
    {
        $validated = $request->validate([
            'order_id'               => 'required|string',
            'sample_diambil_at'      => 'nullable|date',
            'sample_selesai_at'      => 'nullable|date',
            'tenaga_medis_pemeriksa' => 'nullable|string|max:255',
        ]);

        $status = '0';
        if (!empty($validated['sample_diambil_at'])) $status = '2';
        if (!empty($validated['sample_selesai_at'])) $status = '1';

        DB::table('simpus_permohonan_lab')
            ->whereRaw('TRIM(idPermohonan)=?', [trim($validated['order_id'])])
            ->update([
                'sampleDiambil'       => $validated['sample_diambil_at'] ?? null,
                'sampleSelesai'       => $validated['sample_selesai_at'] ?? null,
                'tenagaMedisPenerima' => $validated['tenaga_medis_pemeriksa'] ?? null,
                'statusLayanan'       => $status,
                'modifiedDate'        => now(),
            ]);

        return back();
    }

    /** =========================
     *  TINDAKAN: Hapus Semua / Hapus Satu / Update Nilai
     *  ========================= */
    public function hapusSemuaTindakan(Request $req)
    {
        $validated = $req->validate([
            'order_id' => ['required','string'],
        ]);

        $orderId = trim($validated['order_id']);

        $loketId = DB::table('simpus_permohonan_lab')
            ->whereRaw('TRIM(idPermohonan)=?', [$orderId])
            ->value('loketId');

        if (!$loketId) {
            return back()->withErrors(['hapus' => 'Permohonan tidak ditemukan / loketId kosong.']);
        }

        DB::beginTransaction();
        try {
            $deleted = DB::table('simpus_tindakan')
                ->whereRaw('TRIM(permohonanId)=?', [$orderId])
                ->whereRaw('TRIM(loketId)=?', [$loketId])
                ->delete();

            DB::commit();
            return back()->with('ok', "hapus-semua-berhasil ($deleted item)");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['hapus' => $e->getMessage()]);
        }
    }

    public function hapusTindakan(Request $request)
    {
        $validated = $request->validate([
            'order_id'  => 'required|string',
            'detail_id' => 'required|integer',
        ]);

        DB::table('simpus_tindakan')
            ->where('idTindakan', $validated['detail_id'])
            ->whereRaw('TRIM(permohonanId)=?', [trim($validated['order_id'])])
            ->delete();

        return back();
    }

    public function updateNilaiLab(Request $request)
    {
        $validated = $request->validate([
            'order_id'          => 'required|string',
            'hasil'             => 'required|array',
            'hasil.*.detail_id' => 'required|integer',
            'hasil.*.nilai'     => 'nullable|string',
        ]);

        $permId = trim($validated['order_id']);

        foreach ($validated['hasil'] as $row) {
            DB::table('simpus_tindakan')
                ->where('idTindakan', $row['detail_id'])
                ->whereRaw('TRIM(permohonanId)=?', [$permId])
                ->update(['nilaiLab' => $row['nilai']]);
        }

        return back();
    }

    /** =========================
     *  HEADER PERMOHONAN: Simpan
     *  ========================= */
    public function simpanPermohonan(Request $request)
    {
        $validated = $request->validate([
            'idLoket'             => 'required|string',
            'idPasien'            => 'required',
            'idPelayanan'         => 'nullable',
            'hasilLabLuarFaskes'  => 'nullable|in:,0,1',
            'kdPoli'              => 'nullable|string',
            'tenagaMedisPengirim' => 'nullable|string|max:255',
            'alasanDirujuk'       => 'nullable|string',
            'tglDibuat'           => 'nullable|string',
        ]);

        $loketId   = $this->resolveLoketId($validated['idLoket']);
        $nmPoli    = $this->getPoliNameByLoket($loketId) ?? null;
        $tglDibuat = $validated['tglDibuat'] ?: now();

        $insert = [
            'loketId'             => $loketId,
            'pasienId'            => $validated['idPasien'],
            'tglDibuat'           => $tglDibuat,
            'tenagaMedisPengirim' => $validated['tenagaMedisPengirim'] ?? null,
            'tenagaMedisPenerima' => null,
            'nmPoli'              => $nmPoli,
            'alasanDirujuk'       => $validated['alasanDirujuk'] ?? null,
            'hasilLabLuarFaskes'  => ($validated['hasilLabLuarFaskes'] === '1') ? 1 : 0,
            'statusLayanan'       => '0',
            'createdDate'         => now(),
            'modifiedDate'        => now(),
        ];

        if (Schema::hasColumn('simpus_permohonan_lab', 'idPelayanan') && !empty($validated['idPelayanan'])) {
            $insert['idPelayanan'] = $validated['idPelayanan'];
        }
        if (Schema::hasColumn('simpus_permohonan_lab', 'kdPoli') && !empty($validated['kdPoli'])) {
            $insert['kdPoli'] = $validated['kdPoli'];
        }

        DB::table('simpus_permohonan_lab')->insert($insert);

        return back();
    }

    /** =========================
     *  Helper Insert Batch (master pemeriksaan lama)
     *  ========================= */
// ====== GANTI: insertTindakanBatch ======
/**
 * Insert batch dari master_pemeriksaan lama → simpus_tindakan.
 * - Cegah duplikat per (loketId, permohonanId, kdTindakan_norm).
 * - kdTindakan selalu disimpan dalam bentuk sudah dinormalisasi.
 */
private function insertTindakanBatch(string $loketId, string $permohonanId, $pelayananId, array $masters, array $nilaiMap = []): int
{
    $now            = now();
    $cols           = Schema::getColumnListing('simpus_tindakan');
    $hasIdPelayanan = in_array('idPelayanan', $cols);
    $hasCreatedDate = in_array('createdDate', $cols);
    $hasModifiedDate= in_array('modifiedDate', $cols);

    $insertRows = [];

    foreach ($masters as $m) {

        // ----------------------------- //
        // Bagian normalisasi dan cek exist
        // ----------------------------- //
        $kode = $this->normCode((string)$m->kode);
        if ($kode === '') continue;

        // gunakan versi anti-collation error ini
        $loketIdN = $this->norm($loketId);
        $permIdN  = $this->norm($permohonanId);
        $kodeN    = $this->normCode($kode);

        $exists = DB::table('simpus_tindakan')
            ->whereRaw(
                $this->sqlNorm('loketId') . " COLLATE utf8mb4_unicode_ci = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci",
                [$loketIdN]
            )
            ->whereRaw(
                $this->sqlNorm('permohonanId') . " COLLATE utf8mb4_unicode_ci = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci",
                [$permIdN]
            )
            ->whereRaw(
                $this->sqlNorm('kdTindakan') . " COLLATE utf8mb4_unicode_ci = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci",
                [$kodeN]
            )
            ->exists();

        if ($exists) continue; // sudah ada → skip
        // ----------------------------- //

        // buat row baru
        $row = [
            'loketId'       => $loketId,
            'permohonanId'  => $permohonanId,
            'kdTindakan'    => $kodeN,
            'nmTindakan'    => $m->nmPemeriksaan,
            'nmTindakanInd' => $m->nmPemeriksaanInd,
            'nilaiLab'      => $nilaiMap[$m->idPemeriksaan] ?? null,
        ];

        if ($hasIdPelayanan && !empty($pelayananId)) $row['idPelayanan'] = $pelayananId;
        if ($hasCreatedDate)  $row['createdDate']   = $now;
        if ($hasModifiedDate) $row['modifiedDate']  = $now;

        $insertRows[] = $row;
    }

    if ($insertRows) {
DB::table('simpus_tindakan')->insertOrIgnore($insertRows);
    }

    return count($insertRows);
}



    /** =========================
     *  Tambah Detail dari master_pemeriksaan (lama)
     *  ========================= */
    public function pemeriksaanSimpan(Request $request)
    {
        $validated = $request->validate([
            'loketId'       => 'required|string',
            'permohonanId'  => 'required|string',
            'pelayananId'   => 'nullable',
            'pilih'         => 'required|array|min:1',
            'pilih.*'       => 'integer',
            'nilaiLab'      => 'nullable|array',
        ]);

        $loketId   = $this->resolveLoketId($validated['loketId']);
        $orderId   = trim($validated['permohonanId']);
        $nilaiMap  = $validated['nilaiLab'] ?? [];
        $pelayanan = $validated['pelayananId'] ?? null;

        $exists = DB::table('simpus_permohonan_lab')
            ->whereRaw('TRIM(idPermohonan)=?', [$orderId])
            ->exists();

        if (!$exists) {
            return back()->withErrors(['permohonanId' => 'Permohonan ID tidak valid.']);
        }

        $masters = DB::table('simpus_master_pemeriksaan_lab')
            ->whereIn('idPemeriksaan', $validated['pilih'])
            ->select('idPemeriksaan','kode','nmPemeriksaan','nmPemeriksaanInd')
            ->orderBy('idPemeriksaan')
            ->get();

        DB::transaction(function () use ($loketId, $orderId, $pelayanan, $masters, $nilaiMap) {
            $this->insertTindakanBatch($loketId, $orderId, $pelayanan, $masters->all(), $nilaiMap);
        });

        return back();
    }

    /** =========================
     *  Tambah Detail dari master paket (lama)
     *  ========================= */
    public function paketPemeriksaanSimpan(Request $request, string $paket)
    {
        $validated = $request->validate([
            'loketId'      => 'required|string',
            'permohonanId' => 'required|string',
            'pelayananId'  => 'nullable',
        ]);

        $mapCol = [
            'paketCatin'        => 'paketCatin',
            'paketAnck1'        => 'paketAnck1',
            'paketAnck3'        => 'paketAnck3',
            'paketPtm'          => 'paketPtm',
            'paketDarahLengkap' => 'paketDarahLengkap',
            'paketWidal'        => 'paketWidal',
        ];
        if (!isset($mapCol[$paket])) {
            return back()->withErrors(['paket' => 'Paket tidak dikenal.']);
        }

        $loketId   = $this->resolveLoketId($validated['loketId']);
        $orderId   = trim($validated['permohonanId']);
        $pelayanan = $validated['pelayananId'] ?? null;

        $exists = DB::table('simpus_permohonan_lab')
            ->whereRaw('TRIM(idPermohonan)=?', [$orderId])
            ->exists();

        if (!$exists) {
            return back()->withErrors(['permohonanId' => 'Permohonan ID tidak valid.']);
        }

        $masters = DB::table('simpus_master_pemeriksaan_lab')
            ->where($mapCol[$paket], 1)
            ->select('idPemeriksaan','kode','nmPemeriksaan','nmPemeriksaanInd')
            ->orderBy('headerId')
            ->orderBy('subheaderId')
            ->orderByRaw('COALESCE(noUrut, 9999)')
            ->get();

        DB::transaction(function () use ($loketId, $orderId, $pelayanan, $masters) {
            $this->insertTindakanBatch($loketId, $orderId, $pelayanan, $masters->all(), []);
        });

        return back();
    }

    /** =========================
     *  DETAIL sederhana untuk Print (JSON)
     *  ========================= */
public function detail($idPermohonan)
{
    $order = DB::table('simpus_permohonan_lab')
        ->whereRaw('TRIM(idPermohonan)=?', [trim($idPermohonan)])
        ->first();

    if (!$order) {
        return response()->json(['error' => 'Permohonan tidak ditemukan'], 404);
    }

    // subquery parameter_uji → unik per kode
    $puSub = DB::table($this->paramTable . ' as pu0')
        ->selectRaw("
            TRIM(pu0.kode_parameter)               AS kode,
            MAX(pu0.satuan)                         AS satuan,
            MAX(NULLIF(pu0.nilai_normal,''))        AS nilai_normal,
            MAX(NULLIF(pu0.nilai_kritis,''))        AS nilai_kritis
        ")
        ->whereNotNull('pu0.kode_parameter')
        ->groupBy(DB::raw('TRIM(pu0.kode_parameter)'));

    $rows = DB::table('simpus_tindakan as t')
        ->leftJoinSub($puSub, 'pu', function ($j) {
            $j->on('pu.kode', '=', DB::raw('TRIM(t.kdTindakan)'));
        })
        ->whereRaw('TRIM(t.permohonanId)=?', [trim($idPermohonan)])
        ->selectRaw("
            t.idTindakan                                        AS detail_id,
            t.kdTindakan                                        AS kode,
            COALESCE(NULLIF(t.nmTindakanInd,''), t.nmTindakan)  AS nama_pemeriksaan,
            t.nilaiLab                                          AS nilai_lab,
            pu.satuan                                           AS satuan,
            CONCAT(
                'Nilai Normal : ', COALESCE(pu.nilai_normal,'-'),
                '\nNilai Kritis : ', COALESCE(pu.nilai_kritis,'-')
            ) AS nilai_normal_kritis
        ")
        ->orderBy('t.idTindakan')
        ->get();

    return response()->json([
        'permohonan'  => $order,
        'pemeriksaan' => $rows,
    ]);
}

    /** =========================
     *  MASTER (lama): Header & Subheader
     *  ========================= */
    public function masterHeaders(Request $request)
    {
        $rows = DB::table('simpus_master_header_lab as h')
            ->leftJoin('simpus_master_pemeriksaan_lab as m', 'm.headerId', '=', 'h.idHeader')
            ->select(
                'h.idHeader',
                'h.nmHeader',
                DB::raw('COUNT(m.idPemeriksaan) as jumlah_item')
            )
            ->groupBy('h.idHeader', 'h.nmHeader')
            ->orderBy('h.nmHeader')
            ->get();

        return response()->json($rows);
    }

    public function masterSubheaders(Request $request, int $headerId)
    {
        $rows = DB::table('simpus_master_subheader_lab as s')
            ->where('s.headerId', $headerId)
            ->orderBy('s.nmSubheader')
            ->get(['s.idSubheader', 's.headerId', 's.nmSubheader']);

        return response()->json($rows);
    }

    public function paginasiMasterPemeriksaan(Request $request)
    {
        $search        = trim((string) $request->query('search', ''));
        $headerId      = $request->query('header_id');
        $subheaderId   = $request->query('subheader_id');
        $headerName    = trim((string) $request->query('header_name', ''));
        $subheaderName = trim((string) $request->query('subheader_name', ''));
        $paket         = trim((string) $request->query('paket', ''));

        $q = DB::table('simpus_master_pemeriksaan_lab as m')
            ->leftJoin('simpus_master_header_lab as h', 'h.idHeader', '=', 'm.headerId')
            ->leftJoin('simpus_master_subheader_lab as s', 's.idSubheader', '=', 'm.subheaderId');

        if ($search !== '') {
            $q->where(function ($x) use ($search) {
                $x->where('m.nmPemeriksaan', 'like', "%{$search}%")
                  ->orWhere('m.nmPemeriksaanInd', 'like', "%{$search}%")
                  ->orWhere('m.kode', 'like', "%{$search}%");
            });
        }

        if (!empty($headerId))    $q->where('m.headerId', (int) $headerId);
        if (!empty($subheaderId)) $q->where('m.subheaderId', (int) $subheaderId);
        if ($headerName !== '')    $q->where('h.nmHeader', $headerName);
        if ($subheaderName !== '') $q->where('s.nmSubheader', $subheaderName);

        if ($paket !== '') {
            $map = [
                'catin'         => 'm.paketCatin',
                'anck1'         => 'm.paketAnck1',
                'anck3'         => 'm.paketAnck3',
                'ptm'           => 'm.paketPtm',
                'darah_lengkap' => 'm.paketDarahLengkap',
                'widal'         => 'm.paketWidal',
            ];
            if (isset($map[$paket])) {
                $q->where($map[$paket], 1);
            }
        }

        $Master = $q->select([
                DB::raw('m.idPemeriksaan as id'),
                'm.idPemeriksaan',
                'm.kode',
                DB::raw('m.nmPemeriksaan as nama'),
                'm.nmPemeriksaanInd',
                'm.satuan',
                'm.headerId',
                'm.subheaderId',
                'h.nmHeader as header_name',
                's.nmSubheader as subheader_name',
                'm.noUrut',
                DB::raw("CONCAT(
                    'Nilai Normal : ', COALESCE(m.nilaiNormal, '-'),
                    '\nNilai Kritis : ', COALESCE(m.nilaiKritis, '-')
                ) as nilai_normal_kritis"),
            ])
            ->orderBy('h.nmHeader')
            ->orderBy('s.nmSubheader')
            ->orderByRaw('COALESCE(m.noUrut, 9999)')
            ->orderBy('m.kode')
            ->paginate(15);

        $links   = [];
        $links[] = ['label' => 'Previous', 'url' => $Master->previousPageUrl(), 'active' => false];

        $last = $Master->lastPage();
        $cur  = $Master->currentPage();

        $links[] = ['label' => 1, 'url' => $Master->url(1), 'active' => $cur === 1];
        if ($cur > 3) $links[] = ['label' => '...', 'url' => null, 'active' => false];

        for ($i = max(2, $cur - 1); $i <= min($last - 1, $cur + 1); $i++) {
            $links[] = ['label' => $i, 'url' => $Master->url($i), 'active' => $cur === $i];
        }

        if ($cur < $last - 2) $links[] = ['label' => '...', 'url' => null, 'active' => false];
        if ($last > 1) $links[] = ['label' => $last, 'url' => $Master->url($last), 'active' => $cur === $last];
        $links[] = ['label' => 'Next', 'url' => $Master->nextPageUrl(), 'active' => false];

        return response()->json([
            'data'  => $Master->items(),
            'meta'  => [
                'current_page' => $Master->currentPage(),
                'last_page'    => $Master->lastPage(),
                'per_page'     => $Master->perPage(),
                'total'        => $Master->total(),
            ],
            'links' => $links,
        ]);
    }

    /** =========================
     *  PARAMETER_UJI (baru): Header, Isi, Browse, Insert
     *  ========================= */
    public function paramHeaders(Request $request)
    {
        $rows = DB::table($this->paramTable . ' as h')
            ->leftJoin($this->paramTable . ' as d', function($j){
                $j->on('d.header', '=', DB::raw(0))
                  ->on('d.sub_header', '=', 'h.header');
            })
            ->where('h.header', '>', 0)
            ->when(Schema::hasColumn('parameter_uji', 'paket'), fn($q) => $q->where('h.paket', 'TRUE'))
            ->select([
                'h.header',
                DB::raw("MAX(h.nama_parameter) AS header_name"),
                DB::raw('COUNT(d.id_parameter) AS jumlah')
            ])
            ->groupBy('h.header')
            ->orderBy('h.header')
            ->get();

        return response()->json($rows);
    }

    public function paramSubheaders(Request $request, int $header)
    {
        $rows = DB::table('parameter_uji')
            ->where('header', 0)
            ->where('sub_header', $header)
            ->select([
                DB::raw('sub_header'),
                DB::raw('COUNT(*) AS jumlah')
            ])
            ->groupBy('sub_header')
            ->orderBy('sub_header')
            ->get();

        return response()->json($rows);
    }

    /** Helper insert batch dari PARAMETER_UJI → simpus_tindakan */
// ====== GANTI: insertParamBatch ======
/**
 * Insert batch dari parameter_uji → simpus_tindakan.
 * - Cegah duplikat per (loketId, permohonanId, kdTindakan_norm).
 * - kdTindakan selalu disimpan dalam bentuk sudah dinormalisasi.
 */
private function insertParamBatch(
    string $loketId,
    string $permohonanId,
    $pelayananId,
    array $items
): int {
    $now             = now();
    $cols            = Schema::getColumnListing('simpus_tindakan');
    $hasIdPelayanan  = in_array('idPelayanan', $cols);
    $hasCreatedDate  = in_array('createdDate', $cols);
    $hasModifiedDate = in_array('modifiedDate', $cols);

    $loketIdN = $this->norm($loketId);
    $permIdN  = $this->norm($permohonanId);

    $insertRows = [];

    foreach ($items as $p) {
        // 1) Normalisasi kode
        $kode = $this->normCode((string)($p->kode_parameter ?? ''));
        if ($kode === '') continue;

        // 2) Cek sudah ada?
        $exists = DB::table('simpus_tindakan')
            ->whereRaw($this->sqlNorm('loketId')      . ' = ?', [$loketIdN])
            ->whereRaw($this->sqlNorm('permohonanId') . ' = ?', [$permIdN])
            ->whereRaw($this->sqlNorm('kdTindakan')   . ' = ?', [$kode])
            ->exists();

        if ($exists) continue;

        // 3) Susun row baru
        $row = [
            'loketId'       => $loketIdN,
            'permohonanId'  => $permIdN,
            'kdTindakan'    => $kode, // normalized
            'nmTindakan'    => $p->nama_parameter ?? null,
            'nmTindakanInd' => $p->nama_parameter ?? null,
            'nilaiLab'      => null,
        ];

        if ($hasIdPelayanan && !empty($pelayananId)) $row['idPelayanan'] = $pelayananId;
        if ($hasCreatedDate)  $row['createdDate']   = $now;
        if ($hasModifiedDate) $row['modifiedDate']  = $now;

        $insertRows[] = $row;
    }

    if ($insertRows) {
DB::table('simpus_tindakan')->insertOrIgnore($insertRows);
    }

    return count($insertRows);
}

/** Normalisasi KODE di PHP: trim, samakan semua dash, hapus spasi, upper */
private function normCode(string $s): string
{
    $s = trim($s);
    // Normalisasi dash & spasi KESEMUANYA di PHP, bukan SQL:
    $s = strtr($s, [
        '–' => '-', '—' => '-', '‒' => '-', '−' => '-', // dash varian ke '-'
    ]);
    // kalau mau, hilangkan spasi dalam kode:
    // $s = preg_replace('/\s+/', '', $s);
    return mb_strtoupper($s, 'UTF-8');
}

/** Ekspresi SQL untuk normalisasi kolom kode di DB (samakan dash + buang spasi + upper) */
/** SQL normalizer: CONVERT ke utf8mb4 + TRIM + UPPER */
private function sqlNorm(string $col): string
{
    // CONVERT(... USING utf8mb4) memastikan collation seragam
    return "UPPER(TRIM(CONVERT($col USING utf8mb4)))";
}



    /** Ambil isi paket (anak) dari PARAMETER_UJI untuk header tertentu */
    public function paketParamItems(Request $request, int $header)
    {
        $sub = $request->query('sub_header');

        $q = DB::table($this->paramTable)
            ->where('header', 0)
            ->where('sub_header', $header);

        if (!is_null($sub) && $sub !== '') {
            $q->where('sub_header', (int)$sub);
        }

        $items = $q->select([
                'id_parameter',
                DB::raw('TRIM(kode_parameter) as kode'),
                DB::raw('nama_parameter as nama'),
                'satuan',
                'nilai_normal','nilai_kritis','header','sub_header',
                DB::raw("CONCAT(
                    'Nilai Normal : ', COALESCE(NULLIF(nilai_normal,''),'-'),
                    '\nNilai Kritis : ', COALESCE(NULLIF(nilai_kritis,''),'-')
                ) as nilai_normal_kritis")
            ])
            ->orderBy('sub_header')
            ->orderBy('id_parameter')
            ->get();

        return response()->json($items);
    }

    /** Simpan semua isi paket (berdasar header) ke simpus_tindakan */
public function paramSimpan(Request $request, int $header)
{
    $validated = $request->validate([
        'loketId'      => 'required|string',   // hanya untuk UI
        'permohonanId' => 'required|string',
        'pelayananId'  => 'nullable',
        'sub_header'   => 'nullable|integer',
    ]);

    $orderId = trim($validated['permohonanId']);

    $loketId = trim((string) DB::table('simpus_permohonan_lab')
        ->whereRaw('TRIM(idPermohonan)=?', [$orderId])
        ->value('loketId'));

    if ($loketId === '') return back()->withErrors(['permohonanId' => 'Permohonan ID tidak valid.']);

    $this->ensureLoketParentExists($loketId, $orderId);

    $pelayanan = $validated['pelayananId'] ?? null;

    $q = DB::table($this->paramTable)
        ->where('header', 0)
        ->where('sub_header', $header);

    if (!empty($validated['sub_header'])) {
        $q->where('sub_header', (int) $validated['sub_header']);
    }

    $items = $q->select([
            'id_parameter','kode_parameter','nama_parameter','nilai_normal','nilai_kritis','satuan'
        ])
        ->orderBy('id_parameter')
        ->get();

    DB::transaction(function () use ($loketId, $orderId, $pelayanan, $items) {
        $this->insertParamBatch($loketId, $orderId, $pelayanan, $items->all());
    });

    return back();
}


    /** Browse parameter_uji (pencarian bebas / per header) */
public function paramBrowse(Request $r)
{
    $search      = trim($r->query('search', ''));
    $headerQ     = $r->query('header');
    $subHeaderQ  = $r->query('sub_header');
    $kategoriQ   = $r->query('kategori');   // filter kategori by id_kategori (opsional)
    $perPage     = max(5, (int)$r->query('per_page', 25));

    $q = DB::table('parameter_uji as pu')
        ->leftJoin('master_kategori as k', 'k.id_kategori', '=', 'pu.id_kategori');

    // Filter paket header/sub yang sudah ada
    if ($headerQ !== null && $subHeaderQ === null) {
        $q->where('pu.header', 0)->where('pu.sub_header', (int)$headerQ);
    }
    if ($subHeaderQ !== null) {
        $q->where('pu.sub_header', (int)$subHeaderQ);
    }

    // Filter kategori (opsional)
    if ($kategoriQ !== null && $kategoriQ !== '') {
        $q->where('pu.id_kategori', (int)$kategoriQ);
    }

    // Pencarian bebas
    if ($search !== '') {
        $like = '%'.str_replace(['%','_'], ['\%','\_'], $search).'%';
        $q->where(function($w) use ($like){
            $w->where('pu.kode_parameter', 'like', $like)
              ->orWhere('pu.nama_parameter', 'like', $like)
              ->orWhere('pu.tipe_hasil_pemeriksaan', 'like', $like);
        });
    }

    $data = $q->select([
            'pu.id_parameter',
            DB::raw('TRIM(pu.kode_parameter) as kode'),
            DB::raw('pu.nama_parameter as nama'),
            'pu.satuan',
            'pu.id_kategori',
            // ← ini kuncinya: kasih nama kategori buat ditampilkan & di-group di FE
            DB::raw("COALESCE(k.nama_kategori, 'Tanpa kategori') AS kategori_nama"),
            DB::raw("TRIM(CONCAT(
                'Nilai Normal : ', COALESCE(NULLIF(pu.nilai_normal,''),'Negatif'), '\n',
                'Nilai Kritis : ', COALESCE(NULLIF(pu.nilai_kritis,''),'')
            )) AS nilai_normal_kritis"),
        ])
        // Urut: yang punya kategori dulu, lalu alfabet kategori, lalu nama pemeriksaan
        ->orderByRaw('(k.nama_kategori IS NULL)')
        ->orderBy('k.nama_kategori')
        ->orderBy('pu.nama_parameter')
        ->paginate($perPage);

    return response()->json($data);
}

    /** Simpan item terpilih (berdasar id_parameter[]) ke simpus_tindakan */
public function paramSimpanTerpilih(Request $request)
{
    $val = $request->validate([
        'loketId'      => 'required|string',   // hanya untuk UI
        'permohonanId' => 'required|string',
        'pelayananId'  => 'nullable',
        'ids'          => 'required|array|min:1',
        'ids.*'        => 'integer',
        'nilaiLab'     => 'nullable|array',
    ]);

    $orderId = trim($val['permohonanId']);

    $loketId = trim((string) DB::table('simpus_permohonan_lab')
        ->whereRaw('TRIM(idPermohonan)=?', [$orderId])
        ->value('loketId'));

    if ($loketId === '') return back()->withErrors(['permohonanId' => 'Permohonan ID tidak valid.']);

    $this->ensureLoketParentExists($loketId, $orderId);

    $pelayanan = $val['pelayananId'] ?? null;

    $items = DB::table($this->paramTable)
        ->whereIn('id_parameter', $val['ids'])
        ->select(['id_parameter','kode_parameter','nama_parameter','satuan','nilai_normal','nilai_kritis'])
        ->get();

    DB::transaction(function () use ($loketId, $orderId, $pelayanan, $items) {
        $this->insertParamBatch($loketId, $orderId, $pelayanan, $items->all());
    });

    return back();
}

public function simpanPermohonanLab(Request $request, $idLoket)
{
    $namaPoli = SimpusPoliFKTP::where('kdPoli', $request->idPoli)->first();

    SimpusPermohonanLab::create([
        'idPermohonan'        => Str::random(20),
        'pasienId'            => $request->idPasien,
        'loketId'             => $idLoket,
        'pelayananId'         => $request->idPelayanan,
        'nmPoli'              => $namaPoli->nmPoli,
        'tglDibuat'           => now(),
        'tenagaMedisPengirim' => $request->tenaga_medis,
        'alasanDirujuk'       => $request->alasan,
        'hasilLabLuarFaskes'  => $request->hasil_lab_luar_faskes,
        'statusLayanan'       => '0',
        'createdDate'         => now(),
    ]);

    return redirect()->back();
}


    public function paramCategories(Request $request)
{
    $rows = DB::table('master_kategori as k')
        ->leftJoin('parameter_uji as pu', 'pu.id_kategori', '=', 'k.id_kategori')
        ->select(
            'k.id_kategori',
            'k.nama_kategori',
            DB::raw('COUNT(pu.id_parameter) as jumlah')
        )
        ->groupBy('k.id_kategori','k.nama_kategori')
        ->orderBy('k.nama_kategori')
        ->get();

    return response()->json($rows);
}


}
