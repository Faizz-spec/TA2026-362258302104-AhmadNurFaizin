<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Loket;
use App\Models\Poli;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\MKeluarga;
use App\Models\Agama;
use App\Models\Pekerjaan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\MasterUnit;
use App\Models\UnitDetail;
use App\Models\Wilayah;
use App\Models\UnitProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\LoketService;
use App\Services\WilayahService;
use Carbon\Carbon;

class LoketController extends Controller
{
    protected $service;
    protected $wilayahService;

    public function __construct(LoketService $service, WilayahService $wilayahService)
    {
        $this->service = $service;
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $poliList = Poli::where('poliSakit', 'true')->aktif()->get(['kdPoli', 'nmPoli']);
        $loket = $this->wilayahService->getLoketWithWilayah(10);

        $kategoriUnits = $this->getKategoriUnit();
        $wilayah = $this->getWilayah();

        return Inertia::render('Loket/Index', [
            'loket' => $loket,
            'poliList' => $poliList,
            'pasienId' => $request->query('pasienId', null),
            'message' => session('message'),
            'kategoriUnits' => $kategoriUnits,
            'wilayah' => $wilayah,
        ]);
    }

    public function ajaxList(Request $request)
    {
        $result = $this->service->datatable($request->all());
        return response()->json($result);
    }

