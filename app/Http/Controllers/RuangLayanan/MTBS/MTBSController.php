<?php

namespace App\Http\Controllers\RuangLayanan\MTBS;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\DataMasterUnitDetail;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class MTBSController extends Controller
{
    public function index()
    {
        $userAuth = Auth::user();

        $DataUnit = DataMasterUnitDetail::with('DataMasterUnit')
            ->where('id_unit', $userAuth->unit)
            ->orderBy('id_kategori')
            ->get();

        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')

            ->leftJoin('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                     ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                     ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                     ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->leftJoin('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                     ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                     ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->leftJoin('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                     ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->leftJoin('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')

->where('l.kdPoli', '003')
->whereDate('l.tglKunjungan', now()->toDateString())
            ->select(
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'pel.sudahDilayani',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kdPoli'
            )
            ->get();

        return Inertia::render('Ruang_Layanan/KIA/MTBS/Index', [
            'DataUnit' => $DataUnit,
            'DataPasien' => $DataPasien,
        ]);
    }

public function pelayanan($id, $idPoli, $idPelayanan)
{
    $DataPasien = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('pel.idpelayanan', $idPelayanan)
        ->select(
            'p.ID as ID',
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'p.JENIS_KLMIN',
            'p.TGL_LHR',
            'poli.nmPoli',
            'p.alamat',
            'l.tglKunjungan',
            'l.kdPoli'
        )
        ->get()
        ->map(function ($pasien) {
            $pasien->jenis_kelamin_label = $pasien->JENIS_KLMIN == 1 ? 'L' : 'P';

if (!empty($pasien->TGL_LHR)) {
    $lahir = Carbon::parse($pasien->TGL_LHR);
    $kunjungan = Carbon::parse($pasien->tglKunjungan ?? now());

    $diff = $lahir->diff($kunjungan);

    $pasien->umur = $diff->y;
    $pasien->umur_bulan_sisa = $diff->m;
    $pasien->umur_hari = $diff->d;

    // ✅ INI YANG DIPAKAI GIZI MTBS
    $pasien->umur_bulan = ($diff->y * 12) + $diff->m;
} else {
    $pasien->umur = 0;
    $pasien->umur_bulan_sisa = 0;
    $pasien->umur_bulan = 0;
    $pasien->umur_hari = 0;
}
            return $pasien;
        });

    return Inertia::render('Ruang_Layanan/KIA/MTBS/Pelayanan', [
        'idPelayanan' => $idPelayanan,
        'idPoli' => $idPoli,
        'DataPasien' => $DataPasien,
    ]);
}

public function storeSubjektif(Request $request)
{
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'umurTahun' => $request->umurTahun === '' ? null : $request->umurTahun,
        'umurBulan' => $request->umurBulan === '' ? null : $request->umurBulan,
        'batukLama' => $request->batukLama === '' ? null : $request->batukLama,
        'diareLama' => $request->diareLama === '' ? null : $request->diareLama,
        'demamLama' => $request->demamLama === '' ? null : $request->demamLama,
        'telingaLama' => $request->telingaLama === '' ? null : $request->telingaLama,
    ]);

    $validated = validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'jenisKunjungan' => ['nullable', 'in:pertama,ulang'],
        'umurTahun' => ['nullable', 'integer', 'min:0', 'max:4'],
        'umurBulan' => ['nullable', 'integer', 'min:0', 'max:11'],
        'jenisKelamin' => ['nullable', 'in:L,P'],
        'keluhanUtama' => ['nullable', 'array'],
        'keluhanLain' => ['nullable', 'string'],
        'batukLama' => ['nullable', 'integer', 'min:0'],
        'diareLama' => ['nullable', 'integer', 'min:0'],
        'demamLama' => ['nullable', 'integer', 'min:0'],
        'telingaLama' => ['nullable', 'integer', 'min:0'],
        'riwayatImunisasi' => ['nullable', 'string'],
        'vitaminA' => ['nullable', 'string'],
        'riwayatASI' => ['nullable', 'string'],
        'riwayatPenyakit' => ['nullable', 'string'],
        'hivIbu' => ['nullable', 'string'],

        'anamnesisKhusus' => ['required', 'array'],
        'anamnesisKhusus.tanda_bahaya' => ['required', 'array'],
        'anamnesisKhusus.tanda_bahaya.bisa_minum_menyusu' => ['required', 'boolean'],
        'anamnesisKhusus.tanda_bahaya.memuntahkan_semua' => ['required', 'boolean'],
        'anamnesisKhusus.tanda_bahaya.pernah_kejang' => ['required', 'boolean'],
        'anamnesisKhusus.batuk.ada' => ['required', 'boolean'],
        'anamnesisKhusus.diare.ada' => ['required', 'boolean'],
        'anamnesisKhusus.demam.ada' => ['required', 'boolean'],
        'anamnesisKhusus.telinga.ada' => ['required', 'boolean'],
    ])->validate();

    $kunjunganId = (string) $validated['kunjungan_id'];
    $anamnesis = $this->normalisasiAnamnesisKhususMtbs($validated['anamnesisKhusus']);
    $identitas = $this->identitasPasienMtbs($kunjunganId);

    try {
        DB::table('mtbs_subjektif')->updateOrInsert(
            ['kunjungan_id' => $kunjunganId],
            [
                'jenis_kunjungan' => $validated['jenisKunjungan'] ?? $this->getJenisKunjunganOtomatis($kunjunganId),
                'umur_tahun' => $identitas['umur_tahun'] ?? ($validated['umurTahun'] ?? null),
                'umur_bulan' => $identitas['umur_bulan'] ?? ($validated['umurBulan'] ?? null),
                'jenis_kelamin' => $identitas['jenis_kelamin'] ?? ($validated['jenisKelamin'] ?? null),

                'keluhan_utama' => json_encode($validated['keluhanUtama'] ?? []),
                'keluhan_lain' => $validated['keluhanLain'] ?? null,

                'batuk_lama_hari' => data_get($anamnesis, 'batuk.ada') ? ($validated['batukLama'] ?? null) : null,
                // Napas cepat dan mengi merupakan hasil pemeriksaan Objektif.
                'napas_cepat' => 0,
                'mengi' => 0,

                'diare_lama_hari' => data_get($anamnesis, 'diare.ada') ? ($validated['diareLama'] ?? null) : null,
                'darah_tinja' => (int) (data_get($anamnesis, 'diare.ada') && $request->boolean('darahTinja')),

                'demam_lama_hari' => data_get($anamnesis, 'demam.ada') ? ($validated['demamLama'] ?? null) : null,
                'demam_tiap_hari' => (int) (data_get($anamnesis, 'demam.ada') && $request->boolean('demamTiapHari')),
                'riwayat_malaria' => (int) (data_get($anamnesis, 'demam.ada') && $request->boolean('riwayatMalaria')),
                'riwayat_campak' => (int) (data_get($anamnesis, 'demam.ada') && $request->boolean('riwayatCampak')),

                'nyeri_telinga' => (int) (data_get($anamnesis, 'telinga.ada') && $request->boolean('nyeriTelinga')),
                'cairan_telinga' => (int) (data_get($anamnesis, 'telinga.ada') && $request->boolean('cairanTelinga')),
                'telinga_lama_hari' => (data_get($anamnesis, 'telinga.ada') && $request->boolean('cairanTelinga'))
                    ? ($validated['telingaLama'] ?? null)
                    : null,

                'riwayat_imunisasi' => $validated['riwayatImunisasi'] ?? null,
                'vitamin_a' => $validated['vitaminA'] ?? null,
                'riwayat_asi' => $validated['riwayatASI'] ?? null,
                'riwayat_penyakit' => $validated['riwayatPenyakit'] ?? null,
                'hiv_ibu' => $validated['hivIbu'] ?? null,
                'anamnesis_khusus' => json_encode($anamnesis),
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Subjektif MTBS berhasil disimpan',
            'tandaBahaya' => $this->tandaBahayaDariAnamnesisMtbs($anamnesis),
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBS storeSubjektif error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan subjektif MTBS',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function showSubjektif($kunjunganId)
{
    $kunjunganId = (string) $kunjunganId;
    $jenisKunjunganOtomatis = $this->getJenisKunjunganOtomatis($kunjunganId);
    $identitas = $this->identitasPasienMtbs($kunjunganId);

    $row = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    if (!$row) {
        return response()->json([
            'data' => [
                'jenisKunjungan' => $jenisKunjunganOtomatis,
                'jenisKunjunganOtomatis' => true,
                'umurTahun' => $identitas['umur_tahun'],
                'umurBulan' => $identitas['umur_bulan'],
                'jenisKelamin' => $identitas['jenis_kelamin'],
                'keluhanUtama' => [],
                'keluhanLain' => '',
                'batukLama' => null,
                'napasCepat' => false,
                'mengi' => false,
                'diareLama' => null,
                'darahTinja' => false,
                'demamLama' => null,
                'demamTiapHari' => false,
                'riwayatMalaria' => false,
                'riwayatCampak' => false,
                'nyeriTelinga' => false,
                'cairanTelinga' => false,
                'telingaLama' => null,
                'anamnesisKhusus' => $this->defaultAnamnesisKhususMtbs(),
                'riwayatImunisasi' => '',
                'vitaminA' => '',
                'riwayatASI' => '',
                'riwayatPenyakit' => '',
                'hivIbu' => '',
            ],
        ], 200);
    }

    $anamnesis = $this->normalisasiAnamnesisKhususMtbs($row->anamnesis_khusus ?? null);

    // Kompatibilitas data lama sebelum kolom anamnesis_khusus digunakan.
    if (empty($row->anamnesis_khusus)) {
        $anamnesis['batuk']['ada'] = ((int) ($row->batuk_lama_hari ?? 0) > 0);
        $anamnesis['diare']['ada'] = ((int) ($row->diare_lama_hari ?? 0) > 0) || (bool) ($row->darah_tinja ?? false);
        $anamnesis['demam']['ada'] = ((int) ($row->demam_lama_hari ?? 0) > 0);
        $anamnesis['demam']['campak_3_bulan'] = (bool) ($row->riwayat_campak ?? false);
        $anamnesis['telinga']['ada'] = (bool) ($row->nyeri_telinga ?? false) || (bool) ($row->cairan_telinga ?? false);

        $legacyTandaBahaya = DB::table('mtbs_objektif')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->value('tanda_bahaya');
        $legacyTandaBahaya = $this->decodeArrayMtbs($legacyTandaBahaya);
        if ($this->adaTeksMtbs($legacyTandaBahaya, ['tidak bisa minum', 'tidak bisa menyusu'])) {
            $anamnesis['tanda_bahaya']['bisa_minum_menyusu'] = false;
        }
        if ($this->adaTeksMtbs($legacyTandaBahaya, ['memuntahkan semua'])) {
            $anamnesis['tanda_bahaya']['memuntahkan_semua'] = true;
        }
        if ($this->adaTeksMtbs($legacyTandaBahaya, ['pernah kejang', 'kejang selama sakit'])) {
            $anamnesis['tanda_bahaya']['pernah_kejang'] = true;
        }
    }

    return response()->json([
        'data' => [
            'jenisKunjungan' => $jenisKunjunganOtomatis,
            'jenisKunjunganOtomatis' => true,
            'umurTahun' => $row->umur_tahun ?? $identitas['umur_tahun'],
            'umurBulan' => $row->umur_bulan ?? $identitas['umur_bulan'],
            'jenisKelamin' => $row->jenis_kelamin ?? $identitas['jenis_kelamin'],
            'keluhanUtama' => json_decode($row->keluhan_utama ?? '[]', true) ?: [],
            'keluhanLain' => $row->keluhan_lain,
            'batukLama' => $row->batuk_lama_hari,
            'napasCepat' => false,
            'mengi' => false,
            'diareLama' => $row->diare_lama_hari,
            'darahTinja' => (bool) $row->darah_tinja,
            'demamLama' => $row->demam_lama_hari,
            'demamTiapHari' => (bool) $row->demam_tiap_hari,
            'riwayatMalaria' => (bool) $row->riwayat_malaria,
            'riwayatCampak' => (bool) $row->riwayat_campak,
            'nyeriTelinga' => (bool) $row->nyeri_telinga,
            'cairanTelinga' => (bool) $row->cairan_telinga,
            'telingaLama' => $row->telinga_lama_hari,
            'anamnesisKhusus' => $anamnesis,
            'riwayatImunisasi' => $row->riwayat_imunisasi,
            'vitaminA' => $row->vitamin_a,
            'riwayatASI' => $row->riwayat_asi,
            'riwayatPenyakit' => $row->riwayat_penyakit,
            'hivIbu' => $row->hiv_ibu,
        ],
    ], 200);
}
public function riwayatPasien(Request $request, $idPelayanan)
{
    $pasienSekarang = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->where('pel.idpelayanan', $idPelayanan)
        ->select(
            'p.ID as pasien_id',
            'p.NAMA_LGKP',
            'p.NO_MR',
            'p.NIK'
        )
        ->first();

    if (!$pasienSekarang) {
        return response()->json([
            'message' => 'Data pasien tidak ditemukan',
            'filter' => [
                'tahun' => null,
                'tahunTersedia' => [],
            ],
            'data' => [
                'alergiObat' => [],
                'alergiMakanan' => [],
                'riwayatKeluhan' => [],
                'riwayatDiagnosa' => [],
                'riwayatObat' => [],
                'riwayatKunjungan' => [],
            ],
        ], 404);
    }

    /*
     * Daftar tahun diambil dari seluruh kunjungan pasien.
     * Jika parameter tahun belum dikirim, gunakan tahun kunjungan terbaru.
     */
    $tahunTersedia = DB::table('simpus_loket')
        ->where('pasienId', $pasienSekarang->pasien_id)
        ->whereNotNull('tglKunjungan')
        ->selectRaw('YEAR(tglKunjungan) as tahun')
        ->distinct()
        ->orderByDesc('tahun')
        ->pluck('tahun')
        ->map(fn ($tahun) => (int) $tahun)
        ->filter(fn ($tahun) => $tahun > 0)
        ->values();

    $tahunDiminta = $request->query('tahun');

    $tahun = ($tahunDiminta !== null && $tahunDiminta !== '')
        ? (int) $tahunDiminta
        : ($tahunTersedia->first() ?? (int) now()->year);

    validator(
        ['tahun' => $tahun],
        [
            'tahun' => [
                'required',
                'integer',
                'min:1900',
                'max:' . ((int) now()->year + 1),
            ],
        ]
    )->validate();

    $user = Auth::user();
    $unitName = $user?->name
        ?? $user?->username
        ?? 'Puskesmas';

    $riwayatKunjungan = DB::table('simpus_loket as l')
        ->leftJoin('simpus_pelayanan as pel', 'pel.loketId', '=', 'l.idLoket')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('l.pasienId', $pasienSekarang->pasien_id)
        ->whereYear('l.tglKunjungan', $tahun)
        ->select(
            'l.idLoket',
            'l.tglKunjungan',
            'l.kdPoli',
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'pel.sudahDilayani',
            'poli.nmPoli'
        )
        ->orderByDesc('l.tglKunjungan')
        ->orderByDesc('l.idLoket')
        ->get()
        ->map(function ($row) use ($unitName) {
            return [
                'idLoket' => $row->idLoket,
                'idpelayanan' => $row->idpelayanan,
                'tanggal_kunjungan' => $row->tglKunjungan,
                'tanggal_pelayanan' => $row->tglPelayanan,
                'poli' => $row->nmPoli ?? '-',
                'kdPoli' => $row->kdPoli ?? '-',
                'puskesmas' => $unitName,
                'status' => !empty($row->sudahDilayani)
                    ? 'Sudah dilayani'
                    : 'Belum dilayani',
            ];
        });

    // Keluhan dan klasifikasi MTBS bukan diagnosis medis ICD.
    $riwayatKeluhan = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->leftJoin('mtbs_subjektif as sub', 'sub.kunjungan_id', '=', 'pel.idpelayanan')
        ->leftJoin('mtbs_assessment as ass', 'ass.kunjungan_id', '=', 'pel.idpelayanan')
        ->where('l.pasienId', $pasienSekarang->pasien_id)
        ->whereYear('l.tglKunjungan', $tahun)
        ->select(
            'pel.idpelayanan',
            'l.tglKunjungan',
            'poli.nmPoli',
            'sub.keluhan_utama',
            'sub.keluhan_lain',
            'ass.klasifikasi_global'
        )
        ->orderByDesc('l.tglKunjungan')
        ->orderByDesc('pel.idpelayanan')
        ->get()
        ->map(function ($row) {
            $keluhanUtama = json_decode($row->keluhan_utama ?? '[]', true) ?: [];
            $klasifikasi = json_decode($row->klasifikasi_global ?? '[]', true) ?: [];

            $keluhanText = count($keluhanUtama)
                ? implode(', ', $keluhanUtama)
                : '';

            if (!empty($row->keluhan_lain)) {
                $keluhanText .= $keluhanText
                    ? ', ' . $row->keluhan_lain
                    : $row->keluhan_lain;
            }

            return [
                'id' => $row->idpelayanan,
                'tanggal' => $row->tglKunjungan,
                'poli' => $row->nmPoli ?? '-',
                'keluhan' => $keluhanText ?: '-',
                'klasifikasi' => count($klasifikasi)
                    ? implode(', ', $klasifikasi)
                    : '-',
            ];
        });

    // Diagnosis medis yang benar-benar dipilih petugas dari master diagnosis.
    $riwayatDiagnosa = DB::table('mtbs_diagnosa_medis as d')
        ->join('simpus_pelayanan as pel', 'd.kunjungan_id', '=', 'pel.idpelayanan')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('l.pasienId', $pasienSekarang->pasien_id)
        ->whereYear('l.tglKunjungan', $tahun)
        ->select(
            'd.id',
            'd.kunjungan_id',
            'l.tglKunjungan',
            'poli.nmPoli',
            'd.kdDiag',
            'd.nmDiag',
            'd.kasus',
            'd.keterangan',
            'd.created_by'
        )
        ->orderByDesc('l.tglKunjungan')
        ->orderByDesc('d.id')
        ->get()
        ->map(function ($row) {
            return [
                'id' => $row->id,
                'kunjungan_id' => $row->kunjungan_id,
                'tanggal' => $row->tglKunjungan,
                'poli' => $row->nmPoli ?? '-',
                'kode' => $row->kdDiag ?? '-',
                'nama' => $row->nmDiag ?? '-',
                'kasus' => $row->kasus ?? '-',
                'keterangan' => $row->keterangan ?: '-',
                'petugas' => $row->created_by ?: '-',
            ];
        });

    // Obat MTBS disimpan sebagai JSON pada mtbs_planning.pengobatan.
    // Satu kunjungan dapat berisi lebih dari satu obat, sehingga hasil diratakan.
    $riwayatObat = DB::table('mtbs_planning as plan')
        ->join('simpus_pelayanan as pel', 'plan.kunjungan_id', '=', 'pel.idpelayanan')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('l.pasienId', $pasienSekarang->pasien_id)
        ->whereYear('l.tglKunjungan', $tahun)
        ->select(
            'plan.id',
            'plan.kunjungan_id',
            'plan.pengobatan',
            'plan.created_by',
            'l.tglKunjungan',
            'poli.nmPoli'
        )
        ->orderByDesc('l.tglKunjungan')
        ->orderByDesc('plan.id')
        ->get()
        ->flatMap(function ($row) {
            $pengobatan = json_decode($row->pengobatan ?? '[]', true);

            if (!is_array($pengobatan)) {
                return [];
            }

            return collect($pengobatan)
                ->map(function ($obat, $index) use ($row) {
                    if (is_string($obat)) {
                        $obat = ['nama' => $obat];
                    }

                    if (!is_array($obat)) {
                        return null;
                    }

                    $nama = trim((string) ($obat['nama'] ?? ''));
                    if ($nama === '') {
                        return null;
                    }

                    $lama = $obat['lama'] ?? null;

                    return [
                        'id' => $row->id . '-' . $index,
                        'kunjungan_id' => $row->kunjungan_id,
                        'tanggal' => $row->tglKunjungan,
                        'poli' => $row->nmPoli ?? '-',
                        'nama' => $nama,
                        'dosis' => $obat['dosis'] ?? '-',
                        'cara' => !empty($obat['cara'])
                            ? ucfirst((string) $obat['cara'])
                            : '-',
                        'lama' => ($lama !== null && $lama !== '')
                            ? $lama . ' hari'
                            : '-',
                        'petugas' => $row->created_by ?: '-',
                    ];
                })
                ->filter()
                ->values();
        })
        ->values();

    /*
     * Alergi tidak difilter per tahun karena tetap penting sebagai informasi
     * keselamatan pasien meskipun dicatat pada kunjungan tahun sebelumnya.
     */
    $riwayatAlergi = DB::table('mtbs_alergi as a')
        ->leftJoin('simpus_pelayanan as pel', 'a.kunjungan_id', '=', 'pel.idpelayanan')
        ->leftJoin('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where(function ($query) use ($pasienSekarang) {
            $query->where('a.pasien_id', $pasienSekarang->pasien_id)
                ->orWhere('l.pasienId', $pasienSekarang->pasien_id);
        })
        ->select('a.alergi_obat', 'a.alergi_makanan')
        ->orderByDesc('a.id')
        ->get();

    $pecahAlergi = static function ($values) {
        return collect($values)
            ->flatMap(function ($value) {
                return preg_split('/[,;]+/', (string) $value) ?: [];
            })
            ->map(fn ($value) => trim($value))
            ->filter(fn ($value) => $value !== '' && $value !== '-')
            ->unique(fn ($value) => strtolower($value))
            ->values();
    };

    return response()->json([
        'pasien' => $pasienSekarang,
        'filter' => [
            'tahun' => $tahun,
            'tahunTersedia' => $tahunTersedia,
        ],
        'data' => [
            'alergiObat' => $pecahAlergi($riwayatAlergi->pluck('alergi_obat')),
            'alergiMakanan' => $pecahAlergi($riwayatAlergi->pluck('alergi_makanan')),
            'riwayatKeluhan' => $riwayatKeluhan,
            'riwayatDiagnosa' => $riwayatDiagnosa,
            'riwayatObat' => $riwayatObat,
            'riwayatKunjungan' => $riwayatKunjungan,
        ],
    ], 200);
}

public function storeAlergi(Request $request)
{
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'pasien_id' => $request->pasien_id === '' ? null : $request->pasien_id,
    ]);

    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'pasien_id' => ['nullable', 'integer'],
        'alergiMakanan' => ['nullable', 'string', 'max:150'],
        'alergiObat' => ['nullable', 'string', 'max:150'],
        'keteranganAlergi' => ['nullable', 'string'],
    ])->validate();

    try {
        $user = Auth::user();
        $createdBy = $user ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas') : 'Petugas';

        DB::table('mtbs_alergi')->updateOrInsert(
            ['kunjungan_id' => $request->kunjungan_id],
            [
                'pasien_id' => $request->pasien_id,
                'alergi_makanan' => $request->alergiMakanan,
                'alergi_obat' => $request->alergiObat,
                'keterangan_alergi' => $request->keteranganAlergi,
                'created_by' => $createdBy,
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Alergi MTBS berhasil disimpan',
        ], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS storeAlergi error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan alergi MTBS',
            'error' => $e->getMessage(),
        ], 500);
    }
}



