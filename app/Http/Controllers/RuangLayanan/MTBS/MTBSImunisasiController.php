<?php

namespace App\Http\Controllers\RuangLayanan\MTBS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MTBSImunisasiController extends Controller
{
    private const SUMBER_VERIFIKASI = [
        'buku_kia',
        'data_posyandu',
        'pengakuan_orang_tua',
        'tidak_ada_bukti',
        'lainnya',
    ];

    private const KONDISI_ANAK = [
        'sehat_sakit_ringan',
        'belum_stabil',
        'rujuk_segera',
    ];

    /**
     * Jadwal sesuai Buku Bagan MTBS 2022 halaman pemeriksaan status imunisasi.
     * PCV dan JE dihitung bila program wilayah diaktifkan.
     */
    private const JADWAL_IMUNISASI = [
        [
            'code' => 'HB0',
            'label' => 'HB-0',
            'jadwal' => '0–24 jam',
            'umur_bulan' => 0,
            'program_wilayah' => null,
        ],
        [
            'code' => 'BCG',
            'label' => 'BCG',
            'jadwal' => '1 bulan',
            'umur_bulan' => 1,
            'program_wilayah' => null,
        ],
        [
            'code' => 'OPV0',
            'label' => 'OPV 0',
            'jadwal' => '1 bulan',
            'umur_bulan' => 1,
            'program_wilayah' => null,
        ],
        [
            'code' => 'DPT_HB_HIB_1',
            'label' => 'DPT-HB-Hib 1',
            'jadwal' => '2 bulan',
            'umur_bulan' => 2,
            'program_wilayah' => null,
        ],
        [
            'code' => 'OPV1',
            'label' => 'OPV 1',
            'jadwal' => '2 bulan',
            'umur_bulan' => 2,
            'program_wilayah' => null,
        ],
        [
            'code' => 'PCV1',
            'label' => 'PCV 1',
            'jadwal' => '2 bulan',
            'umur_bulan' => 2,
            'program_wilayah' => 'pcv',
        ],
        [
            'code' => 'DPT_HB_HIB_2',
            'label' => 'DPT-HB-Hib 2',
            'jadwal' => '3 bulan',
            'umur_bulan' => 3,
            'program_wilayah' => null,
        ],
        [
            'code' => 'OPV2',
            'label' => 'OPV 2',
            'jadwal' => '3 bulan',
            'umur_bulan' => 3,
            'program_wilayah' => null,
        ],
        [
            'code' => 'PCV2',
            'label' => 'PCV 2',
            'jadwal' => '3 bulan',
            'umur_bulan' => 3,
            'program_wilayah' => 'pcv',
        ],
        [
            'code' => 'DPT_HB_HIB_3',
            'label' => 'DPT-HB-Hib 3',
            'jadwal' => '4 bulan',
            'umur_bulan' => 4,
            'program_wilayah' => null,
        ],
        [
            'code' => 'OPV3',
            'label' => 'OPV 3',
            'jadwal' => '4 bulan',
            'umur_bulan' => 4,
            'program_wilayah' => null,
        ],
        [
            'code' => 'IPV',
            'label' => 'IPV / Polio suntik',
            'jadwal' => '4 bulan',
            'umur_bulan' => 4,
            'program_wilayah' => null,
        ],
        [
            'code' => 'MR9',
            'label' => 'Campak Rubella',
            'jadwal' => '9 bulan',
            'umur_bulan' => 9,
            'program_wilayah' => null,
        ],
        [
            'code' => 'JE10',
            'label' => 'Japanese Encephalitis',
            'jadwal' => '10 bulan',
            'umur_bulan' => 10,
            'program_wilayah' => 'je',
        ],
        [
            'code' => 'PCV3_12',
            'label' => 'PCV 3',
            'jadwal' => '12 bulan',
            'umur_bulan' => 12,
            'program_wilayah' => 'pcv',
        ],
        [
            'code' => 'DPT_HB_HIB_BOOSTER_18',
            'label' => 'DPT-HB-Hib lanjutan',
            'jadwal' => '18 bulan',
            'umur_bulan' => 18,
            'program_wilayah' => null,
        ],
        [
            'code' => 'MR18',
            'label' => 'Campak Rubella lanjutan',
            'jadwal' => '18 bulan',
            'umur_bulan' => 18,
            'program_wilayah' => null,
        ],
    ];

