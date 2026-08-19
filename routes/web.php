<?php

use App\Http\Controllers\Filter\FilterController;
use App\Http\Controllers\RuangLayanan\indexController;
use App\Http\Controllers\RuangLayanan\PoliBpUmumController;
use App\Http\Controllers\RuangLayanan\PoliGigiController;
use App\Http\Controllers\RuangLayananController;
use App\Http\Controllers\RuangLayanan\PoliKIAController;
use App\Http\Controllers\RuangLayanan\KematianController;
use App\Http\Controllers\RuangLayanan\TumbuhKembangController;
use App\Http\Controllers\RuangLayanan\PNCController;
use App\Http\Controllers\RuangLayanan\INCController;
use App\Http\Controllers\RuangLayanan\NeonatusController;
use App\Http\Controllers\RuangLayanan\AncController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoketController;
use Inertia\Inertia;
use App\Http\Controllers\Laporan\LaporanLoketController;
use App\Http\Controllers\Pasien\PasienController;
use App\Http\Controllers\Laporan\Rujukan\RujukanController;
use App\Http\Controllers\MalSehat\PTMController;
use App\Http\Controllers\Laporan\Kb\KbController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Laporan\Sanitasi\SanitasiController;
use App\Http\Controllers\Laporan\Ugd\UgdController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\RuangLayanan\KunjOnline\KunjOnlineController;
use App\Http\Controllers\RuangLayanan\LaboratoriumController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Owner\PanelController;
use App\Http\Controllers\Auth\PasswordForceController;
use App\Http\Controllers\Owner\OwnerLogController;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Farmasi\MasterObatController;
use App\Http\Controllers\Farmasi\PelayananResepController;
use App\Http\Controllers\Farmasi\PengeluaranLangsungController;
use App\Http\Controllers\Farmasi\PengeluaranLangsungDetailController;
use App\Http\Controllers\RuangLayanan\SkriningPTM\SkriningPTMController;
use App\Http\Controllers\RuangLayanan\MTBM\MTBMStatusPasienController;
use App\Http\Controllers\RuangLayanan\MTBS\MTBSController;
use App\Http\Controllers\RuangLayanan\MTBS\MTBSImunisasiController;
use App\Http\Controllers\RuangLayanan\MTBS\MTBSStatusPasienController;
use App\Http\Controllers\RuangLayanan\MTBM\MTBMLAPORANController;
use App\Http\Controllers\RuangLayanan\Laporan\LaporanMTBM_MTBSController;
use App\Http\Controllers\RuangLayanan\Dashboard\DashboardMTBM_MTBSController;
use App\Http\Controllers\RuangLayanan\MTBS\MTBSSatusehatController;
use App\Http\Controllers\RuangLayanan\MTBM\MTBMController;
use App\Http\Controllers\RuangLayanan\MTBM\MTBMImunisasiController;
use App\Http\Requests\SimpanTindakanRequest;
use App\Http\Controllers\RuangLayanan\MTBS\MTBSRujukanController;
use App\Http\Controllers\RuangLayanan\MTBM\MTBMSatusehatController;
Route::post('/test-form-request', function (SimpanTindakanRequest $request) {
    dd([
        'validated' => $request->validated(),
        'all' => $request->all(),
        'class' => get_class($request),
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');
});

Route::get('/ping-google', function () {
    try {
        // ping ke endpoint 204 Google; cukup untuk ukur latency & konektivitas
        Http::timeout(4)->get('https://www.google.com/generate_204');
        return response()->noContent(); // 204
    } catch (\Throwable $e) {
        return response()->noContent(503); // dianggap offline
    }
});

Route::get('/simpus/kia/mtbs/obat', [MTBSController::class, 'getObatMtbs']);
Route::post('/mtbs/gizi/store', [MTBSController::class, 'storeGizi']);
Route::get('/mtbs/gizi/{kunjunganId}', [MTBSController::class, 'showGizi']);


Route::prefix('mtbs')->group(function () {
    Route::get('{idPoli}/{idPelayanan}/satusehat-preview', [MTBSController::class, 'satusehatPreview'])
        ->name('mtbs.satusehat.preview');

    Route::post('{idPoli}/{idPelayanan}/send-satusehat-dummy', [MTBSController::class, 'sendSatusehatDummy'])
        ->name('mtbs.satusehat.send-dummy');
});

Route::prefix('simpus/kia/mtbs/rujukan')->group(function () {
    Route::get('/', [MTBSRujukanController::class, 'index'])->name('mtbs.rujukan.index');
    Route::get('/create/{idPelayanan}', [MTBSRujukanController::class, 'create'])->name('mtbs.rujukan.create');
    Route::post('/store', [MTBSRujukanController::class, 'store'])->name('mtbs.rujukan.store');
    Route::get('/cetak/{id}', [MTBSRujukanController::class, 'cetak'])->name('mtbs.rujukan.cetak');
});




Route::get('/simpus/kia/mtbs/planning/rekomendasi/{kunjunganId}', [MTBSController::class, 'rekomendasiPlanning']);
Route::get('/simpus/kia/mtbs/assessment/{kunjunganId}', [MTBSController::class, 'showAssessment']);
Route::match(['GET', 'POST'], '/_csrf-debug', function (Request $r) {
    return response()->json([
        'method' => $r->method(),
        'has_cookie_xsrf' => $r->cookies->has('XSRF-TOKEN'),
        'has_cookie_sess' => $r->cookies->has(config('session.cookie')),
        'token_input' => $r->input('_token') ? 'YES' : 'NO',
        'token_header' => $r->header('X-CSRF-TOKEN') ? 'YES' : 'NO',
        'token_x_xsrf' => $r->header('X-XSRF-TOKEN') ? 'YES' : 'NO',
        'session_driver' => config('session.driver'),
        'session_id' => $r->session()->getId(),
        'host' => $r->getHost(),
        'origin' => $r->headers->get('Origin'),
        'referer' => $r->headers->get('Referer'),
    ]);
})->middleware('web');

//

// =========================================================
// LOGIN
// =========================================================
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');


// =========================================================
// DASHBOARD UTAMA
// =========================================================
Route::middleware('auth')->group(function () {

    /*
     * Dashboard utama.
     * Data disiapkan oleh HomeController@index.
     * Halaman Vue: resources/js/Pages/Dashboard.vue
     */
    Route::get('/dashboard', [HomeController::class, 'index'])
        ->name('dashboard');

    /*
     * Route lama tetap tersedia, tetapi diarahkan
     * ke dashboard utama.
     */
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home.home');

    /*
     * Ketika membuka URL utama, langsung masuk dashboard.
     */
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    /*
     * Kompatibilitas link PTM lama.
     */
    Route::get('/ptm-dashboard', function () {
        return redirect()->route('dashboard');
    })->name('ptm.dashboard');

    // OWNER ONLY
    Route::middleware('role:owner')->group(function () {
        Route::get('/reports', function () {
            return Inertia::render('Reports/Index');
        })->name('reports.index');
    });
});


// =========================================================
// LOGOUT
// =========================================================
Route::post('/logout', function (Request $request) {
    \Illuminate\Support\Facades\Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Inertia::location(route('login'));
})->middleware('auth')->name('logout');
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':loket,owner,admin'])->get('/loket', fn() => Inertia::render('Loket/Index'))->name('loket.index');
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':pelayanan,owner,admin'])
    ->get('/ruang-layanan/poli', fn() => Inertia::render('RuangLayanan/Poli'))
    ->name('ruang-layanan.poli.alt');  // nama beda, tidak bentrok
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':owner,admin,loket,pelayanan'])->group(function () {
    // semua rute laporan
});
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':laborat,pelayanan'])->get('/laborat', fn() => Inertia::render('Laborat/Index'))->name('laborat.index');




