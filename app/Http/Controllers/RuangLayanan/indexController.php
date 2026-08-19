<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\SimpusLoket;
use App\Models\RuangLayanan\SimpusMasterObat;
use App\Models\RuangLayanan\SimpusPelayanan;
use App\Models\RuangLayanan\SimpusPermohonanLab;
use App\Models\RuangLayanan\SimpusPoliFKTP;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RuangLayanan\MasterKamar;
use App\Models\RuangLayanan\MasterBed;
use App\Models\RuangLayanan\SimpusRanap;
use App\Models\RuangLayanan\SimpusLogKamar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class indexController extends Controller
{
    // === API Kamar/Bed untuk modal Pilih Kamar ===
    public function kamarList()
    {
        $kamar = MasterKamar::query()
            ->orderBy('nama_kamar')
            ->get(['id_kamar as id', 'nama_kamar']);
        return response()->json($kamar);
    }

    public function bedOn($kamarId, Request $request)
    {
        // Param: tglCek wajib, puskId opsional (jika tersedia dari frontend, gunakan untuk akurasi)
        $tglCek = $request->get('tglCek'); // format 'Y-m-d H:i:s'
        $puskId = $request->get('puskId');
        $excludePelayananId = $request->get('excludePelayananId');

        $beds = MasterBed::where('id_kamar', $kamarId)
            ->get(['id_bed as id', 'nama_bed', 'status', 'digunakan', 'id_kamar']);

        // Tentukan occupancy berdasarkan overlap tanggal pada SimpusRanap
        $occupiedByBed = [];
        try {
            $query = SimpusRanap::query()
                ->where('kamarId', $kamarId)
                ->whereRaw("tglMasuk <= ?", [$tglCek])
                ->whereRaw("COALESCE(tglKeluar, '2050-12-31 00:00:00') >= ?", [$tglCek]);
            if (!empty($puskId)) {
                $query->where('puskId', $puskId);
            }
            if (!empty($excludePelayananId)) {
                $query->where('pelayananId', '<>', $excludePelayananId);
            }
            $occupiedByBed = $query->pluck('bedId')->filter()->toArray();
        } catch (\Throwable $e) {
            $occupiedByBed = [];
        }

        $result = $beds->map(function ($b) use ($occupiedByBed) {
            $penuhByOverlap = in_array($b->id, $occupiedByBed);
            $penuhByFlag = (int)($b->digunakan ?? 0) === 1;
            // Jika ada kolom status (0/1), anggap 0=nonaktif → treat tidak dapat dipilih
            $nonAktif = isset($b->status) && (int)$b->status === 0;
            return [
                'id' => $b->id,
                'nama' => $b->nama_bed,
                'penuh' => $penuhByOverlap || $penuhByFlag || $nonAktif,
            ];
        });
        return response()->json($result);
    }

    public function simpanKamar(Request $request)
    {
        $data = $request->validate([
            'idPelayanan' => 'required',
            'ranapMsk' => 'required', // 'Y-m-d H:i:s'
            'kamarId' => 'required|integer',
            'bedId' => 'required|integer',
            'pilihan' => 'required|in:1,2,3,4',
        ]);

        Log::info('simpanKamar: payload diterima', [
            'payload' => $data,
            'userId' => Auth::id(),
        ]);

        if (!Schema::hasTable('simpus_ranap') || !Schema::hasTable('simpus_log_kamar')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tabel simpus_ranap / simpus_log_kamar belum ada di database yang aktif. Mohon buat tabel terlebih dahulu atau sesuaikan DB_DATABASE di .env.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $pelayananId = $data['idPelayanan'];
            $tglEfektif = $data['ranapMsk'];
            $kamarId = (int)$data['kamarId'];
            $bedId = (int)$data['bedId'];
            $now = now();

            // Muat pelayanan & loket untuk validasi kdPoli dan puskId
            $pel = DB::table('simpus_pelayanan as pel')
                ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
                ->where('pel.idpelayanan', $pelayananId)
                ->select('pel.kdPoli', 'l.puskId', 'l.idLoket')
                ->first();
            if (!$pel) {
                throw new \RuntimeException('Pelayanan tidak ditemukan');
            }
            if ((string)$pel->kdPoli !== '098') {
                throw new \RuntimeException('Pelayanan bukan poli rawat inap (098)');
            }
            $puskId = $pel->puskId;

            // Validasi bed milik kamar dan status aktif (jika ada kolom status)
            $bed = MasterBed::where('id_bed', $bedId)->first();
            if (!$bed || (int)$bed->id_kamar !== $kamarId) {
                throw new \RuntimeException('Bed tidak sesuai dengan kamar');
            }
            if (isset($bed->status) && (int)$bed->status === 0) {
                throw new \RuntimeException('Bed tidak aktif');
            }

            // upsert status ranap
            $ranap = SimpusRanap::firstOrNew(['pelayananId' => $pelayananId]);
            // Set puskId sinkron dengan pelayanan/loket
            $ranap->puskId = $puskId;

            // Tentukan endDate untuk cek overlap (pakai sentinel hanya dalam query)
            $endDate = $ranap->tglKeluar ? $ranap->tglKeluar : null;

            // Cek ketersediaan bed untuk rentang [tglEfektif, endDate]
            $exists = SimpusRanap::query()
                ->where('puskId', $puskId)
                ->where('bedId', $bedId)
                ->where('pelayananId', '<>', $pelayananId)
                ->whereRaw("tglMasuk <= COALESCE(?, '2050-12-31 00:00:00')", [$endDate])
                ->whereRaw("COALESCE(tglKeluar, '2050-12-31 00:00:00') >= ?", [$tglEfektif])
                ->exists();
            if ($exists) {
                throw new \RuntimeException('Bed tidak tersedia pada tanggal tersebut');
            }

            if (in_array($data['pilihan'], ['1', '2', '3'])) {
                // Masuk / Pindah / Salah Kamar
                if (!$ranap->tglMasuk) {
                    $ranap->tglMasuk = $tglEfektif;
                }
                $ranap->kamarId = $kamarId;
                $ranap->bedId = $bedId;
                $ranap->ranapStatus = 1; // aktif
                if (!$ranap->createdDate) {
                    $ranap->createdDate = $now;
                }
                $ranap->modifiedDate = $now;
                $ranap->save();

                SimpusLogKamar::create([
                    'pelayananId' => substr((string)$pelayananId, 0, 36),
                    'kamarId' => $kamarId,
                    'bedId' => $bedId,
                    'tglMskKmr' => $tglEfektif,
                    'createdBy' => Auth::id() !== null ? (int)Auth::id() : null,
                    'createdDate' => $now,
                    'deskripsi' => $data['pilihan'] === '1' ? 'Masuk' : ($data['pilihan'] === '2' ? 'Pindah' : 'Salah'),
                ]);
            } else {
                // 4 = Pasien Keluar
                $ranap->tglKeluar = $tglEfektif;
                $ranap->ranapStatus = 0;
                $ranap->pulangStatus = 1;
                if (!$ranap->createdDate) {
                    $ranap->createdDate = $now;
                }
                $ranap->modifiedDate = $now;
                $ranap->save();

                SimpusLogKamar::create([
                    'pelayananId' => substr((string)$pelayananId, 0, 36),
                    'kamarId' => $ranap->kamarId !== null ? (int)$ranap->kamarId : null,
                    'bedId' => $ranap->bedId !== null ? (int)$ranap->bedId : null,
                    'tglKlrKmr' => $tglEfektif,
                    'createdBy' => Auth::id() !== null ? (int)Auth::id() : null,
                    'createdDate' => $now,
                    'deskripsi' => 'Keluar',
                ]);
            }

            DB::commit();
            Log::info('simpanKamar: sukses', [
                'pelayananId' => $pelayananId,
                'pilihan' => $data['pilihan'],
            ]);
            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('simpanKamar: gagal', [
                'error' => $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : null,
                'trace' => $e->getTraceAsString(),
                'payload' => $data,
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function listPoli()
    {
        $listPoli = SimpusPoliFKTP::where('pelayanan', 'true')->get();
        return Inertia::render(
            'Ruang_Layanan/indexPoli',
            ['listPoli' => $listPoli]
        );
    }

    public function totalPasienKlusterAndPoli()
    {
        $jumlahPasienKluster2 = SimpusPelayanan::with('SimpusLoket')->whereIn('kdPoli', ['001', '003'])->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '<=', 17)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        $jumlahPasienKluster3 = SimpusPelayanan::with('SimpusLoket')->whereIn('kdPoli', ['001', '008', '006'])->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '>=', 18)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        $jumlahPasienLintasKluster = SimpusPelayanan::with('SimpusLoket')->whereIn('kdPoli', ['002', '004', '005', '098'])->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '>=', 18)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        $jumlahPasienSanitasi = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '097')->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '>', 17)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        $jumlahPasienGizi =  SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '997')->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '>', 17)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        $jumlahPasienKunjOnline =  SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '999')->whereHas('SimpusLoket', function ($query) {
            $query->where('umur', '>', 17)->where('unitId', Auth()->user()->unit);
        })->where('tglPelayanan', now()->toDateString())->count();
        return response()->json([
            'jumlahPasienKluster2' => $jumlahPasienKluster2,
            'jumlahPasienKluster3' => $jumlahPasienKluster3,
            'jumlahPasienLintasKluster' => $jumlahPasienLintasKluster,
            'jumlahPasienSanitasi' => $jumlahPasienSanitasi,
            'jumlahPasienGizi' => $jumlahPasienGizi,
            'jumlahPasienKunjOnline' => $jumlahPasienKunjOnline
        ]);
    }

    public function listPoliKluster($kluster)
    {
        if ($kluster == '2') {
            $listPoli = SimpusPoliFKTP::whereIn('kdPoli', [001, 003, 006])->get();
            $totalPasienUmum = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '001')->whereHas('SimpusLoket', function ($query) {
                $query->where('umur', '<=', 17)->where('unitId', Auth()->user()->unit);
            })->where('tglPelayanan', now()->toDateString())->count();
            $totalPasienKIA = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '003')->whereHas('SimpusLoket', function ($query) {})->where('tglPelayanan', now()->toDateString())->count();


            return Inertia::render(
                'Ruang_Layanan/klusterPasien/kluster2',
                [
                    'listPoli' => $listPoli,
                    'totalPasienUmum' => $totalPasienUmum,
                    'totalPasienKIA' => $totalPasienKIA,
                ]
            );
        } elseif ($kluster == '3') {
            $listPoli = SimpusPoliFKTP::whereIn('kdPoli', ['001', '008', '006'])->get();
            $totalPasienUmum = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '001')->whereHas('SimpusLoket', function ($query) {
                $query->where('umur', '>', 17)->where('unitId', Auth()->user()->unit);
            })->where('tglPelayanan', now()->toDateString())->count();
            $totalPasienKB = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '008')->whereHas('SimpusLoket', function ($query) {
                $query->where('umur', '>', 17)->where('unitId', Auth()->user()->unit);
            })->where('tglPelayanan', now()->toDateString())->count();
            $totalPasienPTM = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '006')->whereHas('SimpusLoket', function ($query) {})->where('tglPelayanan', now()->toDateString())->count();
            // dd($listPoli);
            //dd($totalPasienKB);
            return Inertia::render(
                'Ruang_Layanan/klusterPasien/kluster3',
                [
                    'listPoli' => $listPoli,
                    'totalPasienUmum' => $totalPasienUmum,
                    'totalPasienKB' => $totalPasienKB,
                    'totalPasienPTM' => $totalPasienPTM,
                ]
            );
        } else {
            $listPoli = SimpusPoliFKTP::whereIn('kdPoli', ['002', '005', '098',])->get();
            $totalPasienGigi = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '002')->where('tglPelayanan', now()->toDateString())->count();
            $totalPasienUGD = SimpusPelayanan::with('SimpusLoket')->where('kdPoli', '005')->where('tglPelayanan', now()->toDateString())->count();
            // dd($listPoli);
            return Inertia::render(
                'Ruang_Layanan/klusterPasien/lintaskluster',
                [
                    'listPoli' => $listPoli,
                    'totalPasienGigi' => $totalPasienGigi,
                    'totalPasienUGD' => $totalPasienUGD
                ]
            );
        }
    }

    public function paginasiSimpusDiagnosa(Request $request)
    {
        $DiagnosaMedis = DB::table('simpus_diagnosa')->paginate(10);

        $links = [];

        // Previous
        $links[] = [
            'label' => 'Previous',
            'url' => $DiagnosaMedis->previousPageUrl(),
            'active' => false,
        ];

        $lastPage = $DiagnosaMedis->lastPage();
        $current = $DiagnosaMedis->currentPage();

        // Always tampil halaman pertama
        $links[] = [
            'label' => 1,
            'url' => $DiagnosaMedis->url(1),
            'active' => $current === 1,
        ];

        // Tambah "..." kalau current jauh dari 1
        if ($current > 3) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Window: current-1, current, current+1
        for ($i = max(2, $current - 1); $i <= min($lastPage - 1, $current + 1); $i++) {
            $links[] = [
                'label' => $i,
                'url' => $DiagnosaMedis->url($i),
                'active' => $current === $i,
            ];
        }

        // Tambah "..." kalau current jauh dari lastPage
        if ($current < $lastPage - 2) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Always tampil halaman terakhir (kalau lebih dari 1)
        if ($lastPage > 1) {
            $links[] = [
                'label' => $lastPage,
                'url' => $DiagnosaMedis->url($lastPage),
                'active' => $current === $lastPage,
            ];
        }

        // Next
        $links[] = [
            'label' => 'Next',
            'url' => $DiagnosaMedis->nextPageUrl(),
            'active' => false,
        ];

        return response()->json([
            'data' => $DiagnosaMedis->items(),
            'meta' => [
                'current_page' => $DiagnosaMedis->currentPage(),
                'last_page' => $DiagnosaMedis->lastPage(),
                'per_page' => $DiagnosaMedis->perPage(),
                'total' => $DiagnosaMedis->total(),
            ],
            'links' => $links,
        ]);
    }

    public function paginasiSimpusTindakan(Request $request)
    {
        $search = $request->get('search');

        $query = DB::table('simpus_master_tindakan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tindakan', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }
        $MasterTindakan = $query->paginate(10);

        $links = [];

        // Previous
        $links[] = [
            'label' => 'Previous',
            'url' => $MasterTindakan->previousPageUrl(),
            'active' => false,
        ];

        $lastPage = $MasterTindakan->lastPage();
        $current = $MasterTindakan->currentPage();

        // Always tampil halaman pertama
        $links[] = [
            'label' => 1,
            'url' => $MasterTindakan->url(1),
            'active' => $current === 1,
        ];

        // Tambah "..." kalau current jauh dari 1
        if ($current > 3) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Window: current-1, current, current+1
        for ($i = max(2, $current - 1); $i <= min($lastPage - 1, $current + 1); $i++) {
            $links[] = [
                'label' => $i,
                'url' => $MasterTindakan->url($i),
                'active' => $current === $i,
            ];
        }

        // Tambah "..." kalau current jauh dari lastPage
        if ($current < $lastPage - 2) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Always tampil halaman terakhir (kalau lebih dari 1)
        if ($lastPage > 1) {
            $links[] = [
                'label' => $lastPage,
                'url' => $MasterTindakan->url($lastPage),
                'active' => $current === $lastPage,
            ];
        }

        // Next
        $links[] = [
            'label' => 'Next',
            'url' => $MasterTindakan->nextPageUrl(),
            'active' => false,
        ];

        return response()->json([
            'data' => $MasterTindakan->items(),
            'meta' => [
                'current_page' => $MasterTindakan->currentPage(),
                'last_page' => $MasterTindakan->lastPage(),
                'per_page' => $MasterTindakan->perPage(),
                'total' => $MasterTindakan->total(),
            ],
            'links' => $links,
        ]);
    }

    public function MasterObat(Request $request)
    {
        $search = $request->get('search');

        $query = SimpusMasterObat::where('AKTIF', '1');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        $MasterObat = $query->paginate(10);

        $links = [];

        // Previous
        $links[] = [
            'label' => 'Previous',
            'url' => $MasterObat->previousPageUrl(),
            'active' => false,
        ];

        $lastPage = $MasterObat->lastPage();
        $current = $MasterObat->currentPage();

        // Always tampil halaman pertama
        $links[] = [
            'label' => 1,
            'url' => $MasterObat->url(1),
            'active' => $current === 1,
        ];

        // Tambah "..." kalau current jauh dari 1
        if ($current > 3) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Window: current-1, current, current+1
        for ($i = max(2, $current - 1); $i <= min($lastPage - 1, $current + 1); $i++) {
            $links[] = [
                'label' => $i,
                'url' => $MasterObat->url($i),
                'active' => $current === $i,
            ];
        }

        // Tambah "..." kalau current jauh dari lastPage
        if ($current < $lastPage - 2) {
            $links[] = ['label' => '...', 'url' => null, 'active' => false];
        }

        // Always tampil halaman terakhir (kalau lebih dari 1)
        if ($lastPage > 1) {
            $links[] = [
                'label' => $lastPage,
                'url' => $MasterObat->url($lastPage),
                'active' => $current === $lastPage,
            ];
        }

        // Next
        $links[] = [
            'label' => 'Next',
            'url' => $MasterObat->nextPageUrl(),
            'active' => false,
        ];

        return response()->json([
            'data' => $MasterObat->items(),
            'meta' => [
                'current_page' => $MasterObat->currentPage(),
                'last_page' => $MasterObat->lastPage(),
                'per_page' => $MasterObat->perPage(),
                'total' => $MasterObat->total(),
            ],
            'links' => $links,
        ]);
    }

    public function getPermohonanLaborat($idLoket)
    {
        $PermohonanLabs = SimpusPermohonanLab::where('loketId', $idLoket)->with('tenagaMedis')->get();
        return response()->json(
            [
                'PermohonanLabs' => $PermohonanLabs
            ]
        );
    }
}