public function storeDiagnosaMedis(Request $request)
{
    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'pasien_id' => ['nullable', 'integer'],
        'diagnosa_id' => ['required', 'integer'],
        'kodeDiagnosa' => ['required', 'string', 'max:255'],
        'namaDiagnosa' => ['required', 'string', 'max:255'],
        'keterangan' => ['nullable', 'string'],
        'kasus' => ['required', 'in:baru,lama'],
        'poli' => ['nullable', 'string', 'max:100'],
    ])->validate();

    try {
        $user = Auth::user();
        $createdBy = $user ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas') : 'Petugas';

        DB::table('mtbs_diagnosa_medis')->insert([
            'kunjungan_id' => $request->kunjungan_id,
            'pasien_id' => $request->pasien_id,
            'diagnosa_id' => $request->diagnosa_id,
            'kdDiag' => $request->kodeDiagnosa,
            'nmDiag' => $request->namaDiagnosa,
            'kasus' => $request->kasus,
            'keterangan' => $request->keterangan,
            'poli' => $request->poli,
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Diagnosa medis berhasil disimpan',
        ], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS storeDiagnosaMedis error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan diagnosa medis',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function showDiagnosaMedis($kunjunganId)
{
    try {
        $data = DB::table('mtbs_diagnosa_medis')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'diagnosaId' => $row->diagnosa_id,
                    'kodeDiagnosa' => $row->kdDiag,
                    'namaDiagnosa' => $row->nmDiag,
                    'keterangan' => $row->keterangan,
                    'kasus' => $row->kasus,
                    'poli' => $row->poli,
                ];
            });

        return response()->json([
            'data' => $data,
        ], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS showDiagnosaMedis error', [
            'msg' => $e->getMessage(),
            'kunjungan_id' => $kunjunganId,
        ]);

        return response()->json([
            'message' => 'Gagal mengambil diagnosa medis',
            'error' => $e->getMessage(),
            'data' => [],
        ], 500);
    }
}
public function cariDiagnosaMedis(Request $request)
{
    $q = trim($request->q ?? '');

    $limit = (int) ($request->limit ?? 10);
    $limit = $limit > 0 ? min($limit, 10) : 10;

    $data = DB::table('simpus_diagnosa')
        ->select(
            'id',
            'kdDiag',
            'nmDiag',
            'kunjSehat',
            'klb',
            'klb_kategori',
            'kategori_penyakit'
        )
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('kdDiag', 'like', '%' . $q . '%')
                    ->orWhere('nmDiag', 'like', '%' . $q . '%');
            });
        })
        ->orderBy('nmDiag', 'asc')
        ->limit($limit)
        ->get();

    return response()->json([
        'data' => $data
    ], 200);
}

