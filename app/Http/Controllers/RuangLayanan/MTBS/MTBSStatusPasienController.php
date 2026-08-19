<?php

namespace App\Http\Controllers\RuangLayanan\MTBS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MTBSStatusPasienController extends Controller
{
    public function index(Request $request)
    {
        validator($request->all(), [
            'kunjungan_id' => ['required', 'string', 'max:100'],
        ])->validate();

        $rows = DB::table('mtbs_statuspasien')
            ->where('kunjungan_id', $request->kunjungan_id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'asalPoli' => $r->asal_poli,
                    'statusPulang' => $r->status_pulang,
                    'keterangan' => $this->buildKeterangan($r),
                    'poliTujuan' => $r->poli_internal_tujuan ?? $r->nama_poli ?? '-',
                    'tenagaMedis' => $r->tenaga_medis,
                    'createdBy' => $r->created_by,
                    'mulai' => $r->mulai_melayani,
                    'selesai' => $r->selesai_melayani,
                ];
            });

        return response()->json([
            'data' => $rows,
        ], 200);
    }

public function options()
{
    $user = Auth::user();
    $unitId = $user->unit ?? null;

    $unit = DB::table('unit_profiles')
        ->where('unit_id', $unitId)
        ->first();

    $ruangLayanan = DB::table('master_ruang_layanan')
        ->select(
            'id_ruang_layanan as id',
            'name',
            'description',
            'aktif'
        )
        ->where('aktif', '1')
        ->orderBy('id_ruang_layanan')
        ->get();

    $statusPulang = DB::table('simpus_statuspulang')
        ->select(
            'kdStatusPulang as kode',
            'nmStatusPulang as nama',
            'rawatInap'
        )
        ->whereNotNull('nmStatusPulang')
        ->orderByRaw('CAST(kdStatusPulang AS UNSIGNED)')
        ->get()
        ->filter(function ($item) use ($unit) {
            $nama = strtolower($item->nama ?? '');
            $puskRawat = strtolower($unit->pusk_rawat ?? '');

            if (str_contains($nama, 'rawat inap')) {
                return str_contains($puskRawat, 'rawat inap');
            }

            return true;
        })
        ->values();

    $dokter = DB::table('master_dokter')
        ->select(
            'idDokter as id',
            'nmDokter as nama',
            'kdDokter as kode',
            'ihs_nakes'
        )
        ->where('aktif', '1')
        ->whereNotNull('nmDokter')
        ->when($unitId, function ($q) use ($unitId) {
            $q->where(function ($qq) use ($unitId) {
                $qq->where('pusk_id', $unitId)
                   ->orWhereNull('pusk_id');
            });
        })
        ->orderBy('nmDokter')
        ->get();

    return response()->json([
        'user_unit_id' => $unitId,
        'unit' => $unit,
        'ruang_layanan' => $ruangLayanan,
        'status_pulang' => $statusPulang,
        'dokter' => $dokter,
    ], 200);
}

    public function store(Request $request)
    {
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
            'statusPulang' => $request->statusPulang === '' ? null : $request->statusPulang,
            'poliInternal' => $request->poliInternal === '' ? null : $request->poliInternal,
            'tenagaMedis' => $request->tenagaMedis === '' ? null : $request->tenagaMedis,
            'ppkRujukan' => $request->ppkRujukan === '' ? null : $request->ppkRujukan,
            'namaPoli' => $request->namaPoli === '' ? null : $request->namaPoli,
            'namaDokter' => $request->namaDokter === '' ? null : $request->namaDokter,
            'spesialis' => $request->spesialis === '' ? null : $request->spesialis,
            'catatan' => $request->catatan === '' ? null : $request->catatan,
            'tglRencanaBerkunjung' => $request->tglRencanaBerkunjung === '' ? null : $request->tglRencanaBerkunjung,
        ]);

        validator($request->all(), [
            'kunjungan_id' => ['required', 'string', 'max:100'],
            'statusPulang' => ['required', 'string', 'max:100'],
            'tenagaMedis' => ['nullable', 'string', 'max:100'],

            'poliInternal' => [
                'nullable',
                'string',
                'max:150',
                'required_if:statusPulang,Rujuk Internal',
            ],

            'ppkRujukan' => [
                'nullable',
                'string',
                'max:150',
                'required_if:statusPulang,Rujuk Vertikal PCare,Rujuk Rumah Sakit Bukan BPJS,Rujuk Rumah Sakit',
            ],

            'namaPoli' => ['nullable', 'string', 'max:150'],
            'namaDokter' => ['nullable', 'string', 'max:150'],
            'spesialis' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string'],
            'tglRencanaBerkunjung' => ['nullable', 'date'],
        ], [
            'poliInternal.required_if' => 'Poli / ruang tujuan internal wajib dipilih.',
            'ppkRujukan.required_if' => 'PPK rujukan wajib diisi.',
        ])->validate();

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $createdBy = $user
                ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas')
                : 'Petugas';

            DB::table('mtbs_statuspasien')->insert([
                'kunjungan_id' => $request->kunjungan_id,
                'asal_poli' => 'MTBS',

                'status_pulang' => $request->statusPulang,
                'poli_internal_tujuan' => $request->statusPulang === 'Rujuk Internal'
                    ? $request->poliInternal
                    : null,

                'tenaga_medis' => $request->tenagaMedis,

                'ppk_rujukan' => $request->ppkRujukan,
                'nama_poli' => $request->namaPoli,
                'nama_dokter' => $request->namaDokter,
                'spesialis' => $request->spesialis,
                'catatan' => $request->catatan,
                'tgl_rencana_berkunjung' => $request->tglRencanaBerkunjung,

                'mulai_melayani' => now(),
                'selesai_melayani' => now(),

                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $updatedPelayanan = DB::table('simpus_pelayanan')
                ->where('idpelayanan', $request->kunjungan_id)
                ->update([
                    'sudahDilayani' => 1,
                    'tglPelayanan' => now(),
                ]);

            Log::info('MTBS STATUS PASIEN selesai pelayanan', [
                'kunjungan_id' => $request->kunjungan_id,
                'status_pulang' => $request->statusPulang,
                'updatedPelayanan' => $updatedPelayanan,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Status pasien berhasil disimpan',
                'updatedPelayanan' => $updatedPelayanan,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MTBSStatusPasien store error', [
                'msg' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan status pasien',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildKeterangan($r)
    {
        if ($r->status_pulang === 'Rujuk Internal') {
            return 'Rujuk Internal ke ' . ($r->poli_internal_tujuan ?? '-');
        }

        if (
            $r->status_pulang === 'Rujuk Vertikal PCare' ||
            $r->status_pulang === 'Rujuk Rumah Sakit Bukan BPJS' ||
            $r->status_pulang === 'Rujuk Rumah Sakit'
        ) {
            $parts = [];

            if ($r->ppk_rujukan) $parts[] = 'PPK: ' . $r->ppk_rujukan;
            if ($r->nama_poli) $parts[] = 'Poli: ' . $r->nama_poli;
            if ($r->nama_dokter) $parts[] = 'Dokter: ' . $r->nama_dokter;
            if ($r->spesialis) $parts[] = 'Spesialis: ' . $r->spesialis;
            if ($r->tgl_rencana_berkunjung) $parts[] = 'Rencana: ' . $r->tgl_rencana_berkunjung;
            if ($r->catatan) $parts[] = 'Catatan: ' . $r->catatan;

            return count($parts) ? implode(' | ', $parts) : $r->status_pulang;
        }

        return $r->status_pulang;
    }
}