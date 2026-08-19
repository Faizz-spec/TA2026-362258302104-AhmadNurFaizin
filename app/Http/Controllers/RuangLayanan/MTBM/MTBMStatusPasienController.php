<?php

namespace App\Http\Controllers\RuangLayanan\MTBM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MTBMStatusPasienController extends Controller
{
    /**
     * Mengambil riwayat status pasien MTBM.
     */
    public function index(Request $request)
    {
        validator($request->all(), [
            'kunjungan_id' => ['required', 'string', 'max:100'],
        ])->validate();

        $rows = DB::table('mtbm_statuspasien')
            ->where('kunjungan_id', $request->kunjungan_id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'asalPoli' => $row->asal_poli ?? 'MTBM',
                    'statusPulang' => $row->status_pulang,
                    'keterangan' => $this->buildKeterangan($row),

                    'poliTujuan' => $row->poli_internal_tujuan
                        ?? $row->nama_poli
                        ?? '-',

                    'tenagaMedis' => $row->tenaga_medis,
                    'createdBy' => $row->created_by,
                    'mulai' => $row->mulai_melayani,
                    'selesai' => $row->selesai_melayani,
                ];
            });

        return response()->json([
            'data' => $rows,
        ], 200);
    }

    /**
     * Mengambil opsi status pulang, ruang layanan, dan tenaga medis.
     */
    public function options()
    {
        $user = Auth::user();
        $unitId = $user?->unit;

        /*
        |--------------------------------------------------------------------------
        | Informasi unit Puskesmas pengguna
        |--------------------------------------------------------------------------
        */
        $unit = null;

        if ($unitId) {
            $unit = DB::table('unit_profiles')
                ->where('unit_id', $unitId)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Ruang layanan untuk rujuk internal
        |--------------------------------------------------------------------------
        */
        $ruangLayanan = DB::table('master_ruang_layanan')
            ->select(
                'id_ruang_layanan as id',
                'name',
                'description',
                'aktif'
            )
            ->where('aktif', '1')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Status pulang dari database
        |--------------------------------------------------------------------------
        */
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
                $namaStatus = strtolower(
                    trim((string) ($item->nama ?? ''))
                );

                $jenisPuskesmas = strtolower(
                    trim((string) ($unit->pusk_rawat ?? ''))
                );

                /*
                 * Status rawat inap hanya muncul apabila unit merupakan
                 * Puskesmas rawat inap.
                 */
                if (str_contains($namaStatus, 'rawat inap')) {
                    return str_contains($jenisPuskesmas, 'rawat inap');
                }

                return true;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Tenaga medis dari master_dokter
        |--------------------------------------------------------------------------
        | Tidak lagi menggunakan Practitioner 1, 2, 3, dan 4.
        */
        $dokter = DB::table('master_dokter')
            ->select(
                'idDokter as id',
                'nmDokter as nama',
                'kdDokter as kode',
                'ihs_nakes'
            )
            ->where('aktif', '1')
            ->whereNotNull('nmDokter')
            ->when($unitId, function ($query) use ($unitId) {
                $query->where(function ($subQuery) use ($unitId) {
                    $subQuery
                        ->where('pusk_id', $unitId)
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

    /**
     * Menyimpan status pasien MTBM.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Ubah string kosong menjadi null
        |--------------------------------------------------------------------------
        */
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === ''
                ? null
                : $request->kunjungan_id,

            'statusPulang' => $request->statusPulang === ''
                ? null
                : $request->statusPulang,

            'poliInternal' => $request->poliInternal === ''
                ? null
                : $request->poliInternal,

            'tenagaMedis' => $request->tenagaMedis === ''
                ? null
                : $request->tenagaMedis,

            'ppkRujukan' => $request->ppkRujukan === ''
                ? null
                : $request->ppkRujukan,

            'namaPoli' => $request->namaPoli === ''
                ? null
                : $request->namaPoli,

            'namaDokter' => $request->namaDokter === ''
                ? null
                : $request->namaDokter,

            'spesialis' => $request->spesialis === ''
                ? null
                : $request->spesialis,

            'catatan' => $request->catatan === ''
                ? null
                : $request->catatan,

            'tglRencanaBerkunjung' =>
                $request->tglRencanaBerkunjung === ''
                    ? null
                    : $request->tglRencanaBerkunjung,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */
        validator($request->all(), [
            'kunjungan_id' => [
                'required',
                'string',
                'max:100',
            ],

            'statusPulang' => [
                'required',
                'string',
                'max:100',
            ],

            'tenagaMedis' => [
                'nullable',
                'string',
                'max:150',
            ],

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

            'namaPoli' => [
                'nullable',
                'string',
                'max:150',
            ],

            'namaDokter' => [
                'nullable',
                'string',
                'max:150',
            ],

            'spesialis' => [
                'nullable',
                'string',
                'max:150',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'tglRencanaBerkunjung' => [
                'nullable',
                'date',
            ],
        ], [
            'poliInternal.required_if' =>
                'Poli / ruang tujuan internal wajib dipilih.',

            'ppkRujukan.required_if' =>
                'PPK rujukan wajib diisi.',
        ])->validate();

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $createdBy = $user
                ? (
                    $user->name
                    ?? $user->username
                    ?? $user->email
                    ?? 'Petugas'
                )
                : 'Petugas';

            $waktuPelayanan = now();

            /*
            |--------------------------------------------------------------------------
            | Simpan status pasien MTBM
            |--------------------------------------------------------------------------
            */
            DB::table('mtbm_statuspasien')->insert([
                'kunjungan_id' => $request->kunjungan_id,
                'asal_poli' => 'MTBM',

                'status_pulang' => $request->statusPulang,

                'poli_internal_tujuan' =>
                    $request->statusPulang === 'Rujuk Internal'
                        ? $request->poliInternal
                        : null,

                /*
                 * Nilai ini berasal dari master_dokter.nmDokter.
                 */
                'tenaga_medis' => $request->tenagaMedis,

                'ppk_rujukan' => $request->ppkRujukan,
                'nama_poli' => $request->namaPoli,
                'nama_dokter' => $request->namaDokter,
                'spesialis' => $request->spesialis,
                'catatan' => $request->catatan,

                'tgl_rencana_berkunjung' =>
                    $request->tglRencanaBerkunjung,

                'mulai_melayani' => $waktuPelayanan,
                'selesai_melayani' => $waktuPelayanan,

                'created_by' => $createdBy,
                'created_at' => $waktuPelayanan,
                'updated_at' => $waktuPelayanan,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tandai pelayanan selesai
            |--------------------------------------------------------------------------
            */
            $updatedPelayanan = DB::table('simpus_pelayanan')
                ->where(
                    'idpelayanan',
                    $request->kunjungan_id
                )
                ->update([
                    'sudahDilayani' => 1,
                    'tglPelayanan' => $waktuPelayanan,
                ]);

            DB::commit();

            Log::info('MTBM status pasien selesai pelayanan', [
                'kunjungan_id' => $request->kunjungan_id,
                'status_pulang' => $request->statusPulang,
                'tenaga_medis' => $request->tenagaMedis,
                'updated_pelayanan' => $updatedPelayanan,
            ]);

            return response()->json([
                'message' => 'Status pasien MTBM berhasil disimpan',
                'updatedPelayanan' => $updatedPelayanan,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MTBMStatusPasien store error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan status pasien MTBM',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membentuk keterangan yang ditampilkan pada tabel riwayat.
     */
    private function buildKeterangan($row): string
    {
        if ($row->status_pulang === 'Rujuk Internal') {
            return 'Rujuk Internal ke '
                . ($row->poli_internal_tujuan ?? '-');
        }

        if (
            $row->status_pulang === 'Rujuk Vertikal PCare'
            || $row->status_pulang === 'Rujuk Rumah Sakit Bukan BPJS'
            || $row->status_pulang === 'Rujuk Rumah Sakit'
        ) {
            $parts = [];

            if (!empty($row->ppk_rujukan)) {
                $parts[] = 'PPK: ' . $row->ppk_rujukan;
            }

            if (!empty($row->nama_poli)) {
                $parts[] = 'Poli: ' . $row->nama_poli;
            }

            if (!empty($row->nama_dokter)) {
                $parts[] = 'Dokter: ' . $row->nama_dokter;
            }

            if (!empty($row->spesialis)) {
                $parts[] = 'Spesialis: ' . $row->spesialis;
            }

            if (!empty($row->tgl_rencana_berkunjung)) {
                $parts[] = 'Rencana: '
                    . $row->tgl_rencana_berkunjung;
            }

            if (!empty($row->catatan)) {
                $parts[] = 'Catatan: ' . $row->catatan;
            }

            return count($parts)
                ? implode(' | ', $parts)
                : ($row->status_pulang ?? '-');
        }

        return $row->status_pulang ?? '-';
    }
}