    public function index(Request $request)
    {
        $validated = validator($request->all(), [
            'kunjungan_id' => ['nullable', 'string', 'max:100'],
            'pasien_id' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        try {
            $query = DB::table('mtbs_imunisasi_skrining')
                ->orderByDesc('updated_at')
                ->orderByDesc('id');

            if (!empty($validated['pasien_id'])) {
                $query->where('pasien_id', $validated['pasien_id']);
            } elseif (!empty($validated['kunjungan_id'])) {
                $query->where('kunjungan_id', $validated['kunjungan_id']);
            }

            $rows = $query->get();

            $data = $rows
                ->map(fn ($row) => $this->formatSkrining($row))
                ->values();

            $current = null;
            if (!empty($validated['kunjungan_id'])) {
                $currentRow = DB::table('mtbs_imunisasi_skrining')
                    ->where('kunjungan_id', $validated['kunjungan_id'])
                    ->first();

                $current = $currentRow
                    ? $this->formatSkrining($currentRow)
                    : null;
            }

            $latestPatient = null;
            if (!empty($validated['pasien_id'])) {
                $latestQuery = DB::table('mtbs_imunisasi_skrining')
                    ->where('pasien_id', $validated['pasien_id']);

                if (!empty($validated['kunjungan_id'])) {
                    $latestQuery->where('kunjungan_id', '!=', $validated['kunjungan_id']);
                }

                $latestRow = $latestQuery
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->first();

                $latestPatient = $latestRow
                    ? $this->formatSkrining($latestRow)
                    : null;
            }

            $assessment = null;
            if (!empty($validated['kunjungan_id'])) {
                $assessment = DB::table('mtbs_assessment')
                    ->where('kunjungan_id', $validated['kunjungan_id'])
                    ->orderByDesc('id')
                    ->first();
            }

            return response()->json([
                'data' => $data,
                'current' => $current,
                'latest_patient' => $latestPatient,
                'context' => [
                    'assessment_ada' => (bool) $assessment,
                    'status_kegawatan' => $assessment->status_kegawatan ?? null,
                    'rujuk_segera' => $this->isRujukSegera($assessment),
                ],
                'jadwal' => self::JADWAL_IMUNISASI,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBSImunisasi index error', [
                'msg' => $e->getMessage(),
                'query' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal mengambil skrining imunisasi MTBS.',
                'error' => $e->getMessage(),
                'data' => [],
                'current' => null,
                'latest_patient' => null,
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->merge([
            'kunjungan_id' => $request->kunjungan_id === '' ? null : $request->kunjungan_id,
            'pasien_id' => $request->pasien_id === '' ? null : $request->pasien_id,
            'umur_bulan_total' => $request->umur_bulan_total === ''
                ? null
                : $request->umur_bulan_total,
        ]);

        $allowedCodes = array_column(self::JADWAL_IMUNISASI, 'code');

        $validated = validator($request->all(), [
            'kunjungan_id' => ['required', 'string', 'max:100'],
            'pasien_id' => ['required', 'integer', 'min:1'],
            'umur_bulan_total' => ['nullable', 'integer', 'min:0', 'max:59'],

            'sumber_verifikasi' => [
                'required',
                'in:' . implode(',', self::SUMBER_VERIFIKASI),
            ],
            'vaksin_tercatat' => ['nullable', 'array'],
            'vaksin_tercatat.*' => [
                'string',
                'in:' . implode(',', $allowedCodes),
            ],
            'kondisi_anak' => [
                'required',
                'in:' . implode(',', self::KONDISI_ANAK),
            ],
            'program_pcv' => ['nullable', 'boolean'],
            'program_je' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ])->validate();

        try {
            $assessment = DB::table('mtbs_assessment')
                ->where('kunjungan_id', $validated['kunjungan_id'])
                ->orderByDesc('id')
                ->first();

            $rujukSegera = $this->isRujukSegera($assessment);
            $programPcv = $request->boolean('program_pcv');
            $programJe = $request->boolean('program_je');
            $umurBulan = $validated['umur_bulan_total'] ?? null;

            $vaksinTercatat = collect($validated['vaksin_tercatat'] ?? [])
                ->filter(fn ($code) => in_array($code, $allowedCodes, true))
                ->unique()
                ->values()
                ->all();

            $vaksinWajib = $this->getVaksinWajibSampaiUmur(
                $umurBulan,
                $programPcv,
                $programJe,
            );

            $vaksinBelum = array_values(
                array_diff($vaksinWajib, $vaksinTercatat),
            );

            $statusImunisasi = $this->tentukanStatusImunisasi(
                $validated['sumber_verifikasi'],
                $vaksinTercatat,
                $vaksinBelum,
                $umurBulan,
            );

            $kondisiAnak = $rujukSegera
                ? 'rujuk_segera'
                : $validated['kondisi_anak'];

            $tindakLanjut = $this->tentukanTindakLanjut(
                $statusImunisasi,
                $kondisiAnak,
            );

            $user = Auth::user();
            $petugas = $user
                ? ($user->name ?? $user->username ?? $user->email ?? 'Petugas')
                : 'Petugas';

            $existing = DB::table('mtbs_imunisasi_skrining')
                ->where('kunjungan_id', $validated['kunjungan_id'])
                ->first();

            $values = [
                'pasien_id' => $validated['pasien_id'],
                'umur_bulan_total' => $umurBulan,
                'sumber_verifikasi' => $validated['sumber_verifikasi'],
                'vaksin_tercatat' => json_encode($vaksinTercatat),
                'vaksin_belum' => json_encode($vaksinBelum),
                'status_imunisasi' => $statusImunisasi,
                'kondisi_anak' => $kondisiAnak,
                'tindak_lanjut' => $tindakLanjut,
                'program_pcv' => $programPcv ? 1 : 0,
                'program_je' => $programJe ? 1 : 0,
                'catatan' => $validated['catatan'] ?? null,
                'updated_by' => $petugas,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('mtbs_imunisasi_skrining')
                    ->where('id', $existing->id)
                    ->update($values);
            } else {
                DB::table('mtbs_imunisasi_skrining')->insert([
                    'kunjungan_id' => $validated['kunjungan_id'],
                    ...$values,
                    'created_by' => $petugas,
                    'created_at' => now(),
                ]);
            }

            $saved = DB::table('mtbs_imunisasi_skrining')
                ->where('kunjungan_id', $validated['kunjungan_id'])
                ->first();

            return response()->json([
                'message' => 'Skrining status imunisasi MTBS berhasil disimpan.',
                'data' => $saved ? $this->formatSkrining($saved) : null,
                'context' => [
                    'assessment_ada' => (bool) $assessment,
                    'status_kegawatan' => $assessment->status_kegawatan ?? null,
                    'rujuk_segera' => $rujukSegera,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBSImunisasi store error', [
                'msg' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan skrining imunisasi MTBS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('mtbs_imunisasi_skrining')
                ->where('id', $id)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'message' => 'Data skrining imunisasi tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'message' => 'Data skrining imunisasi berhasil dihapus.',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MTBSImunisasi destroy error', [
                'msg' => $e->getMessage(),
                'id' => $id,
            ]);

            return response()->json([
                'message' => 'Gagal menghapus skrining imunisasi MTBS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getVaksinWajibSampaiUmur(
        ?int $umurBulan,
        bool $programPcv,
        bool $programJe,
    ): array {
        if ($umurBulan === null) {
            return [];
        }

        return collect(self::JADWAL_IMUNISASI)
            ->filter(function (array $item) use ($umurBulan, $programPcv, $programJe) {
                if ($item['umur_bulan'] > $umurBulan) {
                    return false;
                }

                if ($item['program_wilayah'] === 'pcv' && !$programPcv) {
                    return false;
                }

                if ($item['program_wilayah'] === 'je' && !$programJe) {
                    return false;
                }

                return true;
            })
            ->pluck('code')
            ->values()
            ->all();
    }

    private function tentukanStatusImunisasi(
        string $sumberVerifikasi,
        array $vaksinTercatat,
        array $vaksinBelum,
        ?int $umurBulan,
    ): string {
        if (
            $umurBulan === null
            || (
                $sumberVerifikasi === 'tidak_ada_bukti'
                && count($vaksinTercatat) === 0
            )
        ) {
            return 'tidak_diketahui';
        }

        return count($vaksinBelum) === 0
            ? 'lengkap_sesuai_umur'
            : 'belum_lengkap';
    }

    private function tentukanTindakLanjut(
        string $statusImunisasi,
        string $kondisiAnak,
    ): string {
        if ($kondisiAnak === 'rujuk_segera') {
            return 'tunda_rujuk_segera';
        }

        if ($statusImunisasi === 'tidak_diketahui') {
            return 'verifikasi_ulang';
        }

        if ($statusImunisasi === 'lengkap_sesuai_umur') {
            return 'tidak_perlu';
        }

        if ($kondisiAnak === 'sehat_sakit_ringan') {
            return 'arahkan_ruang_imunisasi_hari_ini';
        }

        return 'jadwalkan_kembali';
    }

    private function isRujukSegera(?object $assessment): bool
    {
        if (!$assessment) {
            return false;
        }

        $status = Str::lower((string) ($assessment->status_kegawatan ?? ''));

        return Str::contains($status, [
            'penyakit sangat berat',
            'rujuk segera',
            'perlu rujukan',
            'gagal jantung paru',
        ]);
    }

    private function formatSkrining(object $row): array
    {
        return [
            'id' => $row->id,
            'kunjungan_id' => $row->kunjungan_id,
            'pasien_id' => $row->pasien_id,
            'umur_bulan_total' => $row->umur_bulan_total,
            'sumber_verifikasi' => $row->sumber_verifikasi,
            'vaksin_tercatat' => json_decode($row->vaksin_tercatat ?? '[]', true) ?: [],
            'vaksin_belum' => json_decode($row->vaksin_belum ?? '[]', true) ?: [],
            'status_imunisasi' => $row->status_imunisasi,
            'kondisi_anak' => $row->kondisi_anak,
            'tindak_lanjut' => $row->tindak_lanjut,
            'program_pcv' => (bool) $row->program_pcv,
            'program_je' => (bool) $row->program_je,
            'catatan' => $row->catatan,
            'created_by' => $row->created_by,
            'updated_by' => $row->updated_by,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }
}