Route::prefix('wilayah')->group(function () {
    Route::get('/kabupaten/{provinsi}', [PasienController::class, 'getKabupaten'])->name('wilayah.kabupaten');
    Route::get('/kecamatan/{provinsi}/{kabupaten}', [PasienController::class, 'getKecamatan'])->name('wilayah.kecamatan');
    Route::get('/kelurahan/{provinsi}/{kabupaten}/{kecamatan}', [PasienController::class, 'getKelurahan'])->name('wilayah.kelurahan');
});
// Route::inertia('/simpus/kia', 'Ruang_Layanan/KIA/index')->name('ruang-layanan.kia');
Route::get('/simpus/kia', [PoliKIAController::class, 'index'])->name('ruang-layanan.kia');
//ANC
// Route::inertia('/simpus/kia/anc1', 'Ruang_Layanan/KIA/ANC/Index')->name('ruang-layanan.anc1');
Route::get('/simpus/kia/anc', [AncController::class, 'index'])->name('ruang-layanan.anc');
Route::get('/simpus/kia/anc/pelayanan/{id}/{idPoli}/{idPelayanan}', [AncController::class, 'pelayanan'])->name('ruang-layanan-anc.pelayanan');
Route::post('simpus/kia/anc/pelayanan/', [AncController::class, 'setKunjunganANC'])->name('ruang-layanan-anc.kunjunganANC');
Route::post('simpus/kia/anc/pelayanan/obstetri', [AncController::class, 'setObstetri'])->name('ruang-layanan-anc.obstetri');
Route::post('simpus/kia/anc/pelayanan/DataDiagnosa', [AncController::class, 'setDataDiagnosa'])->name('ruang-layanan-anc.dataDiagnosa');
Route::delete('simpus/kia/anc/pelayanan/DataDiagnosa/{id}', [AncController::class, 'hapusDataDiagnosa'])->name('diagnosa.destroy');
Route::post('simpus/kia/anc/pelayanan/diagnosaKep', [AncController::class, 'setDataDiagnosaKep'])->name('ruang-layanan-anc.diagnosaKep');

// Route::get('/simpus/kia/ruang-layanan', [PoliKIAController::class, 'index'])->name('ruang-layanan.kia');
Route::get('/simpus/kia/pelayanan/{id}/{idPoli}/{idPelayanan}', [PoliKIAController::class, 'pelayanan'])->name('ruang-layanan-kia.pelayanan');

// Route::get('/api/kia/cari-diagnosa', [PoliKIAController::class, 'searchDiagnosa'])->name('api.cari-diagnosa');
// Kematian Maternal dan Perinatal
Route::get('/simpus/kia/kematian', [KematianController::class, 'index'])->name('ruang-layanan.kematian');
Route::get('/simpus/kia/kematian/pelayanan/{id}/{idPoli}/{idPelayanan}', [KematianController::class, 'pelayanan'])->name('ruang-layanan-kematian.pelayanan');
Route::get('/simpus/kia/neonatus', [NeonatusController::class, 'index'])->name('ruang-layanan.neonatus');
Route::get('/simpus/kia/neonatus/pelayanan/{id}/{idPoli}/{idPelayanan}', [NeonatusController::class, 'pelayanan'])->name('ruang-layanan-neonatus.pelayanan');
Route::get('/simpus/kia/pnc', [PNCController::class, 'index'])->name('ruang-layanan.pnc');
Route::get('/simpus/kia/pnc/pelayanan/{id}/{idPoli}/{idPelayanan}', [PNCController::class, 'pelayanan'])->name('ruang-layanan-pnc.pelayanan');
Route::get('/simpus/kia/inc', [INCController::class, 'index'])->name('ruang-layanan.inc');
Route::get('/simpus/kia/inc/pelayanan/{id}/{idPoli}/{idPelayanan}', [INCController::class, 'pelayanan'])->name('ruang-layanan-inc.pelayanan');
Route::get('/simpus/kia/tumbuhkembang', [TumbuhKembangController::class, 'index'])->name('ruang-layanan.tkembang');
Route::get('/simpus/kia/tumbuhkembang/pelayanan/{id}/{idPoli}/{idPelayanan}', [TumbuhKembangController::class, 'pelayanan'])->name('ruang-layanan-tkembang.pelayanan');
Route::delete('/simpus/kia/mtbs/diagnosa-medis/{id}', [MTBSController::class, 'deleteDiagnosaMedis']);
Route::get('/simpus/kia/mtbs/diagnosa-medis/{kunjunganId}', [MTBSController::class, 'showDiagnosaMedis']);
Route::get('/simpus/kia/mtbs', [MTBSController::class, 'index']) ->name('ruang-layanan.mtbs');
Route::post('/simpus/kia/mtbs/diagnosa-medis', [MTBSController::class, 'storeDiagnosaMedis']);
Route::get('/simpus/kia/mtbs/pelayanan/{id}/{idPoli}/{idPelayanan}', [MTBSController::class, 'pelayanan']) ->name('ruang-layanan-mtbs.pelayanan');
Route::get('/simpus/kia/mtbs/master-diagnosa', [MTBSController::class, 'cariDiagnosaMedis']);
Route::post('/simpus/kia/mtbs/alergi', [MTBSController::class, 'storeAlergi']);
Route::get('/simpus/kia/mtbs/alergi/{kunjunganId}', [MTBSController::class, 'showAlergi']);
Route::get('/simpus/kia/mtbs/riwayat-pasien/{idPelayanan}', [MTBSController::class, 'riwayatPasien']);
Route::post('/simpus/kia/mtbs/subjektif/store', [MTBSController::class, 'storeSubjektif'])
    ->middleware('auth')
    ->name('mtbs.subjektif.store');
Route::prefix('mtbs')->middleware('auth')->group(function () {
    Route::get('{idPoli}/{idPelayanan}/satusehat-preview', [MTBSSatusehatController::class, 'satusehatPreview'])
        ->name('mtbs.satusehat.preview');

    Route::post('{idPoli}/{idPelayanan}/send-satusehat-dummy', [MTBSSatusehatController::class, 'sendSatusehatDummy'])
        ->name('mtbs.satusehat.send-dummy');
});
Route::get('/simpus/kia/mtbs/tindakan', [MTBSController::class, 'cariTindakan']);
Route::post('/simpus/kia/mtbs/objektif/store', [MTBSController::class, 'storeObjektif'])
    ->middleware('auth')
    ->name('mtbs.objektif.store');
Route::post('/simpus/kia/mtbs/assessment/store', [MTBSController::class, 'storeAssessment'])
    ->middleware('auth')
    ->name('mtbs.assessment.store');
Route::post('/simpus/kia/mtbs/assessment/auto', [MTBSController::class, 'storeAssessmentAuto'])
    ->middleware('auth')
    ->name('mtbs.assessment.auto');
Route::get('/simpus/kia/mtbs/subjektif/{kunjunganId}', [MTBSController::class, 'showSubjektif'])
  ->middleware('auth')
  ->name('mtbs.subjektif.show');
Route::get('/simpus/kia/mtbs/objektif/{kunjunganId}', [MTBSController::class, 'showObjektif'])
  ->middleware('auth')
  ->name('mtbs.objektif.show');
Route::get('/simpus/kia/mtbs/imunisasi', [MTBSImunisasiController::class, 'index'])
    ->middleware('auth')
    ->name('mtbs.imunisasi.index');

Route::post('/simpus/kia/mtbs/imunisasi/store', [MTBSImunisasiController::class, 'store'])
    ->middleware('auth')
    ->name('mtbs.imunisasi.store');

Route::delete('/simpus/kia/mtbs/imunisasi/{id}', [MTBSImunisasiController::class, 'destroy'])
    ->middleware('auth')
    ->name('mtbs.imunisasi.destroy');
Route::post('/simpus/kia/mtbs/planning/store', [MTBSController::class, 'storePlanning'])
    ->middleware('auth')
    ->name('mtbs.planning.store');
Route::get(
    '/simpus/kia/mtbs/ringkasan/{kunjunganId}',
    [MTBSController::class, 'ringkasanSubjektifObjektif']
)->name('mtbs.ringkasan');
Route::get('/simpus/kia/mtbs/planning/{kunjunganId}', [MTBSController::class, 'showPlanning'])
    ->middleware('auth')
    ->name('mtbs.planning.show');
Route::get('/simpus/kia/mtbs/statuspasien', [MTBSStatusPasienController::class, 'index'])
    ->middleware('auth')
    ->name('mtbs.statuspasien.index');

Route::post('/simpus/kia/mtbs/statuspasien/store', [MTBSStatusPasienController::class, 'store'])
    ->middleware('auth')
    ->name('mtbs.statuspasien.store');
Route::get('/simpus/kia/mtbs/statuspasien', [MTBSStatusPasienController::class, 'index']);
Route::get('/simpus/kia/mtbs/statuspasien/options', [MTBSStatusPasienController::class, 'options']);
Route::post('/simpus/kia/mtbs/statuspasien/store', [MTBSStatusPasienController::class, 'store']);
Route::delete(
    '/simpus/kia/mtbs/testing/hapus/{kunjunganId}',
    [MTBSController::class, 'hapusDataTesting']
)->name('mtbs.testing.hapus');