public function cariTindakan(Request $request)
{
    $q = trim($request->get('q', ''));

    $rows = DB::table('simpus_master_tindakan')
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('kode', 'like', "%{$q}%")
                    ->orWhere('nama_tindakan', 'like', "%{$q}%")
                    ->orWhere('nama_tindakan_indonesia', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        })
        ->orderBy('kode')
        ->limit(10)
        ->get();

    return response()->json([
        'data' => $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'kode' => $r->kode ?? '',
                'nama' => $r->nama_tindakan ?? '',
                'nama_ind' => $r->nama_tindakan_indonesia ?? '',
                'keterangan' => $r->deskripsi ?? '',
                'peraturan' => $r->nilai_normal ?? '',
                'harga' => $r->harga ?? '',
                'bayar' => $r->simTarif ?? '',
                'poli' => 'Umum',
                'ket_gigi' => '',
                'created_by' => Auth::user()->name ?? Auth::user()->username ?? '',
            ];
        }),
    ]);
}
public function showAlergi($kunjunganId)
{
    $pelayanan = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('pel.idpelayanan', $kunjunganId)
        ->select('l.pasienId')
        ->first();

    if (!$pelayanan) {
        return response()->json([
            'data' => [
                'current' => null,
                'riwayat' => [],
            ]
        ], 200);
    }

    $current = DB::table('mtbs_alergi')
        ->where('kunjungan_id', $kunjunganId)
        ->first();

    $riwayat = DB::table('mtbs_alergi as a')
        ->leftJoin('simpus_pelayanan as pel', 'a.kunjungan_id', '=', 'pel.idpelayanan')
        ->leftJoin('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('a.pasien_id', $pelayanan->pasienId)
        ->orWhere('l.pasienId', $pelayanan->pasienId)
        ->select(
            'a.id',
            'a.alergi_makanan',
            'a.alergi_obat',
            'a.keterangan_alergi',
            'a.created_at'
        )
        ->orderByDesc('a.id')
        ->get()
        ->map(function ($row) {
            return [
                'id' => $row->id,
                'alergiMakanan' => $row->alergi_makanan,
                'alergiObat' => $row->alergi_obat,
                'keteranganAlergi' => $row->keterangan_alergi,
                'tanggal' => $row->created_at ? date('Y-m-d', strtotime($row->created_at)) : '-',
            ];
        });

    return response()->json([
        'data' => [
            'current' => $current ? [
                'alergiMakanan' => $current->alergi_makanan,
                'alergiObat' => $current->alergi_obat,
                'keteranganAlergi' => $current->keterangan_alergi,
            ] : null,
            'riwayat' => $riwayat,
        ],
    ], 200);
}
public function storeObjektif(Request $request)
{
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'rr' => $request->rr === '' ? null : $request->rr,
        'suhu' => $request->suhu === '' ? null : $request->suhu,
        'spo2' => $request->spo2 === '' ? null : $request->spo2,
        'bb' => $request->bb === '' ? null : $request->bb,
        'tb' => $request->tb === '' ? null : $request->tb,
        'lila' => $request->lila === '' ? null : $request->lila,
        'lk' => $request->lk === '' ? null : $request->lk,
    ]);

    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'tandaBahaya' => ['nullable', 'array'],
        'saga' => ['nullable', 'array'],
        'saga.penampilan' => ['nullable', 'array'],
        'saga.napas' => ['nullable', 'array'],
        'saga.sirkulasi' => ['nullable', 'array'],
        'rr' => ['nullable', 'integer', 'min:0', 'max:200'],
        'suhu' => ['nullable', 'numeric', 'min:0', 'max:50'],
        'spo2' => ['nullable', 'integer', 'min:0', 'max:100'],
        'bb' => ['nullable', 'numeric', 'min:0', 'max:300'],
        'tb' => ['nullable', 'numeric', 'min:0', 'max:250'],
        'lila' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'lk' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'pemeriksaanKhusus' => ['nullable', 'array'],
        'statusSAGA' => ['nullable', 'string', 'max:50'],
    ])->validate();

    $kunjunganId = (string) $request->kunjungan_id;
    $subjektif = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();
    $anamnesis = $this->normalisasiAnamnesisKhususMtbs($subjektif->anamnesis_khusus ?? null);

    // Tanda bahaya umum bersumber dari anamnesis Subjektif. Payload lama tetap
    // digabung agar data kunjungan lama tidak hilang.
    $tandaBahaya = array_values(array_unique(array_filter(array_merge(
        $request->tandaBahaya ?? [],
        $this->tandaBahayaDariAnamnesisMtbs($anamnesis)
    ))));
    $penampilan = array_values(array_filter(data_get($request->saga, 'penampilan', []) ?? []));
    $napas = array_values(array_filter(data_get($request->saga, 'napas', []) ?? []));
    $sirkulasi = array_values(array_filter(data_get($request->saga, 'sirkulasi', []) ?? []));

    $adaTandaBahaya = count($tandaBahaya) > 0;
    $adaPenampilan = count($penampilan) > 0;
    $adaNapas = count($napas) > 0;
    $adaSirkulasi = count($sirkulasi) > 0;

    if ($adaPenampilan && $adaNapas && $adaSirkulasi) {
        $statusSAGA = 'GAGAL JANTUNG PARU';
    } elseif ($adaTandaBahaya || $adaPenampilan || $adaNapas || $adaSirkulasi) {
        $statusSAGA = 'PENYAKIT SANGAT BERAT';
    } else {
        $statusSAGA = 'STABIL';
    }

    $toNullIfEmpty = static fn ($value) => ($value === '' || $value === null) ? null : $value;

    try {
        DB::table('mtbs_objektif')->updateOrInsert(
            ['kunjungan_id' => $kunjunganId],
            [
                'tanda_bahaya' => json_encode($tandaBahaya),
                'saga_penampilan' => json_encode($penampilan),
                'saga_napas' => json_encode($napas),
                'saga_sirkulasi' => json_encode($sirkulasi),
                'rr' => $toNullIfEmpty($request->rr),
                'suhu' => $toNullIfEmpty($request->suhu),
                'spo2' => $toNullIfEmpty($request->spo2),
                'bb' => $toNullIfEmpty($request->bb),
                'tb' => $toNullIfEmpty($request->tb),
                'lila' => $toNullIfEmpty($request->lila),
                'lk' => $toNullIfEmpty($request->lk),
                'pemeriksaan_khusus' => json_encode($request->pemeriksaanKhusus ?? []),
                'status_saga' => $statusSAGA,
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Objektif MTBS berhasil disimpan',
            'statusSAGA' => $statusSAGA,
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBS storeObjektif error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan objektif MTBS',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function showObjektif($kunjunganId)
{
    $row = DB::table('mtbs_objektif')
        ->where('kunjungan_id', $kunjunganId)
        ->first(); // karena nanti 1 kunjungan_id = 1 baris (unique)

    if (!$row) {
        return response()->json(['data' => null], 200);
    }

    return response()->json([
        'data' => [
            'tandaBahaya' => json_decode($row->tanda_bahaya ?? '[]', true) ?: [],
            'saga' => [
                'penampilan' => json_decode($row->saga_penampilan ?? '[]', true) ?: [],
                'napas'      => json_decode($row->saga_napas ?? '[]', true) ?: [],
                'sirkulasi'  => json_decode($row->saga_sirkulasi ?? '[]', true) ?: [],
            ],

            'rr'   => $row->rr,
            'suhu' => $row->suhu,
            'spo2' => $row->spo2,
            'bb'   => $row->bb,
            'tb'   => $row->tb,
            'lila' => $row->lila,
            'lk'   => $row->lk,

            'pemeriksaanKhusus' => json_decode($row->pemeriksaan_khusus ?? '[]', true) ?: [],
            'statusSAGA' => $row->status_saga,
        ]
    ], 200);
}


public function ringkasanSubjektifObjektif($kunjunganId)
{
    try {
        $subjektif = DB::table('mtbs_subjektif')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $objektif = DB::table('mtbs_objektif')
            ->where('kunjungan_id', $kunjunganId)
            ->orderByDesc('id')
            ->first();

        $decodeArray = static function ($value): array {
            if (is_array($value)) {
                return array_values(array_filter($value, static fn ($item) => $item !== null && $item !== ''));
            }

            if ($value === null || $value === '') {
                return [];
            }

            $decoded = json_decode($value, true);

            return is_array($decoded)
                ? array_values(array_filter($decoded, static fn ($item) => $item !== null && $item !== ''))
                : [];
        };

        $subjektifData = null;

        if ($subjektif) {
            $umurParts = [];

            if ($subjektif->umur_tahun !== null) {
                $umurParts[] = (int) $subjektif->umur_tahun . ' tahun';
            }

            if ($subjektif->umur_bulan !== null) {
                $umurParts[] = (int) $subjektif->umur_bulan . ' bulan';
            }

            $temuanSubjektif = [];

            if (!empty($subjektif->napas_cepat)) {
                $temuanSubjektif[] = 'Napas cepat';
            }

            if (!empty($subjektif->mengi)) {
                $temuanSubjektif[] = 'Mengi';
            }

            if (!empty($subjektif->darah_tinja)) {
                $temuanSubjektif[] = 'Darah dalam tinja';
            }

            if (!empty($subjektif->demam_tiap_hari)) {
                $temuanSubjektif[] = 'Demam setiap hari';
            }

            if (!empty($subjektif->riwayat_malaria)) {
                $temuanSubjektif[] = 'Riwayat malaria';
            }

            if (!empty($subjektif->riwayat_campak)) {
                $temuanSubjektif[] = 'Riwayat campak';
            }

            if (!empty($subjektif->nyeri_telinga)) {
                $temuanSubjektif[] = 'Nyeri telinga';
            }

            if (!empty($subjektif->cairan_telinga)) {
                $temuanSubjektif[] = 'Cairan dari telinga';
            }

            $subjektifData = [
                'jenisKunjungan' => $subjektif->jenis_kunjungan,
                'umurTahun' => $subjektif->umur_tahun,
                'umurBulan' => $subjektif->umur_bulan,
                'umurLabel' => count($umurParts) > 0 ? implode(' ', $umurParts) : null,
                'jenisKelamin' => $subjektif->jenis_kelamin,
                'jenisKelaminLabel' => match ($subjektif->jenis_kelamin) {
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                    default => null,
                },
                'keluhanUtama' => $decodeArray($subjektif->keluhan_utama),
                'keluhanLain' => $subjektif->keluhan_lain,
                'durasiKeluhan' => [
                    'batuk' => $subjektif->batuk_lama_hari,
                    'diare' => $subjektif->diare_lama_hari,
                    'demam' => $subjektif->demam_lama_hari,
                    'telinga' => $subjektif->telinga_lama_hari,
                ],
                'temuanPenting' => $temuanSubjektif,
                'riwayatImunisasi' => $subjektif->riwayat_imunisasi,
                'vitaminA' => $subjektif->vitamin_a,
                'riwayatASI' => $subjektif->riwayat_asi,
                'riwayatPenyakit' => $subjektif->riwayat_penyakit,
                'hivIbu' => $subjektif->hiv_ibu,
                'anamnesisKhusus' => $this->normalisasiAnamnesisKhususMtbs($subjektif->anamnesis_khusus ?? null),
                'updatedAt' => $subjektif->updated_at,
            ];
        }

        $objektifData = null;

        if ($objektif) {
            $sagaPenampilan = $decodeArray($objektif->saga_penampilan);
            $sagaNapas = $decodeArray($objektif->saga_napas);
            $sagaSirkulasi = $decodeArray($objektif->saga_sirkulasi);

            $objektifData = [
                'statusSAGA' => $objektif->status_saga,
                'tandaBahaya' => $decodeArray($objektif->tanda_bahaya),
                'temuanSAGA' => array_values(array_unique(array_merge(
                    $sagaPenampilan,
                    $sagaNapas,
                    $sagaSirkulasi
                ))),
                'vital' => [
                    'rr' => $objektif->rr,
                    'suhu' => $objektif->suhu,
                    'spo2' => $objektif->spo2,
                ],
                'antropometri' => [
                    'bb' => $objektif->bb,
                    'tb' => $objektif->tb,
                    'lila' => $objektif->lila,
                    'lk' => $objektif->lk,
                ],
                'pemeriksaanKhusus' => $decodeArray($objektif->pemeriksaan_khusus),
                'updatedAt' => $objektif->updated_at,
            ];
        }

        return response()->json([
            'message' => 'Ringkasan Subjektif dan Objektif MTBS berhasil dimuat.',
            'data' => [
                'subjektif_ada' => (bool) $subjektif,
                'objektif_ada' => (bool) $objektif,
                'subjektif' => $subjektifData,
                'objektif' => $objektifData,
            ],
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBS ringkasanSubjektifObjektif error', [
            'kunjungan_id' => $kunjunganId,
            'message' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Gagal memuat ringkasan Subjektif dan Objektif MTBS.',
            'error' => $e->getMessage(),
            'data' => [
                'subjektif_ada' => false,
                'objektif_ada' => false,
                'subjektif' => null,
                'objektif' => null,
            ],
        ], 500);
    }
}

public function storeAssessment(Request $request)
{
    // Normalisasi kosong -> null (biar rapi)
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'pasien_id'    => $request->pasien_id === '' ? null : $request->pasien_id,
    ]);

    // Validasi sesuai payload Vue (assessment_mtbs object)
    $request->validate([
        'kunjungan_id' => ['nullable', 'string', 'max:100'],
        'pasien_id'    => ['required', 'integer', 'min:1'],

        'assessment_mtbs' => ['required', 'array'],

        'assessment_mtbs.batuk'  => ['nullable', 'string', 'max:50'],
        'assessment_mtbs.diare'  => ['nullable', 'string', 'max:50'],
        'assessment_mtbs.demam'  => ['nullable', 'string', 'max:50'],
        'assessment_mtbs.gizi'   => ['nullable', 'string', 'max:50'],
        'assessment_mtbs.anemia' => ['nullable', 'string', 'max:50'],

        'klasifikasi'       => ['nullable', 'array'],
        'status_kegawatan'  => ['required', 'string', 'max:50'],
    ]);

    $toNullIfEmpty = fn ($v) => ($v === '' || $v === null) ? null : $v;

    try {
        $assessment = $request->assessment_mtbs;

        DB::table('mtbs_assessment')->insert([
            'kunjungan_id' => $toNullIfEmpty($request->kunjungan_id),
            'pasien_id'    => $request->pasien_id,

            'batuk'  => $toNullIfEmpty($assessment['batuk']  ?? null),
            'diare'  => $toNullIfEmpty($assessment['diare']  ?? null),
            'demam'  => $toNullIfEmpty($assessment['demam']  ?? null),
            'gizi'   => $toNullIfEmpty($assessment['gizi']   ?? null),
            'anemia' => $toNullIfEmpty($assessment['anemia'] ?? null),

            'klasifikasi_global' => json_encode($request->klasifikasi ?? []),
            'status_kegawatan'   => $request->status_kegawatan,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Assessment MTBS berhasil disimpan'], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS storeAssessment error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan assessment MTBS',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function storeAssessmentAuto(Request $request)
{
    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
    ])->validate();

    $kunjunganId = (string) $request->kunjungan_id;

    $sub = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $obj = DB::table('mtbs_objektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    if (!$sub || !$obj) {
        return response()->json([
            'message' => 'Data subjektif/objektif belum lengkap untuk dihitung.',
            'subjektif_ada' => (bool) $sub,
            'objektif_ada' => (bool) $obj,
        ], 422);
    }

    $pasienId = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('pel.idpelayanan', $kunjunganId)
        ->value('l.pasienId');

    $hasil = $this->hitungKlasifikasiMtbs(
        $sub,
        $obj,
        $pasienId !== null ? (int) $pasienId : null
    );

    DB::table('mtbs_assessment')->updateOrInsert(
        ['kunjungan_id' => $kunjunganId],
        [
            'pasien_id' => $hasil['pasien_id'],
            'batuk' => $hasil['batuk'],
            'diare' => $hasil['diare'],
            'demam' => $hasil['demam'],
            'gizi' => $hasil['gizi'],
            'anemia' => $hasil['anemia'],
            'klasifikasi_global' => json_encode($hasil['klasifikasi_global']),
            'status_kegawatan' => $hasil['status_kegawatan'],
            'updated_at' => now(),
        ]
    );

    return response()->json([
        'message' => 'Assessment MTBS berhasil digenerate otomatis.',
        'data' => $hasil,
    ], 200);
}
private function hitungKlasifikasiMtbs($sub, $obj, ?int $pasienId = null): array
{
    $tandaBahayaObjektif = $this->decodeArrayMtbs($obj->tanda_bahaya ?? null);
    $pemeriksaanKhusus = $this->decodeArrayMtbs($obj->pemeriksaan_khusus ?? null);
    $sagaPenampilan = $this->decodeArrayMtbs($obj->saga_penampilan ?? null);
    $sagaNapas = $this->decodeArrayMtbs($obj->saga_napas ?? null);
    $sagaSirkulasi = $this->decodeArrayMtbs($obj->saga_sirkulasi ?? null);
    $keluhanUtama = $this->decodeArrayMtbs($sub->keluhan_utama ?? null);
    $anamnesis = $this->normalisasiAnamnesisKhususMtbs($sub->anamnesis_khusus ?? null);
    $tandaBahayaSubjektif = $this->tandaBahayaDariAnamnesisMtbs($anamnesis);
    $tandaBahayaLengkap = data_get($anamnesis, 'tanda_bahaya.bisa_minum_menyusu') !== null
        && data_get($anamnesis, 'tanda_bahaya.memuntahkan_semua') !== null
        && data_get($anamnesis, 'tanda_bahaya.pernah_kejang') !== null;

    // Setelah form baru terisi lengkap, sumber tanda bahaya hanya Subjektif.
    // Data Objektif lama dipakai sebagai fallback untuk kunjungan sebelum revisi.
    $tandaBahaya = $tandaBahayaLengkap
        ? $tandaBahayaSubjektif
        : array_values(array_unique(array_merge($tandaBahayaObjektif, $tandaBahayaSubjektif)));

    if (!empty($sub->keluhan_lain)) {
        $keluhanUtama[] = (string) $sub->keluhan_lain;
    }

    $umurTahun = is_numeric($sub->umur_tahun ?? null) ? (int) $sub->umur_tahun : 0;
    $umurBulanSisa = is_numeric($sub->umur_bulan ?? null) ? (int) $sub->umur_bulan : 0;
    $umurBulan = ($umurTahun * 12) + $umurBulanSisa;
    $catatanRule = [];

    if ($umurBulan < 2 || $umurBulan >= 60) {
        $catatanRule[] = 'Umur berada di luar rentang MTBS 2 bulan sampai kurang dari 5 tahun.';
    }

    /* 1. STATUS KEGAWATAN SAGA */
    $adaTandaBahaya = count($tandaBahaya) > 0;
    $adaPenampilan = count($sagaPenampilan) > 0;
    $adaNapas = count($sagaNapas) > 0;
    $adaSirkulasi = count($sagaSirkulasi) > 0;

    if ($adaPenampilan && $adaNapas && $adaSirkulasi) {
        $statusKegawatan = 'Gagal jantung paru';
    } elseif ($adaTandaBahaya || $adaPenampilan || $adaNapas || $adaSirkulasi) {
        $statusKegawatan = 'Penyakit sangat berat';
    } else {
        $statusKegawatan = 'Stabil';
    }

    /* 2. BATUK / SUKAR BERNAPAS */
    $adaBatukAtauSukarBernapas = (bool) data_get($anamnesis, 'batuk.ada', false)
        || (is_numeric($sub->batuk_lama_hari ?? null) && (int) $sub->batuk_lama_hari > 0)
        || $this->adaTeksMtbs($keluhanUtama, ['batuk', 'sukar bernapas', 'sulit bernapas', 'sesak']);

    $tarikanDindingDada = $this->adaTeksMtbs(
        array_merge($sagaNapas, $pemeriksaanKhusus),
        ['tarikan dinding dada ke dalam', 'tarikan dinding dada']
    );
    $wheezing = $this->adaTeksMtbs($pemeriksaanKhusus, ['wheezing', 'mengi']);
    $spo2 = is_numeric($obj->spo2 ?? null) ? (int) $obj->spo2 : null;
    $rr = is_numeric($obj->rr ?? null) ? (int) $obj->rr : null;

    $napasCepatDariUmur = false;
    if ($rr !== null) {
        if ($umurBulan >= 2 && $umurBulan < 12) {
            $napasCepatDariUmur = $rr >= 50;
        } elseif ($umurBulan >= 12 && $umurBulan < 60) {
            $napasCepatDariUmur = $rr >= 40;
        }
    }

    $batuk = 'tidak_batuk_atau_sukar_bernapas';
    if ($adaBatukAtauSukarBernapas) {
        if ($tarikanDindingDada || ($spo2 !== null && $spo2 <= 92)) {
            $batuk = 'pneumonia_berat';
        } elseif ($napasCepatDariUmur) {
            $batuk = 'pneumonia';
        } elseif ($rr !== null) {
            $batuk = 'batuk_bukan_pneumonia';
        } else {
            $batuk = 'batuk_belum_lengkap';
            $catatanRule[] = 'RR belum diisi sehingga batuk/sukar bernapas belum dapat diklasifikasikan.';
        }
    }

    /* 3. DIARE */
    $lamaDiare = is_numeric($sub->diare_lama_hari ?? null) ? (int) $sub->diare_lama_hari : null;
    $adaDiare = (bool) data_get($anamnesis, 'diare.ada', false)
        || ($lamaDiare !== null && $lamaDiare > 0)
        || (bool) ($sub->darah_tinja ?? false)
        || $this->adaTeksMtbs($keluhanUtama, ['diare', 'mencret', 'berak cair', 'bab cair']);

    $keadaanLetargi = $this->adaTeksMtbs($pemeriksaanKhusus, ['keadaan umum diare letargi', 'keadaan umum diare tidak sadar']);
    $keadaanRewel = $this->adaTeksMtbs($pemeriksaanKhusus, ['keadaan umum diare rewel', 'keadaan umum diare mudah marah']);
    $keadaanDinilai = $keadaanLetargi || $keadaanRewel
        || $this->adaTeksMtbs($pemeriksaanKhusus, ['keadaan umum diare sadar', 'keadaan umum diare tenang']);
    $mataCekung = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Mata cekung']);
    $mataDinilai = $mataCekung || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Mata tidak cekung']);
    $tidakBisaAtauMalasMinum = $this->adaTeksMtbs($pemeriksaanKhusus, ['tidak bisa minum', 'malas minum']);
    $hausMinumLahap = $this->adaTeksMtbs($pemeriksaanKhusus, ['haus minum dengan lahap', 'minum dengan lahap']);
    $minumDinilai = $tidakBisaAtauMalasMinum || $hausMinumLahap
        || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Kemampuan minum normal']);
    $cubitanSangatLambat = $this->adaTeksMtbs($pemeriksaanKhusus, ['cubitan kulit perut kembali sangat lambat', 'cubitan kulit sangat lambat']);
    $cubitanLambat = !$cubitanSangatLambat && $this->adaTeksMtbs($pemeriksaanKhusus, ['cubitan kulit perut kembali lambat', 'cubitan kulit lambat']);
    $turgorDinilai = $cubitanSangatLambat || $cubitanLambat
        || $this->adaTeksMtbs($pemeriksaanKhusus, ['cubitan kulit kembali segera']);

    $tandaBerat = ($keadaanLetargi ? 1 : 0) + ($mataCekung ? 1 : 0)
        + ($tidakBisaAtauMalasMinum ? 1 : 0) + ($cubitanSangatLambat ? 1 : 0);
    $tandaRingan = ($keadaanRewel ? 1 : 0) + ($mataCekung ? 1 : 0)
        + ($hausMinumLahap ? 1 : 0) + ($cubitanLambat ? 1 : 0);

    $diare = 'tidak_diare';
    if ($adaDiare) {
        if ($tandaBerat >= 2) {
            $diare = 'diare_dehidrasi_berat';
        } elseif ($tandaRingan >= 2) {
            $diare = 'diare_dehidrasi_ringan_sedang';
        } elseif ($keadaanDinilai && $mataDinilai && $minumDinilai && $turgorDinilai) {
            $diare = 'diare_tanpa_dehidrasi';
        } else {
            $diare = 'diare_belum_lengkap';
            $catatanRule[] = 'Keadaan umum, mata, kemampuan minum, atau cubitan kulit belum lengkap untuk klasifikasi diare.';
        }
    }

    $diareTambahan = [];
    if ($adaDiare && $lamaDiare !== null && $lamaDiare >= 14) {
        if (in_array($diare, ['diare_dehidrasi_berat', 'diare_dehidrasi_ringan_sedang'], true)) {
            $diareTambahan[] = 'diare_persisten_berat';
        } elseif ($diare === 'diare_tanpa_dehidrasi') {
            $diareTambahan[] = 'diare_persisten';
        }
    }
    if ($adaDiare && (bool) ($sub->darah_tinja ?? false)) {
        $diareTambahan[] = 'disentri';
    }

    /* 4. ANEMIA */
    $hb = $this->ambilHbMtbs($obj, $pemeriksaanKhusus);
    $sangatPucat = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Sangat pucat']);
    $pucat = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Pucat'])
        || $this->adaTeksMtbs($pemeriksaanKhusus, ['telapak tangan pucat', 'konjungtiva pucat', 'bibir pucat', 'lidah pucat', 'bantalan kuku pucat']);
    $tidakPucat = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tidak pucat']);

    if ($sangatPucat || ($hb !== null && $hb < 7)) {
        $anemia = 'anemia_berat';
    } elseif ($pucat || ($hb !== null && $hb >= 7 && $hb < 10)) {
        $anemia = 'anemia';
    } elseif ($tidakPucat || ($hb !== null && $hb >= 10)) {
        $anemia = 'tidak_anemia';
    } else {
        $anemia = 'anemia_belum_lengkap';
        $catatanRule[] = 'Derajat kepucatan atau nilai Hb belum dicatat.';
    }

    /* 5. GIZI: dibaca dari modul mtbs_gizi */
    $gizi = null;
    $giziRow = DB::table('mtbs_gizi')
        ->where('kunjungan_id', $sub->kunjungan_id)
        ->orderByDesc('id')
        ->first();

    if ($giziRow && !empty($giziRow->klasifikasi)) {
        $mapKlasifikasiGizi = [
            'GIZI BURUK DENGAN KOMPLIKASI' => 'gizi_buruk_dengan_komplikasi',
            'GIZI BURUK TANPA KOMPLIKASI' => 'gizi_buruk_tanpa_komplikasi',
            'GIZI KURANG' => 'gizi_kurang',
            'GIZI BAIK' => 'gizi_baik',
            'BERISIKO GIZI LEBIH' => 'berisiko_gizi_lebih',
            'BERESIKO GIZI LEBIH' => 'berisiko_gizi_lebih',
            'GIZI LEBIH' => 'gizi_lebih',
            'OBESITAS' => 'obesitas',
        ];
        $gizi = $mapKlasifikasiGizi[strtoupper(trim((string) $giziRow->klasifikasi))] ?? null;
    } else {
        $catatanRule[] = 'Modul Gizi belum menghasilkan klasifikasi.';
    }

    /* 6. DEMAM / MALARIA */
    $suhu = is_numeric($obj->suhu ?? null) ? (float) $obj->suhu : null;
    $lamaDemam = is_numeric($sub->demam_lama_hari ?? null) ? (int) $sub->demam_lama_hari : null;
    $adaDemam = (bool) data_get($anamnesis, 'demam.ada', false)
        || ($lamaDemam !== null && $lamaDemam > 0)
        || ($suhu !== null && $suhu > 37.5)
        || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Anak teraba panas'])
        || $this->adaTeksMtbs($keluhanUtama, ['demam', 'panas']);

    $kakuKuduk = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Kaku kuduk']);
    $rdtPositif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['RDT malaria positif']);
    $rdtNegatif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['RDT malaria negatif']);
    $mikroPositif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Mikroskopis malaria positif']);
    $mikroNegatif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Mikroskopis malaria negatif']);
    $malariaPositif = $rdtPositif || $mikroPositif;
    $malariaNegatif = !$malariaPositif && ($rdtNegatif || $mikroNegatif);
    $wilayahEndemisTinggi = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Endemis malaria tinggi']);
    $wilayahEndemisRendah = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Endemis malaria rendah']);
    $wilayahNonEndemis = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Non endemis malaria']);
    $perjalananEndemis = (bool) data_get($anamnesis, 'demam.perjalanan_endemis', false);
    $risikoDaerahTujuan = (string) data_get($anamnesis, 'demam.risiko_daerah_tujuan', '');
    $jalurEndemis = $wilayahEndemisTinggi || $wilayahEndemisRendah
        || ($wilayahNonEndemis && $perjalananEndemis && in_array($risikoDaerahTujuan, ['endemis_tinggi', 'endemis_rendah'], true));
    $jalurNonEndemis = $wilayahNonEndemis && (!$perjalananEndemis || $risikoDaerahTujuan === 'non_endemis');
    $tesMalariaTidakTersedia = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tes malaria tidak tersedia']);
    $penyebabLainDemam = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Penyebab lain demam ditemukan']);

    $demam = 'tidak_demam';
    $perluNilaiMalaria = $adaDemam || $wilayahEndemisTinggi;
    if ($perluNilaiMalaria) {
        if ($adaDemam && ($adaTandaBahaya || $kakuKuduk || ($jalurNonEndemis && $umurBulan <= 3))) {
            $demam = 'penyakit_berat_dengan_demam';
        } elseif ($malariaPositif || ($jalurEndemis && $tesMalariaTidakTersedia)) {
            $demam = 'malaria';
        } elseif ($adaDemam && $jalurNonEndemis) {
            $demam = 'demam_bukan_malaria';
        } elseif ($adaDemam && $jalurEndemis && ($malariaNegatif || $penyebabLainDemam)) {
            $demam = 'demam_mungkin_bukan_malaria';
        } elseif ($adaDemam) {
            $demam = 'demam_perlu_data_malaria';
            $catatanRule[] = 'Endemisitas/perjalanan atau hasil tes malaria belum lengkap.';
        }
    }
/* 7. CAMPAK */

// Ambil riwayat campak dari data subjektif versi baru
$campakDariAnamnesis = (bool) data_get(
    $anamnesis,
    'demam.campak_3_bulan',
    false
);

// Kompatibilitas dengan kolom lama
$campakDariKolomLama = (bool) ($sub->riwayat_campak ?? false);

// Kompatibilitas jika checkbox campak tersimpan
// sebagai bagian pemeriksaan khusus/objektif
$campakDariPemeriksaan = $this->adaTeksMtbs(
    $pemeriksaanKhusus,
    [
        'campak saat ini/3 bulan terakhir',
        'campak saat ini atau dalam 3 bulan terakhir',
        'anak menderita campak saat ini atau dalam 3 bulan terakhir',
    ]
);

// Campak saat ini juga dapat ditentukan dari ruam menyeluruh
// disertai batuk, pilek, atau mata merah
$ruamMenyeluruh = $this->adaTeksMtbs(
    $pemeriksaanKhusus,
    [
        'ruam kemerahan menyeluruh',
        'ruam campak menyeluruh',
    ]
);

$tandaKataralCampak = $this->adaTeksMtbs(
    array_merge($keluhanUtama, $pemeriksaanKhusus),
    [
        'batuk pada campak',
        'pilek pada campak',
        'mata merah pada campak',
        'batuk',
        'pilek',
        'mata merah',
    ]
);

$campakSekarangAtauTigaBulan =
    $campakDariAnamnesis
    || $campakDariKolomLama
    || $campakDariPemeriksaan
    || ($ruamMenyeluruh && $tandaKataralCampak);

$campak = 'tidak_diklasifikasikan';

if ($campakSekarangAtauTigaBulan) {
    $korneaKeruh = $this->adaTeksMtbs(
        $pemeriksaanKhusus,
        [
            'kekeruhan kornea',
            'kornea keruh',
        ]
    );

    $lukaMulutBerat = $this->adaTeksMtbs(
        $pemeriksaanKhusus,
        [
            'luka mulut dalam',
            'luka mulut luas',
            'luka di mulut yang dalam',
            'luka di mulut yang luas',
        ]
    );

    $nanahMata = $this->adaTeksMtbs(
        $pemeriksaanKhusus,
        [
            'nanah pada mata',
            'mata bernanah',
        ]
    );

    $lukaMulut = $this->adaTeksMtbs(
        $pemeriksaanKhusus,
        [
            'luka pada mulut',
            'luka di mulut',
        ]
    );

    // Sesuai buku:
    // tanda bahaya umum ATAU kornea keruh
    // ATAU luka mulut dalam/luas
    if ($adaTandaBahaya || $korneaKeruh || $lukaMulutBerat) {
        $campak = 'campak_dengan_komplikasi_berat';
    } elseif ($nanahMata || $lukaMulut) {
        $campak = 'campak_dengan_komplikasi_mata_mulut';
    } else {
        $campak = 'campak';
    }
}
    /* 8. INFEKSI DENGUE */
    $demamDuaSampaiTujuhHari = $adaDemam && $lamaDemam !== null && $lamaDemam >= 2 && $lamaDemam <= 7;
    $dengueAnamnesis = (array) data_get($anamnesis, 'demam.dengue', []);
    $dengueBool = static fn (string $key): bool => (bool) ($dengueAnamnesis[$key] ?? false);

    $shockPucat = $this->adaTeksMtbs($pemeriksaanKhusus, ['kaki tangan tampak pucat']);
    $shockCrt = $this->adaTeksMtbs($pemeriksaanKhusus, ['pengisian kapiler lebih dari 2 detik']);
    $shockDingin = $this->adaTeksMtbs($pemeriksaanKhusus, ['kaki tangan teraba dingin']);
    $shockNadi = $this->adaTeksMtbs($pemeriksaanKhusus, ['nadi lemah', 'nadi tidak teraba']);
    $shockNadiCepat = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Nadi cepat']);
    $syokDengue = $shockCrt || $shockNadi || ($shockPucat && $shockDingin) || ($shockDingin && $shockNadiCepat);

    $distresNapas = $this->adaTeksMtbs($pemeriksaanKhusus, ['distres napas', 'sesak napas']) || $napasCepatDariUmur;
    $perdarahanSaluranCerna = $this->adaTeksMtbs($pemeriksaanKhusus, ['muntah darah', 'muntah coklat seperti kopi', 'bab berdarah', 'bab berwarna hitam'])
        || $dengueBool('muntahDarah') || $dengueBool('muntahKopi') || $dengueBool('babBerdarahHitam');
    $gangguanOrgan = $this->adaTeksMtbs(array_merge($sagaPenampilan, $pemeriksaanKhusus), [
        'penurunan kesadaran', 'penurunan frekuensi denyut nadi', 'ikterik', 'nyeri perut hebat', 'tidak bak selama 6 jam',
    ]) || $dengueBool('tidakBak6Jam');

    $nyeriPerut = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Nyeri perut']) || $dengueBool('nyeriPerut');
    $nyeriTekanKananAtas = $this->adaTeksMtbs($pemeriksaanKhusus, ['nyeri tekan perut kanan atas']);
    $muntahTerus = $this->adaTeksMtbs($pemeriksaanKhusus, ['muntah terus menerus']) || $dengueBool('muntahTerus');
    $akumulasiCairan = $this->adaTeksMtbs($pemeriksaanKhusus, ['akumulasi cairan']);
    $perdarahanMukosa = $this->adaTeksMtbs($pemeriksaanKhusus, ['perdarahan mukosa']) || $dengueBool('mimisan');
    $letargiGelisah = $this->adaTeksMtbs($pemeriksaanKhusus, ['letargi atau gelisah']) || $dengueBool('lemasGelisah');
    $heparLebihDua = $this->adaTeksMtbs($pemeriksaanKhusus, ['pembesaran hepar lebih dari 2 cm']);
    $hctNaikTrombositTurun = $this->adaTeksMtbs($pemeriksaanKhusus, ['hematokrit meningkat disertai penurunan trombosit']);
    $warningSign = $nyeriPerut || $nyeriTekanKananAtas || $muntahTerus || $akumulasiCairan
        || $perdarahanMukosa || $letargiGelisah || $heparLebihDua || $hctNaikTrombositTurun;

    $leukosit = $this->ambilAngkaTemuanMtbs($pemeriksaanKhusus, ['leukosit']);
    $trombosit = $this->ambilAngkaTemuanMtbs($pemeriksaanKhusus, ['trombosit']);
    $tandaRinganDengue = $dengueBool('nyeriPegal') || $dengueBool('ruam')
        || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Uji tourniquet positif', 'NS1 positif'])
        || ($leukosit !== null && $leukosit < 4000)
        || ($trombosit !== null && $trombosit < 100000);

    $dengue = 'tidak_diklasifikasikan';
    if ($demamDuaSampaiTujuhHari) {
        if ($adaTandaBahaya || $syokDengue || $distresNapas || $perdarahanSaluranCerna || $gangguanOrgan) {
            $dengue = 'dengue_berat';
        } elseif ($warningSign) {
            $dengue = 'dengue_dengan_warning_signs';
        } elseif ($tandaRinganDengue) {
            $dengue = 'dengue_tanpa_warning_signs';
        } else {
            $dengue = 'demam_mungkin_bukan_dengue';
        }
    } elseif ($adaDemam && $lamaDemam === null) {
        $catatanRule[] = 'Lama demam belum diisi sehingga klasifikasi dengue belum dapat dijalankan.';
    }
/* 9. MASALAH TELINGA */
$lamaTelinga = is_numeric($sub->telinga_lama_hari ?? null)
    ? (int) $sub->telinga_lama_hari
    : null;

$statusMasalahTelinga = data_get($anamnesis, 'telinga.ada');
$adaMasalahTelinga = $statusMasalahTelinga === true;

$nyeriTelinga = (bool) ($sub->nyeri_telinga ?? false);

$rasaPenuhTelinga = (bool) data_get(
    $anamnesis,
    'telinga.rasa_penuh',
    false
);

$cairanDariAnamnesis = (bool) ($sub->cairan_telinga ?? false);

$cairanTerlihat = $this->adaTeksMtbs(
    $pemeriksaanKhusus,
    ['terlihat cairan atau nanah keluar dari telinga']
);

$bengkakNyeriBelakang = $this->adaTeksMtbs(
    $pemeriksaanKhusus,
    ['pembengkakan yang nyeri di belakang telinga']
);

// Default tidak dimasukkan ke klasifikasi global
$telinga = 'tidak_diklasifikasikan';

// Pemeriksaan telinga hanya diklasifikasikan jika pertanyaan
// "Ada masalah telinga?" dijawab Ya atau ditemukan tanda objektif
if (
    $adaMasalahTelinga
    || $nyeriTelinga
    || $rasaPenuhTelinga
    || $cairanDariAnamnesis
    || $cairanTerlihat
    || $bengkakNyeriBelakang
) {
    if ($bengkakNyeriBelakang) {
        $telinga = 'mastoiditis';
    } elseif (
        ($cairanDariAnamnesis || $cairanTerlihat)
        && $lamaTelinga !== null
        && $lamaTelinga >= 14
    ) {
        $telinga = 'infeksi_telinga_kronis';
    } elseif (
        $nyeriTelinga
        || $rasaPenuhTelinga
        || (
            ($cairanDariAnamnesis || $cairanTerlihat)
            && $lamaTelinga !== null
            && $lamaTelinga < 14
        )
    ) {
        $telinga = 'infeksi_telinga_akut';
    } elseif (
        ($cairanDariAnamnesis || $cairanTerlihat)
        && $lamaTelinga === null
    ) {
        $telinga = 'telinga_belum_lengkap';
        $catatanRule[] = 'Lama cairan/nanah telinga belum diisi.';
    } else {
        // Ada masalah telinga, tetapi tidak memenuhi infeksi akut/kronis
        $telinga = 'tidak_ada_infeksi_telinga';
    }
}

    /* 10. STATUS HIV */
    $statusHivIbu = Str::lower(trim((string) ($sub->hiv_ibu ?? '')));
    $ibuHivPositif = in_array($statusHivIbu, ['positif', 'positive', 'reaktif'], true)
        || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Status HIV ibu: positif']);
    $ibuHivNegatif = in_array($statusHivIbu, ['negatif', 'negative', 'nonreaktif', 'non-reaktif'], true)
        || $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Status HIV ibu: negatif']);
    $virologiPositif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tes virologi anak: positif']);
    $virologiNegatif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tes virologi anak: negatif']);
    $serologiPositif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tes serologi anak: positif']);
    $serologiNegatif = $this->adaTeksPersisMtbs($pemeriksaanKhusus, ['Tes serologi anak: negatif']);
    $ibuPernahTes = (string) data_get($anamnesis, 'hiv.ibu_pernah_tes', '');
    $anakPernahTes = (string) data_get($anamnesis, 'hiv.anak_pernah_tes', '');
    $asiSaatTes = (bool) data_get($anamnesis, 'hiv.asi_saat_tes_atau_6_minggu', false);
    $asiSekarang = (bool) data_get($anamnesis, 'hiv.asi_sekarang', false);

    $hiv = null;
    if ($virologiPositif || ($serologiPositif && $umurBulan >= 18)) {
        $hiv = 'infeksi_hiv_terkonfirmasi';
    } elseif (
        ($ibuHivPositif && $virologiNegatif && ($asiSaatTes || $asiSekarang))
        || ($ibuHivPositif && !$virologiPositif && !$virologiNegatif)
        || ($serologiPositif && $umurBulan < 18)
    ) {
        $hiv = 'terpajan_hiv';
    } elseif ($ibuHivNegatif && ($virologiNegatif || $serologiNegatif)) {
        $hiv = 'mungkin_bukan_infeksi_hiv';
    } elseif ($ibuPernahTes === 'tidak' || $anakPernahTes === 'tidak' || (!$ibuHivPositif && !$ibuHivNegatif)) {
        $indikasiTesHiv = (array) data_get($anamnesis, 'hiv.indikasi_tes', []);
        if (in_array(true, array_map(static fn ($value) => (bool) $value, $indikasiTesHiv), true)) {
            $catatanRule[] = 'Terdapat indikasi tes HIV, tetapi hasil tes ibu/anak belum lengkap.';
        }
    }

    /* KLASIFIKASI GLOBAL */
    $klasifikasiGlobal = [];
    if ($statusKegawatan === 'Gagal jantung paru') {
        $klasifikasiGlobal[] = 'Gagal jantung paru';
    } elseif ($statusKegawatan === 'Penyakit sangat berat') {
        $klasifikasiGlobal[] = 'Penyakit sangat berat';
    } else {
        $klasifikasiGlobal[] = 'Stabil';
    }

    $maps = [
        [$batuk, ['pneumonia_berat' => 'Pneumonia berat', 'pneumonia' => 'Pneumonia', 'batuk_bukan_pneumonia' => 'Batuk bukan pneumonia']],
        [$diare, ['diare_dehidrasi_berat' => 'Diare dehidrasi berat', 'diare_dehidrasi_ringan_sedang' => 'Diare dehidrasi ringan/sedang', 'diare_tanpa_dehidrasi' => 'Diare tanpa dehidrasi']],
        [$demam, ['penyakit_berat_dengan_demam' => 'Penyakit berat dengan demam', 'malaria' => 'Malaria', 'demam_mungkin_bukan_malaria' => 'Demam mungkin bukan malaria', 'demam_bukan_malaria' => 'Demam bukan malaria']],
        [$campak, ['campak_dengan_komplikasi_berat' => 'Campak dengan komplikasi berat', 'campak_dengan_komplikasi_mata_mulut' => 'Campak dengan komplikasi pada mata dan/atau mulut', 'campak' => 'Campak']],
        [$dengue, ['dengue_berat' => 'Dengue berat', 'dengue_dengan_warning_signs' => 'Dengue dengan warning signs', 'dengue_tanpa_warning_signs' => 'Dengue tanpa warning signs', 'demam_mungkin_bukan_dengue' => 'Demam mungkin bukan dengue']],
        [$telinga, ['mastoiditis' => 'Mastoiditis', 'infeksi_telinga_akut' => 'Infeksi telinga akut', 'infeksi_telinga_kronis' => 'Infeksi telinga kronis', 'tidak_ada_infeksi_telinga' => 'Tidak ada infeksi telinga']],
        [$anemia, ['anemia_berat' => 'Anemia berat', 'anemia' => 'Anemia', 'tidak_anemia' => 'Tidak anemia']],
        [$gizi, ['gizi_buruk_dengan_komplikasi' => 'Gizi buruk dengan komplikasi', 'gizi_buruk_tanpa_komplikasi' => 'Gizi buruk tanpa komplikasi', 'gizi_kurang' => 'Gizi kurang', 'gizi_baik' => 'Gizi baik', 'berisiko_gizi_lebih' => 'Berisiko gizi lebih', 'gizi_lebih' => 'Gizi lebih', 'obesitas' => 'Obesitas']],
        [$hiv, ['infeksi_hiv_terkonfirmasi' => 'Infeksi HIV terkonfirmasi', 'terpajan_hiv' => 'Terpajan HIV', 'mungkin_bukan_infeksi_hiv' => 'Mungkin bukan infeksi HIV']],
    ];
    foreach ($maps as [$value, $map]) {
        if ($value !== null && isset($map[$value])) $klasifikasiGlobal[] = $map[$value];
    }
    $tambahanMap = ['diare_persisten_berat' => 'Diare persisten berat', 'diare_persisten' => 'Diare persisten', 'disentri' => 'Disentri'];
    foreach ($diareTambahan as $tambahan) if (isset($tambahanMap[$tambahan])) $klasifikasiGlobal[] = $tambahanMap[$tambahan];

    return [
        'pasien_id' => $pasienId,
        'umur_bulan' => $umurBulan,
        'batuk' => $batuk,
        'wheezing' => $wheezing,
        'diare' => $diare,
        'diare_tambahan' => array_values(array_unique($diareTambahan)),
        'demam' => $demam,
        'campak' => $campak,
        'dengue' => $dengue,
        'telinga' => $telinga,
        'gizi' => $gizi,
        'anemia' => $anemia,
        'hiv' => $hiv,
        'klasifikasi_global' => array_values(array_unique($klasifikasiGlobal)),
        'status_kegawatan' => $statusKegawatan,
        'catatan_rule' => array_values(array_unique($catatanRule)),
    ];
}

private function defaultAnamnesisKhususMtbs(): array
{
    return [
        'tanda_bahaya' => [
            'bisa_minum_menyusu' => null,
            'memuntahkan_semua' => null,
            'pernah_kejang' => null,
        ],
        'batuk' => ['ada' => null],
        'diare' => ['ada' => null],
        'demam' => [
            'ada' => null,
            'minum_obat_antimalaria' => false,
            'campak_3_bulan' => false,
            'perjalanan_endemis' => false,
            'risiko_daerah_tujuan' => null,
            'dengue' => [
                'demamMendadakTinggiTerus' => false,
                'badanDingin' => false,
                'lemasGelisah' => false,
                'mual' => false,
                'muntah' => false,
                'muntahTerus' => false,
                'nyeriPerut' => false,
                'mimisan' => false,
                'muntahDarah' => false,
                'muntahKopi' => false,
                'babBerdarahHitam' => false,
                'ruam' => false,
                'nyeriPegal' => false,
                'tidakBak6Jam' => false,
            ],
        ],
        'telinga' => [
            'ada' => null,
            'rasa_penuh' => false,
        ],
        'hiv' => [
            'ibu_pernah_tes' => null,
            'anak_pernah_tes' => null,
            'asi_saat_tes_atau_6_minggu' => false,
            'asi_sekarang' => false,
            'arv_profilaksis' => false,
            'indikasi_tes' => [
                'pneumoniaBerulang' => false,
                'diarePersistenBerulang' => false,
                'thrushBerulang' => false,
                'infeksiBeratBerulang' => false,
                'giziTidakMembaik' => false,
            ],
        ],
    ];
}

private function normalisasiAnamnesisKhususMtbs($value): array
{
    if (is_string($value) && $value !== '') {
        $decoded = json_decode($value, true);
        $value = is_array($decoded) ? $decoded : [];
    }

    if (!is_array($value)) {
        $value = [];
    }

    return array_replace_recursive($this->defaultAnamnesisKhususMtbs(), $value);
}

private function tandaBahayaDariAnamnesisMtbs(array $anamnesis): array
{
    $tanda = [];

    if (data_get($anamnesis, 'tanda_bahaya.bisa_minum_menyusu') === false) {
        $tanda[] = 'Tidak bisa minum / menyusu';
    }
    if (data_get($anamnesis, 'tanda_bahaya.memuntahkan_semua') === true) {
        $tanda[] = 'Memuntahkan semua makanan dan minuman';
    }
    if (data_get($anamnesis, 'tanda_bahaya.pernah_kejang') === true) {
        $tanda[] = 'Pernah kejang selama sakit ini';
    }

    return $tanda;
}

private function identitasPasienMtbs(string $kunjunganId): array
{
    $row = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->where('pel.idpelayanan', $kunjunganId)
        ->select('p.TGL_LHR', 'p.JENIS_KLMIN', 'l.tglKunjungan')
        ->first();

    $hasil = [
        'umur_tahun' => null,
        'umur_bulan' => null,
        'jenis_kelamin' => null,
    ];

    if (!$row) {
        return $hasil;
    }

    if (!empty($row->TGL_LHR)) {
        try {
            $lahir = Carbon::parse($row->TGL_LHR);
            $kunjungan = Carbon::parse($row->tglKunjungan ?? now());
            $diff = $lahir->diff($kunjungan);
            $hasil['umur_tahun'] = $diff->y;
            $hasil['umur_bulan'] = $diff->m;
        } catch (\Throwable $e) {
            Log::warning('MTBS gagal menghitung umur pasien', [
                'kunjungan_id' => $kunjunganId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    $hasil['jenis_kelamin'] = match ((string) ($row->JENIS_KLMIN ?? '')) {
        '1', 'L', 'l' => 'L',
        '2', 'P', 'p' => 'P',
        default => null,
    };

    return $hasil;
}

private function decodeArrayMtbs($value): array
{
    if (is_array($value)) {
        return array_values(array_filter($value, static fn ($item) => $item !== null && $item !== ''));
    }

    if ($value === null || $value === '') {
        return [];
    }

    $decoded = json_decode((string) $value, true);

    return is_array($decoded)
        ? array_values(array_filter($decoded, static fn ($item) => $item !== null && $item !== ''))
        : [];
}

private function normalisasiTeksMtbs($value): string
{
    $text = Str::lower(trim((string) $value));
    $text = str_replace(
        ['/', '\\', '_', '-', '(', ')', '[', ']', ':', ';', ','],
        ' ',
        $text
    );

    return preg_replace('/\s+/u', ' ', $text) ?: '';
}

private function adaTeksMtbs(array $values, array $needles): bool
{
    foreach ($values as $value) {
        $text = $this->normalisasiTeksMtbs($value);

        if ($text === '') {
            continue;
        }

        foreach ($needles as $needle) {
            $needleNormal = $this->normalisasiTeksMtbs($needle);

            if ($needleNormal !== '' && str_contains($text, $needleNormal)) {
                return true;
            }
        }
    }

    return false;
}

private function adaTeksPersisMtbs(array $values, array $needles): bool
{
    $normalizedNeedles = array_values(array_filter(array_map(
        fn ($needle) => $this->normalisasiTeksMtbs($needle),
        $needles
    )));

    foreach ($values as $value) {
        $text = $this->normalisasiTeksMtbs($value);

        if ($text !== '' && in_array($text, $normalizedNeedles, true)) {
            return true;
        }
    }

    return false;
}

private function ambilAngkaTemuanMtbs(array $values, array $labels): ?float
{
    $normalizedLabels = array_values(array_filter(array_map(
        fn ($label) => $this->normalisasiTeksMtbs($label),
        $labels
    )));

    foreach ($values as $value) {
        $raw = str_replace(',', '.', trim((string) $value));
        $normalizedRaw = $this->normalisasiTeksMtbs($raw);

        foreach ($normalizedLabels as $label) {
            if ($label === '' || !str_contains($normalizedRaw, $label)) {
                continue;
            }

            if (preg_match('/[-+]?\d+(?:\.\d+)?/', $raw, $match)) {
                return (float) $match[0];
            }
        }
    }

    return null;
}

private function ambilHbMtbs($obj, array $semuaTemuan): ?float
{
    if (property_exists($obj, 'hb') && is_numeric($obj->hb)) {
        return (float) $obj->hb;
    }

    foreach ($semuaTemuan as $item) {
        $text = str_replace(',', '.', (string) $item);

        if (preg_match('/\bhb\s*[:=]?\s*(\d+(?:\.\d+)?)/i', $text, $match)) {
            return (float) $match[1];
        }

        if (preg_match('/hemoglobin\s*[:=]?\s*(\d+(?:\.\d+)?)/i', $text, $match)) {
            return (float) $match[1];
        }
    }

    return null;
}
public function getSubjektifByKunjungan($kunjunganId)
{
    $data = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    if (!$data) {
        return response()->json(['data' => null], 200);
    }

    return response()->json([
        'data' => [
            'jenisKunjungan'   => $data->jenis_kunjungan,
            'umurTahun'        => $data->umur_tahun,
            'umurBulan'        => $data->umur_bulan,
            'jenisKelamin'     => $data->jenis_kelamin,

            'keluhanUtama'     => json_decode($data->keluhan_utama ?? '[]', true) ?: [],
            'keluhanLain'      => $data->keluhan_lain,

            'batukLama'        => $data->batuk_lama_hari,
            'napasCepat'       => (bool) $data->napas_cepat,
            'mengi'            => (bool) $data->mengi,

            'diareLama'        => $data->diare_lama_hari,
            'darahTinja'       => (bool) $data->darah_tinja,

            'demamLama'        => $data->demam_lama_hari,
            'demamTiapHari'    => (bool) $data->demam_tiap_hari,
            'riwayatMalaria'   => (bool) $data->riwayat_malaria,
            'riwayatCampak'    => (bool) $data->riwayat_campak,

            'nyeriTelinga'     => (bool) $data->nyeri_telinga,
            'cairanTelinga'    => (bool) $data->cairan_telinga,
            'telingaLama'      => $data->telinga_lama_hari,

            'riwayatImunisasi' => $data->riwayat_imunisasi,
            'vitaminA'         => $data->vitamin_a,
            'riwayatASI'       => $data->riwayat_asi,
            'riwayatPenyakit'  => $data->riwayat_penyakit,
            'hivIbu'           => $data->hiv_ibu,
            'anamnesisKhusus' => $this->normalisasiAnamnesisKhususMtbs($data->anamnesis_khusus ?? null),
        ]
    ], 200);
}


public function getObatMtbs(Request $request)
{
    $q = trim($request->q ?? '');

    $data = DB::table('simpus_master_obat')
        ->select(
            'OBAT_ID as obat_id',
            'KODE_OBAT as kode_obat',
            'NAMA as nama',
            'SATUAN as satuan'
        )
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('NAMA', 'like', '%' . $q . '%')
                    ->orWhere('KODE_OBAT', 'like', '%' . $q . '%');
            });
        })
        ->orderBy('NAMA')
        ->get();

    return response()->json([
        'data' => $data
    ]);
}




public function rekomendasiPlanning($kunjunganId)
{
    $assessment = DB::table('mtbs_assessment')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $klasifikasi = $assessment
        ? (json_decode($assessment->klasifikasi_global ?? '[]', true) ?: [])
        : [];

    /*
    |--------------------------------------------------------------------------
    | Assessment dokter / diagnosis medis
    |--------------------------------------------------------------------------
    | Diagnosis dokter tetap disimpan pada mtbs_diagnosa_medis sebagai sumber
    | data utama. Planning hanya membaca dan menampilkannya agar tidak terjadi
    | duplikasi data yang dapat menjadi tidak sinkron.
    */
    $diagnosaDokter = DB::table('mtbs_diagnosa_medis')
        ->where('kunjungan_id', $kunjunganId)
        ->orderBy('id')
        ->get()
        ->map(function ($row) {
            return [
                'id' => $row->id,
                'diagnosaId' => $row->diagnosa_id,
                'kodeDiagnosa' => $row->kdDiag,
                'namaDiagnosa' => $row->nmDiag,
                'kasus' => $row->kasus,
                'keterangan' => $row->keterangan,
                'poli' => $row->poli,
                'dokterPetugas' => $row->created_by,
            ];
        })
        ->values();

    $rekomendasi = [];

    $subPlanning = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $objPlanning = DB::table('mtbs_objektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $umurBulanPlanning = null;
    if ($subPlanning) {
        $umurBulanPlanning = ((int) ($subPlanning->umur_tahun ?? 0) * 12)
            + (int) ($subPlanning->umur_bulan ?? 0);
    }

    $pemeriksaanPlanning = $objPlanning
        ? $this->decodeArrayMtbs($objPlanning->pemeriksaan_khusus ?? null)
        : [];

    $wheezingPlanning = $this->adaTeksMtbs($pemeriksaanPlanning, ['wheezing', 'mengi'])
        || (bool) ($subPlanning->mengi ?? false);

    $wilayahEndemisPlanning = $this->adaTeksPersisMtbs($pemeriksaanPlanning, [
        'Endemis malaria tinggi',
        'Endemis malaria rendah',
        'Risiko daerah tujuan malaria: endemis tinggi',
        'Risiko daerah tujuan malaria: endemis rendah',
    ]);

    $hivTerkonfirmasiAtauTerpajan = in_array('Infeksi HIV terkonfirmasi', $klasifikasi, true)
        || in_array('Terpajan HIV', $klasifikasi, true);

    $tambah = static function (
        array &$target,
        string $namaKlasifikasi,
        array $tindakan = [],
        array $pengobatan = [],
        ?int $kunjunganUlang = null
    ): void {
        $items = array_values(array_unique(array_filter(array_merge($tindakan, $pengobatan))));

        $target[] = [
            'klasifikasi' => $namaKlasifikasi,
            'items' => $items,
            'tindakan' => array_values(array_unique(array_filter($tindakan))),
            'pengobatan' => array_values(array_unique(array_filter($pengobatan))),
            'kunjungan_ulang' => $kunjunganUlang,
            'sumber' => 'rule_mtbs_2022',
        ];
    };

    foreach (array_values(array_unique($klasifikasi)) as $item) {
        $item = trim((string) $item);
        $itemLower = Str::lower($item);

        // Rekomendasi gizi dibaca langsung dari mtbs_gizi di bagian bawah.
        if (
            str_starts_with($itemLower, 'gizi')
            || $itemLower === 'obesitas'
            || $itemLower === 'berisiko gizi lebih'
        ) {
            continue;
        }

        switch ($item) {
            case 'Gagal jantung paru':
                $tambah($rekomendasi, $item, [
                    'Lakukan Bantuan Hidup Dasar (BHD)',
                    'Rujuk segera',
                ]);
                break;

            case 'Penyakit sangat berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Jika ada stridor, pastikan tidak ada sumbatan jalan napas',
                        'Berikan oksigen 3-5 L/menit melalui nasal prongs',
                        'Cegah agar gula darah tidak turun',
                        'Jaga tubuh anak tetap hangat',
                        'Rujuk segera',
                    ],
                    [
                        'Jika sedang kejang dan belum ada akses IV, berikan Diazepam rektal sesuai pedoman',
                        'Jika sedang kejang dan akses IV tersedia, berikan Diazepam IV sesuai pedoman',
                    ]
                );
                break;

            case 'Stabil':
                $tambah(
                    $rekomendasi,
                    $item,
                    ['Lanjutkan pemeriksaan dan tata laksana sesuai keluhan anak'],
                    []
                );
                break;

            case 'Pneumonia berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Berikan oksigen 1-4 L/menit melalui nasal prongs',
                        'Obati wheezing bila ada',
                        'Rujuk segera',
                    ],
                    [
                        'Berikan Ampisilin IM sebagai antibiotik prarujukan',
                        'Berikan Gentamisin IM sebagai antibiotik prarujukan',
                    ]
                );
                break;

            case 'Pneumonia':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Beri pelega tenggorokan dan pereda batuk yang aman',
                        'Obati wheezing bila ada',
                        'Jika batuk 14 hari atau lebih, rujuk untuk pemeriksaan TB dan penyebab lain',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [$hivTerkonfirmasiAtauTerpajan
                        ? 'Amoksisilin 2 kali sehari selama 5 hari karena anak terkonfirmasi/terpajan HIV'
                        : 'Amoksisilin 2 kali sehari selama 3 hari'],
                    2
                );
                break;

            case 'Batuk bukan pneumonia':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Beri pelega tenggorokan dan pereda batuk yang aman',
                        'Obati wheezing bila ada',
                        'Jika batuk 14 hari atau lebih, lacak kemungkinan TB',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [],
                    5
                );
                break;

            case 'Diare dehidrasi berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Jika tidak ada klasifikasi berat lain, lakukan Rencana Terapi C',
                        'Jika ada klasifikasi berat lain, rujuk segera',
                        'Jika masih bisa minum, berikan ASI dan oralit selama perjalanan',
                        'Jika umur lebih dari 2 tahun dan ada wabah kolera, berikan antibiotik kolera sesuai pedoman',
                    ],
                    [
                        'Berikan Oralit sesuai Rencana Terapi C atau selama perjalanan bila anak masih mampu minum',
                        'Berikan tablet Zinc sesuai umur selama 10 hari',
                    ]
                );
                break;

            case 'Diare dehidrasi ringan/sedang':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Berikan cairan dan makanan sesuai Rencana Terapi B',
                        'Jika ada klasifikasi berat lain, rujuk segera dan berikan ASI/oralit selama perjalanan bila mampu minum',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Oralit sesuai Rencana Terapi B',
                        'Berikan tablet Zinc sesuai umur selama 10 hari',
                    ],
                    2
                );
                break;

            case 'Diare tanpa dehidrasi':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Berikan cairan dan makanan sesuai Rencana Terapi A',
                        'Lanjutkan makan dan ASI',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Oralit sesuai Rencana Terapi A',
                        'Berikan tablet Zinc sesuai umur selama 10 hari',
                    ],
                    2
                );
                break;

            case 'Diare persisten berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Atasi dehidrasi sebelum dirujuk, kecuali terdapat klasifikasi berat lain',
                        'Rujuk',
                    ],
                    [
                        'Berikan Oralit sesuai derajat dehidrasi bila anak mampu minum',
                        'Berikan tablet Zinc sesuai umur selama 10 hari',
                    ]
                );
                break;

            case 'Diare persisten':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Berikan oralit',
                        'Nasihati pemberian makan',
                        'Nasihati kapan harus kembali segera',
                    ],
                    ['Berikan tablet zinc selama 10 hari berturut-turut'],
                    2
                );
                break;

            case 'Disentri':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Berikan oralit',
                        'Nasihati pemberian makan',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Oralit sesuai derajat dehidrasi',
                        'Berikan Zinc selama 10 hari berturut-turut',
                        'Berikan Kotrimoksazol sebagai antibiotik lini pertama untuk disentri',
                        'Berikan Sefiksim sebagai antibiotik lini kedua bila diperlukan',
                    ],
                    2
                );
                break;

            case 'Penyakit berat dengan demam':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Cegah agar gula darah tidak turun',
                        'Lakukan tes malaria bila tersedia',
                        'Rujuk segera',
                    ],
                    [
                        'Berikan Artesunat injeksi IM/IV sebagai obat prarujukan malaria berat',
                        'Berikan Ampisilin IM sebagai antibiotik prarujukan',
                        'Berikan Gentamisin IM sebagai antibiotik prarujukan',
                        'Berikan Parasetamol bila suhu 38°C atau lebih',
                    ]
                );
                break;

            case 'Malaria':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Nasihati kapan harus kembali segera',
                        'Jika demam berlanjut lebih dari 7 hari, rujuk untuk penilaian lebih lanjut',
                    ],
                    [
                        'Berikan DHP (Dihidroartemisinin-Piperakuin) sesuai umur dan berat badan',
                        'Berikan Primakuin sesuai jenis malaria dan pedoman',
                        'Berikan Parasetamol bila suhu 38°C atau lebih',
                    ],
                    3
                );
                break;

            case 'Demam mungkin bukan malaria':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Obati penyebab lain dari demam',
                        'Nasihati kapan harus kembali segera',
                        'Jika demam berlanjut lebih dari 7 hari, rujuk untuk penilaian lebih lanjut',
                    ],
                    ['Berikan parasetamol bila suhu 38°C atau lebih'],
                    3
                );
                break;

            case 'Demam bukan malaria':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Obati penyebab lain dari demam',
                        'Nasihati kapan harus kembali segera',
                        'Jika demam berlanjut lebih dari 7 hari, rujuk untuk penilaian lebih lanjut',
                    ],
                    ['Berikan parasetamol bila suhu 38°C atau lebih'],
                    2
                );
                break;

            case 'Campak dengan komplikasi berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    ['Rujuk segera'],
                    [
                        'Berikan Vitamin A dosis pengobatan',
                        'Berikan Ampisilin IM sebagai antibiotik prarujukan',
                        'Berikan Gentamisin IM sebagai antibiotik prarujukan',
                        'Bila kornea keruh atau mata bernanah, berikan tetes/salep mata Kloramfenikol atau Tetrasiklin',
                        'Berikan Parasetamol bila suhu 38°C atau lebih',
                    ]
                );
                break;

            case 'Campak dengan komplikasi pada mata dan/atau mulut':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Jika terdapat luka mulut, oleskan antiseptik mulut',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Vitamin A dosis pengobatan',
                        'Jika mata bernanah, berikan tetes/salep mata Kloramfenikol atau Tetrasiklin',
                    ],
                    3
                );
                break;

            case 'Campak':
                $tambah(
                    $rekomendasi,
                    $item,
                    ['Nasihati kapan harus kembali segera'],
                    ['Berikan vitamin A dosis pengobatan']
                );
                break;

            case 'Dengue berat':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Jika syok atau distres napas, berikan oksigen 1-2 L/menit melalui nasal prongs',
                        'Berikan cairan kristaloid isotonis IV sesuai pedoman bila terdapat indikasi',
                        'Rujuk segera',
                        'Jangan berikan aspirin, ibuprofen, natrium diklofenak, atau NSAID lain',
                    ],
                    ['Berikan parasetamol bila suhu 38°C atau lebih']
                );
                break;

            case 'Dengue dengan warning signs':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Jika muntah terus, tidak dapat minum, atau perdarahan, berikan cairan kristaloid isotonis IV sesuai pedoman',
                        'Rujuk segera untuk rawat inap di rumah sakit',
                        'Jangan berikan aspirin, ibuprofen, natrium diklofenak, atau NSAID lain',
                    ],
                    ['Berikan parasetamol bila suhu 38°C atau lebih']
                );
                break;

            case 'Dengue tanpa warning signs':
                $tindakanDengueTanpaWarning = [
                    'Pasien dapat dipulangkan bila kondisi memungkinkan',
                    'Observasi di rumah dan nasihati kapan harus kembali segera',
                    'Jangan berikan aspirin, ibuprofen, natrium diklofenak, atau NSAID lain',
                ];

                if ($umurBulanPlanning !== null && $umurBulanPlanning < 12) {
                    $tindakanDengueTanpaWarning[] = 'Karena umur kurang dari 1 tahun dan terinfeksi dengue, rujuk untuk penanganan lebih lanjut';
                }

                $tambah(
                    $rekomendasi,
                    $item,
                    $tindakanDengueTanpaWarning,
                    ['Berikan parasetamol bila suhu 38°C atau lebih'],
                    1
                );
                break;

            case 'Demam mungkin bukan dengue':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Obati penyebab lain dari demam',
                        'Nasihati kapan harus kembali segera',
                        'Jangan berikan aspirin, ibuprofen, natrium diklofenak, atau NSAID lain',
                    ],
                    ['Berikan parasetamol bila suhu 38°C atau lebih'],
                    2
                );
                break;

            case 'Mastoiditis':
                $tambah(
                    $rekomendasi,
                    $item,
                    ['Rujuk segera'],
                    [
                        'Berikan Ampisilin IM sebagai antibiotik prarujukan',
                        'Berikan Gentamisin IM sebagai antibiotik prarujukan',
                        'Berikan Parasetamol untuk mengatasi nyeri',
                    ]
                );
                break;

            case 'Infeksi telinga akut':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Keringkan telinga dengan bahan penyerap',
                        'Jika cairan keluar, cuci dengan NaCl 0,9% atau H2O2 3%',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Amoksisilin oral selama 10 hari',
                        'Berikan Parasetamol untuk mengatasi nyeri',
                        'Jika cairan keluar, berikan tetes telinga derivat Kuinolon',
                    ],
                    5
                );
                break;

            case 'Infeksi telinga kronis':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Keringkan telinga dengan bahan penyerap setelah dicuci dengan NaCl 0,9% atau H2O2 3%',
                        'Nasihati kapan harus kembali segera',
                    ],
                    ['Berikan tetes telinga derivat Kuinolon'],
                    5
                );
                break;

            case 'Tidak ada infeksi telinga':
                $tambah($rekomendasi, $item, [
                    'Tangani masalah telinga lain yang ditemukan',
                    'Nasihati kapan harus kembali segera',
                ]);
                break;

            case 'Anemia berat':
                $tambah($rekomendasi, $item, [
                    'Bila masih menyusu, teruskan pemberian ASI',
                    'Rujuk segera',
                ]);
                break;

            case 'Anemia':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Nilai masalah pemberian makan; bila ada masalah, kunjungan ulang 7 hari',
                        'Lakukan pemeriksaan tinja untuk deteksi cacingan; bila positif, berikan obat cacing sesuai pedoman/SOP',
                        'Jika tinggal di daerah endemis tinggi malaria, lakukan RDT malaria',
                        'Nasihati kapan harus kembali segera',
                    ],
                    [
                        'Berikan Zat Besi elemental sesuai umur dan berat badan',
                        'Jika belum mendapat obat cacing dalam 6 bulan terakhir, berikan Albendazol sesuai umur',
                        'Pirantel Pamoat dapat digunakan sebagai alternatif pada kondisi tertentu',
                    ],
                    7
                );
                break;

            case 'Tidak anemia':
                $tambah($rekomendasi, $item, [
                    'Jika anak berumur kurang dari 2 tahun, nilai masalah pemberian makan',
                    'Lanjutkan pemantauan pertumbuhan dan perkembangan',
                ]);
                break;

            case 'Sangat pendek (severely stunted)':
                $tambah($rekomendasi, $item, [
                    'Rujuk ke rumah sakit untuk penanganan lebih lanjut',
                ]);
                break;

            case 'Pendek (stunted)':
                $tindakanPendek = [];
                if ($umurBulanPlanning !== null && $umurBulanPlanning < 24) {
                    $tindakanPendek[] = 'Karena umur kurang dari 2 tahun, rujuk ke rumah sakit';
                } else {
                    $tindakanPendek[] = 'Rujuk internal untuk konfirmasi BB/U, BB/PB atau BB/TB, SDIDTK, Buku KIA, dan KPSP';
                    $tindakanPendek[] = 'Jika ada indikator antropometri tidak sesuai, masalah perkembangan, infeksi, tidak membaik setelah tata laksana gizi standar, atau curiga hormonal, rujuk ke rumah sakit';
                }
                $tambah($rekomendasi, $item, $tindakanPendek);
                break;

            case 'Status pertumbuhan normal':
                $tambah($rekomendasi, $item, [
                    'Pantau pertumbuhan dan perkembangan setiap bulan',
                ]);
                break;

            case 'Tinggi (tall)':
                $tambah($rekomendasi, $item, [
                    'Rujuk ke rumah sakit untuk penanganan lebih lanjut',
                ]);
                break;

            case 'Makrosefali':
            case 'Mikrosefali':
                $tambah($rekomendasi, $item, [
                    'Rujuk ke rumah sakit untuk penanganan lebih lanjut',
                ]);
                break;

            case 'Lingkar kepala normal':
                $tambah($rekomendasi, $item, [
                    'Pantau pertumbuhan dan perkembangan setiap bulan',
                ]);
                break;

            case 'Infeksi HIV terkonfirmasi':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Lacak kemungkinan TB; berikan OAT bila terbukti TB atau terapi pencegahan TB bila memenuhi syarat',
                        'Rujuk ke rumah sakit rujukan ARV',
                    ],
                    ['Berikan profilaksis kotrimoksazol sesuai pedoman']
                );
                break;

            case 'Terpajan HIV':
                $tambah(
                    $rekomendasi,
                    $item,
                    [
                        'Rujuk ke puskesmas atau rumah sakit rujukan ARV untuk tes virologi/serologi sesuai umur',
                    ],
                    ['Berikan profilaksis kotrimoksazol sesuai pedoman']
                );
                break;

            case 'Mungkin bukan infeksi HIV':
                $tambah($rekomendasi, $item, [
                    'Atasi, edukasi, dan lakukan tindak lanjut infeksi yang terjadi',
                    'Nasihati kapan harus kembali segera',
                ]);
                break;
        }
    }

    if ($wheezingPlanning) {
        $tambah(
            $rekomendasi,
            'Wheezing',
            ['Nilai ulang setelah pemberian bronkodilator sesuai pedoman MTBS'],
            ['Berikan salbutamol inhalasi/nebulisasi sesuai pedoman dan ketersediaan']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REKOMENDASI GIZI
    |--------------------------------------------------------------------------
    | Gizi mempunyai tindakan dan pengobatan sendiri. Rekomendasi dihitung ulang
    | dari klasifikasi terbaru agar data lama yang kolom `tindakan`-nya kosong
    | tetap muncul pada Planning. Snapshot lama tetap dibaca sebagai fallback.
    */
    $gizi = DB::table('mtbs_gizi')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $klasifikasiGizi = trim((string) (
        $gizi->klasifikasi
        ?? $assessment->gizi
        ?? ''
    ));

    if ($klasifikasiGizi !== '') {
        $klasifikasiGiziRule = Str::upper($klasifikasiGizi);

        $dataGiziPlanning = [
            'umur_bulan' => isset($gizi->umur_bulan)
                && $gizi->umur_bulan !== null
                && $gizi->umur_bulan !== ''
                    ? (int) $gizi->umur_bulan
                    : $umurBulanPlanning,

            'syok' => (bool) ($gizi->syok ?? false),
            'diare' => (bool) ($gizi->diare ?? false),
        ];

        // Sumber utama: hitung ulang menggunakan rule gizi yang sama dengan form Gizi.
        $hasilGiziPlanning = $this->rekomendasiGizi(
            $klasifikasiGiziRule,
            $dataGiziPlanning
        );

        $tindakanGizi = is_array($hasilGiziPlanning['tindakan'] ?? null)
            ? $hasilGiziPlanning['tindakan']
            : [];

        $pengobatanGizi = is_array($hasilGiziPlanning['pengobatan'] ?? null)
            ? $hasilGiziPlanning['pengobatan']
            : [];

        // Kompatibilitas data lama: gabungkan snapshot yang pernah disimpan.
        if ($gizi && !empty($gizi->tindakan)) {
            $snapshotGizi = json_decode($gizi->tindakan, true);

            if (is_array($snapshotGizi)) {
                if (
                    array_key_exists('tindakan', $snapshotGizi)
                    || array_key_exists('pengobatan', $snapshotGizi)
                ) {
                    if (is_array($snapshotGizi['tindakan'] ?? null)) {
                        $tindakanGizi = array_merge(
                            $tindakanGizi,
                            $snapshotGizi['tindakan']
                        );
                    }

                    if (is_array($snapshotGizi['pengobatan'] ?? null)) {
                        $pengobatanGizi = array_merge(
                            $pengobatanGizi,
                            $snapshotGizi['pengobatan']
                        );
                    }
                } else {
                    // Format legacy: kolom tindakan berupa array datar.
                    $tindakanGizi = array_merge(
                        $tindakanGizi,
                        $snapshotGizi
                    );
                }
            }
        }

        $normalisasiItemGizi = static function (array $items): array {
            return array_values(array_unique(array_filter(
                array_map(
                    static fn ($item) => trim((string) $item),
                    $items
                ),
                static fn ($item) => $item !== ''
            )));
        };

        $tindakanGizi = $normalisasiItemGizi($tindakanGizi);
        $pengobatanGizi = $normalisasiItemGizi($pengobatanGizi);

        if ($tindakanGizi !== [] || $pengobatanGizi !== []) {
            $kunjunganUlangGizi = match ($klasifikasiGiziRule) {
                'GIZI BURUK TANPA KOMPLIKASI' => 7,
                'GIZI KURANG',
                'BERISIKO GIZI LEBIH',
                'GIZI LEBIH' => 14,
                default => null,
            };

            $rekomendasi[] = [
                'klasifikasi' => Str::title(
                    Str::lower($klasifikasiGiziRule)
                ),
                'items' => array_values(array_unique(array_merge(
                    $tindakanGizi,
                    $pengobatanGizi
                ))),
                'tindakan' => $tindakanGizi,
                'pengobatan' => $pengobatanGizi,
                'kunjungan_ulang' => $kunjunganUlangGizi,
                'sumber' => 'mtbs_gizi',
            ];
        }
    }

    return response()->json([
        // Dipertahankan untuk kompatibilitas dengan Vue lama.
        'data' => $rekomendasi,

        // Dua sumber hasil Assessment yang ditampilkan pada Planning.
        'rekomendasi_sistem' => $rekomendasi,
        'diagnosa_dokter' => $diagnosaDokter,
        'assessment_sistem_ada' => (bool) $assessment,
        'assessment_dokter_ada' => $diagnosaDokter->isNotEmpty(),
        'message' => (!$assessment && $diagnosaDokter->isEmpty())
            ? 'Assessment MTBS dan diagnosis dokter belum tersedia.'
            : 'Data Planning berhasil dimuat.',
    ], 200);
}
public function showAssessment($kunjunganId)
{
    $row = DB::table('mtbs_assessment')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    if (!$row) {
        return response()->json(['data' => null], 200);
    }

    return response()->json([
        'data' => [
            'batuk' => $row->batuk,
            'diare' => $row->diare,
            'demam' => $row->demam,
            'gizi' => $row->gizi,
            'anemia' => $row->anemia,
            'klasifikasi_global' => json_decode($row->klasifikasi_global ?? '[]', true) ?: [],
            'status_kegawatan' => $row->status_kegawatan,
        ]
    ], 200);
}
public function storePlanning(Request $request)
{
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'kunjunganUlang' => $request->kunjunganUlang === '' ? null : $request->kunjunganUlang,
    ]);

    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],

        'tindakanSegera' => ['nullable', 'array'],
        'tindakanSegera.*' => ['string', 'max:255'],

        'pengobatan' => ['nullable', 'array'],
        'pengobatan.*.nama' => ['nullable', 'string', 'max:255'],
        'pengobatan.*.dosis' => ['nullable', 'string', 'max:255'],
        'pengobatan.*.cara' => ['nullable', 'in:oral,suntik,infus'],
        'pengobatan.*.lama' => ['nullable', 'integer', 'min:0', 'max:365'],

        'edukasi' => ['nullable', 'array'],
        'edukasi.*' => ['string', 'max:255'],

        'catatanEdukasi' => ['nullable', 'string'],
        'kunjunganUlang' => ['nullable', 'integer', 'in:1,2,3,5,7,14'],
    ])->validate();

    DB::beginTransaction();

    try {
        $user = Auth::user();

        $createdBy = $user
            ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas')
            : 'Petugas';

        DB::table('mtbs_planning')->updateOrInsert(
            [
                'kunjungan_id' => $request->kunjungan_id,
            ],
            [
                'tindakan_segera' => json_encode($request->tindakanSegera ?? []),
                'pengobatan' => json_encode($request->pengobatan ?? []),
                'edukasi' => json_encode($request->edukasi ?? []),
                'catatan_edukasi' => $request->catatanEdukasi,
                'kunjungan_ulang_hari' => $request->kunjunganUlang,
                'created_by' => $createdBy,
                'updated_at' => now(),
            ]
        );

        $updatedPelayanan = DB::table('simpus_pelayanan')
            ->where('idpelayanan', $request->kunjungan_id)
            ->update([
                'sudahDilayani' => 1,
                'tglPelayanan' => now(),
            ]);

        Log::info('MTBS UPDATE sudahDilayani setelah planning', [
            'kunjungan_id' => $request->kunjungan_id,
            'updatedPelayanan' => $updatedPelayanan,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Planning MTBS berhasil disimpan',
            'updatedPelayanan' => $updatedPelayanan,
        ], 200);

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('MTBS storePlanning error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan planning MTBS',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function showPlanning($kunjunganId)
{
    $row = DB::table('mtbs_planning')
        ->where('kunjungan_id', $kunjunganId)
        ->first();

    if (!$row) {
        return response()->json(['data' => null], 200);
    }

    return response()->json([
        'data' => [
            'tindakanSegera' => json_decode($row->tindakan_segera ?? '[]', true) ?: [],
            'pengobatan' => json_decode($row->pengobatan ?? '[]', true) ?: [],
            'edukasi' => json_decode($row->edukasi ?? '[]', true) ?: [],
            'catatanEdukasi' => $row->catatan_edukasi,
            'kunjunganUlang' => $row->kunjungan_ulang_hari,
        ]
    ], 200);
}

public function satusehatPreview($idPoli, $idPelayanan)
{
    $pasien = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('pel.idpelayanan', $idPelayanan)
        ->select(
            'p.ID as pasien_id',
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'pel.sudahDilayani',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'poli.nmPoli',
            'p.alamat',
            'l.tglKunjungan',
            'l.kdPoli',
            'l.idLoket'
        )
        ->first();

    if (!$pasien) {
        abort(404, 'Data pasien tidak ditemukan');
    }

    $kunjunganId = (string) $idPelayanan;

    $subjektif = DB::table('mtbs_subjektif')->where('kunjungan_id', $kunjunganId)->first();
    $objektif = DB::table('mtbs_objektif')->where('kunjungan_id', $kunjunganId)->first();
    $assessment = DB::table('mtbs_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
    $planning = DB::table('mtbs_planning')->where('kunjungan_id', $kunjunganId)->first();

    $keluhanUtama = $subjektif ? (json_decode($subjektif->keluhan_utama ?? '[]', true) ?: []) : [];
    $tandaBahaya = $objektif ? (json_decode($objektif->tanda_bahaya ?? '[]', true) ?: []) : [];
    $pemeriksaanKhusus = $objektif ? (json_decode($objektif->pemeriksaan_khusus ?? '[]', true) ?: []) : [];
    $klasifikasiGlobal = $assessment ? (json_decode($assessment->klasifikasi_global ?? '[]', true) ?: []) : [];
    $tindakanSegera = $planning ? (json_decode($planning->tindakan_segera ?? '[]', true) ?: []) : [];
    $pengobatan = $planning ? (json_decode($planning->pengobatan ?? '[]', true) ?: []) : [];
    $edukasi = $planning ? (json_decode($planning->edukasi ?? '[]', true) ?: []) : [];

    $preview = [
        'header' => [
            'nama_pasien'       => $pasien->NAMA_LGKP ?? '-',
            'no_rm'             => $pasien->NO_MR ?? '-',
            'nik'               => $pasien->NIK ?? '-',
            'bpjs'              => '-',
            'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan ?? null,
            'jenis_kunjungan'   => $subjektif->jenis_kunjungan ?? 'pertama',
            'poli'              => $pasien->nmPoli ?? 'MTBS',
            'status_layanan'    => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
            'ihs_pasien'        => null,
            'ihs_label'         => 'Belum dicari ke SATUSEHAT',
        ],

        'kunjungan_mtbs' => [
            'encounter_id' => null,
            'location'     => env('SATUSEHAT_LOCATION_NAME', $pasien->nmPoli ?? 'Poli MTBS'),
            'practitioner' => env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', null),
            'status_history' => [
                'arrived'  => $pasien->tglKunjungan ?? null,
                'progress' => $pasien->tglPelayanan ?? null,
                'finished' => !empty($pasien->sudahDilayani) ? now()->format('Y-m-d H:i:s') : null,
            ],
            'keluhan_utama'      => $keluhanUtama,
            'batuk'              => $assessment->batuk ?? null,
            'diare'              => $assessment->diare ?? null,
            'demam'              => $assessment->demam ?? null,
            'gizi'               => $assessment->gizi ?? null,
            'anemia'             => $assessment->anemia ?? null,
            'klasifikasi_global' => $klasifikasiGlobal,
            'status_kegawatan'   => $assessment->status_kegawatan ?? null,
        ],

        'observasi_mtbs' => [
            'rr'                 => $objektif->rr ?? null,
            'suhu'               => $objektif->suhu ?? null,
            'spo2'               => $objektif->spo2 ?? null,
            'bb'                 => $objektif->bb ?? null,
            'tb'                 => $objektif->tb ?? null,
            'lila'               => $objektif->lila ?? null,
            'lk'                 => $objektif->lk ?? null,
            'status_saga'        => $objektif->status_saga ?? null,
            'tanda_bahaya'       => $tandaBahaya,
            'pemeriksaan_khusus' => $pemeriksaanKhusus,
        ],

        'tatalaksana_mtbs' => [
            'tindakan_segera' => $tindakanSegera,
            'pengobatan'      => $pengobatan,
        ],

        'edukasi_mtbs' => [
            'edukasi'         => $edukasi,
            'catatan'         => $planning->catatan_edukasi ?? null,
            'kunjungan_ulang' => $planning->kunjungan_ulang_hari ?? null,
        ],
    ];

    return Inertia::render('Ruang_Layanan/KIA/MTBS/Satusehat/Preview', [
        'idPelayanan' => $idPelayanan,
        'idPoli' => $idPoli,
        'preview' => $preview,
    ]);
}

public function sendSatusehatDummy(Request $request, $idPoli, $idPelayanan)
{
    try {
        $bundle = $this->buildSatusehatLocalBundle($idPelayanan);

        if (!$bundle['ok']) {
            return response()->json([
                'success' => false,
                'message' => $bundle['message'],
                'logs' => $bundle['logs'] ?? [],
            ], 422);
        }

        $logs = [];
        $result = [
            'token' => null,
            'organization_id' => null,
            'location_id' => null,
            'patient_id' => null,
            'practitioner_id' => null,
            'encounter_id' => null,
            'condition_ids' => [],
            'observation_ids' => [],
            'procedure_ids' => [],
            'questionnaire_response_id' => null,
            'service_request_id' => null,
        ];

        // =========================
        // PHASE 1 - AUTH
        // =========================
        $tokenResp = $this->ssGetAccessToken();
        $logs[] = $tokenResp['log'];

        if (!$tokenResp['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate token SATUSEHAT',
                'logs' => $logs,
                'result' => $result,
            ], 500);
        }

        $token = $tokenResp['token'];
        $result['token'] = 'OK';

        // =========================
        // PHASE 2 - REFERENCE
        // =========================
        $orgId = env('SATUSEHAT_ORG_ID');
        $result['organization_id'] = $orgId;

        $logs[] = $this->makeStepLog('organization', true, 'Organization ID dari env digunakan.', [
            'organization_id' => $orgId,
        ]);

        $locationResp = $this->ssResolveLocation($token);
        $logs[] = $locationResp['log'];

        if (!$locationResp['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Location SATUSEHAT tidak ditemukan',
                'logs' => $logs,
                'result' => $result,
                'local_preview' => $bundle['data'],
            ], 422);
        }

        $locationId = $locationResp['id'];
        $result['location_id'] = $locationId;

        $patientResp = $this->ssResolvePatient($token, $bundle['data']['pasien']);
        $logs[] = $patientResp['log'];

        if (!$patientResp['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Patient SATUSEHAT tidak ditemukan',
                'logs' => $logs,
                'result' => $result,
                'local_preview' => $bundle['data'],
            ], 422);
        }

        $patientId = $patientResp['id'];
        $result['patient_id'] = $patientId;

        $practitionerResp = $this->ssResolvePractitioner($token, $bundle['data']['nakes']);
        $logs[] = $practitionerResp['log'];

        if (!$practitionerResp['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Practitioner SATUSEHAT tidak ditemukan',
                'logs' => $logs,
                'result' => $result,
                'local_preview' => $bundle['data'],
            ], 422);
        }

        $practitionerId = $practitionerResp['id'];
        $result['practitioner_id'] = $practitionerId;

        $logs[] = $this->makeStepLog('related_person', true, 'RelatedPerson di-skip untuk dummy awal.', [
            'status' => 'skipped',
        ]);

        // =========================
        // PHASE 3 - CORE
        // =========================
        $encounterResp = $this->ssCreateEncounter(
            $token,
            $bundle,
            $patientId,
            $practitionerId,
            $locationId,
            $orgId
        );

        $logs[] = $encounterResp['log'];

        if (!$encounterResp['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Encounter SATUSEHAT gagal dibuat',
                'logs' => $logs,
                'result' => $result,
                'encounter_payload' => $encounterResp['payload'] ?? null,
                'local_preview' => $bundle['data'],
            ], 422);
        }

        $encounterId = $encounterResp['id'];
        $result['encounter_id'] = $encounterId;

        // =========================
        // PHASE 4 - CLINICAL
        // =========================

        // 4A. CONDITION
        $conditionRefs = [];
        $conditionPayloads = [];

        $conditionCandidates = $this->mapMtbsConditionCandidates($bundle);

        if (count($conditionCandidates) === 0) {
            $logs[] = $this->makeStepLog('condition', true, 'Condition di-skip. Tidak ada diagnosis MTBS yang bisa dipetakan.', [
                'status' => 'skipped',
            ]);
        } else {
            foreach ($conditionCandidates as $idx => $diagnosis) {
                $conditionResp = $this->ssCreateCondition(
                    $token,
                    $bundle,
                    $patientId,
                    $encounterId,
                    $practitionerId,
                    $orgId,
                    $diagnosis,
                    $idx + 1
                );

                $logs[] = $conditionResp['log'];
                $conditionPayloads[] = $conditionResp['payload'] ?? null;

                if (!$conditionResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Condition SATUSEHAT gagal dibuat',
                        'logs' => $logs,
                        'result' => $result,
                        'condition_payloads' => $conditionPayloads,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['condition_ids'][] = $conditionResp['id'];
                $conditionRefs[] = [
                    'id' => $conditionResp['id'],
                    'display' => $diagnosis['display'],
                    'rank' => $idx + 1,
                ];
            }
        }

        // 4B. FINISH ENCOUNTER
        if (count($conditionRefs) > 0) {
            $finishEncounterResp = $this->ssFinishEncounter(
                $token,
                $bundle,
                $encounterId,
                $patientId,
                $practitionerId,
                $locationId,
                $orgId,
                $conditionRefs
            );

            $logs[] = $finishEncounterResp['log'];

            if (!$finishEncounterResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encounter SATUSEHAT gagal di-finish',
                    'logs' => $logs,
                    'result' => $result,
                    'encounter_payload' => $finishEncounterResp['payload'] ?? null,
                    'local_preview' => $bundle['data'],
                ], 422);
            }
        } else {
            $logs[] = $this->makeStepLog('finish_encounter', true, 'Finish Encounter di-skip karena tidak ada Condition.', [
                'status' => 'skipped',
            ]);
        }

        // 4C. OBSERVATION
        $observationCandidates = $this->mapMtbsObservationCandidates($bundle);

        if (count($observationCandidates) === 0) {
            $logs[] = $this->makeStepLog('observation', true, 'Observation di-skip. Tidak ada data objektif yang bisa dikirim.', [
                'status' => 'skipped',
            ]);
        } else {
            foreach ($observationCandidates as $obs) {
                $observationResp = $this->ssCreateObservation(
                    $token,
                    $bundle,
                    $patientId,
                    $encounterId,
                    $practitionerId,
                    $orgId,
                    $obs
                );

                $logs[] = $observationResp['log'];

                if (!$observationResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Observation SATUSEHAT gagal dibuat',
                        'logs' => $logs,
                        'result' => $result,
                        'observation_payload' => $observationResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['observation_ids'][] = $observationResp['id'];
            }
        }

        // 4D. PROCEDURE
        $procedureCandidates = $this->mapMtbsProcedureCandidates($bundle);

        if (count($procedureCandidates) === 0) {
            $logs[] = $this->makeStepLog('procedure', true, 'Procedure di-skip. Tidak ada tindakan_segera yang bisa dipetakan.', [
                'status' => 'skipped',
            ]);
        } else {
            foreach ($procedureCandidates as $proc) {
                $procedureResp = $this->ssCreateProcedure(
                    $token,
                    $bundle,
                    $patientId,
                    $encounterId,
                    $practitionerId,
                    $orgId,
                    $proc
                );

                $logs[] = $procedureResp['log'];

                if (!$procedureResp['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Procedure SATUSEHAT gagal dibuat',
                        'logs' => $logs,
                        'result' => $result,
                        'procedure_payload' => $procedureResp['payload'] ?? null,
                        'local_preview' => $bundle['data'],
                    ], 422);
                }

                $result['procedure_ids'][] = $procedureResp['id'];
            }
        }

        // 4E. SERVICE REQUEST
        $serviceRequestCandidate = $this->mapMtbsServiceRequestCandidate($bundle);

        if (!$serviceRequestCandidate) {
            $logs[] = $this->makeStepLog('service_request', true, 'ServiceRequest di-skip. Tidak ada kontrol ulang / tindak lanjut yang bisa dikirim.', [
                'status' => 'skipped',
            ]);
        } else {
            $serviceRequestResp = $this->ssCreateServiceRequest(
                $token,
                $bundle,
                $patientId,
                $encounterId,
                $practitionerId,
                $locationId,
                $orgId,
                $serviceRequestCandidate
            );

            $logs[] = $serviceRequestResp['log'];

            if (!$serviceRequestResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'ServiceRequest SATUSEHAT gagal dibuat',
                    'logs' => $logs,
                    'result' => $result,
                    'service_request_payload' => $serviceRequestResp['payload'] ?? null,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $result['service_request_id'] = $serviceRequestResp['id'];
        }

        // 4F. QUESTIONNAIRE RESPONSE
        $questionnaireItems = $this->mapMtbsQuestionnaireResponseItems($bundle);

        if (count($questionnaireItems) === 0) {
            $logs[] = $this->makeStepLog('questionnaire_response', true, 'QuestionnaireResponse di-skip. Tidak ada item MTBS yang bisa dikirim.', [
                'status' => 'skipped',
            ]);
        } else {
            $questionnaireResp = $this->ssCreateQuestionnaireResponse(
                $token,
                $bundle,
                $patientId,
                $encounterId,
                $practitionerId,
                $orgId,
                $questionnaireItems
            );

            $logs[] = $questionnaireResp['log'];

            if (!$questionnaireResp['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'QuestionnaireResponse SATUSEHAT gagal dibuat',
                    'logs' => $logs,
                    'result' => $result,
                    'questionnaire_payload' => $questionnaireResp['payload'] ?? null,
                    'local_preview' => $bundle['data'],
                ], 422);
            }

            $result['questionnaire_response_id'] = $questionnaireResp['id'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Dummy SATUSEHAT berhasil dijalankan sampai seluruh alur utama.',
            'logs' => $logs,
            'result' => $result,
            'local_preview' => $bundle['data'],
        ], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS sendSatusehatDummy error', [
            'msg' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'idPelayanan' => $idPelayanan,
            'idPoli' => $idPoli,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi error saat kirim dummy ke SATUSEHAT',
            'error' => $e->getMessage(),
        ], 500);
    }
}

