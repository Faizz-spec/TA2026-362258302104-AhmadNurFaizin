<?php

namespace App\Http\Controllers\RuangLayanan\SkriningPTM;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\SkriningPTMService;
use App\Services\PelayananPTMService;
use App\Services\TindakanService;
use App\Http\Requests\SimpanTindakanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class SkriningPTMController extends Controller
{
    protected $skriningService;
    protected $pelayananService;
    protected $tindakanService;

    public function __construct(
        SkriningPTMService $skriningService,
        PelayananPTMService $pelayananService,
        TindakanService $tindakanService
    ) {
        $this->skriningService = $skriningService;
        $this->pelayananService = $pelayananService;
        $this->tindakanService = $tindakanService;
    }

    public function index(Request $request)
    {
        $dataPasien = $this->skriningService->getAllData($request);
        // dd($data);
        return Inertia::render('Ruang_Layanan/SkriningPTM/Index', $dataPasien);
    }

    public function pelayanan($id, $idPoli, $idPelayanan)
    {
        $DataPasien = $this->pelayananService->getData($id, $idPoli);
        $masterData = $this->pelayananService->getMasterData();
        $DataTindakan = $this->tindakanService->getTindakanPelayanan($idPelayanan);

        // dd($DataTindakan);

        return Inertia::render('Ruang_Layanan/SkriningPTM/Pelayanan', [
            'DataPasien' => $DataPasien,
            'DataTindakan' => $DataTindakan,
            'idPelayanan' => $idPelayanan,
            'idPoli' => $idPoli,
            ...$masterData,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $this->pelayananService->updateStatusPelayanan(
            $request->idpelayanan,
            $request->status
        );

        return back();
    }
    public function simpanTindakan(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idpelayanan' => 'required',
            'kode_tindakan' => 'required',
            'nama_tindakan' => 'required',
            'loketId' => 'required',
            'nama_tindakan_ind' => 'nullable',
            'keterangan' => 'nullable',
            'kdPoli' => 'required',
        ])->validate();

        $this->tindakanService->svTindakan($validated);

        return redirect()->back();
    }

    public function tambahKunjunganPTM(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idpelayanan' => 'required',
            'idLoket' => 'required',
            'nikPasien' => 'required',
            'tanggal_skrining' => 'required|date',
            'dokter' => 'required',
            'fasyankes' => 'required',
            'jenis_kunjungan' => 'required',
            'keluhan_utama' => 'required',
            'merokok' => 'required|string',
            'status_merokok' => 'required|string',
            'btg_rokok' => 'nullable|integer|min:0',
            'lama_rokok' => 'nullable|integer|min:0',
            'paparan_rokok' => 'nullable|string',
            'gula' => 'nullable|string',
            'garam' => 'nullable|string',
            'minyak' => 'nullable|string',
            'sayur' => 'nullable|string',
            'aktivitas' => 'nullable|string',
            'alkohol' => 'nullable|string',
            'r_pribadi_htn' => 'nullable|boolean',
            'r_pribadi_dm' => 'nullable|boolean',
            'r_pribadi_stroke' => 'nullable|boolean',
            'r_pribadi_jantung' => 'nullable|boolean',
            'r_keluarga_htn' => 'nullable|boolean',
            'r_keluarga_dm' => 'nullable|boolean',
            'r_keluarga_stroke' => 'nullable|boolean',
            'r_keluarga_jantung' => 'nullable|boolean',
            'obat' => 'nullable|string',
            'kesiapan' => 'nullable|string',
            'dukung' => 'nullable|string',
            'skor_faktor_risiko' => 'nullable|integer|min:0',
            'kategori_faktor_risiko' => 'nullable|string',
            'detail_faktor_risiko' => 'nullable|array',
        ])->validate();

        if ($this->pelayananService->kunjunganPTMExists($validated['idpelayanan'], $validated['idLoket'])) {
            return redirect()->back()->withErrors([
                'kunjungan_ptm' => 'Data kunjungan PTM pasien ini sudah tersimpan sebelumnya.',
            ]);
        }

        $this->pelayananService->addKunjunganPTM($validated);

        return redirect()->back();
    }

    public function addAssessmentPTM(Request $request){
        $validated = Validator::make($request->all(), [
            'skrining_ptm_id' => 'nullable|exists:skrining_ptm,id',
            'idpelayanan' => 'required_without:skrining_ptm_id',
            'masalah_hasil_skrining' => 'nullable|array',
            'ringkasan_temuan' => 'nullable|array',
            'diagnosis_utama' => 'nullable|string',
            'diagnosis_utama_saran' => 'nullable|string',
            'status_klinis' => 'nullable|string|max:50',
            'catatan_diagnosis' => 'nullable|string',
            'kategori_risiko' => 'nullable|string|max:50',
            'ringkasan_klinis' => 'nullable|string',
            'catatan_assessment' => 'nullable|string',
        ])->validate();

        $this->pelayananService->addAssessmentPTM($validated);

        return redirect()->back();
    }

    public function tindakanHapus($id)
    {
        $result = $this->tindakanService->hapusTindakan($id);

        if (!$result['success']) {
            return back()->withErrors(['message' => $result['message']]);
        }

        return back();
    }
}