Route::delete(
    '/simpus/kia/mtbm/testing/hapus/{kunjunganId}',
    [MTBMController::class, 'hapusDataTesting']
)
    ->middleware('auth')
    ->name('mtbm.testing.hapus');


    

Route::prefix('mtbm/{idPoli}/{idPelayanan}')->middleware('auth')->group(function () {
    Route::get('satusehat-preview', [MTBMSatusehatController::class, 'satusehatPreview'])
        ->name('mtbm.satusehat.preview');
    Route::post('send-satusehat', [MTBMSatusehatController::class, 'sendSatusehatMtbm'])
        ->name('mtbm.satusehat.send');
});
// Halaman list MTBM
Route::get('/simpus/kia/mtbm/master-diagnosa', [MTBMController::class, 'cariDiagnosaMedis']);

Route::get('/simpus/kia/mtbm/diagnosa-medis/{kunjunganId}', [MTBMController::class, 'showDiagnosaMedis']);
Route::post('/simpus/kia/mtbm/diagnosa-medis', [MTBMController::class, 'storeDiagnosaMedis']);
Route::delete('/simpus/kia/mtbm/diagnosa-medis/{id}', [MTBMController::class, 'deleteDiagnosaMedis']);

Route::get('/simpus/kia/mtbm', [MTBMController::class, 'index'])
    ->middleware('auth')
    ->name('ruang-layanan.mtbm');

Route::get('/simpus/kia/mtbm/pelayanan/{id}/{idPoli}/{idPelayanan}', [MTBMController::class, 'pelayanan'])
    ->middleware('auth')
    ->name('ruang-layanan-mtbm.pelayanan');

// === contoh endpoint CRUD seperti MTBS (kalau kamu butuh) ===
Route::post('/simpus/kia/mtbm/subjektif/store', [MTBMController::class, 'storeSubjektif'])
    ->middleware('auth')
    ->name('mtbm.subjektif.store');

Route::post('/simpus/kia/mtbm/objektif/store', [MTBMController::class, 'storeObjektif'])
    ->middleware('auth')
    ->name('mtbm.objektif.store');

Route::post('/simpus/kia/mtbm/assessment/store', [MTBMController::class, 'storeAssessment'])
    ->middleware('auth')
    ->name('mtbm.assessment.store');

Route::post('/simpus/kia/mtbm/assessment/auto', [MTBMController::class, 'storeAssessmentAuto'])
    ->middleware('auth')
    ->name('mtbm.assessment.auto');

Route::post('/simpus/kia/mtbm/planning/store', [MTBMController::class, 'storePlanning'])
    ->middleware('auth')
    ->name('mtbm.planning.store');

Route::get('/simpus/kia/mtbm/planning/{kunjunganId}', [MTBMController::class, 'showPlanning'])
    ->middleware('auth')
    ->name('mtbm.planning.show');
Route::get(
    '/simpus/kia/mtbm/tindakan',
    [MTBMController::class, 'cariTindakan']
)->name('mtbm.tindakan.search');

Route::get(
    '/simpus/kia/mtbm/obat',
    [MTBMController::class, 'getObatMtbm']
)->name('mtbm.obat');

Route::get(
    '/simpus/kia/mtbm/planning/rekomendasi/{kunjunganId}',
    [MTBMController::class, 'rekomendasiPlanning']
)->name('mtbm.planning.rekomendasi');

Route::post(
    '/simpus/kia/mtbm/planning/store',
    [MTBMController::class, 'storePlanning']
)->name('mtbm.planning.store');

Route::get(
    '/simpus/kia/mtbm/planning/{kunjunganId}',
    [MTBMController::class, 'getPlanning']
)->name('mtbm.planning.show');
// show subjektif/objektif
Route::get('/simpus/kia/mtbm/subjektif/{kunjunganId}', [MTBMController::class, 'showSubjektif'])
    ->middleware('auth')
    ->name('mtbm.subjektif.show');

Route::get('/simpus/kia/mtbm/objektif/{kunjunganId}', [MTBMController::class, 'showObjektif'])
    ->middleware('auth')
    ->name('mtbm.objektif.show');

// imunisasi (kalau memang MTBM punya halaman imunisasi sendiri)
Route::get('/simpus/kia/mtbm/imunisasi', [MTBMImunisasiController::class, 'index'])
    ->middleware('auth')
    ->name('mtbm.imunisasi.index');

Route::post('/simpus/kia/mtbm/imunisasi/store', [MTBMImunisasiController::class, 'store'])
    ->middleware('auth')
    ->name('mtbm.imunisasi.store');

Route::delete('/simpus/kia/mtbm/imunisasi/{id}', [MTBMImunisasiController::class, 'destroy'])
    ->middleware('auth')
    ->name('mtbm.imunisasi.destroy');

// status pasien (kalau kamu bikin tabel status pasien juga)
Route::get('/simpus/kia/mtbm/statuspasien', [MTBMStatusPasienController::class, 'index'])
    ->middleware('auth')
    ->name('mtbm.statuspasien.index');

Route::post('/simpus/kia/mtbm/statuspasien/store', [MTBMStatusPasienController::class, 'store'])
    ->middleware('auth')
    ->name('mtbm.statuspasien.store');

Route::get('/simpus/kia/mtbm/subjektif/{kunjunganId}', [MTBMController::class, 'showSubjektif'])
    ->middleware('auth')
    ->name('mtbm.subjektif.show');

Route::post('/simpus/kia/mtbm/subjektif/store', [MTBMController::class, 'storeSubjektif'])
    ->middleware('auth')
    ->name('mtbm.subjektif.store');
Route::get('/simpus/kia/mtbm/objektif/{kunjunganId}', [MTBMController::class, 'showObjektif'])
    ->middleware('auth')
    ->name('mtbm.objektif.show');
Route::get('/simpus/kia/mtbm/statuspasien/options', [MTBMController::class, 'getStatusPasienOptions']);
Route::get('/simpus/kia/mtbm/statuspasien', [MTBMController::class, 'getStatusPasien']);
Route::post('/simpus/kia/mtbm/statuspasien/store', [MTBMController::class, 'storeStatusPasien']);
Route::post('/simpus/kia/mtbm/objektif/store', [MTBMController::class, 'storeObjektif'])
    ->middleware('auth')
    ->name('mtbm.objektif.store');
Route::get('/simpus/kia/mtbm/assessment/{kunjunganId}', [MTBMController::class, 'getAssessment'])
    ->middleware('auth')
    ->name('mtbm.assessment.show');

Route::post('/simpus/kia/mtbm/assessment/store', [MTBMController::class, 'storeAssessment'])
    ->middleware('auth')
    ->name('mtbm.assessment.store');

Route::post('/simpus/kia/mtbm/assessment/auto', [MTBMController::class, 'autoAssessment'])
    ->middleware('auth')
    ->name('mtbm.assessment.auto');
Route::get('/simpus/kia/mtbm/planning/{kunjunganId}', [MTBMController::class, 'getPlanning'])
    ->middleware('auth')
    ->name('mtbm.planning.show');

Route::get('/simpus/kia/mtbm/statuspasien', [MTBMStatusPasienController::class, 'index']);
Route::get('/simpus/kia/mtbm/statuspasien/options', [MTBMStatusPasienController::class, 'options']);
Route::post('/simpus/kia/mtbm/statuspasien/store', [MTBMStatusPasienController::class, 'store']);
Route::post('/simpus/kia/mtbm/planning/store', [MTBMController::class, 'storePlanning'])
    ->middleware('auth')
    ->name('mtbm.planning.store');

Route::get('/simpus/laporan/mtbm', [MTBMLAPORANController::class, 'index'])
    ->middleware('auth')
    ->name('laporan.mtbm');

Route::get('/simpus/laporan/mtbm/data', [MTBMLAPORANController::class, 'data'])
    ->middleware('auth')
    ->name('laporan.mtbm.data');
Route::get('/laporan/mtbm/export', [MTBMLAPORANController::class, 'export'])
         ->middleware('auth')
     ->name('laporan.mtbm.export');

Route::get('/simpus/laporan/mtbm-mtbs', [LaporanMTBM_MTBSController::class, 'index'])
  ->middleware('auth')
  ->name('laporan.mtbm_mtbs');

Route::get('/simpus/laporan/mtbm-mtbs/data', [LaporanMTBM_MTBSController::class, 'data'])
  ->middleware('auth')
  ->name('laporan.mtbm_mtbs.data');