private function buildSatusehatLocalBundle($idPelayanan): array
{
    $pasien = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->where('pel.idpelayanan', $idPelayanan)
        ->select(
            'p.ID as pasien_id',
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'pel.sudahDilayani',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'poli.nmPoli',
            'p.alamat',
            'l.tglKunjungan',
            'l.kdPoli',
            'l.idLoket'
        )
        ->first();

    if (!$pasien) {
        return [
            'ok' => false,
            'message' => 'Data pasien lokal tidak ditemukan.',
            'logs' => [
                $this->makeStepLog('load_local', false, 'Data pasien lokal tidak ditemukan.', [
                    'idPelayanan' => $idPelayanan,
                ])
            ],
        ];
    }

    $kunjunganId = (string) $idPelayanan;

    $subjektif = DB::table('mtbs_subjektif')->where('kunjungan_id', $kunjunganId)->first();
    $objektif = DB::table('mtbs_objektif')->where('kunjungan_id', $kunjunganId)->first();
    $assessment = DB::table('mtbs_assessment')->where('kunjungan_id', $kunjunganId)->orderByDesc('id')->first();
    $planning = DB::table('mtbs_planning')->where('kunjungan_id', $kunjunganId)->first();

    $data = [
        'pasien' => [
            'pasien_id' => $pasien->pasien_id,
            'nama' => $pasien->NAMA_LGKP,
            'nik' => $pasien->NIK,
            'no_rm' => $pasien->NO_MR,
            'alamat' => $pasien->alamat,
            'birth_date' => null, // isi nanti kalau kolom tersedia
        ],
        'nakes' => [
            'nama' => env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'dr. Alexander'),
            'nik'  => env('SATUSEHAT_DUMMY_PRACTITIONER_NIK', '7209061211900001'),
            'ihs'  => env('SATUSEHAT_DUMMY_PRACTITIONER_IHS'),
            'birth_date' => env('SATUSEHAT_DUMMY_PRACTITIONER_BIRTHDATE'),
        ],
        'kunjungan' => [
            'tanggal_kunjungan' => $pasien->tglKunjungan ?? $pasien->tglPelayanan,
            'poli' => $pasien->nmPoli,
            'kdPoli' => $pasien->kdPoli,
            'id_loket' => $pasien->idLoket,
            'status_layanan' => !empty($pasien->sudahDilayani) ? 'Sudah Dilayani' : 'Draft SATUSEHAT',
        ],
        'subjektif' => $subjektif,
        'objektif' => $objektif,
        'assessment' => $assessment,
        'planning' => $planning,
        'kunjungan_id' => $kunjunganId,
        'id_pelayanan' => $idPelayanan,
    ];

    return [
        'ok' => true,
        'message' => 'Data lokal siap.',
        'data' => $data,
    ];
}