    public function create()
    {
        return Inertia::render('Loket/AddPasien', [
            'kecamatanList' => Kecamatan::where('NO_PROP', '35')
                ->where('NO_KAB', '10')
                ->orderBy('NAMA_KEC')
                ->get(),
            'defaultPropinsi' => 'JAWA TIMUR',
            'defaultKabupaten' => 'KAB. BANYUWANGI',
            'hubunganKeluargaList' => MKeluarga::all(),
            'agamaList' => Agama::orderBy('NO')->get(),
            'poliList' => Poli::all(),
            'pekerjaanList' => Pekerjaan::orderBy('NO')->get(),
            'kategoriUnits' => $this->getKategoriUnit(),
            'wilayah' => $this->getWilayah(),
            'puskesmas' => $this->getPuskesmas(),
            'providers' => $this->getProvider(),
            'poliDropdown' => $this->getPoli(),
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $lastSeq = Pasien::max('SEQ') ?? 0;
            $nextSeq = $lastSeq + 1;
            $formattedSeq = str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
            $noMr = '01' . $formattedSeq;

            $data = $request->all();
            $data['SEQ'] = $nextSeq;
            $data['NO_MR'] = $noMr;

            $pasien = Pasien::create($data);

            DB::commit();

            return redirect()->route('loket.index')->with('success', 'Pasien berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pasien: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function search(Request $request)
    {
        $results = $this->wilayahService->searchPasienWithWilayah($request->all(), 50);

        return Inertia::render('Loket/Search', [
            'results' => $results,
            'searchParams' => $request->all(),
            'kecamatanList' => Kecamatan::all(),
        ]);
    }

    public function apiSearch(Request $request)
    {
        $pasien = $this->wilayahService->searchPasienWithWilayah($request->all(), 1)->first();

        if (!$pasien) {
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }

        return response()->json($pasien);
    }

    public function register(Request $request)
    {
        $data = $request->all();
        Log::info('Data register:', $data);

        // Generate nomor urut berdasarkan tanggal kunjungan
        $lastNoUrut = Loket::whereDate('tglKunjungan', $data['tglKunjungan'] ?? date('Y-m-d'))
            ->max('noUrut') ?? 0;

        $data['noUrut'] = str_pad($lastNoUrut + 1, 4, '0', STR_PAD_LEFT);

        $data['createdBy'] = Auth::user()?->username ?? 'test_user';

        // KONVERSI MENGGUNAKAN LOGIKA OTOMATIS
        $data['kunjSakit'] = ($data['jenisKunjungan'] ?? '') === 'Kunjungan Sakit' ? 'true' : 'false';
        $data['kunjBaru'] = ($data['jenisPengunjung'] ?? '') === 'Pengunjung Baru' ? 'true' : 'false';

        $defaultValues = [
            'kelUmur' => 1,
            'umur' => 30,
            'jknPbi' => $data['kategori'] ?? null,
            'statusPasien' => 'umum',
            'kdProvider' => ($data['kategori'] ?? '') === 'BPJS' ? 'BPJS' : '',
            'statusKartu' => ($data['kategori'] ?? '') === 'BPJS' ? 'AKTIF' : '',
            'noKartu' => ($data['kategori'] ?? '') === 'BPJS' ? ($data['no_bpjs'] ?? '') : '',
            'kdKegiatan' => 1,
            'puskId' => 1,
            'unitId' => 1,
            'kategoriUnitId' => 1,
        ];

        $data = array_merge($defaultValues, $data);

        try {
            $loket = Loket::create($data);
            Log::info('Loket created successfully:', $loket->toArray());

            return redirect()->route('loket.index')->with('success', 'Pasien berhasil didaftarkan');
        } catch (\Exception $e) {
            Log::error('Error creating loket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendaftarkan pasien: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menentukan jenis pengunjung otomatis Pengunjung Baru: Jika pertama kali daftar dalam bulan berjalan, Pengunjung Lama: Jika sudah pernah daftar dalam bulan yang sama
     */
    private function tentukanJenisPengunjung($pasienId, $tglKunjungan)
    {
        if (!$pasienId) {
            return 'Pengunjung Baru'; // Default untuk pasien baru
        }

        try {
            // Parse tanggal kunjungan
            $tanggalKunjungan = \Carbon\Carbon::parse($tglKunjungan);

            // Cek apakah pasien sudah pernah daftar di bulan dan tahun yang sama
            $existingVisit = DB::table('simpus_loket')
                ->where('pasienId', $pasienId)
                ->whereYear('tglKunjungan', $tanggalKunjungan->year)
                ->whereMonth('tglKunjungan', $tanggalKunjungan->month)
                ->exists();

            return $existingVisit ? 'Pengunjung Lama' : 'Pengunjung Baru';
        } catch (\Exception $e) {
            Log::error('Error menentukan jenis pengunjung: ' . $e->getMessage());
            return 'Pengunjung Baru'; // Fallback ke Pengunjung Baru jika error
        }
    }

    /**
     * API untuk pengecekan status pengunjung otomatis
     */
    public function checkJenisPengunjung(Request $request)
    {
        $pasienId = $request->get('pasienId');
        $tglKunjungan = $request->get('tglKunjungan');

        if (!$pasienId || !$tglKunjungan) {
            return response()->json(['status' => 'Pengunjung Baru']);
        }

        $jenisPengunjung = $this->tentukanJenisPengunjung($pasienId, $tglKunjungan);

        return response()->json([
            'status' => $jenisPengunjung,
            'pasienId' => $pasienId,
            'tglKunjungan' => $tglKunjungan,
            'message' => 'Deteksi otomatis berdasarkan riwayat kunjungan'
        ]);
    }

    /**
     * Method untuk menentukan wilayah otomatis - DEPRECATED, gunakan tentukanWilayahDenganUnit
     */
    private function tentukanWilayahOtomatis($pasienId, $userId)
    {
        $user = Auth::user();

        if (!$user || !$user->unit) {
            Log::warning("User atau unit tidak valid - UserID: {$userId}");
            return '';
        }

        return $this->tentukanWilayahDenganUnit($pasienId, $user->unit);
    }


    /**
     * Logic perhitungan wilayah berdasarkan perbandingan lokasi
     */
    private function hitungWilayahBerdasarLokasi(
        $pasienProp,
        $pasienKab,
        $pasienKec,
        $pasienKel,
        $unitProp,
        $unitKab,
        $unitKec,
        $unitKel
    ) {
        // Konversi ke string untuk konsistensi
        $pasienProp = (string) $pasienProp;
        $pasienKab = (string) $pasienKab;
        $pasienKec = (string) $pasienKec;
        $pasienKel = (string) $pasienKel;
        $unitProp = (string) $unitProp;
        $unitKab = (string) $unitKab;
        $unitKec = (string) $unitKec;
        $unitKel = (string) $unitKel;

        // 1. LUAR KABUPATEN - Jika propinsi atau kabupaten berbeda
        if ($pasienProp !== $unitProp || $pasienKab !== $unitKab) {
            return '3';
        }

        // 2. LUAR WILAYAH - Jika kecamatan berbeda (tapi masih kabupaten yang sama)
        if ($pasienKec !== $unitKec) {
            return '2';
        }

        // 3. DALAM WILAYAH - Jika kecamatan sama (dan kelurahan bisa sama atau berbeda)
        return '1';
    }

    /**
     * Tentukan wilayah berdasarkan unit user
     */
    private function tentukanWilayahDenganUnit($pasienId, $userUnit)
    {
        if (!$pasienId || !$userUnit) {
            return '';
        }

        try {
            // Ambil data pasien
            $pasien = DB::table('simpus_pasien')->where('ID', $pasienId)->first();
            if (!$pasien) {
                Log::warning("Pasien tidak ditemukan: {$pasienId}");
                return '';
            }

            // Ambil unit profile berdasarkan userUnit
            $unitProfile = DB::table('unit_profiles')->where('unit_id', $userUnit)->first();
            if (!$unitProfile) {
                Log::warning("Unit profile tidak ditemukan untuk unit: {$userUnit}");
                return '';
            }

            // Gunakan logic yang sama
            $idWilayah = $this->hitungWilayahBerdasarLokasi(
                $pasien->NO_PROP ?? null,
                $pasien->NO_KAB ?? null,
                $pasien->NO_KEC ?? null,
                $pasien->NO_KEL ?? null,
                $unitProfile->no_prop ?? null,
                $unitProfile->no_kab ?? null,
                $unitProfile->no_kec ?? null,
                $unitProfile->no_kel ?? null
            );

            Log::info("Wilayah determined - Pasien: {$pasienId}, Kec: {$pasien->NO_KEC}, Wilayah: {$idWilayah}");

            return $idWilayah;
        } catch (\Exception $e) {
            Log::error('Error menentukan wilayah dengan unit: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * API untuk mendapatkan wilayah otomatis
     */
    public function getWilayahOtomatis(Request $request)
    {
        $pasienId = $request->get('pasienId');

        // user harus login
        if (!Auth::check()) {
            Log::warning("Attempt to access wilayah otomatis without authentication");
            return response()->json([
                'wilayah' => '',
                'pasienId' => $pasienId,
                'error' => 'unauthorized',
                'message' => 'Silakan login untuk mengakses fitur ini'
            ], 401);
        }

        $user = Auth::user();

        // VALIDASI user memiliki unit
        if (!$user->unit) {
            Log::error("User {$user->id} tidak memiliki unit assignment");
            return response()->json([
                'wilayah' => '',
                'pasienId' => $pasienId,
                'error' => 'no_unit',
                'message' => 'User tidak memiliki unit yang valid'
            ], 400);
        }

        $wilayah = $this->tentukanWilayahDenganUnit($pasienId, $user->unit);

        Log::info("API Wilayah - Pasien: {$pasienId}, Unit: {$user->unit}, Hasil: {$wilayah}");

        return response()->json([
            'wilayah' => $wilayah,
            'pasienId' => $pasienId,
            'userUnit' => $user->unit
        ]);
    }

    /**
     * MASTER DATA FUNCTIONS
     */
    public function getKategoriUnit($id = null)
    {
        $query = MasterUnit::query();

        if ($id) {
            $query->where('id_kategori', '<>', 1);
        }

        $kategori = $query->orderBy('id_kategori')->pluck('kategori', 'id_kategori')->toArray();

        return count($kategori) > 0 ? $kategori : ['0' => 'Tidak ada data'];
    }

    public function getUnitList($id_unit)
    {
        $idpkm = Auth::user()->unit ?? 1;

        $units = UnitDetail::where('id_unit', $idpkm)
            ->where('id_kategori', $id_unit)
            ->where('status', 1)
            ->pluck('nama_unit', 'id_detail')
            ->toArray();

        return count($units) > 0 ? $units : ['0' => 'Tidak ada data'];
    }

    public function getUnitListAll()
    {
        $idpkm = Auth::user()->unit ?? 1;

        $query = UnitDetail::with('masterUnit')
            ->where('status', 1)
            ->orderBy('id_kategori')
            ->orderBy('nama_unit');

        if ($idpkm != '46') {
            $query->where('id_unit', $idpkm);
        }

        $units = $query->get();

        $data = [];
        foreach ($units as $unit) {
            $cleanName = str_replace('PUSKESMAS', '', $unit->nama_unit);
            $data[$unit->id_detail] = '[ <strong>' . $unit->masterUnit->kategori . ' </strong>] ' . $cleanName;
        }

        return count($data) > 0 ? $data : ['0' => 'Tidak ada data'];
    }

    public function getWilayah()
    {
        $wilayah = Wilayah::orderBy('id_wilayah')
            ->pluck('wilayah', 'id_wilayah')
            ->toArray();

        return ['0' => '- Pilih -'] + $wilayah;
    }

    public function getPuskesmas()
    {
        $idpkm = Auth::user()->unit ?? 1;

        $query = UnitProfile::whereNull('kategori')
            ->orderBy('nama_unit');

        if ($idpkm) {
            $query->where('unit_id', $idpkm);
        }

        $puskesmas = $query->pluck('nama_unit', 'unit_id')->toArray();

        return count($puskesmas) > 0 ? $puskesmas : ['0' => 'Tidak ada data'];
    }

    public function getProvider()
    {
        $providers = DB::table('simpus_provider')
            ->orderBy('kdProvider')
            ->pluck('nmProvider', 'kdProvider')
            ->toArray();

        return ['0' => '- Pilih -'] + $providers;
    }

    public function getPoli()
    {
        $poli = DB::table('simpus_poli_fktp')
            ->where('pelayanan', 'true')
            ->pluck('nmPoli', 'kdPoli')
            ->toArray();

        return ['0' => '- Pilih -'] + $poli;
    }

    /**
     * API ENDPOINTS untuk AJAX
     */
    public function unitList($kategoriUnitId)
    {
        $units = $this->getUnitList($kategoriUnitId);

        $options = '<option value="">- Pilih Unit -</option>';
        foreach ($units as $key => $value) {
            $selected = request('selected') == $key ? 'selected' : '';
            $options .= "<option value='{$key}' {$selected}>{$value}</option>";
        }

        return response($options);
    }

    public function getDataUnitById($id_detail)
    {
        $unit = UnitDetail::find($id_detail);
        return response()->json($unit);
    }

    public function getProvinsiList()
    {
        $provinsi = Provinsi::orderBy('NAMA_PROP')->get(['NO_PROP', 'NAMA_PROP']);
        return response()->json($provinsi);
    }

    public function getKabupatenByProvinsi(Request $request)
    {
        $kabupaten = Kabupaten::where('NO_PROP', $request->provinsi)
            ->orderBy('NAMA_KAB')
            ->get(['NO_KAB', 'NAMA_KAB']);

        return response()->json($kabupaten);
    }

    public function getKecamatanList(Request $request)
    {
        $query = Kecamatan::query();

        $no_prop = $request->propinsi ?? '35';
        $query->where('NO_PROP', $no_prop);

        $no_kab = $request->kabupaten ?? '10';
        $query->where('NO_KAB', $no_kab);

        return response()->json($query->orderBy('NAMA_KEC')->get(['NO_KEC', 'NAMA_KEC']));
    }

    public function getKelurahanByKecamatan(Request $request)
    {
        if (!$request->kecamatan) {
            return response()->json(['error' => 'Kecamatan harus dipilih'], 400);
        }

        $query = Kelurahan::query();

        $query->where('NO_PROP', $request->propinsi);
        $query->where('NO_KAB', $request->kabupaten);
        $query->where('NO_KEC', $request->kecamatan);

        return response()->json($query->orderBy('NAMA_KEL')->get(['NO_KEL', 'NAMA_KEL']));
    }

    public function getPoliByJenisKunjungan(Request $request)
    {
        if (($request->jenisKunjungan ?? '') === 'Kunjungan Sakit') {
            $poliList = Poli::where('poliSakit', 'true')->aktif()->get(['kdPoli', 'nmPoli']);
        } else {
            $poliList = Poli::where('poliSakit', 'false')->aktif()->get(['kdPoli', 'nmPoli']);
        }

        return response()->json($poliList);
    }

    public function pasien($id)
    {
        $pasien = Pasien::findOrFail($id);
        return Inertia::render('Loket/Pasien', [
            'pasien' => $pasien,
        ]);
    }

    public function cetak_kartu($id)
    {
        $pasien = $this->wilayahService->getPasienWithWilayah($id);

        if (!$pasien) {
            abort(404);
        }

        $alamat = DB::table('unit_profiles')->where('unit_id', Auth::user()?->unit ?? 1)->first();

        return Inertia::render('Loket/CetakKartu', [
            'pasien' => $pasien,
            'alamat' => $alamat,
        ]);
    }

    public function gen_barcode($NO_MR)
    {
        try {
            // Generate QR Code sebagai SVG
            $qrCode = QrCode::size(200)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->margin(1)
                ->generate($NO_MR);

            return response($qrCode, 200)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            Log::error('Error generating barcode: ' . $e->getMessage());

            // Fallback ke SVG sederhana
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="50" viewBox="0 0 200 50">
            <rect width="200" height="50" fill="#fff" stroke="#ccc"/>
            <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-family="monospace" font-size="12">' . e($NO_MR) . '</text>
        </svg>';

            return response($svg, 200)->header('Content-Type', 'image/svg+xml');
        }
    }

    public function cetak_noAntrian()
    {
        return Inertia::render('Loket/CetakNo', [
            'menu' => 'Cetak'
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->all();

        // Set default otomatis HANYA jika tidak ada pilihan manual
        if (!isset($data['jenisPengunjung']) && isset($data['pasienId']) && isset($data['tglKunjungan'])) {
            $jenisPengunjung = $this->tentukanJenisPengunjung($data['pasienId'], $data['tglKunjungan']);
            $data['jenisPengunjung'] = $jenisPengunjung;
        }

        // Set default otomatis untuk Wilayah
        if (!isset($data['wilayah']) && isset($data['pasienId'])) {
            $wilayahOtomatis = $this->tentukanWilayahOtomatis($data['pasienId'], Auth::id());
            if ($wilayahOtomatis) {
                $data['wilayah'] = $wilayahOtomatis;
            }
        }

        // KONVERSI FIELD DARI VUE KE STRUKTUR DATABASE
        $data['kunjBaru'] = ($data['jenisPengunjung'] ?? 'Pengunjung Baru') === 'Pengunjung Baru' ? 'true' : 'false';
        $data['kunjSakit'] = ($data['jenisKunjungan'] ?? '') === 'Kunjungan Sakit' ? 'true' : 'false';
        $data['jknPbi'] = $data['kategori'] ?? 'NON_BPJS';

        // KONVERSI kodeTKP -> kdTkp
        if (isset($data['kodeTKP'])) {
            $data['kdTkp'] = $data['kodeTKP'];
            unset($data['kodeTKP']);
        }

        // Hapus field yang tidak ada di database
        unset($data['jenisPengunjung'], $data['jenisKunjungan'], $data['kategori']);

        // DEBUG: Log data sebelum calculateAgeGroups
        Log::info('Data sebelum calculateAgeGroups:', $data);

        // Hitung umur/kelompok umur via service - DENGAN FALLBACK
        try {
            $this->service->calculateAgeGroups($data);
        } catch (\Exception $e) {
            Log::error('Error calculateAgeGroups: ' . $e->getMessage());
            // FALLBACK: Set default values jika calculateAgeGroups gagal
            $data['kelUmur'] = $data['kelUmur'] ?? 1;
            $data['umur'] = $data['umur'] ?? 30;
            $data['umur_bulan'] = $data['umur_bulan'] ?? 0;
            $data['umur_hari'] = $data['umur_hari'] ?? 0;
        }

        if ($data['umur'] < 0 || $data['umur'] > 150) {
            Log::warning('Umur tidak valid: ' . $data['umur'] . ', menggunakan default 30');
            $data['umur'] = 30;
            $data['kelUmur'] = 8; // Default untuk usia 20-44
        }

        // memastikan umur_bulan dan umur_hari tidak negatif
        if ($data['umur_bulan'] < 0)
            $data['umur_bulan'] = 0;
        if ($data['umur_hari'] < 0)
            $data['umur_hari'] = 0;

        // SANITIZE: Pastikan kelUmur tidak null
        if (empty($data['kelUmur'])) {
            $data['kelUmur'] = 1; // Default value
        }

        // DEBUG: Log data setelah calculateAgeGroups
        Log::info('Data setelah calculateAgeGroups:', $data);

        // sanitize unset fields
        unset($data['TGL_LHR'], $data['NO_MR'], $data['NIK']);

        $data['idLoket'] = \Illuminate\Support\Str::uuid()->toString();
        $data['puskId'] = Auth::user()?->unit ?? 1;
        $data['createdBy'] = Auth::user()?->username ?? 'system';

        // Generate nomor urut
        $lastNoUrut = DB::table('simpus_loket')
            ->whereDate('tglKunjungan', $data['tglKunjungan'])
            ->max('noUrut') ?? 0;
        $data['noUrut'] = str_pad($lastNoUrut + 1, 4, '0', STR_PAD_LEFT);

        try {
            DB::beginTransaction();

            // DEBUG: Log data sebelum insert
            Log::info('Data sebelum insert ke simpus_loket:', $data);

            // Insert ke tabel loket
            DB::table('simpus_loket')->insert($data);
            Log::info('✅ Berhasil insert ke simpus_loket');
            // Insert ke tabel pelayanan
            $layanan = [
                'loketId' => $data['idLoket'],
                'idpelayanan' => \Illuminate\Support\Str::uuid()->toString(),
                'tglPelayanan' => $data['tglKunjungan'] ?? date('Y-m-d'),
                'kdPoli' => $data['kdPoli'] ?? null,
                'kdKegiatanPel' => $data['kdKegiatan'] ?? null,
                'kunjSakitPel' => $data['kunjSakit'] ?? 'false',
                'pelIdSebelum' => '0',
                'createdBy' => $data['createdBy'],
                'startTime' => now(),
            ];
            DB::table('simpus_pelayanan')->insert($layanan);
            Log::info('✅ Berhasil insert ke simpus_pelayanan');

            // Update data pasien
            if (!empty($data['pasienId'])) {
                DB::table('simpus_pasien')->where('ID', $data['pasienId'])->update([
                    'kdProvider' => $data['kdProvider'] ?? null,
                    'PHONE' => $data['PHONE'] ?? null,
                ]);
                Log::info('✅ Berhasil update data pasien');
            }

            DB::commit();
            Log::info('✅ SEMUA OPERASI DATABASE BERHASIL');

            //Return Inertia response instead of JSON
            return redirect()->route('loket.index')->with([
                'success' => 'Pasien berhasil didaftarkan!',
                'bridging_status' => ($data['statusKartu'] ?? '') != '' ?
                    ($data['kdProvider'] == ($this->pcareKodePpk() ?? '') ? 'success' : 'no_bridging') : 'no_bpjs'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error simpan loket: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Data yang gagal disimpan:', $data);

            //Return Inertia redirect dengan error
            return redirect()->back()->with([
                'error' => 'Gagal mendaftarkan pasien: ' . $e->getMessage(),
                'input_data' => $request->all() // Untuk prepopulate form jika needed
            ]);
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        // Tentukan jenisPengunjung otomatis
        if (isset($data['pasienId']) && isset($data['tglKunjungan'])) {
            $jenisPengunjung = $this->tentukanJenisPengunjung($data['pasienId'], $data['tglKunjungan']);
            $data['jenisPengunjung'] = $jenisPengunjung;
            $data['kunjBaru'] = $jenisPengunjung === 'Pengunjung Baru' ? 'true' : 'false';
        }

        // Konversi field untuk update
        if (isset($data['jenisKunjungan'])) {
            $data['kunjSakit'] = $data['jenisKunjungan'] === 'Kunjungan Sakit' ? 'true' : 'false';
            unset($data['jenisKunjungan']);
        }

        if (isset($data['kategori'])) {
            $data['jknPbi'] = $data['kategori'];
            unset($data['kategori']);
        }

        $this->service->calculateAgeGroups($data);

        if ($data['umur'] < 0 || $data['umur'] > 150) {
            Log::warning('Umur tidak valid: ' . $data['umur'] . ', menggunakan default 30');
            $data['umur'] = 30;
            $data['kelUmur'] = 8;
        }

        // Juga pastikan umur_bulan dan umur_hari tidak negatif
        if ($data['umur_bulan'] < 0)
            $data['umur_bulan'] = 0;
        if ($data['umur_hari'] < 0)
            $data['umur_hari'] = 0;

        $pel = [
            'tglPelayanan' => $data['tglKunjungan'] ?? null,
            'kdPoli' => $data['kdPoli'] ?? null,
            'kdKegiatanPel' => $data['kdKegiatan'] ?? null,
            'kunjSakitPel' => $data['kunjSakit'] ?? 'false',
            'modifiedDate' => now(),
            'modifiedBy' => Auth::user()?->username ?? 'system',
        ];

        try {
            DB::beginTransaction();

            if (!empty($data['idpelayanan'])) {
                DB::table('simpus_pelayanan')->where('idpelayanan', $data['idpelayanan'])->update($pel);
            }

            unset($data['TGL_LHR'], $data['NO_MR'], $data['NIK'], $data['idpelayanan']);

            if (!empty($data['pasienId']) && isset($data['kdProvider'])) {
                DB::table('simpus_pasien')->where('ID', $data['pasienId'])->update(['kdProvider' => $data['kdProvider']]);
            }

            $data['modifiedDate'] = now();
            $data['modifiedBy'] = Auth::user()?->username ?? 'system';

            DB::table('simpus_loket')->where('idLoket', $data['idLoket'])->update($data);

            DB::commit();

            return response()->json(['status' => 'done', 'message' => 'update']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update loket: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function hapus($id)
    {
        $agent = request()->header('User-Agent', 'Unidentified User Agent');
        $qitem = DB::table('simpus_loket')->where('idLoket', $id)->first();

        if (!$qitem) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $del = [
            'puskId' => Auth::user()?->unit ?? 1,
            'agent' => $agent,
            'platform' => php_uname('s'),
            'ip_address' => request()->ip(),
            'pasienId' => $qitem->pasienId,
            'loketId' => $qitem->idLoket,
            'kdPoli' => $qitem->kdPoli,
            'tglKunjungan' => $qitem->tglKunjungan,
            'deleteBy' => Auth::user()?->username ?? 'system',
            'deleteDate' => now(),
        ];

        DB::table('log_loket_delete')->insert($del);
        DB::table('simpus_loket')->where('idLoket', $id)->delete();
        DB::table('simpus_pelayanan')->where('loketId', $id)->delete();

        return redirect()->route('loket.create')->with('success', 'Data berhasil dihapus');
    }

    public function cekrapid($pasienId, $tglKunjungan)
    {
        $tglcek = Carbon::parse($tglKunjungan)->format('Y-m-d');

        $cek = DB::selectOne("
            SELECT COUNT(loket.`puskId`) AS jmlRapid, up.nama_unit, tglKunjungan 
            FROM simpus_loket loket 
            INNER JOIN simpus_pelayanan pel ON pel.`loketId`=loket.`idLoket`
            INNER JOIN unit_profiles up ON up.`unit_id`=loket.puskId
            WHERE loket.`kdPoli`='095' 
              AND tglKunjungan BETWEEN DATE_SUB(?, INTERVAL 14 DAY) AND ? 
              AND loket.pasienId = ?
            ORDER BY tglKunjungan
            LIMIT 1
        ", [$tglcek, $tglcek, $pasienId]);

        $jmlRapid = $cek->jmlRapid ?? 0;
        $nama_unit = $cek->nama_unit ?? '';
        $tglRapid = $cek->tglKunjungan ?? null;

        if ($jmlRapid > 0) {
            return response()->json(['status' => 'pernah', 'message' => 'Pasien sudah pernah melakukan rapid tes di ' . $nama_unit . ' pada tanggal ' . Carbon::parse($tglRapid)->format('d-m-Y')]);
        }

        return response()->json(['status' => 'belum', 'message' => 'Pasien belum melakukan rapid tes']);
    }

    public function cek_beda_provider($pasienId, $tglKunjungan)
    {
        $PPK = $this->pcareKodePpk();
        $tglcek = Carbon::parse($tglKunjungan)->format('Y-m-d');

        $cek = DB::selectOne("
            SELECT COUNT(noUrut) AS bridging, pasienId FROM (
                SELECT a.`noUrut`, a.`pasienId`, a.`tglKunjungan`, a.`kdProvider`, a.`providerKartu`
                FROM simpus_loket a
                WHERE a.kdProvider <> '' AND a.kdProvider <> ?
                AND MONTH(a.`tglKunjungan`) = MONTH(?) 
                AND a.`puskId` = ?
                AND a.`pasienId` = ?
            ) prov GROUP BY pasienId
        ", [$PPK, $tglcek, Auth::user()?->unit ?? 1, $pasienId]);

        $jmlbridging = $cek->bridging ?? 0;

        if ($jmlbridging > 0 && $jmlbridging <= 2) {
            return response()->json(['status' => 'pernah', 'message' => 'PERCOBAAN !! Pasien sudah pernah melakukan bridging ' . $jmlbridging . ' kali pada bulan ini']);
        } elseif ($jmlbridging >= 3) {
            return response()->json(['status' => 'gagal', 'message' => 'PERCOBAAN !! Pasien sudah pernah melakukan bridging 3 kali pada bulan ini']);
        } else {
            return response()->json(['status' => 'belum', 'message' => 'PERCOBAAN !! pasien belum bridging pada bulan ini']);
        }
    }

    protected function pcareKodePpk()
    {
        return env('PCARE_KODE_PPK', 'KODE_PPK_DEFAULT');
    }

    public function lap_reg_kunj_pas($is_html, $unit, $unit_details, $tgl_awal, $tgl_akhir, $kel = null, $pusk = null)
    {
        $items = $this->service->rawSelect("SELECT * FROM simpus_loket WHERE tglKunjungan BETWEEN ? AND ? LIMIT 1000", [$tgl_awal, $tgl_akhir]);
        return view('laporan.loket.v_lap_reg_kunj_pas', [
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'is_html' => $is_html,
            'desa' => $kel,
            'items' => $items,
            'menu' => 'laporan'
        ]);
    }

    /**
     * HELPER FUNCTIONS
     */
    // private function getFieldTable($tableName)
    // {
    //     $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
    //     $data = [];
    //     foreach ($columns as $column) {
    //         $data[$column] = '';
    //     }
    //     return (object)$data;
    // }

    public function getId()
    {
        return Auth::user()->unit ?? 1;
    }

    public function getKdPpk()
    {
        $user_id = Auth::id();
        return DB::table('users as u')
            ->join('pcare as p', 'p.pusk_id', '=', 'u.unit')
            ->where('u.id', $user_id)
            ->value('p.kode_ppk');
    }
}