Route::get('/simpus/laporan/mtbm-mtbs/export-aggregat', [LaporanMTBM_MTBSController::class, 'exportAggregat'])
  ->middleware('auth')
  ->name('laporan.mtbm_mtbs.export_aggregat');

Route::get('/simpus/laporan/mtbm-mtbs/export-detail', [LaporanMTBM_MTBSController::class, 'exportDetail'])
  ->middleware('auth')
  ->name('laporan.mtbm_mtbs.export_detail');
Route::get('/simpus/laporan/mtbm-mtbs', [LaporanMTBM_MTBSController::class, 'index'])
  ->middleware('auth')
  ->name('laporan.mtbm_mtbs');


Route::get('/dashboard/mtbm-mtbs', [DashboardMTBM_MTBSController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.mtbm_mtbs');

Route::get('/dashboard/mtbm-mtbs/data', [DashboardMTBM_MTBSController::class, 'data'])
    ->middleware('auth')
    ->name('dashboard.mtbm_mtbs.data');

// Grup Admin
Route::prefix('admin')->group(function () {
    Route::get('/', fn() => Inertia::render('Admin/Index'))->name('admin.index');
});

//Grup Farmasi
Route::prefix('farmasi')->group(function () {
    Route::get('/', fn() => Inertia::render('Farmasi/Index'))->name('farmasi.index');
    Route::get('/laporan', fn() => Inertia::render('Farmasi/LaporanFarmasi'))->name('farmasi.laporan');
    //master-obat
    Route::get('/master-obat', [MasterObatController::class, 'index'])->name('farmasi.master-obat.data');
    Route::get('/master-obat/tambah', [MasterObatController::class, 'create'])->name('master-obat.create');
    Route::post('/master-obat', [MasterObatController::class, 'store'])->name('master-obat.store');
    //pengeluaran-langsung
    Route::get('/units', [PengeluaranLangsungController::class, 'getUnits']);
    Route::get('/get-sub-units', [PengeluaranLangsungController::class, 'getSubUnits']);
    Route::get('/pengeluaran-langsung', [PengeluaranLangsungController::class, 'index'])->name('farmasi.pengeluaran-langsung');
    Route::post('/pengeluaran-langsung', [PengeluaranLangsungController::class, 'store'])->name('farmasi.pengeluaran-langsung.store');
    Route::get('/pengeluaran-langsung/{id}', [PengeluaranLangsungController::class, 'show'])->name('farmasi.pengeluaran-langsung.show');
    Route::put('/pengeluaran-langsung/{id}', [PengeluaranLangsungController::class, 'update'])->name('farmasi.pengeluaran-langsung.update');
    Route::delete('/pengeluaran-langsung/{id}', [PengeluaranLangsungController::class, 'destroy'])->name('farmasi.pengeluaran-langsung.destroy');
    Route::post('/pengeluaran-langsung/filter', [PengeluaranLangsungController::class, 'filter'])->name('farmasi.pengeluaran-langsung.filter');
    Route::get('/pengeluaran-langsung/{id}/detail', [PengeluaranLangsungController::class, 'detail'])->name('pengeluaran-langsung.detail');
    Route::get('/pengeluaran-langsung/{id}/detail/items', [PengeluaranLangsungDetailController::class, 'index'])->name('pengeluaran.detail.index');
    Route::get('/pengeluaran-langsung/{id}/detail/data', [PengeluaranLangsungDetailController::class, 'getData'])->name('pengeluaran.detail.data');
    Route::post('/pengeluaran-langsung/{id}/detail', [PengeluaranLangsungDetailController::class, 'store'])->name('pengeluaran.detail.store');
    Route::delete('/pengeluaran-langsung/{id}/detail/{detailId}', [PengeluaranLangsungDetailController::class, 'destroy'])->name('pengeluaran.detail.destroy');

    //pelayanan-resep
    Route::get('/pelayanan-resep', [PelayananResepController::class, 'index'])->name('farmasi.pelayanan-resep.index');
    Route::get('/pelayanan-resep/data', [PelayananResepController::class, 'getData']);
    Route::get('/get-units', [PelayananResepController::class, 'getUnits']);
    Route::get('/get-sub-units', [PelayananResepController::class, 'getSubUnits']);
});

// Grup Filter
Route::prefix('filter')->controller(FilterController::class)->group(function () {
    Route::get('/', 'index')->name('filter');
    // Route::get('/dev', 'dev')->name('filter.dev');
    // Route::get('/modal', 'modal')->name('filter.modal');
});

// Grup Loket
Route::prefix('loket')->group(function () {
    Route::get('/', [LoketController::class, 'index'])->name('loket.index');
    Route::get('/data', [LoketController::class, 'ajaxList'])->name('loket.data');
    Route::get('/pasien', [LoketController::class, 'create'])->name('loket.pasien');
    Route::post('/pasien', [LoketController::class, 'store'])->name('loket.pasien.store');
    Route::get('/search', [PasienController::class, 'index'])->name('loket.search');
    Route::get('/edit/{id}', [PasienController::class, 'edit'])->name('loket.edit');
    Route::post('/update/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::post('/register', [LoketController::class, 'register'])->name('loket.register');

    // API untuk master data wilayah
    Route::get('/api/provinsi', [LoketController::class, 'getProvinsiList'])->name('loket.api.provinsi');
    Route::get('/api/kabupaten', [LoketController::class, 'getKabupatenByProvinsi'])->name('loket.api.kabupaten');
    Route::get('/api/kecamatan', [LoketController::class, 'getKecamatanList'])->name('loket.api.kecamatan');
    Route::get('/api/kelurahan', [LoketController::class, 'getKelurahanByKecamatan'])->name('loket.api.kelurahan');
    Route::get('/api/poli-by-jenis', [LoketController::class, 'getPoliByJenisKunjungan'])->name('loket.api.poli-by-jenis');

    // API untuk master data unit
    Route::get('/api/kategori-unit', [LoketController::class, 'getKategoriUnit'])->name('loket.api.kategori-unit');
    Route::get('/api/unit-list/{kategoriUnitId}', [LoketController::class, 'unitList'])->name('loket.api.unit-list');
    Route::get('/api/unit/{id}', [LoketController::class, 'getDataUnitById'])->name('loket.api.unit');
    Route::get('/api/wilayah', [LoketController::class, 'getWilayah'])->name('loket.api.wilayah');
    Route::get('/api/puskesmas', [LoketController::class, 'getPuskesmas'])->name('loket.api.puskesmas');
    Route::get('/api/provider', [LoketController::class, 'getProvider'])->name('loket.api.provider');
    Route::get('/api/poli', [LoketController::class, 'getPoli'])->name('loket.api.poli');

    // API untuk pencarian pasien
    Route::get('/api/pasien/search', [LoketController::class, 'apiSearch'])->name('api.pasien.search');
    Route::get('/api/check-jenis-pengunjung', [LoketController::class, 'checkJenisPengunjung'])->name('loket.api.check-jenis-pengunjung');
    Route::get('/api/wilayah-otomatis', [LoketController::class, 'getWilayahOtomatis'])->middleware('auth')->name('loket.api.wilayah-otomatis');
    Route::get('/api/pasien/{id}', function ($id) {
        return \App\Models\Pasien::findOrFail($id);
    });

    // Route untuk halaman pasien
    Route::get('/pasien/{id}', [LoketController::class, 'pasien'])->name('loket.pasien.show');
    Route::get('/cetak-kartu/{id}', [LoketController::class, 'cetak_kartu'])->name('loket.cetak_kartu');
    Route::get('/gen-barcode/{NO_MR}', [LoketController::class, 'gen_barcode'])->name('loket.gen_barcode');

    // Route untuk operasi CRUD
    Route::post('/simpan', [LoketController::class, 'simpan'])->name('loket.simpan');
    Route::post('/update', [LoketController::class, 'update'])->name('loket.update');
    Route::delete('/hapus/{id}', [LoketController::class, 'hapus'])->name('loket.hapus');

    // Route untuk validasi
    Route::get('/cekrapid/{pasienId}/{tglKunjungan}', [LoketController::class, 'cekrapid'])->name('loket.cekrapid');
    Route::get('/cek_beda_provider/{pasienId}/{tglKunjungan}', [LoketController::class, 'cek_beda_provider'])->name('loket.cek_beda_provider');

    // Reports (example)
    Route::get('/lap_reg_kunj_pas/{is_html}/{unit}/{unit_details}/{tgl_awal}/{tgl_akhir}/{kel?}/{pusk?}', [LoketController::class, 'lap_reg_kunj_pas'])->name('loket.report.reg_kunj');
});

// Grup Templete
Route::prefix('templete')->group(function () {
    Route::get('/button', fn() => Inertia::render('Templete/Button'))->name('templete.button');
    Route::get('/form', fn() => Inertia::render('Templete/Form'))->name('templete.form');
    Route::get('/table', fn() => Inertia::render('Templete/Table'))->name('templete.table');
    Route::get('/card', fn() => Inertia::render('Templete/Card'))->name('templete.card');
    Route::get('/pagination', fn() => Inertia::render('Templete/Pagination'))->name('templete.pagination');
});

// Grup Loket
// Route::prefix('loket')->group(function () {
//     Route::get('/', fn() => Inertia::render('Loket/Index'))->name('loket.index');
// });



// Grup Laporan

Route::prefix('laporan')
    ->middleware(['auth', 'role:owner,admin,loket'])
    ->group(function () {
        Route::get('loket', [\App\Http\Controllers\Laporan\LaporanLoketController::class, 'index'])->name('laporan.loket');
        Route::get('loket/tampilkan', [\App\Http\Controllers\Laporan\LaporanLoketController::class, 'tampil'])->name('laporan.loket.tampilkan-laporan-loket');

        Route::get('/rujukan', [\App\Http\Controllers\Laporan\Rujukan\RujukanController::class, 'index'])->name('laporan.rujukan');
        Route::inertia('umum', 'Laporan/Umum/Umum')->name('laporan.umum');
        Route::inertia('gigi', 'Laporan/Gigi/Gigi')->name('laporan.gigi');
        Route::inertia('kia', 'Laporan/Kia/Kia')->name('laporan.kia');
        Route::inertia('lab', 'Laporan/Lab/Lab')->name('laporan.lab');
        Route::match(['get', 'post'], '/laporan/kb', [\App\Http\Controllers\Laporan\Kb\KbController::class, 'index'])->name('laporan.kb');
        Route::get('/ugd', [\App\Http\Controllers\Laporan\Ugd\UgdController::class, 'index'])->name('laporan.ugd');
        Route::inertia('rawat-inap', 'Laporan/Rawat-inap/Rawat-inap')->name('laporan.rawat-inap');
        Route::inertia('kunjungan-sehat', 'Laporan/KunjunganSehat/Index')->name('laporan.kunjungan-sehat');
        Route::get('/sanitasi', [\App\Http\Controllers\Laporan\Sanitasi\SanitasiController::class, 'index'])->name('laporan.sanitasi');
        Route::get('/sanitasi/register', [\App\Http\Controllers\Laporan\Sanitasi\SanitasiController::class, 'registerSanitasi'])->name('laporan.sanitasi.register');
        Route::get('/sanitasi/sanitasi', [\App\Http\Controllers\Laporan\Sanitasi\SanitasiController::class, 'laporanSanitasi'])->name('laporan.sanitasi.laporan');
        Route::get('/sanitasi/kasus', [\App\Http\Controllers\Laporan\Sanitasi\SanitasiController::class, 'laporanKasus'])->name('laporan.sanitasi.kasus');
    });



// Grup Mal Sehat
Route::prefix('mal-sehat')->name('mal-sehat.')->group(function () {
    Route::inertia('/', 'MalSehat/Index')->name('index');

    // Kesling
    Route::prefix('kesling')->name('kesling.')->group(function () {
        Route::inertia('/', 'MalSehat/Kesling/Index')->name('index');
        Route::inertia('konseling-sanitasi', 'MalSehat/Kesling/KonselingSanitasi')->name('konseling');
        Route::inertia('pengukuran-kebugaran-haji', 'MalSehat/Kesling/PengukuranKebugaranHaji')->name('haji');
        Route::inertia('pengukuran-kebugaran-anak', 'MalSehat/Kesling/PengukuranKebugaranAnak')->name('anak');
        // Halaman detail "Belum Dilayani"
        Route::inertia('detail/{noUrut}', 'MalSehat/Kesling/DetailKonselingSanitasi')->name('detail');
    });

    // Kesga
    Route::prefix('kesga')->name('kesga.')->group(function () {
        Route::inertia('/', 'MalSehat/Kesga/Index')->name('index');
        Route::inertia('konselingcatin', 'MalSehat/Kesga/KonselingCatin')->name('konselingcatin');
        Route::inertia('konselinghaji', 'MalSehat/Kesga/KonselingHaji')->name('konselinghaji');
        Route::inertia('konselingimunisasi', 'MalSehat/Kesga/KonselingImunisasi')->name('konselingimunisasi');
        Route::inertia('konselinganak', 'MalSehat/Kesga/KonselingAnak')->name('konselinganak');
        Route::inertia('konselingibu', 'MalSehat/Kesga/KonselingIbu')->name('konselingibu');
        Route::inertia('konselingkb', 'MalSehat/Kesga/KonselingKB')->name('konselingkb');
        Route::inertia('konsultasigizi', 'MalSehat/Kesga/KonsultasiGizi')->name('konsultasigizi');
        Route::inertia('konsultasilansia', 'MalSehat/Kesga/KonsultasiLansia')->name('konsultasilansia');
    });

    // PTM
    Route::prefix('ptm')->name('ptm.')->group(function () {
        Route::inertia('/', 'MalSehat/PTM/Index')->name('index');
        Route::inertia('dashboard', 'MalSehat/PTM/Dashboard')->name('dashboard');
        Route::inertia('konselingberhentimerokok', 'MalSehat/PTM/KonselingBerhentiMerokok')->name('konselingberhentimerokok');
        Route::inertia('skriningfaktorrisiko', 'MalSehat/PTM/SkriningFaktorRisiko')->name('skriningfaktorrisiko');
    });

    // P3M
    Route::prefix('p3m')->name('p3m.')->group(function () {
        Route::inertia('/', 'MalSehat/P3M/Index')->name('index');
        Route::inertia('konselinghivaids', 'MalSehat/P3M/KonselingHivAids')->name('konselinghivaids');
        Route::inertia('konselinglroa', 'MalSehat/P3M/KonselingLROA')->name('konselinglroa');
        Route::inertia('konselingtb', 'MalSehat/P3M/KonselingPenyakitTB')->name('konselingtb');
    });

    // Yankes Primer
    Route::prefix('yankes-primer')->name('yankes-primer.')->group(function () {
        Route::inertia('/', 'MalSehat/YankesPrimer/Index')->name('index');

        // Route::get('kunjungankonsultasitradisional', [\App\Http\Controllers\MalSehat\YankesController::class, 'kunjunganKonsultasiTradisional'])
        //     ->name('kunjungankonsultasitradisional');

        // Route::get('kunjunganketerangansehat', [\App\Http\Controllers\MalSehat\YankesController::class, 'kunjunganKeteranganSehat'])
        //     ->name('kunjunganketerangansehat');
    });

    // Farmasi
    Route::prefix('farmasi')->name('farmasi.')->group(function () {
        Route::inertia('/', 'MalSehat/Farmasi/Index')->name('index');
        // Route::get('permintaanobat', [\App\Http\Controllers\MalSehat\FarmasiController::class, 'permintaanObat'])
        //     ->name('permintaanobat');

        // Route::get('permintaanobat/pelayanan/{no_mr}', [\App\Http\Controllers\MalSehat\FarmasiController::class, 'pelayanan'])
        //     ->name('permintaanobat.pelayanan')
        //     ->middleware('web');
    });

    // Biakes
    Route::prefix('biakes')->name('biakes.')->group(function () {
        Route::inertia('/', 'MalSehat/Biakes/Index')->name('index');
        Route::get('pembiayaanjaminansehat', [\App\Http\Controllers\MalSehat\BiakesController::class, 'pembiayaanJaminanSehat'])
            ->name('pembiayaanjaminansehat');
    });

    // Promkes
    Route::prefix('promkes')->name('promkes.')->group(function () {
        Route::inertia('/', 'MalSehat/Promkes/Index')->name('index');

        Route::get('kesehatanpeduliremaja', [\App\Http\Controllers\MalSehat\PromkesController::class, 'kesehatanPeduliRemaja'])
            ->name('kesehatanpeduliremaja');

        Route::get('kesehatanpeduliremaja/pelayanan/{no_mr}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'pelayanan'])
            ->name('kesehatanpeduliremaja.pelayanan')
            ->middleware('web');

        Route::get('diagnosa/list', [\App\Http\Controllers\MalSehat\PromkesController::class, 'getDiagnosa'])
            ->name('diagnosa.list');

        Route::get('tindakan/list', [\App\Http\Controllers\MalSehat\PromkesController::class, 'getTindakan'])
            ->name('tindakan.list');

        Route::get('surat-keterangan/{no_mr}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'suratKeterangan'])
            ->name('suratketerangan');

        Route::get('surat-rujukan/{no_mr}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'suratRujukan'])
            ->name('suratrujukan');

        Route::get('promkes/providers', [\App\Http\Controllers\MalSehat\PromkesController::class, 'getProviders'])
            ->name('promkes.providers');

        Route::get('riwayat-pasien/{no_mr}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'riwayatPasien'])
            ->name('riwayatpasien');
    });

    // Surat Keterangan API
    Route::prefix('promkes/surat-keterangan')->group(function () {
        Route::get('/list/{no_mr}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'getSuratKeterangan']);
        Route::post('/store', [\App\Http\Controllers\MalSehat\PromkesController::class, 'storeSuratKeterangan']);
        Route::put('/update/{id}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'updateSuratKeterangan']);
        Route::delete('/delete/{id}', [\App\Http\Controllers\MalSehat\PromkesController::class, 'deleteSuratKeterangan']);
    });


    // Lain-lain
    Route::inertia('home-visit', 'MalSehat/HomeVisit/Index')->name('home-visit');
    Route::inertia('sehat', 'MalSehat/Sehat/Index')->name('sehat');
    Route::inertia('sehat', 'MalSehat/Sehat/Index')->name('sehat');
    Route::inertia('sehat/pelayanan', 'MalSehat/Sehat/Pelayanan')->name('sehat.pelayanan');
    Route::inertia('rapid-test', 'MalSehat/RapidTest/Index')->name('rapid-test');
});
Route::prefix('ruang_layanan/simpus/kunjungan-online')
    ->name('kunj-online.')
    ->middleware(['auth']) // kalau mau wajib login
    ->group(function () {
        Route::get('/{idPoli?}/{klaster?}', [KunjOnlineController::class, 'index'])->name('index');
        Route::get('/pelayanan/{id}/{idPoli}/{idPelayanan}', [KunjOnlineController::class, 'pelayanan'])->name('pelayanan');
        Route::post('/pelayanan/anamnesa', [KunjOnlineController::class, 'setAnamnesa'])->name('setAnamnesa');
        Route::post('/pelayanan/anamnesa/objective', [KunjOnlineController::class, 'setAnamnesaObjective'])->name('setAnamnesaObjective');
        Route::post('/pelayanan/mulaiPelayanan', [KunjOnlineController::class, 'mulaiPemeriksaanPasien'])->name('mulai-pemeriksaan-pasien');
        Route::post('/pelayanan/diagnosa-medis', [KunjOnlineController::class, 'setDiagnosaMedis'])->name('diagnosa-medis');
        Route::get('/surat-rujukan/{id}', [KunjOnlineController::class, 'suratRujukan'])->name('surat-rujukan');
        Route::get('/{id}/surat-rujukan/create', [KunjOnlineController::class, 'createSuratRujukan'])->name('surat-rujukan.create');
        Route::post('/{id}/surat-rujukan', [KunjOnlineController::class, 'storeSuratRujukan'])->name('surat-rujukan.store');
        Route::get('/{id}/riwayat-kesehatan', [KunjOnlineController::class, 'riwayatKesehatan'])->name('riwayat-kesehatan');
        Route::get('/{id}/cppt', [KunjOnlineController::class, 'cppt'])->name('cppt');
    });

Route::prefix('ruang_layanan')->middleware(['auth'])
    ->group(function () {
        Route::get('simpus/popUpFormRujukLanjut', [PoliBpUmumController::class, 'popUpFormRujukLanjut'])->name('ruang-layanan.popUpFormRujukLanjut');

        // Skrining PTM
        Route::get('/simpus/skriningptm', [SkriningPTMController::class, 'index'])->name('ruang-layanan.ptm');
        Route::get('/simpus/skrining-ptm/pelayanan/{id}/{idPoli}/{idPelayanan}', [SkriningPTMController::class, 'pelayanan'])->name('ruang-layanan.skrining-ptm');
        Route::post('/simpus/skrining-ptm/update-status', [SkriningPTMController::class, 'updateStatus'])
            ->name('pelayanan.update-status');
        Route::post('/skrining-ptm/tindakan/simpan', [SkriningPTMController::class, 'simpanTindakan'])
            ->name('ptm.tindakan-simpan');
        Route::delete('/ptm/tindakan/{id}', [SkriningPTMController::class, 'tindakanHapus'])
            ->name('ptm.tindakan-hapus');
        Route::post('/simpus/skrining-ptm/tambah-kunjungan', [SkriningPTMController::class, 'tambahKunjunganPTM'])
            ->name('pelayanan.tambah-kunjungan-ptm');
        Route::post('/simpus/skrining-ptm/simpan-kunjungan', [SkriningPTMController::class, 'simpanKunjunganPTM'])
            ->name('pelayanan.simpan-kunjungan-ptm');
        Route::post('/simpus/skrining-ptm/simpan-assessment', [SkriningPTMController::class, 'addAssessmentPTM'])
            ->name('pelayanan.simpan-assessment-ptm');

        //Simpan rujuk
        Route::post('simpus/pelayanan/simpan-rujuk/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'simpanRujukan'])->name('ruang-layanan.simpanRujukan');
        Route::get('simpus/get-pelayanan/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'getPelayanan'])->name('ruang-layanan.ambilPelayanan');
        Route::delete('simpus/pelayanan/hapus-rujuk/{idpelayanan}', [PoliBpUmumController::class, 'hapusRujuk'])->name('ruang-layanan.hapusRujuk');
        Route::get('simpus/pelayanan/batal-berobat-jalan/{idLoket}/{idpelayanan}', [PoliBpUmumController::class, 'batalBerobatJalan'])->name('ruang-layanan.batal-berobat-jalan');

        // Menampilkan halaman poli
        Route::get('/simpus/poli', [indexController::class, 'listPoli'])->name('ruang-layanan.poli');
        Route::get('/simpus/poli/{kluster}', [indexController::class, 'listPoliKluster'])->name('ruang-layanan.poli-kluster');
        Route::get('simpus/getJumlahPasienKlusterPoli', [indexController::class, 'totalPasienKlusterAndPoli'])->name('ruang-layanan.totalPasienKlusterAndPoli');
        // Umum
        Route::get('/simpus/{idPoli?}/{kluster?}', [PoliBpUmumController::class, 'index'])->name('ruang-layanan.index');
        Route::get('/simpus/pelayanan/{id}/{idPoli}/{idPelayanan}/{kluster?}', [PoliBpUmumController::class, 'pelayanan'])->name('ruang-layanan.pelayanan');

        //Surat
        Route::get('/simpus/pelayananDetail/surat-rujuk/{idPoli}/{idPelayanan}', [PoliBpUmumController::class, 'suratRujukList'])->name('ruang-layanan.surat-rujuk');
        Route::get('/simpus/pelayananDetail/surat-rujuk-form/{idPoli}/{idPelayanan}', [PoliBpUmumController::class, 'suratRujukForm'])->name('ruang-layanan.surat-rujuk-form');
        Route::post('/simpus/pelayananDetail/simpan-rujukan/{idPoli}', [PoliBpUmumController::class, 'simpanSuratRujuk'])->name('ruang-layanan.simpan-rujukan');
        Route::get('simpus/pelayananDetail/cetak-rujukan/{idSurat}', [PoliBpUmumController::class, 'cetakRujukan'])->name('ruang-layanan.cetak-rujukan');
        Route::get('simpus/pelayananDetail/surat-rujukan-form-edit/{idPoli}/{idPelayanan}/{idSurat}', [PoliBpUmumController::class, 'suratRujukForm'])->name('ruang-layanan.surat-rujuk-form-edit');
        Route::post('simpus/pelayananDetail/update-surat-rujukan/{idPoli}/{idSurat}', [PoliBpUmumController::class, 'simpanSuratRujuk'])->name('ruang-layanan.update-rujukan');
        Route::post('simpus/pelayananDetail/hapus-surat-rujukan/{idSurat}', [PoliBpUmumController::class, 'hapusSuratRujukan'])->name('ruang-layanan.hapus-surat-rujukan');

        Route::inertia('/simpus/umum/form-surat-keterangan', 'Ruang_Layanan/Umum/form_surat_keterangan')->name('ruang-layanan-umum.form-surat-keterangan');

        //Anamnesa
        Route::post('simpus/pelayanan/set-anamnesa-subjective/{idLoket}', [PoliBpUmumController::class, 'setAnamnesaSubjective'])->name('ruang-layanan.setAnamnesaSubjective');
        Route::post('simpus/umum/pelayanan/set-anamnesa-objective/{idAnam}', [PoliBpUmumController::class, 'setAnamnesaObjective'])->name('ruang-layanan.setAnamnesaObjective');

        //Tindakan
        Route::post('simpus/pelayanan/tindakan/{idPoli}/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'setTindakan'])->name('ruang-layanan.simpan-Tindakan');
        Route::get('/master-tindakan', [indexController::class, 'paginasiSimpusTindakan'])->name('ruang-layanan.master-tindakan');
        Route::post('simpus/umum/pelayanan/mulaiPelayanan', [PoliBpUmumController::class, 'mulaiPemeriksaanPasien'])->name('ruang-layanan-umum.mulai-pemeriksaan-pasien');

        //Diagnosa
        Route::post('simpus/pelayanan/diagnosa-medis/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'setDiagnosaMedis'])->name('ruang-layanan.set-diagnosa-medis');
        Route::post('simpus/diagnosa/diagnosa-keperawatan-simpan/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'setDiagnosaKeperawatan'])->name(name: 'ruang-layanan.diagnosa-keperawatan');
        Route::get('/api/diagnosa-medis', [PoliBpUmumController::class, 'paginasi'])->name('api.diagnosa-medis');
        Route::delete('simpus/pelayanan/diagnosa-medis/{idDiagnosa}', [PoliBpUmumController::class, 'removeDiagnosaMedis'])->name('ruang-layanan.remove-diagnosa-medis');
        Route::delete('simpus/pelayanan/diagnosa-keperawatan{idDiagnosa}', [PoliBpUmumController::class, 'removeDiagnosaKeperawatan'])->name('ruang-layanan.remove-diagnosa-keperawatan');

        //Gizi
        Route::post('simpus/pelayanan/simpan-gizi/{idLoket}', [PoliBpUmumController::class, 'simpanGizi'])->name('ruang-layanan.simpan-gizi');

        //Simpan Sanitasi
        Route::post('simpus/pelayanan/simpan-sanitasi/{idPelayanan}', [PoliBpUmumController::class, 'simpanSanitasi'])->name('ruang-layanan.simpan-sanitasi');

        //Resep obat (Pengobatan Pasien)
        Route::post('simpus/pelayanan/resep-obat/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'setResepObat'])->name('ruang-layanan.set-resep-obat');
        Route::post('simpus/pelayanan/detail-resep-obat/{idResep}/{idObat}', [PoliBpUmumController::class, 'setDetailResepObat'])->name('ruang-layanan.set-detail-resep');
        Route::delete('simpus/hapus-resep-obat/{idResepObat}', [PoliBpUmumController::class, 'hapusResepObat'])->name('ruang-layanan.hapus-resep-obat');
        Route::post('simpus/hapus-detail-resep-obat/{idDetailResepObat}', [PoliBpUmumController::class, 'hapusDetailResepObat'])->name('ruang-layanan.hapus-detail-resep-obat');

        //Suket
        Route::get('simpus/pelayananDetail/surat-keterangan-list/{idPoli}/{idPelayanan}', [PoliBpUmumController::class, 'suketList'])->name('ruang-layanan.surat-keterangan-list');
        Route::get('simpus/pelayananDetail/create-surat-keterangan/{idPoli}/{idPelayanan}', [PoliBpUmumController::class, 'createSuratKeterangan'])->name('ruang-layanan.create-surat-keterangan');
        Route::post('simpus/pelayananDetail/simpan-surat-keterangan/{idPoli}', [PoliBpUmumController::class, 'simpanSuket'])->name('ruang-layanan.simpanSuket');
        Route::get('simpus/pelayananDetail/cetak-surat-keterangan/{idSurat}', [PoliBpUmumController::class, 'cetakSuket'])->name('ruang-layanan.cetak-suket');
        Route::post('simpus/pelayananDetail/hapus-surat-keterangan/{idSurat}', [PoliBpUmumController::class, 'hapusSuket'])->name('ruang-layanan.hapus-suket');
        Route::get('simpus/pelayananDetail/edit-surat-keterangan//{idPoli}/{idPelayanan}/{idSurat}', [PoliBpUmumController::class, 'editSuket'])->name('ruang-layanan.edit-suket');

        Route::post('simpus/update-surat-keterangan', [PoliBpUmumController::class, 'updateSuket'])->name('ruang-layanan.update-suket');
        Route::get('simpus/laborat/{idPoli}/{idLoket}/{idPelayanan}', [PoliBpUmumController::class, 'formLaborat'])->name('ruang-layanan.form-laborat');

        Route::post('simpus/umum/laborat/simpan-permohonan-lab/{idLoket}', [PoliBpUmumController::class, 'simpanPermohonanLab'])->name('ruang-layanan.simpan-permohonan-lab');
        Route::get('simpus/laborat/list-permohonan/{idLoket}', [indexController::class, 'getPermohonanLaborat'])->name('ruang-layanan.getPermonanLab');
        //riwayat pasien
        Route::get('simpus/riwayat-pasien/{idPoli}/{idPasien}', [PoliBpUmumController::class, 'riwayatPasien'])->name('ruang-layanan.riwayat-pasien');
        //UKK
        Route::get('simpus/pop-up/get-ukk/{idLoket}', [PoliBpUmumController::class, 'getUKK'])->name('ruang-layanan.get-ukk');
        Route::post('simpus/simpan-ukk/{idLoket}', [PoliBpUmumController::class, 'simpanUkk'])->name('ruang-layanan.simpan-ukk');
        //CPPT
        Route::get('simpus/cppt/{idPoli}/{idPasien}', [PoliBpUmumController::class, 'getCppt'])->name('ruang-layanan.cppt');



        //Gigi
        Route::get('/simpus/gigi', [PoliGigiController::class, 'index'])->name('ruang-layanan.gigi');
        Route::get('/simpus/gigi/pelayanan/{id}', [PoliGigiController::class, 'pelayanan'])->name('ruang-layanan-gigi.pelayanan');
        Route::post('simpus/gigi/pelayanan/anamnesa-subjective', [PoliGigiController::class, 'setAnamnesaSubjective'])->name('ruang-layanan-gigi.setAnamnesaSubjective');
        Route::post('simpus/gigi/pelayanan/anamnesa-objective', [PoliGigiController::class, 'setAnamnesaObjective'])->name('ruang-layanan-gigi.setAnamnesaObjective');
        //Diagnosa
        Route::post('simpus/gigi/pelayanan/diagnosa-medis', [PoliGigiController::class, 'setDiagnosaMedis'])->name('ruang-layanan-gigi.diagnosa-medis');
        Route::delete('simpus/gigi/pelayanan/diagnosa-medis/{id}', [PoliGigiController::class, 'removeDiagnosaMedis'])->name('ruang-layanan-gigi.remove-diagnosa-medis');

        Route::post('simpus/gigi/pelayanan/planning-tindakan', [PoliGigiController::class, 'setPlanningTindakan'])->name('ruang-layanan-gigi.set-PlanningTindakan');
        Route::delete('simpus/gigi/pelayanan/planning-tindakan/{id}', [PoliGigiController::class, 'removePlanningTindakan'])->name('ruang-layanan-gigi.remove-data-tindakan');
        Route::post('simpus/gigi/pelayanan/planning-pengobatan', [PoliGigiController::class, 'setPlanningPengobatan'])->name('ruang-layanan-gigi.set-PlanningPengobatan');
        Route::post('simpus/gigi/pelayanan/planning-pengobatan-detail', [PoliGigiController::class, 'setPlanningPengobatandetail'])->name('ruang-layanan-gigi.set-PlanningPengobatanDetail');
        Route::get('/master-obat', [indexController::class, 'MasterObat'])->name('ruang-layanan.master-obat');


        //Ranao 
        Route::inertia('/simpus/ranap', 'Ruang_Layanan/UGD/pasien_poli')->name('ruang-layanan.ranap');


        // 🔹 Kunjungan Online

        //Sanitasi
        Route::inertia('/simpus/sanitasi', 'Ruang_Layanan/Sanitasi/pasien_poli')->name('ruang-layanan.sanitasi');
        Route::inertia('/simpus/sanitasi/pelayanan', 'Ruang_Layanan/Sanitasi/pelayanan')->name('ruang-layanan.sanitasi.pelayanan');
        //Gizi
        Route::inertia('/simpus/gizi', 'Ruang_Layanan/Gizi/pasien_poli')->name('ruang-layanan.gizi');
        Route::inertia('/simpus/gizi/pelayanan', 'Ruang_Layanan/Gizi/pelayanan')->name('ruang-layanan.gizi.pelayanan');





        //Rawat Inap
        Route::inertia('/simpus/rawat-inap', 'Ruang_Layanan/RawatInap/index')->name('ruang-layanan.rawat-inap');
        Route::inertia('/simpus/rawat-inap/penerimaan-pasien', 'Ruang_Layanan/RawatInap/PenerimaanPasien/pasien_poli')->name('ruang-layanan.rawat-inap.penerimaan-pasien');
        Route::inertia('/simpus/rawat-inap/perawatan', 'Ruang_Layanan/RawatInap/DataKeperawatan/DataRanapKeperawatan')->name('ruang-layanan.rawat-inap.perawatan');
        Route::inertia('/simpus/rawat-inap/pengeluaran', 'Ruang_Layanan/RawatInap/PasienKeluar/DataPasienKeluar')->name('ruang-layanan.rawat-inap.pengeluaran');



        // // Skrining PTM
        // Route::inerta('/simpus/skrining-ptm', )
    });

//Laborat
// Route::inertia('/simpus/laborat', 'Ruang_Layanan/Laborat/index')->name('ruang-layanan.laborat');
Route::get('/simpus/laborat', [LaboratoriumController::class, 'index'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat');


Route::get('/simpus/laborat/pemeriksaan/{loketId}', [LaboratoriumController::class, 'pemeriksaan'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.pemeriksaan');

Route::post('/simpus/laborat/set-waktu-sample', [LaboratoriumController::class, 'setWaktuSample'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.setWaktuSample');

Route::post('/simpus/laborat/update-nilai', [LaboratoriumController::class, 'updateNilaiLab'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.updateNilaiLab');

// === endpoint untuk modal master/paket & cetak ===
Route::get('/simpus/laborat/paginasi-master-pemeriksaan', [LaboratoriumController::class, 'paginasiMasterPemeriksaan'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.paginasiMasterPemeriksaan');

Route::post('/simpus/laborat/permohonan/simpan', [LaboratoriumController::class, 'simpanPermohonan'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.simpanPermohonan');

Route::post('/simpus/laborat/pemeriksaan/simpan', [LaboratoriumController::class, 'pemeriksaanSimpan'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.pemeriksaanSimpan');

Route::post('/simpus/laborat/pemeriksaan/paket/{paket}', [LaboratoriumController::class, 'paketPemeriksaanSimpan'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':laborat'])
    ->name('ruang-layanan.laborat.paketPemeriksaanSimpan');

Route::get(
    '/simpus/laborat/detail/{idPermohonan}',
    [LaboratoriumController::class, 'detail']
)->name('ruang-layanan.laborat.detail');
// Paket dari parameter_uji
Route::get(
    '/simpus/laborat/param/headers',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramHeaders']
)->name('ruang-layanan.laborat.param.headers');

Route::get(
    '/simpus/laborat/param/{header}/subheaders',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSubheaders']
)->whereNumber('header')
    ->name('ruang-layanan.laborat.param.subheaders');

Route::post(
    '/simpus/laborat/param/{header}/simpan',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSimpan']
)->whereNumber('header')
    ->name('ruang-layanan.laborat.param.simpan');

Route::post('/simpus/laborat/tindakan/hapus', [LaboratoriumController::class, 'hapusTindakan'])->name('ruang-layanan.laborat.hapusTindakan');
// LIST semua parameter_uji (bisa search + filter paket) — paginated
Route::get(
    '/simpus/laborat/param/browse',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramBrowse']
)->name('ruang-layanan.laborat.param.browse');

// Simpan pilihan manual (by id_parameter[])
Route::post(
    '/simpus/laborat/param/simpan-terpilih',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSimpanTerpilih']
)->name('ruang-layanan.laborat.param.simpanTerpilih');

Route::get(
    '/simpus/laborat/param/{header}/subheaders',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSubheaders']
)->whereNumber('header')
    ->name('ruang-layanan.laborat.param.subheaders');

Route::post(
    '/simpus/laborat/param/{header}/simpan',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSimpan']
)->whereNumber('header')
    ->name('ruang-layanan.laborat.param.simpan');

Route::post('/simpus/laborat/tindakan/hapus', [LaboratoriumController::class, 'hapusTindakan'])->name('ruang-layanan.laborat.hapusTindakan');
// LIST semua parameter_uji (bisa search + filter paket) — paginated
Route::get(
    '/simpus/laborat/param/browse',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramBrowse']
)->name('ruang-layanan.laborat.param.browse');

// Simpan pilihan manual (by id_parameter[])
Route::post(
    '/simpus/laborat/param/simpan-terpilih',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'paramSimpanTerpilih']
)->name('ruang-layanan.laborat.param.simpanTerpilih');
// GET: daftar kategori + jumlah item per kategori
Route::get('/ruang_layanan/laborat/param/categories', [LaboratoriumController::class, 'paramCategories'])
    ->name('ruang-layanan.laborat.param.categories');

Route::post(
    '/ruang-layanan/laborat/hapus-semua',
    [\App\Http\Controllers\RuangLayanan\LaboratoriumController::class, 'hapusSemuaTindakan']
)->name('ruang-layanan.laborat.hapusSemuaTindakan');


// ================== KUNJUNGAN ONLINE (STANDALONE) ==================


// =================== HALAMAN OWNER (Inertia) ===================
Route::get('/owner', [PanelController::class, 'index'])
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':owner'])
    ->name('owner.panel');

// =================== API OWNER (session-based) =================
Route::prefix('api/owner')
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':owner'])
    ->group(function () {
        Route::get('/roles', [OwnerController::class, 'roles']);
        Route::get('/puskesmas', [OwnerController::class, 'puskesmas']);
        Route::get('/users', [OwnerController::class, 'users']);
        Route::post('/users', [OwnerController::class, 'storeUser']); // tambah user
        Route::patch('/users/{id}/roles', [OwnerController::class, 'updateRoles']);
        Route::post('/users/{id}/force-logout', [OwnerController::class, 'forceLogout']);
        Route::get('/online-users', [OwnerController::class, 'onlineUsers']); // opsional
    });
Route::post('/auth/password/force-update', [PasswordForceController::class, 'update'])
    ->middleware(['auth'])
    ->name('auth.password.force-update');
// =================== API OWNER (session-based) =================
Route::prefix('api/owner')
    ->middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':owner'])
    ->group(function () {
        Route::get('/roles', [OwnerController::class, 'roles']);
        Route::get('/puskesmas', [OwnerController::class, 'puskesmas']);
        Route::get('/users', [OwnerController::class, 'users']);
        Route::post('/users', [OwnerController::class, 'storeUser']); // tambah user
        Route::patch('/users/{id}/roles', [OwnerController::class, 'updateRoles']);
        Route::post('/users/{id}/force-logout', [OwnerController::class, 'forceLogout']);
        Route::get('/online-users', [OwnerController::class, 'onlineUsers']); // opsional
    });
Route::post('/auth/password/force-update', [PasswordForceController::class, 'update'])
    ->middleware(['auth'])
    ->name('auth.password.force-update');
// =================== JSON + PAGE: Log Hapus Pasien (Loket) ===================
Route::middleware(['auth', \App\Http\Middleware\Auth\CheckRole::class . ':owner'])
    ->group(function () {
        // JSON endpoint (dipakai halaman khusus log)
        Route::get('/owner/loket-delete-logs', [OwnerLogController::class, 'loketDeletes'])
            ->middleware('throttle:60,1')
            ->name('owner.loket-delete-logs');

        // Halaman khusus log (Inertia)
        Route::get('/owner/logs/loket-delete', function () {
            return Inertia::render('Owner/LoketDeleteLogs');
        })->name('owner.logs.loket');

        // ⬇️ ini yang baru
        Route::patch('/users/{id}/password-changed', [OwnerController::class, 'updatePasswordChanged'])
            ->name('owner.users.password_changed');
    });
Route::delete('/users/{id}', [OwnerController::class, 'destroyUser'])
    ->name('owner.users.destroy'); // ⬅️ beri nama


Route::get('/cek-db', function () {
    $tables = DB::select('SHOW TABLES');
    return response()->json($tables);
});