private function ssGetAccessToken(): array
{
    try {
        $url = rtrim(env('SATUSEHAT_AUTH_URL'), '/') . '/accesstoken?grant_type=client_credentials';

        $res = Http::asForm()->post($url, [
            'client_id' => env('SATUSEHAT_CLIENT_ID'),
            'client_secret' => env('SATUSEHAT_CLIENT_SECRET'),
        ]);

        if (!$res->successful()) {
            return [
                'ok' => false,
                'token' => null,
                'log' => $this->makeStepLog('token', false, 'Generate token gagal.', [
                    'status' => $res->status(),
                    'body' => $res->json(),
                ]),
            ];
        }

        $json = $res->json();

        return [
            'ok' => true,
            'token' => $json['access_token'] ?? null,
            'log' => $this->makeStepLog('token', true, 'Generate token berhasil.', [
                'token_type' => $json['token_type'] ?? null,
                'expires_in' => $json['expires_in'] ?? null,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'token' => null,
            'log' => $this->makeStepLog('token', false, 'Generate token exception.', [
                'error' => $e->getMessage(),
            ]),
        ];
    }
}
public function deleteDiagnosaMedis($id)
{
    try {
        DB::table('mtbs_diagnosa_medis')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Diagnosa medis berhasil dihapus',
        ], 200);

    } catch (\Throwable $e) {
        Log::error('MTBS deleteDiagnosaMedis error', [
            'msg' => $e->getMessage(),
            'id' => $id,
        ]);

        return response()->json([
            'message' => 'Gagal menghapus diagnosa medis',
            'error' => $e->getMessage(),
        ], 500);
    }
}
private function ssResolveLocation(string $token): array
{
    try {
        $locationIdFromEnv = env('SATUSEHAT_LOCATION_ID');

        if (!empty($locationIdFromEnv)) {
            return [
                'ok' => true,
                'id' => $locationIdFromEnv,
                'log' => $this->makeStepLog('location', true, 'Location ID dari env digunakan.', [
                    'location_id' => $locationIdFromEnv,
                ]),
            ];
        }

        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');
        $locationName = env('SATUSEHAT_LOCATION_NAME', 'Poli Tumbuh Kembang');
        $orgId = env('SATUSEHAT_ORG_ID');

        $res = Http::withToken($token)
            ->acceptJson()
            ->get($base . '/Location', [
                'name' => $locationName,
                'organization' => $orgId,
            ]);

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('location', false, 'Search Location gagal.', [
                    'status' => $res->status(),
                    'body' => $res->json(),
                ]),
            ];
        }

        $json = $res->json();
        $entry = $json['entry'][0]['resource'] ?? null;

        if (!$entry) {
            return [
                'ok' => false,
                'id' => null,
                'log' => $this->makeStepLog('location', false, 'Location tidak ditemukan.', [
                    'name' => $locationName,
                    'organization' => $orgId,
                    'total' => $json['total'] ?? 0,
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $entry['id'] ?? null,
            'log' => $this->makeStepLog('location', true, 'Location ditemukan.', [
                'location_id' => $entry['id'] ?? null,
                'location_name' => $entry['name'] ?? null,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'log' => $this->makeStepLog('location', false, 'Search Location exception.', [
                'error' => $e->getMessage(),
            ]),
        ];
    }
}

private function ssResolvePatient(string $token, array $pasien): array
{
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $nikLokal   = $pasien['nik'] ?? null;
        $namaLokal  = $pasien['nama'] ?? null;
        $lahirLokal = $pasien['birth_date'] ?? null;

        $nikDummy   = env('SATUSEHAT_DUMMY_PATIENT_NIK');
        $namaDummy  = env('SATUSEHAT_DUMMY_PATIENT_NAME');
        $lahirDummy = env('SATUSEHAT_DUMMY_PATIENT_BIRTHDATE');
        $ihsDummy   = env('SATUSEHAT_DUMMY_PATIENT_IHS');

        $candidates = [];

        // PRIORITAS 1 = DATABASE
        if (!empty($nikLokal)) {
            $candidates[] = [
                'source' => 'database',
                'nik' => $nikLokal,
                'nama' => $namaLokal,
                'birth_date' => $lahirLokal,
                'ihs' => null,
            ];
        }

        // PRIORITAS 2 = ENV DUMMY
        if (!empty($nikDummy)) {
            $candidates[] = [
                'source' => 'env_dummy',
                'nik' => $nikDummy,
                'nama' => $namaDummy,
                'birth_date' => $lahirDummy,
                'ihs' => $ihsDummy,
            ];
        }

        foreach ($candidates as $candidate) {
            if (!empty($candidate['ihs'])) {
                $res = Http::withToken($token)
                    ->acceptJson()
                    ->get($base . '/Patient/' . $candidate['ihs']);

                if ($res->successful()) {
                    $json = $res->json();
                    if (!empty($json['id'])) {
                        return [
                            'ok' => true,
                            'id' => $json['id'],
                            'log' => $this->makeStepLog('patient', true, 'Patient ditemukan by IHS.', [
                                'source' => $candidate['source'],
                                'ihs_dipakai' => $candidate['ihs'],
                                'patient_id' => $json['id'],
                                'patient_name' => $json['name'][0]['text'] ?? null,
                            ]),
                        ];
                    }
                }
            }

            if (!empty($candidate['nik'])) {
                $query = [
                    'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $candidate['nik'],
                ];

                if (!empty($candidate['nama'])) {
                    $query['name'] = $candidate['nama'];
                }

                if (!empty($candidate['birth_date'])) {
                    $query['birthdate'] = $candidate['birth_date'];
                }

                $res = Http::withToken($token)
                    ->acceptJson()
                    ->get($base . '/Patient', $query);

                if ($res->successful()) {
                    $json = $res->json();
                    $entry = $json['entry'][0]['resource'] ?? null;

                    if ($entry && !empty($entry['id'])) {
                        return [
                            'ok' => true,
                            'id' => $entry['id'],
                            'log' => $this->makeStepLog('patient', true, 'Patient ditemukan by database/fallback search.', [
                                'source' => $candidate['source'],
                                'nik_dipakai' => $candidate['nik'],
                                'patient_id' => $entry['id'],
                                'patient_name' => $entry['name'][0]['text'] ?? null,
                            ]),
                        ];
                    }
                }
            }
        }

        return [
            'ok' => false,
            'id' => null,
            'log' => $this->makeStepLog('patient', false, 'Patient tidak ditemukan di SATUSEHAT.', [
                'nik_lokal' => $nikLokal,
                'nama_lokal' => $namaLokal,
                'birth_date_lokal' => $lahirLokal,
                'nik_dummy' => $nikDummy,
                'ihs_dummy' => $ihsDummy,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'log' => $this->makeStepLog('patient', false, 'Patient exception.', [
                'error' => $e->getMessage(),
            ]),
        ];
    }
}

private function ssResolvePractitioner(string $token, array $nakes): array
{
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $nikLokal   = $nakes['nik'] ?? null;
        $namaLokal  = $nakes['nama'] ?? null;
        $lahirLokal = $nakes['birth_date'] ?? null;

        $nikDummy   = env('SATUSEHAT_DUMMY_PRACTITIONER_NIK');
        $namaDummy  = env('SATUSEHAT_DUMMY_PRACTITIONER_NAME');
        $lahirDummy = env('SATUSEHAT_DUMMY_PRACTITIONER_BIRTHDATE');
        $ihsDummy   = env('SATUSEHAT_DUMMY_PRACTITIONER_IHS');

        $candidates = [];

        if (!empty($nikLokal)) {
            $candidates[] = [
                'source' => 'database',
                'nik' => $nikLokal,
                'nama' => $namaLokal,
                'birth_date' => $lahirLokal,
                'ihs' => null,
            ];
        }

        if (!empty($nikDummy)) {
            $candidates[] = [
                'source' => 'env_dummy',
                'nik' => $nikDummy,
                'nama' => $namaDummy,
                'birth_date' => $lahirDummy,
                'ihs' => $ihsDummy,
            ];
        }

        foreach ($candidates as $candidate) {
            if (!empty($candidate['ihs'])) {
                $res = Http::withToken($token)
                    ->acceptJson()
                    ->get($base . '/Practitioner/' . $candidate['ihs']);

                if ($res->successful()) {
                    $json = $res->json();

                    if (!empty($json['id'])) {
                        return [
                            'ok' => true,
                            'id' => $json['id'],
                            'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by IHS.', [
                                'source' => $candidate['source'],
                                'ihs_dipakai' => $candidate['ihs'],
                                'practitioner_id' => $json['id'],
                                'practitioner_name' => $json['name'][0]['text'] ?? null,
                            ]),
                        ];
                    }
                }
            }

            if (!empty($candidate['nik'])) {
                $res = Http::withToken($token)
                    ->acceptJson()
                    ->get($base . '/Practitioner', [
                        'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $candidate['nik'],
                    ]);

                if ($res->successful()) {
                    $json = $res->json();
                    $entry = $json['entry'][0]['resource'] ?? null;

                    if ($entry && !empty($entry['id'])) {
                        return [
                            'ok' => true,
                            'id' => $entry['id'],
                            'log' => $this->makeStepLog('practitioner', true, 'Practitioner ditemukan by NIK.', [
                                'source' => $candidate['source'],
                                'nik_dipakai' => $candidate['nik'],
                                'practitioner_id' => $entry['id'],
                                'practitioner_name' => $entry['name'][0]['text'] ?? null,
                            ]),
                        ];
                    }
                }
            }
        }

        return [
            'ok' => false,
            'id' => null,
            'log' => $this->makeStepLog('practitioner', false, 'Practitioner tidak ditemukan di SATUSEHAT.', [
                'nik_lokal' => $nikLokal,
                'nik_dummy' => $nikDummy,
                'ihs_dummy' => $ihsDummy,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'log' => $this->makeStepLog('practitioner', false, 'Practitioner exception.', [
                'error' => $e->getMessage(),
            ]),
        ];
    }
}

private function ssCreateEncounter(
    string $token,
    array $bundle,
    string $patientId,
    string $practitionerId,
    string $locationId,
    string $orgId
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';

        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $start = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $end = Carbon::parse($tanggalKunjungan)
            ->copy()
            ->addMinutes(15)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'Encounter',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => 'in-progress',
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'ATND',
                                    'display' => 'attender',
                                ],
                            ],
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . $practitionerId,
                        'display' => $nakesNama,
                    ],
                ],
            ],
            'period' => [
                'start' => $start,
                'end' => $end,
            ],
            'location' => [
                [
                    'location' => [
                        'reference' => 'Location/' . $locationId,
                        'display' => $poliNama,
                    ],
                ],
            ],
            'serviceProvider' => [
                'reference' => 'Organization/' . $orgId,
            ],
            'statusHistory' => [
                [
                    'status' => 'arrived',
                    'period' => [
                        'start' => $start,
                        'end' => $start,
                    ],
                ],
                [
                    'status' => 'in-progress',
                    'period' => [
                        'start' => $start,
                        'end' => $end,
                    ],
                ],
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/Encounter', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('encounter', false, 'Create Encounter gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('encounter', true, 'Create Encounter berhasil.', [
                'encounter_id' => $json['id'] ?? null,
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('encounter', false, 'Create Encounter exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function mapMtbsConditionCandidates(array $bundle): array
{
    $assessment = $bundle['data']['assessment'] ?? null;
    if (!$assessment) {
        return [];
    }

    $items = [];

    $push = function (string $key, string $code, string $display) use (&$items) {
        $items[$key] = [
            'code' => $code,
            'display' => $display,
        ];
    };

    if (($assessment->batuk ?? null) === 'pneumonia') {
        $push('batuk_pneumonia', 'J18.9', 'Pneumonia, unspecified organism');
    }

    if (($assessment->demam ?? null) === 'malaria') {
        $push('demam_malaria', 'B54', 'Unspecified malaria');
    }

    if (($assessment->gizi ?? null) === 'gizi_buruk') {
        $push('gizi_buruk', 'E43', 'Unspecified severe protein-energy malnutrition');
    }

    if (($assessment->gizi ?? null) === 'gizi_kurang') {
        $push('gizi_kurang', 'E44.1', 'Mild protein-energy malnutrition');
    }

    if (($assessment->anemia ?? null) === 'anemia_ringan') {
        $push('anemia_ringan', 'D64.9', 'Anemia, unspecified');
    }

    if (($assessment->diare ?? null) === 'dehidrasi_berat') {
        $push('dehidrasi_berat', 'E86', 'Volume depletion');
    }

    if (($assessment->diare ?? null) === 'dehidrasi_ringan') {
        $push('dehidrasi_ringan', 'E86', 'Volume depletion');
    }

    return array_values($items);
}

private function ssCreateCondition(
    string $token,
    array $bundle,
    string $patientId,
    string $encounterId,
    string $practitionerId,
    string $orgId,
    array $diagnosis,
    int $rank = 1
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $recordedDate = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'Condition',
            'clinicalStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
                        'code' => 'active',
                        'display' => 'Active',
                    ],
                ],
            ],
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/condition-category',
                            'code' => 'encounter-diagnosis',
                            'display' => 'Encounter Diagnosis',
                        ],
                    ],
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/icd-10',
                        'code' => $diagnosis['code'],
                        'display' => $diagnosis['display'],
                    ],
                ],
                'text' => $diagnosis['display'],
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $encounterId,
            ],
            'recordedDate' => $recordedDate,
            'recorder' => [
                'reference' => 'Practitioner/' . $practitionerId,
                'display' => $nakesNama,
            ],
            'asserter' => [
                'reference' => 'Practitioner/' . $practitionerId,
                'display' => $nakesNama,
            ],
            'note' => [
                [
                    'text' => 'MTBS dummy mapping - rank ' . $rank,
                ],
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/Condition', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('condition', false, 'Create Condition gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('condition', true, 'Create Condition berhasil.', [
                'condition_id' => $json['id'] ?? null,
                'condition_code' => $diagnosis['code'],
                'condition_display' => $diagnosis['display'],
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('condition', false, 'Create Condition exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function ssFinishEncounter(
    string $token,
    array $bundle,
    string $encounterId,
    string $patientId,
    string $practitionerId,
    string $locationId,
    string $orgId,
    array $conditionRefs
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';

        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $start = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $end = Carbon::parse($tanggalKunjungan)
            ->copy()
            ->addMinutes(15)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $diagnosis = [];
        foreach ($conditionRefs as $idx => $cond) {
            $diagnosis[] = [
                'condition' => [
                    'reference' => 'Condition/' . $cond['id'],
                    'display' => $cond['display'],
                ],
                'use' => [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/diagnosis-role',
                            'code' => $idx === 0 ? 'AD' : 'DD',
                            'display' => $idx === 0 ? 'Admission diagnosis' : 'Discharge diagnosis',
                        ],
                    ],
                ],
                'rank' => $cond['rank'] ?? ($idx + 1),
            ];
        }

        $payload = [
            'resourceType' => 'Encounter',
            'id' => $encounterId,
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => 'finished',
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'ATND',
                                    'display' => 'attender',
                                ],
                            ],
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . $practitionerId,
                        'display' => $nakesNama,
                    ],
                ],
            ],
            'period' => [
                'start' => $start,
                'end' => $end,
            ],
            'location' => [
                [
                    'location' => [
                        'reference' => 'Location/' . $locationId,
                        'display' => $poliNama,
                    ],
                ],
            ],
            'serviceProvider' => [
                'reference' => 'Organization/' . $orgId,
            ],
            'statusHistory' => [
                [
                    'status' => 'arrived',
                    'period' => [
                        'start' => $start,
                        'end' => $start,
                    ],
                ],
                [
                    'status' => 'in-progress',
                    'period' => [
                        'start' => $start,
                        'end' => $end,
                    ],
                ],
                [
                    'status' => 'finished',
                    'period' => [
                        'start' => $end,
                        'end' => $end,
                    ],
                ],
            ],
            'diagnosis' => $diagnosis,
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->put($base . '/Encounter/' . $encounterId, $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? $encounterId,
            'payload' => $payload,
            'log' => $this->makeStepLog('finish_encounter', true, 'Finish Encounter berhasil.', [
                'encounter_id' => $json['id'] ?? $encounterId,
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('finish_encounter', false, 'Finish Encounter exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function mapMtbsObservationCandidates(array $bundle): array
{
    $objektif = $bundle['data']['objektif'] ?? null;
    if (!$objektif) {
        return [];
    }

    $items = [];

    $pushQuantity = function (
        string $key,
        $value,
        string $loincCode,
        string $display,
        string $unit,
        string $ucumCode
    ) use (&$items) {
        if ($value === null || $value === '') {
            return;
        }

        $items[] = [
            'key' => $key,
            'type' => 'quantity',
            'value' => (float) $value,
            'code' => [
                'system' => 'http://loinc.org',
                'code' => $loincCode,
                'display' => $display,
            ],
            'unit' => [
                'system' => 'http://unitsofmeasure.org',
                'code' => $ucumCode,
                'unit' => $unit,
            ],
            'category' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/observation-category',
                'code' => 'vital-signs',
                'display' => 'Vital Signs',
            ],
        ];
    };

    $pushQuantity('rr', $objektif->rr ?? null, '9279-1', 'Respiratory rate', 'breaths/minute', '/min');
    $pushQuantity('suhu', $objektif->suhu ?? null, '8310-5', 'Body temperature', 'Cel', 'Cel');
    $pushQuantity('spo2', $objektif->spo2 ?? null, '59408-5', 'Oxygen saturation in Arterial blood by Pulse oximetry', '%', '%');
    $pushQuantity('bb', $objektif->bb ?? null, '29463-7', 'Body weight', 'kg', 'kg');
    $pushQuantity('tb', $objektif->tb ?? null, '8302-2', 'Body height', 'cm', 'cm');

    return $items;
}

private function ssCreateObservation(
    string $token,
    array $bundle,
    string $patientId,
    string $encounterId,
    string $practitionerId,
    string $orgId,
    array $obs
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $effectiveDateTime = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'Observation',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/observation/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => 'final',
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => $obs['category']['system'],
                            'code' => $obs['category']['code'],
                            'display' => $obs['category']['display'],
                        ],
                    ],
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => $obs['code']['system'],
                        'code' => $obs['code']['code'],
                        'display' => $obs['code']['display'],
                    ],
                ],
                'text' => $obs['code']['display'],
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $encounterId,
            ],
            'effectiveDateTime' => $effectiveDateTime,
            'performer' => [
                [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
            ],
            'valueQuantity' => [
                'value' => $obs['value'],
                'unit' => $obs['unit']['unit'],
                'system' => $obs['unit']['system'],
                'code' => $obs['unit']['code'],
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/Observation', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('observation', false, 'Create Observation gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('observation', true, 'Create Observation berhasil.', [
                'observation_id' => $json['id'] ?? null,
                'observation_code' => $obs['code']['code'],
                'observation_display' => $obs['code']['display'],
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('observation', false, 'Create Observation exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function mapMtbsProcedureCandidates(array $bundle): array
{
    $planning = $bundle['data']['planning'] ?? null;
    if (!$planning) {
        return [];
    }

    $tindakan = json_decode($planning->tindakan_segera ?? '[]', true) ?: [];
    if (!is_array($tindakan) || count($tindakan) === 0) {
        return [];
    }

    $items = [];

    foreach ($tindakan as $raw) {
        $text = trim((string) $raw);
        $lower = Str::lower($text);

        if ($text === '') {
            continue;
        }

        if (Str::contains($lower, ['oksigen', 'oxygen'])) {
            $items[] = [
                'source_text' => $text,
                'code' => '93.96',
                'display' => 'Administration of oxygen',
                'performed_text' => 'Pemberian oksigen',
            ];
            continue;
        }

        if (Str::contains($lower, ['infus', 'iv', 'intravena'])) {
            $items[] = [
                'source_text' => $text,
                'code' => '99.18',
                'display' => 'Injection or infusion of electrolytes',
                'performed_text' => 'Pemasangan / pemberian infus',
            ];
            continue;
        }

        if (Str::contains($lower, ['nebul', 'nebulizer', 'nebulisasi'])) {
            $items[] = [
                'source_text' => $text,
                'code' => '93.94',
                'display' => 'Respiratory medication administered by nebulizer',
                'performed_text' => 'Nebulisasi',
            ];
            continue;
        }

        if (Str::contains($lower, ['rujuk', 'refer'])) {
            $items[] = [
                'source_text' => $text,
                'code' => '89.59',
                'display' => 'Other referral for care',
                'performed_text' => 'Rujukan pasien',
            ];
            continue;
        }

        if (Str::contains($lower, ['edukasi', 'konseling', 'konsultasi'])) {
            $items[] = [
                'source_text' => $text,
                'code' => '94.09',
                'display' => 'Other individual psychotherapy',
                'performed_text' => 'Edukasi / konseling',
            ];
            continue;
        }

        $items[] = [
            'source_text' => $text,
            'code' => '99.99',
            'display' => 'Other miscellaneous procedures',
            'performed_text' => $text,
        ];
    }

    $unique = [];
    foreach ($items as $item) {
        $key = $item['code'] . '|' . $item['performed_text'];
        $unique[$key] = $item;
    }

    return array_values($unique);
}

private function ssCreateProcedure(
    string $token,
    array $bundle,
    string $patientId,
    string $encounterId,
    string $practitionerId,
    string $orgId,
    array $procedure
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $performedDateTime = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'Procedure',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/procedure/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => 'completed',
            'category' => [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => '103693007',
                        'display' => 'Diagnostic procedure',
                    ],
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/icd-9-cm',
                        'code' => $procedure['code'],
                        'display' => $procedure['display'],
                    ],
                ],
                'text' => $procedure['performed_text'],
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $encounterId,
            ],
            'performedDateTime' => $performedDateTime,
            'performer' => [
                [
                    'actor' => [
                        'reference' => 'Practitioner/' . $practitionerId,
                        'display' => $nakesNama,
                    ],
                ],
            ],
            'note' => [
                [
                    'text' => 'Mapping dummy akademik dari tindakan_segera MTBS: ' . ($procedure['source_text'] ?? $procedure['performed_text']),
                ],
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/Procedure', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('procedure', false, 'Create Procedure gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('procedure', true, 'Create Procedure berhasil.', [
                'procedure_id' => $json['id'] ?? null,
                'procedure_code' => $procedure['code'],
                'procedure_display' => $procedure['display'],
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('procedure', false, 'Create Procedure exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function mapMtbsServiceRequestCandidate(array $bundle): ?array
{
    $planning = $bundle['data']['planning'] ?? null;
    $kunjungan = $bundle['data']['kunjungan'] ?? [];

    $hariKontrol = $planning->kunjungan_ulang_hari ?? 3;
    $catatan = $planning->catatan_edukasi ?? 'Kontrol ulang pasien MTBS untuk evaluasi lanjutan';

    $tanggalKunjungan = $kunjungan['tanggal_kunjungan'] ?? now()->toDateTimeString();

    $occurrence = Carbon::parse($tanggalKunjungan)
        ->copy()
        ->addDays((int) $hariKontrol);

    return [
        'code_system' => 'http://snomed.info/sct',
        'code' => '185389009',
        'display' => 'Follow-up visit',
        'text' => 'Kontrol ulang MTBS',
        'note' => $catatan,
        'hari_kontrol' => $hariKontrol,
        'occurrence' => $occurrence->timezone('UTC')->format('Y-m-d\TH:i:sP'),
    ];
}

private function ssCreateServiceRequest(
    string $token,
    array $bundle,
    string $patientId,
    string $encounterId,
    string $practitionerId,
    string $locationId,
    string $orgId,
    array $serviceRequest
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $poliNama   = $bundle['data']['kunjungan']['poli'] ?? 'KIA';
        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $authoredOn = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'ServiceRequest',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/servicerequest/' . $orgId,
                    'value' => (string) Str::uuid(),
                ],
            ],
            'status' => 'active',
            'intent' => 'original-order',
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $encounterId,
            ],
            'authoredOn' => $authoredOn,
            'requester' => [
                'reference' => 'Practitioner/' . $practitionerId,
                'display' => $nakesNama,
            ],
            'performer' => [
                [
                    'reference' => 'Practitioner/' . $practitionerId,
                    'display' => $nakesNama,
                ],
            ],
            'locationReference' => [
                [
                    'reference' => 'Location/' . $locationId,
                    'display' => $poliNama,
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => $serviceRequest['code_system'],
                        'code' => $serviceRequest['code'],
                        'display' => $serviceRequest['display'],
                    ],
                ],
                'text' => $serviceRequest['text'] ?? 'Kontrol ulang MTBS',
            ],
            'occurrenceDateTime' => $serviceRequest['occurrence'],
            'note' => [
                [
                    'text' => $serviceRequest['note'] ?? 'Tindak lanjut / kontrol ulang pasien MTBS',
                ],
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/ServiceRequest', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('service_request', true, 'Create ServiceRequest berhasil.', [
                'service_request_id' => $json['id'] ?? null,
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('service_request', false, 'Create ServiceRequest exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function mapMtbsQuestionnaireResponseItems(array $bundle): array
{
    $sub = $bundle['data']['subjektif'] ?? null;
    $obj = $bundle['data']['objektif'] ?? null;
    $planning = $bundle['data']['planning'] ?? null;

    $items = [];

    $pushString = function (string $linkId, string $text, $value) use (&$items) {
        if ($value === null || $value === '') {
            return;
        }

        $items[] = [
            'linkId' => $linkId,
            'text' => $text,
            'answer' => [
                [
                    'valueString' => (string) $value,
                ],
            ],
        ];
    };

    $pushBoolean = function (string $linkId, string $text, $value) use (&$items) {
        if ($value === null) {
            return;
        }

        $items[] = [
            'linkId' => $linkId,
            'text' => $text,
            'answer' => [
                [
                    'valueBoolean' => (bool) $value,
                ],
            ],
        ];
    };

    $pushInteger = function (string $linkId, string $text, $value) use (&$items) {
        if ($value === null || $value === '') {
            return;
        }

        $items[] = [
            'linkId' => $linkId,
            'text' => $text,
            'answer' => [
                [
                    'valueInteger' => (int) $value,
                ],
            ],
        ];
    };

    $pushDecimal = function (string $linkId, string $text, $value) use (&$items) {
        if ($value === null || $value === '') {
            return;
        }

        $items[] = [
            'linkId' => $linkId,
            'text' => $text,
            'answer' => [
                [
                    'valueDecimal' => (float) $value,
                ],
            ],
        ];
    };

    $pushStringArray = function (string $linkId, string $text, array $values) use (&$items) {
        if (count($values) === 0) {
            return;
        }

        $answers = [];
        foreach ($values as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $answers[] = [
                'valueString' => (string) $v,
            ];
        }

        if (count($answers) === 0) {
            return;
        }

        $items[] = [
            'linkId' => $linkId,
            'text' => $text,
            'answer' => $answers,
        ];
    };

    if ($sub) {
        $pushStringArray('1.1', 'Keluhan utama', json_decode($sub->keluhan_utama ?? '[]', true) ?: []);
        $pushString('1.2', 'Keluhan lain', $sub->keluhan_lain ?? null);
        $pushInteger('1.3', 'Batuk lama hari', $sub->batuk_lama_hari ?? null);
        $pushBoolean('1.4', 'Napas cepat', isset($sub->napas_cepat) ? (bool) $sub->napas_cepat : null);
        $pushBoolean('1.5', 'Mengi', isset($sub->mengi) ? (bool) $sub->mengi : null);
        $pushInteger('1.6', 'Diare lama hari', $sub->diare_lama_hari ?? null);
        $pushBoolean('1.7', 'Darah pada tinja', isset($sub->darah_tinja) ? (bool) $sub->darah_tinja : null);
        $pushInteger('1.8', 'Demam lama hari', $sub->demam_lama_hari ?? null);
        $pushBoolean('1.9', 'Demam tiap hari', isset($sub->demam_tiap_hari) ? (bool) $sub->demam_tiap_hari : null);
        $pushBoolean('1.10', 'Riwayat malaria', isset($sub->riwayat_malaria) ? (bool) $sub->riwayat_malaria : null);
        $pushBoolean('1.11', 'Riwayat campak', isset($sub->riwayat_campak) ? (bool) $sub->riwayat_campak : null);
        $pushBoolean('1.12', 'Nyeri telinga', isset($sub->nyeri_telinga) ? (bool) $sub->nyeri_telinga : null);
        $pushBoolean('1.13', 'Cairan telinga', isset($sub->cairan_telinga) ? (bool) $sub->cairan_telinga : null);
        $pushInteger('1.14', 'Telinga lama hari', $sub->telinga_lama_hari ?? null);
        $pushString('1.15', 'Riwayat imunisasi', $sub->riwayat_imunisasi ?? null);
        $pushString('1.16', 'Vitamin A', $sub->vitamin_a ?? null);
        $pushString('1.17', 'Riwayat ASI', $sub->riwayat_asi ?? null);
        $pushString('1.18', 'Riwayat penyakit', $sub->riwayat_penyakit ?? null);
        $pushString('1.19', 'HIV ibu', $sub->hiv_ibu ?? null);
    }

    if ($obj) {
        $pushStringArray('2.1', 'Tanda bahaya', json_decode($obj->tanda_bahaya ?? '[]', true) ?: []);
        $pushStringArray('2.2', 'Pemeriksaan khusus', json_decode($obj->pemeriksaan_khusus ?? '[]', true) ?: []);
        $pushDecimal('2.3', 'Respiratory rate', $obj->rr ?? null);
        $pushDecimal('2.4', 'Suhu', $obj->suhu ?? null);
        $pushDecimal('2.5', 'SpO2', $obj->spo2 ?? null);
        $pushDecimal('2.6', 'Berat badan', $obj->bb ?? null);
        $pushDecimal('2.7', 'Tinggi badan', $obj->tb ?? null);
        $pushDecimal('2.8', 'LILA', $obj->lila ?? null);
        $pushDecimal('2.9', 'Lingkar kepala', $obj->lk ?? null);
        $pushString('2.10', 'Status SAGA', $obj->status_saga ?? null);
    }

    if ($planning) {
        $pushStringArray('3.1', 'Tindakan segera', json_decode($planning->tindakan_segera ?? '[]', true) ?: []);
        $pushStringArray('3.2', 'Edukasi', json_decode($planning->edukasi ?? '[]', true) ?: []);
        $pushString('3.3', 'Catatan edukasi', $planning->catatan_edukasi ?? null);
        $pushInteger('3.4', 'Kunjungan ulang hari', $planning->kunjungan_ulang_hari ?? null);
    }

    return $items;
}

private function ssCreateQuestionnaireResponse(
    string $token,
    array $bundle,
    string $patientId,
    string $encounterId,
    string $practitionerId,
    string $orgId,
    array $items
): array {
    try {
        $base = rtrim(env('SATUSEHAT_BASE_URL'), '/');

        $pasienNama = $bundle['data']['pasien']['nama'] ?? 'Pasien';
        $nakesNama  = $bundle['data']['nakes']['nama'] ?? env('SATUSEHAT_DUMMY_PRACTITIONER_NAME', 'Dokter Dummy');
        $tanggalKunjungan = $bundle['data']['kunjungan']['tanggal_kunjungan'] ?? now()->toDateTimeString();

        $authored = Carbon::parse($tanggalKunjungan)
            ->timezone('UTC')
            ->format('Y-m-d\TH:i:sP');

        $payload = [
            'resourceType' => 'QuestionnaireResponse',
            'questionnaire' => 'https://simpuswangi.local/Questionnaire/mtbs-dummy',
            'status' => 'completed',
            'subject' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $encounterId,
            ],
            'authored' => $authored,
            'author' => [
                'reference' => 'Practitioner/' . $practitionerId,
                'display' => $nakesNama,
            ],
            'source' => [
                'reference' => 'Patient/' . $patientId,
                'display' => $pasienNama,
            ],
            'item' => $items,
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($base . '/QuestionnaireResponse', $payload);

        $json = $res->json();

        if (!$res->successful()) {
            return [
                'ok' => false,
                'id' => null,
                'payload' => $payload,
                'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse gagal.', [
                    'status' => $res->status(),
                    'payload' => $payload,
                    'response' => $json,
                    'raw_body' => $res->body(),
                ]),
            ];
        }

        return [
            'ok' => true,
            'id' => $json['id'] ?? null,
            'payload' => $payload,
            'log' => $this->makeStepLog('questionnaire_response', true, 'Create QuestionnaireResponse berhasil.', [
                'questionnaire_response_id' => $json['id'] ?? null,
                'payload' => $payload,
                'response' => $json,
            ]),
        ];
    } catch (\Throwable $e) {
        return [
            'ok' => false,
            'id' => null,
            'payload' => null,
            'log' => $this->makeStepLog('questionnaire_response', false, 'Create QuestionnaireResponse exception.', [
                'error' => $e->getMessage(),
                'trace_line' => $e->getLine(),
                'trace_file' => $e->getFile(),
            ]),
        ];
    }
}

private function makeStepLog(string $step, bool $success, string $message, array $meta = []): array
{
    return [
        'step' => $step,
        'success' => $success,
        'ok' => $success,
        'message' => $message,
        'meta' => $meta,
        'data' => $meta,
        'time' => now()->toDateTimeString(),
    ];
}

public function storeGizi(Request $request)
{
    $request->merge([
        'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
        'edema' => $request->edema === '' ? '0' : $request->edema,
        'catatan' => $request->catatan === '' ? null : $request->catatan,
    ]);

    validator($request->all(), [
        'kunjungan_id' => ['required', 'string', 'max:100'],
        'edema' => ['required', 'in:0,+1,+2,+3'],
        'komplikasi_medis' => ['nullable', 'boolean'],
        'lemah_menyusu' => ['nullable', 'boolean'],
        'bb_tidak_naik' => ['nullable', 'boolean'],
        'syok' => ['nullable', 'boolean'],
        'catatan' => ['nullable', 'string'],
    ])->validate();

    try {
        $konteks = $this->ambilKonteksGizi($request->kunjungan_id);

        if (!$konteks['pasien']) {
            return response()->json([
                'message' => 'Data pasien atau kunjungan tidak ditemukan.',
            ], 404);
        }

        if (!$konteks['objektif']) {
            return response()->json([
                'message' => 'Objektif MTBS belum diisi. Isi BB, TB/PB, dan LiLA di menu Objektif terlebih dahulu.',
            ], 422);
        }

        $input = [
            'umur_bulan' => $konteks['umur_bulan'],
            'jenis_kelamin' => $konteks['jenis_kelamin'],
            'bb' => $konteks['bb'],
            'tb' => $konteks['tb'],
            'lila' => $konteks['lila'],
            'zscore' => $konteks['zscore']['nilai'],
            'edema' => $request->edema ?? '0',

            // Field berikut hanya konfirmasi yang belum bisa diambil pasti dari S/O.
            'komplikasi_medis_manual' => $request->boolean('komplikasi_medis'),
            'lemah_menyusu_manual' => $request->boolean('lemah_menyusu'),
            'bb_tidak_naik_manual' => $request->boolean('bb_tidak_naik'),
            'syok' => $request->boolean('syok'),

            // Temuan otomatis dari data lain.
            'komplikasi_otomatis' => $konteks['otomatis']['komplikasi_medis'],
            'lemah_menyusu_otomatis' => $konteks['otomatis']['lemah_menyusu'],
            'bb_tidak_naik_otomatis' => $konteks['otomatis']['bb_tidak_naik'],
            'diare' => $konteks['otomatis']['diare'],

            'catatan' => $request->catatan,
        ];

        $hasil = $this->hitungKlasifikasiGizi($input);
        $rekomendasi = $this->rekomendasiGizi(
            $hasil['klasifikasi'],
            $input
        );

        DB::table('mtbs_gizi')->updateOrInsert(
            ['kunjungan_id' => $request->kunjungan_id],
            [
                // Snapshot otomatis; petugas tidak mengetik ulang.
                'umur_bulan' => $input['umur_bulan'],
                'bb' => $input['bb'],
                'tb' => $input['tb'],
                'lila' => $input['lila'],
                'zscore' => $input['zscore'],

                'edema' => $input['edema'],
                'komplikasi_medis' => $input['komplikasi_medis_manual'] ? 1 : 0,
                'lemah_menyusu' => $input['lemah_menyusu_manual'] ? 1 : 0,
                'bb_tidak_naik' => $input['bb_tidak_naik_manual'] ? 1 : 0,
                'syok' => $input['syok'] ? 1 : 0,
                'diare' => $input['diare'] ? 1 : 0,

                'klasifikasi' => $hasil['klasifikasi'],

                // Kolom lama tetap dipakai, tetapi isinya sekarang terstruktur.
                'tindakan' => json_encode([
                    'tindakan' => $rekomendasi['tindakan'],
                    'pengobatan' => $rekomendasi['pengobatan'],
                ], JSON_UNESCAPED_UNICODE),

                'catatan' => $input['catatan'],
                'updated_at' => now(),
            ]
        );

        $row = DB::table('mtbs_gizi')
            ->where('kunjungan_id', $request->kunjungan_id)
            ->first();

        return response()->json([
            'message' => 'Data gizi MTBS berhasil disimpan dan dihitung otomatis.',
            'data' => $this->formatResponsGizi(
                $request->kunjungan_id,
                $konteks,
                $row
            ),
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBS storeGizi error', [
            'msg' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Gagal menyimpan data gizi MTBS.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function showGizi($kunjunganId)
{
    try {
        $konteks = $this->ambilKonteksGizi($kunjunganId);

        if (!$konteks['pasien']) {
            return response()->json([
                'message' => 'Data pasien atau kunjungan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $row = DB::table('mtbs_gizi')
            ->where('kunjungan_id', $kunjunganId)
            ->first();

        return response()->json([
            'message' => 'Data gizi MTBS berhasil dimuat.',
            'data' => $this->formatResponsGizi(
                $kunjunganId,
                $konteks,
                $row
            ),
        ], 200);
    } catch (\Throwable $e) {
        Log::error('MTBS showGizi error', [
            'kunjungan_id' => $kunjunganId,
            'msg' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Gagal memuat data gizi MTBS.',
            'error' => $e->getMessage(),
            'data' => null,
        ], 500);
    }
}

private function formatResponsGizi(
    string $kunjunganId,
    array $konteks,
    ?object $row
): array {
    $manual = [
        'komplikasi_medis' => $row ? (bool) $row->komplikasi_medis : false,
        'lemah_menyusu' => $row ? (bool) $row->lemah_menyusu : false,
        'bb_tidak_naik' => $row ? (bool) $row->bb_tidak_naik : false,
        'syok' => $row ? (bool) $row->syok : false,
    ];

    $input = [
        'umur_bulan' => $konteks['umur_bulan'],
        'jenis_kelamin' => $konteks['jenis_kelamin'],
        'bb' => $konteks['bb'],
        'tb' => $konteks['tb'],
        'lila' => $konteks['lila'],
        'zscore' => $konteks['zscore']['nilai'],
        'edema' => $row->edema ?? '0',

        'komplikasi_medis_manual' => $manual['komplikasi_medis'],
        'lemah_menyusu_manual' => $manual['lemah_menyusu'],
        'bb_tidak_naik_manual' => $manual['bb_tidak_naik'],
        'syok' => $manual['syok'],

        'komplikasi_otomatis' => $konteks['otomatis']['komplikasi_medis'],
        'lemah_menyusu_otomatis' => $konteks['otomatis']['lemah_menyusu'],
        'bb_tidak_naik_otomatis' => $konteks['otomatis']['bb_tidak_naik'],
        'diare' => $konteks['otomatis']['diare'],

        'catatan' => $row->catatan ?? '',
    ];

    $hasil = $this->hitungKlasifikasiGizi($input);
    $rekomendasi = $this->rekomendasiGizi(
        $hasil['klasifikasi'],
        $input
    );

    return [
        'kunjungan_id' => $kunjunganId,
        'data_objektif_ada' => (bool) $konteks['objektif'],

        'umur_bulan' => $konteks['umur_bulan'],
        'umur_label' => $this->formatUmurBulanGizi($konteks['umur_bulan']),
        'jenis_kelamin' => $konteks['jenis_kelamin'],
        'jenis_kelamin_label' => $konteks['jenis_kelamin_label'],

        // Dibaca dari Objektif, tidak diketik ulang di halaman Gizi.
        'bb' => $konteks['bb'],
        'tb' => $konteks['tb'],
        'lila' => $konteks['lila'],

        'indikator' => $konteks['zscore']['indikator'],
        'indikator_label' => $konteks['zscore']['indikator_label'],
        'zscore' => $konteks['zscore']['nilai'],
        'zscore_error' => $konteks['zscore']['error'],
        'zscore_sumber' => 'WHO Child Growth Standards BB/PB atau BB/TB',

        'edema' => $input['edema'],
        'komplikasi_medis' => $manual['komplikasi_medis'],
        'lemah_menyusu' => $manual['lemah_menyusu'],
        'bb_tidak_naik' => $manual['bb_tidak_naik'],
        'syok' => $manual['syok'],
        'catatan' => $input['catatan'],

        'otomatis' => [
            'komplikasi_medis' => $konteks['otomatis']['komplikasi_medis'],
            'lemah_menyusu' => $konteks['otomatis']['lemah_menyusu'],
            'bb_tidak_naik' => $konteks['otomatis']['bb_tidak_naik'],
            'diare' => $konteks['otomatis']['diare'],
            'bb_sebelumnya' => $konteks['otomatis']['bb_sebelumnya'],
            'tanggal_bb_sebelumnya' => $konteks['otomatis']['tanggal_bb_sebelumnya'],
        ],

        'efektif' => [
            'ada_komplikasi_medis' =>
                count($konteks['otomatis']['komplikasi_medis']) > 0
                || $manual['komplikasi_medis'],
            'lemah_menyusu' =>
                $konteks['otomatis']['lemah_menyusu']
                || $manual['lemah_menyusu'],
            'bb_tidak_naik' =>
                $konteks['otomatis']['bb_tidak_naik']
                || $manual['bb_tidak_naik'],
            'diare' => $konteks['otomatis']['diare'],
            'syok' => $manual['syok'],
        ],

        'klasifikasi' => $hasil['klasifikasi'],
        'dasar_klasifikasi' => $hasil['dasar'],
        'tindakan' => $rekomendasi['tindakan'],
        'pengobatan' => $rekomendasi['pengobatan'],
    ];
}

private function ambilKonteksGizi(string $kunjunganId): array
{
    $pasien = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->where('pel.idpelayanan', $kunjunganId)
        ->select(
            'p.ID as pasien_id',
            'p.TGL_LHR',
            'p.JENIS_KLMIN',
            'l.tglKunjungan',
            'l.idLoket'
        )
        ->first();

    $objektif = DB::table('mtbs_objektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $subjektif = DB::table('mtbs_subjektif')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $assessment = DB::table('mtbs_assessment')
        ->where('kunjungan_id', $kunjunganId)
        ->orderByDesc('id')
        ->first();

    $umurBulan = null;

    if ($pasien && !empty($pasien->TGL_LHR)) {
        $lahir = Carbon::parse($pasien->TGL_LHR);
        $kunjungan = Carbon::parse($pasien->tglKunjungan ?? now());
        $diff = $lahir->diff($kunjungan);
        $umurBulan = ($diff->y * 12) + $diff->m;
    }

    $jenisKelamin = null;

    if ($pasien) {
        $jenisKelamin = in_array($pasien->JENIS_KLMIN, [1, '1', 'L', 'l'], true)
            ? 'L'
            : (in_array($pasien->JENIS_KLMIN, [2, '2', 'P', 'p'], true) ? 'P' : null);
    }

    if (!$jenisKelamin && $subjektif) {
        $jenisKelamin = in_array($subjektif->jenis_kelamin, ['L', 'P'], true)
            ? $subjektif->jenis_kelamin
            : null;
    }

    $bb = $objektif && $objektif->bb !== null
        ? (float) $objektif->bb
        : null;

    $tb = $objektif && $objektif->tb !== null
        ? (float) $objektif->tb
        : null;

    $lila = $objektif && $objektif->lila !== null
        ? (float) $objektif->lila
        : null;

    $zscore = $this->hitungZScoreWhoGizi(
        $jenisKelamin,
        $umurBulan,
        $bb,
        $tb
    );

    $riwayatBb = $this->ambilBeratSebelumnyaGizi(
        $kunjunganId,
        $pasien
    );

    $komplikasi = $this->deteksiKomplikasiGiziOtomatis(
        $subjektif,
        $objektif,
        $assessment
    );

    $teksObjektif = $this->gabungTeksObjektifGizi($objektif);

    $lemahMenyusuOtomatis = $this->teksMengandungGizi(
        $teksObjektif,
        [
            'tidak bisa minum',
            'tidak bisa menyusu',
            'terlalu lemah untuk menyusu',
            'lemah menyusu',
            'tidak mau menyusu',
        ]
    );

    $diareOtomatis = false;

    if ($subjektif) {
        $keluhan = json_decode($subjektif->keluhan_utama ?? '[]', true) ?: [];

        $diareOtomatis =
            ((int) ($subjektif->diare_lama_hari ?? 0) > 0)
            || $this->teksMengandungGizi(
                implode(' ', $keluhan),
                ['diare', 'mencret']
            );
    }

    $bbTidakNaikOtomatis =
        $bb !== null
        && $riwayatBb['bb'] !== null
        && $bb <= $riwayatBb['bb'];

    return [
        'pasien' => $pasien,
        'objektif' => $objektif,
        'subjektif' => $subjektif,
        'assessment' => $assessment,

        'umur_bulan' => $umurBulan,
        'jenis_kelamin' => $jenisKelamin,
        'jenis_kelamin_label' => match ($jenisKelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => null,
        },

        'bb' => $bb,
        'tb' => $tb,
        'lila' => $lila,
        'zscore' => $zscore,

        'otomatis' => [
            'komplikasi_medis' => $komplikasi,
            'lemah_menyusu' => $lemahMenyusuOtomatis,
            'bb_tidak_naik' => $bbTidakNaikOtomatis,
            'diare' => $diareOtomatis,
            'bb_sebelumnya' => $riwayatBb['bb'],
            'tanggal_bb_sebelumnya' => $riwayatBb['tanggal'],
        ],
    ];
}

private function ambilBeratSebelumnyaGizi(
    string $kunjunganId,
    ?object $pasien
): array {
    if (!$pasien) {
        return ['bb' => null, 'tanggal' => null];
    }

    $row = DB::table('mtbs_objektif as o')
        ->join('simpus_pelayanan as pel', 'o.kunjungan_id', '=', 'pel.idpelayanan')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('l.pasienId', $pasien->pasien_id)
        ->where('o.kunjungan_id', '!=', $kunjunganId)
        ->whereNotNull('o.bb')
        ->where(function ($query) use ($pasien) {
            if (!empty($pasien->tglKunjungan)) {
                $query->whereDate('l.tglKunjungan', '<', $pasien->tglKunjungan)
                    ->orWhere(function ($sub) use ($pasien) {
                        $sub->whereDate('l.tglKunjungan', '=', $pasien->tglKunjungan)
                            ->where('l.idLoket', '<', $pasien->idLoket);
                    });
            } else {
                $query->where('l.idLoket', '<', $pasien->idLoket);
            }
        })
        ->orderByDesc('l.tglKunjungan')
        ->orderByDesc('l.idLoket')
        ->select('o.bb', 'l.tglKunjungan')
        ->first();

    return [
        'bb' => $row && $row->bb !== null ? (float) $row->bb : null,
        'tanggal' => $row->tglKunjungan ?? null,
    ];
}

private function deteksiKomplikasiGiziOtomatis(
    ?object $subjektif,
    ?object $objektif,
    ?object $assessment
): array {
    $hasil = [];

    $teksObjektif = $this->gabungTeksObjektifGizi($objektif);

    if ($this->teksMengandungGizi(
        $teksObjektif,
        ['anoreksia', 'tidak mau makan', 'malas makan', 'tidak bisa minum', 'malas minum']
    )) {
        $hasil[] = 'Anoreksia / tidak dapat makan atau minum';
    }

    if ($this->teksMengandungGizi(
        $teksObjektif,
        ['letargi', 'tidak sadar', 'kesadaran menurun', 'penurunan kesadaran']
    )) {
        $hasil[] = 'Letargi atau penurunan kesadaran';
    }

    if ($this->teksMengandungGizi(
        $teksObjektif,
        ['demam tinggi']
    )) {
        $hasil[] = 'Demam tinggi';
    }

    if ($assessment) {
        $global = json_decode($assessment->klasifikasi_global ?? '[]', true) ?: [];
        $teksAssessment = strtolower(implode(' ', [
            (string) ($assessment->batuk ?? ''),
            (string) ($assessment->diare ?? ''),
            (string) ($assessment->anemia ?? ''),
            (string) ($assessment->status_kegawatan ?? ''),
            implode(' ', $global),
        ]));

        if ($this->teksMengandungGizi(
            $teksAssessment,
            ['dehidrasi_berat', 'dehidrasi berat']
        )) {
            $hasil[] = 'Dehidrasi berat';
        }

        if ($this->teksMengandungGizi(
            $teksAssessment,
            ['pneumonia_berat', 'pneumonia berat']
        )) {
            $hasil[] = 'Pneumonia berat';
        }

        if ($this->teksMengandungGizi(
            $teksAssessment,
            ['anemia_berat', 'anemia berat']
        )) {
            $hasil[] = 'Anemia berat';
        }

        if ($this->teksMengandungGizi(
            $teksAssessment,
            ['penyakit berat dengan demam']
        )) {
            $hasil[] = 'Demam tinggi / penyakit berat dengan demam';
        }
    }

    return array_values(array_unique($hasil));
}

private function gabungTeksObjektifGizi(?object $objektif): string
{
    if (!$objektif) {
        return '';
    }

    $items = [];

    foreach ([
        'tanda_bahaya',
        'saga_penampilan',
        'saga_napas',
        'saga_sirkulasi',
        'pemeriksaan_khusus',
    ] as $field) {
        $decoded = json_decode($objektif->{$field} ?? '[]', true);

        if (is_array($decoded)) {
            $items = array_merge($items, $decoded);
        }
    }

    $items[] = $objektif->status_saga ?? '';

    return strtolower(implode(' ', array_filter($items)));
}

private function teksMengandungGizi(string $text, array $needles): bool
{
    $text = strtolower($text);

    foreach ($needles as $needle) {
        if ($needle !== '' && str_contains($text, strtolower($needle))) {
            return true;
        }
    }

    return false;
}

private function hitungZScoreWhoGizi(
    ?string $jenisKelamin,
    ?int $umurBulan,
    ?float $bb,
    ?float $tb
): array {
    $indikator = $umurBulan !== null && $umurBulan < 24
        ? 'wfl'
        : 'wfh';

    $indikatorLabel = $indikator === 'wfl'
        ? 'BB/PB'
        : 'BB/TB';

    if (!$jenisKelamin) {
        return [
            'nilai' => null,
            'indikator' => $indikator,
            'indikator_label' => $indikatorLabel,
            'error' => 'Jenis kelamin pasien belum tersedia.',
        ];
    }

    if ($umurBulan === null) {
        return [
            'nilai' => null,
            'indikator' => $indikator,
            'indikator_label' => $indikatorLabel,
            'error' => 'Umur pasien belum tersedia.',
        ];
    }

    if ($bb === null || $tb === null || $bb <= 0 || $tb <= 0) {
        return [
            'nilai' => null,
            'indikator' => $indikator,
            'indikator_label' => $indikatorLabel,
            'error' => 'BB atau TB/PB pada Objektif belum tersedia.',
        ];
    }

    $table = config("who_wfl_wfh_lms.{$indikator}.{$jenisKelamin}", []);
    $key = (int) round($tb * 10);

    if (!isset($table[$key])) {
        $range = $indikator === 'wfl'
            ? '45,0–110,0 cm'
            : '65,0–120,0 cm';

        return [
            'nilai' => null,
            'indikator' => $indikator,
            'indikator_label' => $indikatorLabel,
            'error' => "Panjang/tinggi di luar rentang tabel WHO {$range}.",
        ];
    }

    [$l, $m, $s, $sd3Neg, $sd2Neg, $sd2Pos, $sd3Pos] = $table[$key];

    if ((float) $l === 0.0) {
        $z = log($bb / $m) / $s;
    } else {
        $z = (pow($bb / $m, $l) - 1) / ($l * $s);
    }

    // Penyesuaian WHO untuk ekor distribusi di luar ±3 SD.
    if ($bb > $sd3Pos) {
        $jarak = $sd3Pos - $sd2Pos;

        if ($jarak > 0) {
            $z = 3 + (($bb - $sd3Pos) / $jarak);
        }
    } elseif ($bb < $sd3Neg) {
        $jarak = $sd2Neg - $sd3Neg;

        if ($jarak > 0) {
            $z = -3 + (($bb - $sd3Neg) / $jarak);
        }
    }

    return [
        'nilai' => round($z, 2),
        'indikator' => $indikator,
        'indikator_label' => $indikatorLabel,
        'error' => null,
    ];
}

private function formatUmurBulanGizi(?int $umurBulan): ?string
{
    if ($umurBulan === null) {
        return null;
    }

    $tahun = intdiv($umurBulan, 12);
    $bulan = $umurBulan % 12;

    if ($tahun === 0) {
        return "{$bulan} bulan";
    }

    if ($bulan === 0) {
        return "{$tahun} tahun";
    }

    return "{$tahun} tahun {$bulan} bulan";
}

private function getJenisKunjunganOtomatis($idPelayanan)
{
    $kunjunganSekarang = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('pel.idpelayanan', $idPelayanan)
        ->select(
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'l.idLoket',
            'l.pasienId',
            'l.tglKunjungan',
            'l.kdPoli'
        )
        ->first();

    if (!$kunjunganSekarang) {
        return 'pertama';
    }

    $pernahKunjunganSebelumnya = DB::table('simpus_pelayanan as pel')
        ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->where('l.pasienId', $kunjunganSekarang->pasienId)
        ->where('l.kdPoli', $kunjunganSekarang->kdPoli)
        ->where('pel.idpelayanan', '!=', $idPelayanan)
        ->where(function ($q) use ($kunjunganSekarang) {
            if (!empty($kunjunganSekarang->tglKunjungan)) {
                $q->whereDate('l.tglKunjungan', '<', $kunjunganSekarang->tglKunjungan)
                  ->orWhere('l.idLoket', '<', $kunjunganSekarang->idLoket);
            } else {
                $q->where('l.idLoket', '<', $kunjunganSekarang->idLoket);
            }
        })
        ->exists();

    return $pernahKunjunganSebelumnya ? 'ulang' : 'pertama';
}

private function hitungKlasifikasiGizi(array $data): array
{
    $umur = isset($data['umur_bulan']) && $data['umur_bulan'] !== null
        ? (int) $data['umur_bulan']
        : null;

    $bb = isset($data['bb']) && $data['bb'] !== null
        ? (float) $data['bb']
        : null;

    $lila = isset($data['lila']) && $data['lila'] !== null
        ? (float) $data['lila']
        : null;

    $z = isset($data['zscore']) && $data['zscore'] !== null
        ? (float) $data['zscore']
        : null;

    $edema = $data['edema'] ?? '0';

    $komplikasi =
        !empty($data['komplikasi_medis_manual'])
        || count($data['komplikasi_otomatis'] ?? []) > 0;

    $lemahMenyusu =
        !empty($data['lemah_menyusu_manual'])
        || !empty($data['lemah_menyusu_otomatis']);

    $bbTidakNaik =
        !empty($data['bb_tidak_naik_manual'])
        || !empty($data['bb_tidak_naik_otomatis']);

    $dasar = [];

    if ($umur === null || $umur < 2 || $umur > 59) {
        return [
            'klasifikasi' => 'Belum dapat diklasifikasikan',
            'dasar' => ['Umur harus berada pada rentang MTBS 2–59 bulan.'],
        ];
    }

    if ($bb === null || $bb <= 0) {
        return [
            'klasifikasi' => 'Belum dapat diklasifikasikan',
            'dasar' => ['Berat badan pada Objektif belum tersedia.'],
        ];
    }

    if ($z === null) {
        return [
            'klasifikasi' => 'Belum dapat diklasifikasikan',
            'dasar' => ['Z-score BB/PB atau BB/TB belum dapat dihitung.'],
        ];
    }

    // Bayi umur 2 sampai <6 bulan.
    if ($umur < 6) {
        if ($z < -3) {
            $dasar[] = 'Skor Z BB/PB < -3 SD';
        }

        if ($edema !== '0') {
            $dasar[] = 'Ada edema bilateral pitting';
        }

        if ($lemahMenyusu) {
            $dasar[] = 'Terlalu lemah untuk menyusu';
        }

        if ($bbTidakNaik) {
            $dasar[] = 'Berat badan tidak naik atau turun';
        }

        if ($komplikasi) {
            $dasar[] = 'Terdapat komplikasi medis';
        }

        if (count($dasar) > 0) {
            return [
                'klasifikasi' => 'GIZI BURUK DENGAN KOMPLIKASI',
                'dasar' => $dasar,
            ];
        }

        if ($z >= -3 && $z < -2) {
            return [
                'klasifikasi' => 'GIZI KURANG',
                'dasar' => ['Skor Z BB/PB -3 SD sampai < -2 SD'],
            ];
        }

        if ($z >= -2 && $z <= 1) {
            return [
                'klasifikasi' => 'GIZI BAIK',
                'dasar' => ['Skor Z BB/PB -2 SD sampai +1 SD'],
            ];
        }

        if ($z > 3) {
            return [
                'klasifikasi' => 'OBESITAS',
                'dasar' => ['Skor Z BB/PB > +3 SD'],
            ];
        }

        if ($z > 2) {
            return [
                'klasifikasi' => 'GIZI LEBIH',
                'dasar' => ['Skor Z BB/PB > +2 SD sampai +3 SD'],
            ];
        }

        if ($z > 1) {
            return [
                'klasifikasi' => 'BERISIKO GIZI LEBIH',
                'dasar' => ['Skor Z BB/PB > +1 SD sampai +2 SD'],
            ];
        }
    }

    // Klasifikasi gizi lebih dan obesitas ditentukan dari Z-score.
    // LiLA tetap wajib untuk menilai gizi buruk, gizi kurang, dan gizi baik umur 6–59 bulan.
    if ($z > 3) {
        return [
            'klasifikasi' => 'OBESITAS',
            'dasar' => ['Skor Z BB/PB atau BB/TB > +3 SD'],
        ];
    }

    if ($z > 2) {
        return [
            'klasifikasi' => 'GIZI LEBIH',
            'dasar' => ['Skor Z BB/PB atau BB/TB > +2 SD sampai +3 SD'],
        ];
    }

    if ($z > 1) {
        return [
            'klasifikasi' => 'BERISIKO GIZI LEBIH',
            'dasar' => ['Skor Z BB/PB atau BB/TB > +1 SD sampai +2 SD'],
        ];
    }

    // Umur 6–59 bulan: LiLA wajib dipakai untuk klasifikasi gizi kurang/baik/buruk.
    if ($lila === null || $lila <= 0) {
        return [
            'klasifikasi' => 'Belum dapat diklasifikasikan',
            'dasar' => ['LiLA pada Objektif belum tersedia untuk anak umur 6–59 bulan.'],
        ];
    }

    $indikatorGiziBuruk =
        $z < -3
        || $lila < 11.5
        || in_array($edema, ['+1', '+2', '+3'], true);

    if ($edema === '+3') {
        return [
            'klasifikasi' => 'GIZI BURUK DENGAN KOMPLIKASI',
            'dasar' => ['Edema pada seluruh tubuh (derajat +3)'],
        ];
    }

    if ($bb < 4) {
        return [
            'klasifikasi' => 'GIZI BURUK DENGAN KOMPLIKASI',
            'dasar' => ['Berat badan < 4 kg pada umur 6–59 bulan'],
        ];
    }

    if ($indikatorGiziBuruk && $komplikasi) {
        if ($z < -3) {
            $dasar[] = 'Skor Z BB/PB atau BB/TB < -3 SD';
        }

        if ($lila < 11.5) {
            $dasar[] = 'LiLA < 11,5 cm';
        }

        if (in_array($edema, ['+1', '+2'], true)) {
            $dasar[] = "Edema bilateral pitting derajat {$edema}";
        }

        $dasar[] = 'Disertai komplikasi medis';

        return [
            'klasifikasi' => 'GIZI BURUK DENGAN KOMPLIKASI',
            'dasar' => $dasar,
        ];
    }

    if ($indikatorGiziBuruk) {
        if ($z < -3) {
            $dasar[] = 'Skor Z BB/PB atau BB/TB < -3 SD';
        }

        if ($lila < 11.5) {
            $dasar[] = 'LiLA < 11,5 cm';
        }

        if (in_array($edema, ['+1', '+2'], true)) {
            $dasar[] = "Edema minimal derajat {$edema}";
        }

        return [
            'klasifikasi' => 'GIZI BURUK TANPA KOMPLIKASI',
            'dasar' => $dasar,
        ];
    }

    if (($z >= -3 && $z < -2) || ($lila >= 11.5 && $lila < 12.5)) {
        if ($z >= -3 && $z < -2) {
            $dasar[] = 'Skor Z BB/PB atau BB/TB -3 SD sampai < -2 SD';
        }

        if ($lila >= 11.5 && $lila < 12.5) {
            $dasar[] = 'LiLA 11,5 cm sampai < 12,5 cm';
        }

        return [
            'klasifikasi' => 'GIZI KURANG',
            'dasar' => $dasar,
        ];
    }

    // Bagan memakai hubungan DAN untuk Gizi Baik umur 6–59 bulan.
    if ($z >= -2 && $z <= 1 && $lila >= 12.5) {
        return [
            'klasifikasi' => 'GIZI BAIK',
            'dasar' => [
                'Skor Z BB/PB atau BB/TB -2 SD sampai +1 SD',
                'LiLA ≥ 12,5 cm',
            ],
        ];
    }

    return [
        'klasifikasi' => 'Belum dapat diklasifikasikan',
        'dasar' => ['Kombinasi indikator belum memenuhi klasifikasi pada bagan MTBS.'],
    ];
}

private function rekomendasiGizi(
    string $klasifikasi,
    array $data = []
): array {
    $umur = isset($data['umur_bulan']) && $data['umur_bulan'] !== null
        ? (int) $data['umur_bulan']
        : null;

    $syok = !empty($data['syok']);
    $diare = !empty($data['diare']);
    $vitaminA = $this->dosisVitaminAGizi($umur);

    $tindakan = [];
    $pengobatan = [];

    if ($klasifikasi === 'GIZI BURUK DENGAN KOMPLIKASI') {
        $tindakan = [
            'Cegah agar gula darah tidak turun',
            'Nasihati cara menjaga anak tetap hangat selama perjalanan',
        ];

        if ($syok) {
            $tindakan[] = 'Jika disertai syok, berikan glukosa 10% dan cairan infus pra-rujukan sesuai pedoman';
        }

        if ($diare) {
            $tindakan[] = 'Jika disertai diare, berikan cairan ReSoMal atau modifikasinya 5 ml/kgBB sebelum dirujuk';
        }

        $tindakan[] = 'RUJUK SEGERA';

        $pengobatan = [
            'Ampisilin dosis pertama 50 mg/kgBB secara IM/IV',
            'Gentamisin dosis pertama 7,5 mg/kgBB secara IM/IV',
            "Vitamin A dosis pertama {$vitaminA}",
        ];
    } elseif ($klasifikasi === 'GIZI BURUK TANPA KOMPLIKASI') {
        $tindakan = [
            'Cegah agar gula darah tidak turun',
            'Nasihati cara menjaga anak tetap hangat selama perjalanan',
            'Lakukan skrining perkembangan sesuai SDIDTK',
        ];

        if ($diare) {
            $tindakan[] = 'Jika disertai diare, berikan cairan ReSoMal atau modifikasinya';
        }

        $tindakan = array_merge($tindakan, [
            'Kunjungan ulang 7 hari',
            'Nasihati kapan harus kembali segera',
            'RUJUK ke dokter untuk penanganan gizi buruk dan kemungkinan penyakit penyerta seperti TB atau HIV',
        ]);

        $pengobatan = [
            'Amoksisilin 15 mg/kgBB setiap 8 jam selama 5 hari',
            "Vitamin A dosis pertama {$vitaminA}",
        ];
    } elseif ($klasifikasi === 'GIZI KURANG') {
        $tindakan = [
            'Nilai pemberian makan anak; bila ada masalah, lakukan konseling dan kunjungan ulang 7 hari',
            'Lakukan skrining perkembangan sesuai SDIDTK',
            'Kunjungan ulang 14 hari',
            'Nasihati kapan harus kembali segera',
            'RUJUK ke dokter untuk melacak kemungkinan penyakit penyerta seperti TB atau HIV',
        ];
    } elseif ($klasifikasi === 'GIZI BAIK') {
        $tindakan = [
            'Jika anak berumur < 2 tahun, nilai pemberian makan; bila ada masalah, kunjungan ulang 7 hari',
            'Timbang berat badan setiap bulan',
            'Pujilah ibu dan anjurkan melanjutkan pemberian makan sesuai umur',
        ];
    } elseif ($klasifikasi === 'BERISIKO GIZI LEBIH') {
        $tindakan = [
            'Plot IMT/U untuk menegakkan diagnosis',
            'Lakukan konseling gizi untuk menentukan penyebab',
            'Kunjungan ulang 14 hari; bila tidak membaik, RUJUK',
            'Nasihati kapan harus kembali segera',
        ];
    } elseif ($klasifikasi === 'GIZI LEBIH') {
        $tindakan = [
            'Lakukan konseling gizi dan aktivitas anak bersama petugas gizi',
            'Kunjungan ulang 14 hari; bila tidak membaik, RUJUK',
            'Nasihati kapan harus kembali segera',
        ];
    } elseif ($klasifikasi === 'OBESITAS') {
        $tindakan = [
            'RUJUK ke rumah sakit untuk penanganan lebih lanjut',
        ];
    }

    return [
        'tindakan' => $tindakan,
        'pengobatan' => $pengobatan,
    ];
}

private function dosisVitaminAGizi(?int $umurBulan): string
{
    if ($umurBulan === null) {
        return 'sesuai umur';
    }

    if ($umurBulan < 6) {
        return '50.000 IU';
    }

    if ($umurBulan < 12) {
        return '100.000 IU';
    }

    return '200.000 IU';
}


/**
 * Hapus data inti MTBS untuk kebutuhan pengujian pada kunjungan yang sama.
 *
 * Data yang dihapus:
 * - mtbs_assessment
 * - mtbs_gizi
 * - mtbs_objektif
 * - mtbs_subjektif
 *
 * Planning, status pasien, diagnosa medis, alergi, imunisasi, rujukan,
 * dan data loket/pelayanan tidak disentuh.
 */
public function hapusDataTesting(string $kunjunganId)
{
    $kunjunganId = trim($kunjunganId);

    if ($kunjunganId === '') {
        return response()->json([
            'message' => 'ID kunjungan wajib diisi.',
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Hapus data turunan lebih dahulu, kemudian data sumber.
        $deleted = [
            'assessment' => DB::table('mtbs_assessment')
                ->where('kunjungan_id', $kunjunganId)
                ->delete(),

            'gizi' => DB::table('mtbs_gizi')
                ->where('kunjungan_id', $kunjunganId)
                ->delete(),

            'objektif' => DB::table('mtbs_objektif')
                ->where('kunjungan_id', $kunjunganId)
                ->delete(),

            'subjektif' => DB::table('mtbs_subjektif')
                ->where('kunjungan_id', $kunjunganId)
                ->delete(),
        ];

        DB::commit();

        Log::warning('Data inti MTBS dihapus untuk testing', [
            'kunjungan_id' => $kunjunganId,
            'deleted' => $deleted,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Data Subjektif, Objektif, Assessment, dan Gizi MTBS berhasil dihapus.',
            'data' => [
                'kunjungan_id' => $kunjunganId,
                'deleted' => $deleted,
                'total_deleted' => array_sum($deleted),
            ],
        ], 200);
    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('MTBS hapusDataTesting error', [
            'kunjungan_id' => $kunjunganId,
            'message' => $e->getMessage(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Gagal menghapus data testing MTBS.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
