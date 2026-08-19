<?php

namespace App\Http\Controllers\RuangLayanan\MTBS;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MTBSRujukanController extends Controller
{
    private string $table = 'mtbs_rujukan';

public function index(Request $request)
{
    $kunjunganId = $request->get('kunjungan_id');

    $rows = DB::table($this->table . ' as sr')
        ->leftJoin('simpus_pelayanan as pel', 'sr.kunjungan_id', '=', 'pel.idpelayanan')
        ->leftJoin('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
        ->leftJoin('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
        ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
        ->when($kunjunganId, function ($q) use ($kunjunganId) {
            $q->where('sr.kunjungan_id', $kunjunganId);
        })
        ->select(
            'sr.id',
            'sr.kunjungan_id',
            'sr.no_surat',
            'sr.tanggal_rujuk',
            'sr.rumah_sakit',
            'sr.poli',
            'sr.dokter_jaga',
            'p.ID as pasien_id',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'poli.nmPoli',
            'l.kdPoli'
        )
        ->orderByDesc('sr.id')
        ->get();

    $pasien = null;

    if ($kunjunganId) {
        $pasien = $this->getDataPasien($kunjunganId);
    }

    return Inertia::render('Ruang_Layanan/KIA/MTBS/Rujukan/List', [
        'rows' => $rows,
        'idPelayanan' => $kunjunganId,
        'idPoli' => $pasien->kdPoli ?? null,
        'pasienId' => $pasien->ID ?? null,
    ]);
}

    public function create($idPelayanan)
    {
        $pasien = $this->getDataPasien($idPelayanan);

        if (!$pasien) {
            abort(404, 'Data pasien tidak ditemukan');
        }

        $latestStatus = DB::table('mtbs_statuspasien')
            ->where('kunjungan_id', $idPelayanan)
            ->orderByDesc('id')
            ->first();

        $latestAssessment = DB::table('mtbs_assessment')
            ->where('kunjungan_id', $idPelayanan)
            ->orderByDesc('id')
            ->first();

        $latestObjektif = DB::table('mtbs_objektif')
            ->where('kunjungan_id', $idPelayanan)
            ->orderByDesc('id')
            ->first();

        $latestSubjektif = DB::table('mtbs_subjektif')
            ->where('kunjungan_id', $idPelayanan)
            ->orderByDesc('id')
            ->first();

        $existing = DB::table($this->table)
            ->where('kunjungan_id', $idPelayanan)
            ->orderByDesc('id')
            ->first();

        return Inertia::render('Ruang_Layanan/KIA/MTBS/Rujukan/Form', [
            'pasien' => $pasien,
            'existing' => $existing,
            'defaultForm' => [
                'kunjungan_id' => $idPelayanan,
                'tanggal_rujuk' => now()->toDateString(),
                'no_surat' => $existing->no_surat ?? $this->generateNoSurat(),
                'rumah_sakit' => $existing->rumah_sakit ?? ($latestStatus->ppk_rujukan ?? ''),
                'poli' => $existing->poli ?? ($latestStatus->nama_poli ?? ''),
                'dokter_jaga' => $existing->dokter_jaga ?? ($latestStatus->nama_dokter ?? ''),
                'no_telp_hp' => $existing->no_telp_hp ?? '',
                'anamnesa' => $existing->anamnesa ?? $this->buildAnamnesa($latestSubjektif),
                'pemeriksaan_fisik' => $existing->pemeriksaan_fisik ?? $this->buildPemeriksaanFisik($latestObjektif),
                'diagnosa_sementara' => $existing->diagnosa_sementara ?? $this->buildDiagnosa($latestAssessment),
                'catatan' => $existing->catatan ?? ($latestStatus->catatan ?? ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
            'tanggal_rujuk' => $request->tanggal_rujuk === '' ? null : $request->tanggal_rujuk,
            'rumah_sakit' => $request->rumah_sakit === '' ? null : $request->rumah_sakit,
            'poli' => $request->poli === '' ? null : $request->poli,
            'dokter_jaga' => $request->dokter_jaga === '' ? null : $request->dokter_jaga,
        ]);

        validator($request->all(), [
            'kunjungan_id' => ['required', 'string', 'max:100'],
            'tanggal_rujuk' => ['required', 'date'],
            'no_surat' => ['nullable', 'string', 'max:100'],
            'rumah_sakit' => ['required', 'string', 'max:150'],
            'poli' => ['nullable', 'string', 'max:150'],
            'dokter_jaga' => ['nullable', 'string', 'max:150'],
            'no_telp_hp' => ['nullable', 'string', 'max:50'],
            'anamnesa' => ['nullable', 'string'],
            'pemeriksaan_fisik' => ['nullable', 'string'],
            'diagnosa_sementara' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ])->validate();

        DB::beginTransaction();

        try {
            $pasien = $this->getDataPasien($request->kunjungan_id);

            if (!$pasien) {
                return response()->json(['message' => 'Data pasien tidak ditemukan'], 404);
            }

            $user = Auth::user();
            $createdBy = $user
                ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas')
                : 'Petugas';

            DB::table($this->table)->updateOrInsert(
                ['kunjungan_id' => $request->kunjungan_id],
                [
                    'pasien_id' => $pasien->ID ?? null,
                    'no_surat' => $request->no_surat ?: $this->generateNoSurat(),
                    'tanggal_rujuk' => $request->tanggal_rujuk,
                    'rumah_sakit' => $request->rumah_sakit,
                    'poli' => $request->poli,
                    'dokter_jaga' => $request->dokter_jaga,
                    'no_telp_hp' => $request->no_telp_hp,
                    'anamnesa' => $request->anamnesa,
                    'pemeriksaan_fisik' => $request->pemeriksaan_fisik,
                    'diagnosa_sementara' => $request->diagnosa_sementara,
                    'catatan' => $request->catatan,
                    'created_by' => $createdBy,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $surat = DB::table($this->table)
                ->where('kunjungan_id', $request->kunjungan_id)
                ->orderByDesc('id')
                ->first();

            DB::commit();

            return response()->json([
                'message' => 'Surat rujukan berhasil disimpan',
                'id' => $surat->id ?? null,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MTBSRujukan store error', [
                'msg' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan surat rujukan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cetak($id)
    {
        $surat = DB::table($this->table . ' as sr')
            ->leftJoin('simpus_pelayanan as pel', 'sr.kunjungan_id', '=', 'pel.idpelayanan')
            ->leftJoin('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->leftJoin('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
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
            ->select(
                'sr.*',
                'p.ID',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.JENIS_KLMIN',
                'p.TGL_LHR',
                'p.alamat',
                'kel.nama_kel',
                'kec.nama_kec',
                'poli.nmPoli',
                'l.tglKunjungan'
            )
            ->where('sr.id', $id)
            ->first();

        if (!$surat) {
            abort(404, 'Surat rujukan tidak ditemukan');
        }

        $surat->umur_label = $this->hitungUmurLabel($surat->TGL_LHR, $surat->tglKunjungan);
        $surat->jenis_kelamin_label = $surat->JENIS_KLMIN == 1 ? 'Laki-laki' : 'Perempuan';

        return Inertia::render('Ruang_Layanan/KIA/MTBS/Rujukan/Cetak', [
            'surat' => $surat,
        ]);
    }

    private function getDataPasien($idPelayanan)
    {
        return DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
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
            ->where('pel.idpelayanan', $idPelayanan)
            ->select(
                'p.ID',
                'pel.idpelayanan',
                'pel.tglPelayanan',
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.JENIS_KLMIN',
                'p.TGL_LHR',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'l.tglKunjungan',
                'l.kdPoli'
            )
            ->first();
    }

    private function generateNoSurat()
    {
        $count = DB::table($this->table)
            ->whereYear('created_at', now()->year)
            ->count() + 1;

        return str_pad($count, 3, '0', STR_PAD_LEFT)
            . '/MTBS-RJ/'
            . now()->format('m')
            . '/'
            . now()->format('Y');
    }

    private function buildAnamnesa($sub)
    {
        if (!$sub) return '';

        $keluhan = json_decode($sub->keluhan_utama ?? '[]', true) ?: [];

        if (!empty($sub->keluhan_lain)) {
            $keluhan[] = $sub->keluhan_lain;
        }

        return implode(', ', $keluhan);
    }

    private function buildPemeriksaanFisik($obj)
    {
        if (!$obj) return '';

        $parts = [];

        if ($obj->rr) $parts[] = 'RR: ' . $obj->rr;
        if ($obj->suhu) $parts[] = 'Suhu: ' . $obj->suhu;
        if ($obj->spo2) $parts[] = 'SpO2: ' . $obj->spo2;
        if ($obj->bb) $parts[] = 'BB: ' . $obj->bb;
        if ($obj->tb) $parts[] = 'TB/PB: ' . $obj->tb;
        if ($obj->lila) $parts[] = 'LiLA: ' . $obj->lila;
        if ($obj->lk) $parts[] = 'LK: ' . $obj->lk;

        return implode(', ', $parts);
    }

    private function buildDiagnosa($ass)
    {
        if (!$ass) return '';

        $diagnosa = json_decode($ass->klasifikasi_global ?? '[]', true) ?: [];

        return implode(', ', $diagnosa);
    }

    private function hitungUmurLabel($tglLahir, $tglKunjungan = null)
    {
        if (!$tglLahir) return '-';

        $lahir = Carbon::parse($tglLahir);
        $tanggal = Carbon::parse($tglKunjungan ?? now());
        $diff = $lahir->diff($tanggal);

        return "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
    }
}