<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\DataMasterUnitDetail;
use App\Models\RuangLayanan\MasterDokter;
use App\Models\RuangLayanan\masterJenisUkk;
use App\Models\RuangLayanan\SetupKec;
use App\Models\RuangLayanan\SimpusAlergiData;
use App\Models\RuangLayanan\SimpusAnamnesa;
use App\Models\RuangLayanan\SimpusDataDiagnosa;
use App\Models\RuangLayanan\SimpusDetailResepObat;
use App\Models\RuangLayanan\SimpusDiagnosa;
use App\Models\RuangLayanan\SimpusLoket;
use App\Models\RuangLayanan\SimpusMasterObat;
use App\Models\RuangLayanan\SimpusPasien;
use App\Models\RuangLayanan\SimpusPelayanan;
use App\Models\RuangLayanan\SimpusPermohonanLab;
use App\Models\RuangLayanan\SimpusPoliFKTL;
use App\Models\RuangLayanan\SimpusPoliFKTP;
use App\Models\RuangLayanan\SimpusProvider;
use App\Models\RuangLayanan\SimpusResepObat;
use App\Models\RuangLayanan\SimpusTindakan;
use App\Models\RuangLayanan\StatusPulang;
use App\Models\RuangLayanan\SuratKeterangan;
use App\Models\RuangLayanan\SuratMaster;
use App\Models\RuangLayanan\SuratRujuk;
use App\Models\RuangLayanan\Ukk;
use App\Models\RuangLayanan\UnitProfile;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Log;
use Validator;


class PoliBpUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $idPoli, $kluster = null)
    {
        // dd($request->wantsJson());
        // dd($tglKunjungan);
        //dd($kluster);
        $userAuth = Auth()->user();
        $DataUnit = DataMasterUnitDetail::with('DataMasterUnit')
            ->where('id_unit', $userAuth->unit)
            ->orderBy('id_kategori')
            ->get();

        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->leftJoin('simpus_statuspulang as sp' , 'pel.kdStatusPulang', '=', 'sp.kdStatusPulang')
            ->leftJoin('simpus_poli_fktp as poliTujuan', 'pel.tujuanPoli', '=','poliTujuan.kdPoli')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'pel.kdPoli')
            ->join('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->join('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->join('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->join('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')
            ->where('pel.kdPoli', $idPoli)
            ->where('l.puskId', $userAuth->unit);

        if ($request->tglKunjungan) {
            $DataPasien->where('pel.tglPelayanan', $request->tglKunjungan);
        } else {
            $DataPasien->where('pel.tglPelayanan', now()->toDateString());
        }
        if ($request->unit) {
            $DataPasien->where('l.unitId', $request->unit);
        }
        if ($kluster == '2') {
            $DataPasien->where('l.umur', '<=', 17);
        } elseif ($kluster == '3') {
            $DataPasien->where('l.umur', '>', 17);
        }

        $DataPasien = $DataPasien->select(
            'pel.idpelayanan',
            'pel.tglPelayanan',
            'pel.sudahDilayani',
            'pel.kunjSakitPel',
            'pel.kdPoli',
            'sp.nmStatusPulang',
            'p.NO_MR',
            'p.NAMA_LGKP',
            'p.NIK',
            'kel.nama_kel',
            'kec.nama_kec',
            'kab.nama_kab',
            'prop.nama_prop',
            'poli.nmPoli',
            'poli.poliSakit',
            'poliTujuan.nmPoli as poliTujuan',
            'p.alamat',
            'p.no_rt',
            'p.no_rw',
            'l.tglKunjungan',
            'l.idLoket',
            'l.umur',
            'l.unitId',
        )->orderBy('pel.tglPelayanan', 'desc')
            ->paginate(10);
        // dd($DataPasien);
        if ($request->wantsJson()) {
            return response()->json([
                'DataPasien' => $DataPasien,
                'kluster' => $kluster,
                'kdPoli' => $idPoli,
                'tgl' => $request->tglKunjungan,
                'unit' => $request->unit
            ]);
        }
        $data = [
            'DataUnit' => $DataUnit,
            'DataPasien' => $DataPasien,
            'kluster' => $kluster,
            'kdPoli' => $idPoli
        ];
        //dd($idPoli);
        if ($idPoli == '008') {
            return Inertia::render('Ruang_Layanan/KB/pasien_poli', $data);
        } elseif ($idPoli == '001') {
            return Inertia::render('Ruang_Layanan/Umum/pasien_poli', $data);
        } elseif ($idPoli == '002') {
            return Inertia::render('Ruang_Layanan/Gigi/pasien_poli', $data);
        } elseif ($idPoli == '997') {
            return Inertia::render('Ruang_Layanan/Gizi/pasien_poli', $data);
        } elseif ($idPoli == '097') {
            return Inertia::render('Ruang_Layanan/Sanitasi/pasien_poli', $data);
        } elseif ($idPoli == '098') {
            return Inertia::render('Ruang_Layanan/RawatInap/index', $data);
        } else {
            return Inertia::render('Ruang_Layanan/UGD/pasien_poli', $data);
        }
    }
    public function pelayanan($id, $idPoli, $idpelayanan, $kluster = null)
    {
        // dd($idPoli);
        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('pkrjn_master as pkrjn', 'pkrjn.NO', '=', 'p.JENIS_PKRJN')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'pel.kdPoli')
            ->join('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->join('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->join('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->join('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')
            // ->where('l.kdPoli', $idPoli)
            ->where('idLoket', $id)
            ->where('pel.idPelayanan', $idpelayanan)
            ->select(
                'p.NO_MR',
                'p.NAMA_LGKP',
                'p.NIK',
                'p.ID',
                'kel.nama_kel',
                'kec.nama_kec',
                'kab.nama_kab',
                'prop.nama_prop',
                'poli.nmPoli',
                'pel.kdPoli',
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'p.jenis_klmin',
                'l.umur',
                'l.umur_bulan',
                'umur_hari',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kunjSakit',
                'pkrjn.DESCRIP',
                'pel.pelIdSebelum',
                'pel.tujuanPoli'
            )
            ->first();
        //  dd($DataPasien);
        if ($DataPasien->pelIdSebelum) {
            $poliAwal = DB::table('simpus_pelayanan as pel')
                ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'pel.kdPoli')
                ->where('idPelayanan', $DataPasien->pelIdSebelum)
                ->select('pel.kdPoli', 'poli.nmPoli')
                ->first();
            //dd($poliAwal);

            $DataPasien->poliAwal = $poliAwal;
        } else {
            // Kalau DataPasien null atau tujuanPoli kosong, tetap kasih properti poliAwal biar nggak error di view
            $DataPasien->poliAwal = null;
        }

        //dd($poliAwal);
        // dd($DataPasien);
        $DataAnamnesa = DB::table('simpus_anamnesa')->where('loketId', $DataPasien->idLoket)->first();
        $DataKesadaran = DB::table('simpus_kesadaran')->get();
        $DiagnosaKasus = DB::table('master_diagnosa_kasus')->get();
        $MasterAlergi = DB::table('master_alergi')->get();
        $SimpusDataDiagnosa = SimpusDataDiagnosa::with(['SimpusPoliFKTP', 'MasterDiagnosaKasus', 'SimpusDiagnosa'])->where('loketId', $DataPasien->idLoket)->whereNotNull('kdDiagnosa')->get();
        $SimpusTindakan = SimpusTindakan::where('loketId', $DataPasien->idLoket)->where('deskripsi', 'icd9cm')->with('SimpusPoli')->get();
        //  dd($SimpusTindakan);
        $AlergiPasien = SimpusAlergiData::with(['alergiObat', 'alergiMakanan'])->where('pasienId', $DataPasien->ID)->first();
        $StatusPulang = StatusPulang::where('rawatInap', 'false')->whereNotIn('kdStatusPulang', [12, 4, 6])->get();
        $SimpusResepObat = SimpusResepObat::with('DetailResepObat.MasterObat', 'Loket.SimpusPoli')->where('loketId', $DataPasien->idLoket)->get();
        $MasterObat = SimpusMasterObat::all();
        //dd($SimpusDataDiagnosa);
        $TenagaMedisAskep = MasterDokter::whereNotIn('profesi_id', [19, 20])->where('aktif', '1')->get();
        $TenagaMedis = MasterDokter::where('profesi_id', [19, 20])->where('aktif', '1')->get();
        $MasterDiagnosaKeperawatan = SimpusDiagnosa::where('kategori', 1)->get();
        $diagnosaKeperawatan = SimpusDataDiagnosa::where('loketId', $DataPasien->idLoket)->whereNull('kdDiagnosa')->get();
        $DataRujuk = SimpusPelayanan::with(['SimpusLoket', 'SimpusPoli', 'StatusPulang'])->where('loketId', $DataPasien->idLoket)->orderBy('createdDate', 'desc')->get();
        $poliRujukInternal = SimpusPoliFKTP::where('rujukIntern', 'TRUE')->get();
        $pelayanan = SimpusPelayanan::where('idpelayanan', $idpelayanan)->first();
        $masterTacc = DB::table('master_tacc')->get();
        //dd($poliRujukInternal);
        $data = [
            'DataPasien' => $DataPasien,
            'DataAnamnesa' => $DataAnamnesa,
            'DataKesadaran' => $DataKesadaran,
            'DiagnosaKasus' => $DiagnosaKasus,
            'MasterAlergi' => $MasterAlergi,
            'SimpusDataDiagnosa' => $SimpusDataDiagnosa,
            'SimpusTindakan' => $SimpusTindakan,
            'AlergiPasien' => $AlergiPasien,
            'StatusPulang' => $StatusPulang,
            'SimpusResepObat' => $SimpusResepObat,
            'MasterObat' => $MasterObat,
            'idPelayanan' => $idpelayanan,
            'TenagaMedisAskep' => $TenagaMedisAskep,
            'TenagaMedis' => $TenagaMedis,
            'MasterDiagnosaKeperawatan' => $MasterDiagnosaKeperawatan,
            'diagnosaKeperawatan' => $diagnosaKeperawatan,
            'DataRujuk' => $DataRujuk,
            'idPoli' => $idPoli,
            'poliRujukInternal' => $poliRujukInternal,
            'pelayanan' => $pelayanan,
            'masterTacc' => $masterTacc,
            'kluster' => $kluster
        ];
        // dd($data);
        if ($idPoli == '001') {
            return Inertia::render('Ruang_Layanan/Umum/pelayanan', $data);
        } elseif ($idPoli == '008') {
            return Inertia::render('Ruang_Layanan/KB/pelayanan', $data);
        } elseif ($idPoli == '002') {
            return Inertia::render('Ruang_Layanan/Gigi/pelayanan', $data);
        } elseif ($idPoli == '997') {
            $gizi = DB::table('simpus_gizi')->where('loketId', $DataPasien->idLoket)->first();
            $data['gizi'] = $gizi;
            return Inertia::render('Ruang_Layanan/Gizi/pelayanan', $data);
        } elseif ($idPoli == '097') {
            $sanitasi = DB::table('simpus_sanitasi')->where('pelayananId', $idpelayanan)->first();
            $data['sanitasi'] = $sanitasi;
            return Inertia::render('Ruang_Layanan/Sanitasi/pelayanan', $data);
        } else {
            return Inertia::render('Ruang_Layanan/UGD/pelayanan', $data);
        }
    }

    public function setAnamnesaSubjective(Request $request, $idLoket)
    {
        //  dd($request->all());
        $request->validate([
            'tgl_anamnesa' => 'required|date',
            'keluhan_utama' => 'required|string',
            'keluhan_tambahan' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'riwayat_penyakit_sekarang' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'riwayat_penyakit_dahulu' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'riwayat_penyakit_keluarga' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'tindakan' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'obat_digunakan' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'obat_dikonsumsi' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
        ], [
            'keluhan_utama.required' => 'Keluhan Wajib Diisi',
            'keluhan_tambahan.regex' => 'Wajib Diisi Dengan Huruf',
            'riwayat_penyakit_sekarang.regex' => 'Wajib Diisi Dengan Huruf',
            'riwayat_penyakit_dahulu.regex' => 'Wajib Diisi Dengan Huruf',
            'riwayat_penyakit_keluarga.regex' => 'Wajib Diisi Dengan Huruf',
            'tindakan.regex' => 'Wajib Diisi Dengan Huruf',
            'obat_digunakan.regex' => 'Wajib Diisi Dengan Huruf',
            'obat_dikonsumsi.regex' => 'Wajib Diisi Dengan Huruf',

        ]);

        $data = [
            'tglAnamnesa' => $request->tgl_anamnesa,
            'keluhan' => $request->keluhan_utama,
            'keluhanTambahan' => $request->keluhan_tambahan,
            'riwayatPenyakitSekarang' => $request->riwayat_penyakit_sekarang,
            'riwayatPenyakitDahulu' => $request->riwayat_penyakit_dahulu,
            'riwayatPenyakitKeluarga' => $request->riwayat_penyakit_keluarga,
            'terapiYangPernahDijalani' => $request->tindakan,
            'obatSeringDigunakan' => $request->obat_digunakan,
            'obatSeringDikonsumsi' => $request->obat_dikonsumsi
        ];
        $dataAnamnesa = SimpusAnamnesa::where('loketId', $idLoket)->first();
        if (!$dataAnamnesa) {
            SimpusAnamnesa::create(array_merge($data, [
                'idAnamnesa' => Str::uuid(),
                'loketId' => $idLoket,
                'CreatedBy' => Auth()->user()->username
            ]));
        }
        SimpusAnamnesa::where('loketId', $idLoket)->update($data);

        $AlergiPasien = SimpusAlergiData::where('pasienId', $request->idPasien)->first();
        // dd( $AlergiPasien);
        if (!$AlergiPasien) {
            SimpusAlergiData::create([
                'idAlergi' => Str::random(20),
                'pasienId' => $request->idPasien,
                'kodeAlergiObat' => $request->alergi_obat,
                'kodeAlergiMakanan' => $request->alergi_makanan,
                'keterangan' => $request->keterangan_alergi
            ]);
        } else {
            $AlergiPasien->update([
                'kodeAlergiObat' => $request->alergi_obat,
                'kodeAlergiMakanan' => $request->alergi_makanan,
                'keterangan' => $request->keterangan_alergi
            ]);
        }
        return redirect()->back();
    }
    public function setAnamnesaObjective(Request $request, $idAnam)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'keadaan_umum' => 'required',
            'kesadaran' => 'required',
            'imt' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'lingkar_perut' => 'required|numeric',
            'lingkar_lengan' => 'required|numeric',
            'sistole' => 'required|numeric',
            'diastole' => 'required|numeric',
            'resp_rate' => 'required|numeric',
            'heart_rate' => 'required|numeric',
            'suhu' => 'required|numeric',
            'tenaga_medis_askep' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $anamnesa = DB::table('simpus_anamnesa')
            ->where('idAnamnesa', $idAnam)
            ->first();
        $dataUpdate = [
            'keadaanUmum' => $request->keadaan_umum ?? $anamnesa->keadaanUmum,
            'kdSadar' => $request->kesadaran ?? $anamnesa->kdSadar,
            'imt' => $request->imt ?? $anamnesa->imt,
            'tinggiBadan' => $request->tinggi_badan ?? $anamnesa->tinggiBadan,
            'beratBadan' => $request->berat_badan ?? $anamnesa->beratBadan,
            'lingkarPerut' => $request->lingkar_perut ?? $anamnesa->lingkarPerut,
            'lingkarTangan' => $request->lingkar_lengan ?? $anamnesa->lingkarTangan,
            'sistole' => $request->sistole ?? $anamnesa->sistole,
            'diastole' => $request->diastole ?? $anamnesa->diastole,
            'respRate' => $request->resp_rate ?? $anamnesa->respRate,
            'heartRate' => $request->heart_rate ?? $anamnesa->heartRate,
            'suhu' => $request->suhu ?? $anamnesa->suhu,

            'thoraxJantung' => $request->jantung ?? $anamnesa->thoraxJantung,
            'thoraxJantungKet' => $request->ket_jantung ?? $anamnesa->thoraxJantungKet,

            'thoraxPulmo' => $request->pulmo ?? $anamnesa->thoraxPulmo,
            'thoraxPulmoKet' => $request->ket_pulmo ?? $anamnesa->thoraxPulmoKet,

            'abdomanAtas' => $request->abdomen_atas ?? $anamnesa->abdomanAtas,
            'abdomanAtasKet' => $request->ket_abdomen_atas ?? $anamnesa->abdomanAtasKet,

            'abdomanBawah' => $request->abdomen_bawah ?? $anamnesa->abdomanBawah,
            'abdomanBawahKet' => $request->ket_abdomen_bawah ?? $anamnesa->abdomanBawahket,

            'extrimitasAtas' => $request->extrimitas_atas ?? $anamnesa->extrimitasAtas,
            'extrimitasAtasKet' => $request->ket_extrimitas_atas ?? $anamnesa->extrimitasAtasKet,

            'extrimitasBawah' => $request->extrimitas_bawah ?? $anamnesa->extrimitasBawah,
            'extrimitasBawahKet' => $request->ket_extrimitas_bawah ?? $anamnesa->extrimitasBawahKet,

            'kepala' => $request->kepala ?? $anamnesa->kepala,
            'kepalaKet' => $request->ket_kepala ?? $anamnesa->kepalaKet,

            'mata' => $request->mata ?? $anamnesa->mata,
            'mataKet' => $request->ket_mata ?? $anamnesa->mataKet,

            'telinga' => $request->telinga ?? $anamnesa->telinga,
            'telingaKet' => $request->ket_telinga ?? $anamnesa->telingaKet,

            'leher' => $request->leher ?? $anamnesa->leher,
            'leherKet' => $request->ket_leher ?? $anamnesa->leherKet,

            'tenagaMedisAskep' => $request->tenaga_medis_askep ?? $anamnesa->tenagaMedisAskep,
            'perkusi' => $request->perkusi,
            'druk' => $request->druk,
            'palpasi' => $request->palpasi,
            'luxasi' => $request->luxasi,
            'vitalitas' => $request->vitalitas,
            'statusPasien' => $request->statusPasien
        ];
        DB::table('simpus_anamnesa')
            ->where('idAnamnesa', $idAnam)
            ->update($dataUpdate);
        return redirect()->back();
    }

    public function mulaiPemeriksaanPasien(Request $request)
    {
        //dd($request->all());
        // DB::table('simpus_anamnesa')->insert([
        //     'loketid' => $request->idLoket,
        //     'idAnamnesa' => Str::uuid(),
        //     'tglAnamnesa' => $request->tglKunjungan
        // ]);
        $sudahDilayani = '2';
        $progressTime = now()->format('Y-m-d H:i:s');
        SimpusPelayanan::where('idpelayanan', $request->idPelayanan)
            ->update([
                'sudahDilayani' => $sudahDilayani,
                'progressTime' => $progressTime,
            ]);

        return redirect()->back();
    }

    public function setDiagnosaMedis(Request $request, $idLoket, $idPelayanan)
    {
        // dd($request);

        $request->validate([
            'kode_diagnosa' => 'required',
            'nama_diagnosa' => 'required',
            'kunjungan_khusus' => 'required',

        ], [
            'kode_diagnosa.required' => 'Kode Diagnosa wajib diisi',
            'nama_diagnosa.required' => 'Nama Diagnosa wajib diisi',
            'kunjungan_khusus.required' => 'Diagnosa Kasus wajib diisi'
        ]);
        SimpusDataDiagnosa::create([
            'kdDiagnosa' => $request->kode_diagnosa,
            'nmDiagnosa' => $request->nama_diagnosa,
            'diagnosaKasus' => $request->kunjungan_khusus,
            'keterangan' => $request->keterangan_kunjungan,
            'kdPoli' => $request->kdPoli,
            'loketId' => $idLoket,
            'pelayananId' => $idPelayanan,
        ]);
        return redirect()->back();
    }

    public function setDiagnosaKeperawatan(Request $request, $idLoket, $idPelayanan)
    {
        //    dd($request->all());
        $request->validate([
            'kode_diagnosa' => 'required',
            'nama_diagnosa' => 'required',
        ], ['nama_diagnosa.required' => 'Nama Diagnosa wajib diisi', 'kode_diagnosa.required' => 'Kode Diagnosa wajib diisi']);


        SimpusDataDiagnosa::create([
            'nmDiagnosa' => $request->nama_diagnosa,
            'loketId' => $idLoket,
            'pelayananId' => $idPelayanan,
        ]);
        return redirect()->back();
    }

    public function removeDiagnosaMedis($idDiagnosa)
    {
        //dd($idLoket);
        SimpusDataDiagnosa::where('idDiagnosa', $idDiagnosa)->delete();
        return redirect()->back();
    }
    public function removeDiagnosaKeperawatan($idDiagnosa)
    {
        SimpusDataDiagnosa::where('idDiagnosa', $idDiagnosa)->delete();
        return redirect()->back();
    }

    public function setTindakan(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'kode_tindakan' => 'required',
            'nama_tindakan' => 'required',
            'keterangan_tindakan' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',
            'keterangangigi' => 'nullable|string|regex:/^[A-Za-z\s.,-]+$/',

        ], [
            'kode_tindakan.required' => 'Kode Tindakan wajib diisi!',
            'nama_tindakan.required' => 'Nama Tindakan wajib diisi!',
            'keterangan_tindakan.regex' => 'Inputan harus huruf',
            'keterangangigi.regex' => 'Inputan Harus huruf'

        ]);
        SimpusTindakan::create([
            'idPelayanan' => $request->idPelayanan,
            'loketId' => $request->loketId,
            'kdTindakan' => $request->kode_tindakan,
            'nmTindakan' => $request->nama_tindakan,
            'nmTindakanInd' => $request->nama_tindakan_indonesia,
            'keterangan' => $request->keterangan_tindakan,
            'kdPoli' => $request->kdPoli,
            'ketGigi' => $request->keterangangigi,
            'deskripsi' => 'icd9cm',
            'createdBy' => Auth()->user()->username
        ]);
        return redirect()->back();
    }
    public function paginasi(Request $request)
    {
        $search = $request->get('search');

        // query awal
        $query = DB::table('simpus_diagnosa');

        // kalau ada search, filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nmDiag', 'like', "%{$search}%")
                    ->orWhere('kdDiag', 'like', "%{$search}%");
            });
        }

        $DiagnosaMedis = $query->paginate(10);

        // simpan query string search supaya ikut di pagination
        $DiagnosaMedis->appends(['search' => $search]);

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

    public function setResepObat(Request $request, $idLoket, $idPelayanan)
    {
        //  dd($request->all());

        if ($request->jenis_obat === '1') {
            $request->validate([
                'jumlah_puyer' => 'required',
                'dosis_pakai' => 'required',
                'dosis_pakai_jam' => 'required',
                'waktu' => 'required',
                'kondisi' => 'required'
            ], [
                'jumlah_puyer.required' => 'jumlah wajib diisi',
                'dosis_pakai.required' => 'dosis pakai wajib diisi',
                'dosis_pakai_jam.required' => 'dosis pakai wajib diisi',
                'waktu.required' => 'waktu wajib diisi',
                'kondisi.required' => 'kondisi wajib diisi'
            ]);
        }
        SimpusResepObat::create([
            'id_resep' => Str::uuid(),
            'kode_resep' => Str::random(10),
            'loketId' => $idLoket,
            'pelayananId' => $idPelayanan,
            'kategori' => $request->jenis_obat,
            'nama_puyer' => $request->jenis_obat === '1' ? 'Puyer' : ($request->jenis_obat === '0' ? 'Bukan Puyer' : null),
            'jumlah_puyer' => $request->jumlah_puyer,
            'dosis_pakai_puyer' => $request->dosis_pakai,
            'tiapJam' => $request->dosis_pakai_jam,
            'waktu' => is_array($request->waktu) ? implode(',', $request->waktu) : $request->waktu,
            'kondisi' => is_array($request->kondisi) ? implode(',', $request->kondisi) : $request->kondisi,
        ]);
        return redirect()->back();
    }
    public function setDetailResepObat(Request $request, $idResep, $idObat)
    {
        // dd($request->all());
        if ($request->kategori === '0') {
            $request->validate([
                'obat_id' => 'required',
                'jumlah_obat' => 'required',
                'dosis_pakai' => 'required',
                'dosis_pakai_jam' => 'required',
                'waktu' => 'required',
                'kondisi' => 'required',
            ], [
                'obat_id.required' => 'Obat wajib dipilih.',
                'jumlah_obat.required' => 'Jumlah obat wajib diisi.',
                'dosis_pakai.required' => 'Dosis pakai wajib diisi.',
                'dosis_pakai_jam.required' => 'Keterangan jam wajib diisi.',
                'waktu.required' => 'Waktu wajib diisi.',
                'kondisi.required' => 'Kondisi wajib diisi.',
            ]);
        } else {
            $request->validate([
                'jumlah_obat' => 'required',
            ], [
                'jumlah_obat.required' => 'Jumlah obat wajib diisi.',
            ]);

        }

        $data = [
            'id_resep_detail' => Str::uuid(),
            'resep_id' => $idResep,
            'poli' => $request->poli,
            'obat_id' => $idObat,
            'jumlah' => $request->jumlah_obat,
        ];
        if ($request->kategori === '0') {
            $data = array_merge($data, [
                'jumlah_puyer' => $request->jumlah_puyer,
                'dosis_pakai' => $request->dosis_pakai,
                'tiapJam' => $request->dosis_pakai_jam,
                'keterangan' => $request->keterangan,
                'waktu' => is_array($request->waktu) ? implode(',', $request->waktu) : $request->waktu,
                'kondisi' => is_array($request->kondisi) ? implode(',', $request->kondisi) : $request->kondisi,
            ]);
        }

        SimpusDetailResepObat::create($data);
        return redirect()->back();
    }
    public function hapusResepObat($idResepObat)
    {
        // dd($idResepObat);
        SimpusResepObat::where('id_resep', $idResepObat)->delete();
        SimpusDetailResepObat::where('resep_id', $idResepObat)->delete();
        return redirect()->back();
    }
    public function hapusDetailResepObat($idDetailResepObat)
    {
        SimpusDetailResepObat::where('id_resep_detail', $idDetailResepObat)->delete();
        return redirect()->back();
    }

    public function simpanRujukan(Request $request, $idLoket, $idPelayanan)
    {
        // dd($request->all());
        // dd($request->status_pulang == 5);

        $rules = [
            'status_pulang' => 'required',
            'tenaga_medis' => 'required',

        ];
        $messages = [
            'status_pulang.required' => 'status pulang wajib diisi',
            'tenaga_medis.required' => 'Tenaga medis wajib diisi',
        ];

        if ($request->status_pulang == 5) {
            $rules['poli_rujuk_internal'] = 'required';
            $messages['poli_rujuk_internal.required'] = 'poli rujuk internal wajib diisi';
        }

        $request->validate($rules, $messages);
        if ($request->status_pulang == 5) {
            $insert = [
                'tglPelayanan' => now()->toDateString(),
                'kdPoli' => $request->poli_rujuk_internal,
                'startTime' => now(),
                'loketId' => $idLoket,
                'idpelayanan' => Str::uuid(),
                'pelIdSebelum' => $idPelayanan,
                'kunjSakitPel' => true,
                'createdBy' => auth()->user()->username,
                'tenagaMedis' => $request->tenaga_medis,
            ];

            // Simpan ke database (misalnya tabel `pelayanan`)
            SimpusPelayanan::create($insert);
            SimpusPelayanan::where('idPelayanan', $idPelayanan)->update([
                'sudahDilayani' => 1,
                'kdStatusPulang' => $request->status_pulang,
                'tenagaMedis' => $request->tenaga_medis,
                'tujuanPoli' => $request->poli_rujuk_internal,
                'endTime' => now()
            ]);
            if($request->poli_rujuk_internal === '098'){
                DB::table('simpus_ranap')->insert([
                    'pelayananId' => $insert['idpelayanan'],
                    'createdDate' => now(),
                    'createdBy' => Auth()->user()->username,
                    'modifiedDate' => null
                ]);
            }

        } elseif ($request->status_pulang == 6) {
        } else {
            SimpusPelayanan::where('idPelayanan', $idPelayanan)->update([
                'sudahDilayani' => 1,
                'kdStatusPulang' => $request->status_pulang,
                'tenagaMedis' => $request->tenaga_medis,
                'endTime' => now()
            ]);
        }
        return redirect()->back();
    }

    public function suketList($idPoli, $idPelayanan)
    {
        $suketList = SuratKeterangan::where('id_pelayanan', $idPelayanan)->with(['jenisSurat', 'SimpusPelayanan.SimpusLoket'])->get();
        //dd($suketList);
        return Inertia::render('Ruang_Layanan/Umum/suratKeterangan', [
            'suketList' => $suketList,
            'idPoli' => $idPoli,
            'idPelayanan' => $idPelayanan
        ]);
    }

    public function createSuratKeterangan($idPoli, $idPelayanan)
    {
        //dd($idPoli);
        $jenisSurat = SuratMaster::get();
        $pelayanan = SimpusPelayanan::where('idPelayanan', $idPelayanan)->first();
        // dd($pelayanan);
        $dataAnamnesa = SimpusAnamnesa::with(['loket', 'loket.SimpusPasien.SetupKab', 'loket.SimpusPasien.SetupKel', 'loket.SimpusPasien.pkrjnmaster'])->where('loketId', $pelayanan->loketId)->first();
        // dd($dataAnamnesa);
        $TenagaMedisAskep = MasterDokter::whereIn('profesi_id', [19, 20])->where('aktif', 1)->get();
        if ($dataAnamnesa) {
            return Inertia::render('Ruang_Layanan/Umum/SuratKeteranganCreate', [
                'jenisSurat' => $jenisSurat,
                'idPoli' => $idPoli,
                'idPelayanan' => $idPelayanan,
                'dataAnamnesa' => $dataAnamnesa,
                'tenagaMedisAskep' => $TenagaMedisAskep
            ]);
        }
        redirect()->back()->with('message', 'Isikan Terlebih Dahulu Data Pemeriksaan');

    }

    public function simpanSuket(Request $request, $idPoli)
    {
        //  dd($request->all());
        $request->validate([
            'id_jns_surat' => 'required',
            'no_surat' => 'required',
            'tenagaMedis' => 'required',
            'hasil_pemeriksaan' => 'required'
        ], [
            'id_jns_surat.required' => 'Jenis Surat Wajib Dipilih',
            'no_surat.required' => 'No Surat Wajib Diisi',
            'tenagaMedis.required' => 'Tenaga Medis Wajib Dipilih',
            'hasil_pemeriksaan.required' => 'Hasil pemeriksaan Wajib Diisi'
        ]);
        if ($request->idAnamnesa) {
            SimpusAnamnesa::where('idAnamnesa', $request->idAnamnesa)->update(
                [
                    'tinggiBadan' => $request->tinggiBadan,
                    'beratBadan' => $request->beratBadan,
                    'sistole' => $request->sistole,
                    'diastole' => $request->diastole,
                    'respRate' => $request->respRate,
                    'heartRate' => $request->heartRate,
                    'suhu' => $request->suhu
                ]
            );
        }
        SuratKeterangan::create([
            'id_jns_surat' => $request->id_jns_surat,
            'jenis_surat' => $request->jenis_surat,
            'no_surat' => $request->no_surat,
            'keperluan' => $request->keperluan,
            'keterangan' => $request->ket_lain,
            'tgl_ijin_awal' => $request->tgl_ijin_awal,
            'tgl_ijin_akhir' => $request->tgl_ijin_akhir,
            'tgl_kematian' => $request->tgl_kematian,
            'jam_kematian' => $request->jam_kematian,
            'ket_kematian' => $request->ket_kematian,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
            'mata_ka_ki' => $request->mata_ka_ki,
            'telinga_ka_ki' => $request->telinga_ka_ki,
            'test_buta_warna' => $request->test_buta_warna,
            'tenagaMedis' => $request->tenagaMedis,
            'id_pelayanan' => $request->id_pelayanan,
            'modified_date' => now(),
            'created_date' => now(),
        ]);
        return redirect()->back();
    }

    public function hapusSuket($idSurat)
    {
        SuratKeterangan::where('id_surat', $idSurat)->delete();
        return redirect()->back();
    }

    public function cetakSuket($idSurat)
    {
        // dd($idSurat);
        $suket = SuratKeterangan::where('id_surat', $idSurat)->with([
            'jenisSurat',
            'SimpusPelayanan.SimpusLoket.SimpusPasien'
        ])->first();
        $dataPasien = DB::table('simpus_pasien as p')
            ->join('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->join('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->join('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->join('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')->where('p.ID', $suket->SimpusPelayanan->SimpusLoket->pasienId)->first();
        $dataAnamnesa = SimpusAnamnesa::where('loketId', $suket->SimpusPelayanan->loketId)->first();

        $unit = UnitProfile::where('unit_id', 1)->first();
        $dokter = MasterDokter::where('idDokter', $suket->tenagaMedis)->first();
        $kecamatan = SetupKec::where('NO_KAB', 10)->where('NO_PROP', 35)->where('NO_KEC', $unit->no_kec)->first();
        $data = [
            'suket' => $suket,
            'dataPasien' => $dataPasien,
            'dataAnamnesa' => $dataAnamnesa,
            'unit' => $unit,
            'dokter' => $dokter,
            'kecamatan' => $kecamatan
        ];

        if ($suket->id_jns_surat == 1) {
            return Inertia::render('Ruang_Layanan/cetakSuket/cetakSuratKeterangan', $data);
        } elseif ($suket->id_jns_surat == 2) {
            return Inertia::render('Ruang_Layanan/cetakSuket/cetakSuratKeteranganDokter', $data);
        } elseif ($suket->id_jns_surat == 3) {
            return Inertia::render('Ruang_Layanan/cetakSuket/cetakSuratKeteranganCatin', $data);
        } elseif ($suket->id_jns_surat == 4) {
            return Inertia::render('Ruang_Layanan/cetakSuket/cetakSuratKeteranganCalonHaji', $data);
        } elseif ($suket->id_jns_surat == 5) {
            return Inertia::render('Ruang_Layanan/cetakSuket/cetakSuratKematian', $data);
        }
    }
    public function editSuket($idPoli, $idPelayanan, $idSurat)
    {
        //dd($idSurat);
        $jenisSurat = SuratMaster::get();
        $suket = SuratKeterangan::where('id_surat', $idSurat)->first();
        $pelayanan = SimpusPelayanan::where('idPelayanan', $idPelayanan)->first();
        $dataAnamnesa = SimpusAnamnesa::with(['loket', 'loket.SimpusPasien.SetupKab', 'loket.SimpusPasien.SetupKel'])->where('loketId', $pelayanan->loketId)->first();
        $TenagaMedisAskep = MasterDokter::whereIn('profesi_id', [19, 20])->where('aktif', 1)->get();
        return Inertia::render('Ruang_Layanan/Umum/SuratKeteranganUpdate', [
            'jenisSurat' => $jenisSurat,
            'idPoli' => $idPoli,
            'idPelayanan' => $idPelayanan,
            'dataAnamnesa' => $dataAnamnesa,
            'suket' => $suket,
            'tenagaMedisAskep' => $TenagaMedisAskep
        ]);
    }

    public function updateSuket(Request $request)
    {
        //dd($request->all());

        //dd($request->all());
        if ($request->idAnamnesa) {
            SimpusAnamnesa::where('idAnamnesa', $request->idAnamnesa)->update(
                [
                    'tinggiBadan' => $request->tinggiBadan,
                    'beratBadan' => $request->beratBadan,
                    'sistole' => $request->sistole,
                    'diastole' => $request->diastole,
                    'respRate' => $request->respRate,
                    'heartRate' => $request->heartRate,
                    'suhu' => $request->suhu
                ]
            );
        }
        SuratKeterangan::where('id_surat', $request->idSurat)->update([
            'id_jns_surat' => $request->id_jns_surat,
            'no_surat' => $request->no_surat,
            'keperluan' => $request->keperluan,
            'keterangan' => $request->ket_lain,
            'tgl_ijin_awal' => $request->tgl_ijin_awal,
            'tgl_ijin_akhir' => $request->tgl_ijin_akhir,
            'tgl_kematian' => $request->tgl_kematian,
            'jam_kematian' => $request->jam_kematian,
            'ket_kematian' => $request->ket_kematian,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
            'mata_ka_ki' => $request->mata_ka_ki,
            'telinga_ka_ki' => $request->telinga_ka_ki,
            'test_buta_warna' => $request->test_buta_warna,
            'tenagaMedis' => $request->tenagaMedis,
            'id_pelayanan' => $request->id_pelayanan,
            'modified_date' => now(),
        ]);
        return redirect()->back();
    }
    private function sqlNorm(string $col): string
    {
        // CONVERT(... USING utf8mb4) memastikan collation seragam
        return "UPPER(TRIM(CONVERT($col USING utf8mb4)))";
    }

    public function formLaborat(Request $request, $idPoli, $idLoket, $idPelayanan)
    {
        $Pelayanan = SimpusPelayanan::with('SimpusLoket.anamnesa', 'SimpusPoli')->where('idPelayanan', $idPelayanan)->first();
        $Pasien = SimpusPasien::where('ID', $Pelayanan->SimpusLoket->pasienId)->first();
        $TenagaMedisAskep = MasterDokter::whereIn('profesi_id', [19, 20])->where('aktif', 1)->get();


        $puSub = DB::table('parameter_uji as pu0')
            ->leftJoin('master_kategori as k', 'k.id_kategori', '=', 'pu0.id_kategori')
            ->selectRaw("
        {$this->sqlNorm('pu0.kode_parameter')} AS kode,
        MAX(pu0.satuan) AS satuan,
        MAX(NULLIF(pu0.nilai_normal,'')) AS nilai_normal,
        MAX(NULLIF(pu0.nilai_kritis,'')) AS nilai_kritis,
        MAX(pu0.id_kategori) AS id_kategori,
        MAX(k.nama_kategori) AS kategori
    ")
            ->whereNotNull('pu0.kode_parameter')
            ->groupBy(DB::raw($this->sqlNorm('pu0.kode_parameter')));

        $DataPemeriksaan = $DataPemeriksaan = DB::table('simpus_tindakan as t')
            ->leftJoinSub($puSub, 'pu', function ($j) {
                $j->on('pu.kode', '=', DB::raw($this->sqlNorm('t.kdTindakan')));
            })
            ->where('t.loketId', $idLoket)
            ->where('t.deskripsi', 'lab')
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

        // dd($DataPemeriksaan);

        return Inertia::render('Ruang_Layanan/Umum/formLaborat', [
            'Pelayanan' => $Pelayanan,
            'Pasien' => $Pasien,
            'tenagaMedisAskep' => $TenagaMedisAskep,
            'idLoket' => $idLoket,
            'DataPemeriksaan' => $DataPemeriksaan
        ]);
    }

    public function simpanPermohonanLab(Request $request, $idLoket)
    {
        // dd($request->all());

        // $loket = SimpusLoket::where('');
        $namaPoli = SimpusPoliFKTP::where('kdPoli', $request->idPoli)->first();
        SimpusPermohonanLab::create([
            'idPermohonan' => Str::random(20),
            'pasienId' => $request->idPasien,
            'loketId' => $idLoket,
            'pelayananId' => $request->idPelayanan,
            'nmPoli' => $namaPoli->nmPoli,
            'tglDibuat' => now(),
            'tenagaMedisPengirim' => $request->tenaga_medis,
            'alasanDirujuk' => $request->alasan,
            'hasilLabLuarFaskes' => $request->hasil_lab_luar_faskes,
            'statusLayanan' => '0',
            'createdDate' => now(),
        ]);

        return redirect()->back();
    }

    public function suratRujukList($idPoli, $idPelayanan)
    {
        $suratRujuks = SuratRujuk::where('id_Pelayanan', $idPelayanan)->with(['tenagaMedis', 'provider', 'poliRujukan'])->get();
        // dd($suratRujuks);
        return Inertia::render('Ruang_Layanan/Umum/SuratRujukan', [
            'idPoli' => $idPoli,
            'idPelayanan' => $idPelayanan,
            'suratRujuks' => $suratRujuks
        ]);
    }
    public function suratRujukForm($idPoli, $idPelayanan, $idSurat = null)
    {
        $pelayanan = SimpusPelayanan::where('idPelayanan', $idPelayanan)->first();
        // dd($pelayanan);
        $dataAnamnesa = SimpusAnamnesa::with(['loket', 'loket.SimpusPasien.SetupKab', 'loket.SimpusPasien.SetupKel'])->where('loketId', $pelayanan->loketId)->first();
        //dd($dataAnamnesa);
        $TenagaMedisAskep = MasterDokter::whereIn('profesi_id', [19, 20])->where('aktif', 1)->get();
        $provider = SimpusProvider::select('kdProvider', 'nmProvider')->get();
        $poliFktl = SimpusPoliFKTL::get();
        if ($idSurat) {
            $surat = SuratRujuk::where('id_surat_rujukan', $idSurat)->first();
            return Inertia::render('Ruang_Layanan/Umum/SuratRujukanCreate', [
                'idPoli' => $idPoli,
                'idPelayanan' => $idPelayanan,
                'dataAnamnesa' => $dataAnamnesa,
                'tenagaMedisAskep' => $TenagaMedisAskep,
                'provider' => $provider,
                'poliFktl' => $poliFktl,
                'surat' => $surat
            ]);
        }
        return Inertia::render('Ruang_Layanan/Umum/SuratRujukanCreate', [
            'idPoli' => $idPoli,
            'idPelayanan' => $idPelayanan,
            'dataAnamnesa' => $dataAnamnesa,
            'tenagaMedisAskep' => $TenagaMedisAskep,
            'provider' => $provider,
            'poliFktl' => $poliFktl
        ]);
    }

    public function simpanSuratRujuk(Request $request, $idPoli, $idSurat = null)
    {
        //dd($idSurat);
        // dd($request->all());
        $unit = UnitProfile::select('nama_unit', 'alamat')->where('unit_id', Auth()->user()->unit)->first();
        // dd($unit);

        $request->validate([
            'tgl_rujuk' => 'required',
            'no_surat' => 'required',
            'provider' => 'required',
            'Poli' => 'required',
            'tenagaMedis' => 'required',
            'no_hp' => 'required'
        ], [
            'tgl_rujuk.required' => 'Tanggal Wajib Diisi',
            'no_surat.required' => 'No Surat Wajib Diisi',
            'provider.required' => 'Pilih Rumah Sakit Terlebih Dahulu',
            'Poli.required' => 'Poli Rujukan Wajib Diisi',
            'tenagaMedis.required' => 'Tenaga Medis Wajib Diisi',
            'no_hp.required' => 'NO HP Wajib Diisi',
        ]);

        if ($idSurat) {
            SuratRujuk::where('id_surat_rujukan', $idSurat)->update([
                'tgl_rujuk' => $request->tgl_rujuk,
                'no_surat' => $request->no_surat,
                'id_pelayanan' => $request->id_pelayanan,
                'no_hp' => $request->no_hp,
                'kdppk' => $request->provider,
                'kdPoliRujLan' => $request->Poli,
                'tenagaMedis' => $request->tenagaMedis,
                'nama_unit' => $unit->nama_unit,
                'alamat' => $unit->alamat,
                'created_by' => Auth()->user()->unit,
                'modified_date' => now()
            ]);
        } else {
            // dd($unit);
            SuratRujuk::create([
                'tgl_rujuk' => $request->tgl_rujuk,
                'no_surat' => $request->no_surat,
                'id_pelayanan' => $request->id_pelayanan,
                'no_hp' => $request->no_hp,
                'kdppk' => $request->provider,
                'kdPoliRujLan' => $request->Poli,
                'tenagaMedis' => $request->tenagaMedis,
                'nama_unit' => $unit->nama_unit,
                'alamat' => $unit->alamat,
                // 'created_by' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ]);
        }

        return redirect()->back();
    }

    public function cetakRujukan($idSurat)
    {
        // dd($idSurat);
        $suratRujuk = SuratRujuk::where('id_surat_rujukan', $idSurat)->with(['tenagaMedis', 'provider', 'poliRujukan', 'pelayanan.SimpusLoket'])->first();
        // dd($suratRujuk);
        $dataPasien = DB::table('simpus_pasien as p')
            ->join('setup_kel as kel', function ($join) {
                $join->on('p.NO_KEL', '=', 'kel.NO_KEL')
                    ->on('p.NO_KEC', '=', 'kel.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kel.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kel.NO_PROP');
            })
            ->join('setup_kec as kec', function ($join) {
                $join->on('p.NO_KEC', '=', 'kec.NO_KEC')
                    ->on('p.NO_KAB', '=', 'kec.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kec.NO_PROP');
            })
            ->join('setup_kab as kab', function ($join) {
                $join->on('p.NO_KAB', '=', 'kab.NO_KAB')
                    ->on('p.NO_PROP', '=', 'kab.NO_PROP');
            })
            ->join('setup_prop as prop', 'p.NO_PROP', '=', 'prop.NO_PROP')->where('p.ID', $suratRujuk->pelayanan->SimpusLoket->pasienId)->first();
        $dataAnamnesa = SimpusAnamnesa::where('loketId', $suratRujuk->pelayanan->loketId)->with(['kesadaran', 'loket.Diagnosa'])->first();
        $dokter = MasterDokter::where('idDokter', $suratRujuk->tenagaMedis)->first();
        $unit = UnitProfile::where('unit_id', 1)->first();
        $diagnosa = SimpusDataDiagnosa::where('loketId', $suratRujuk->pelayanan->SimpusLoket->idLoket)->get();
        //dd($diagnosa);
        return Inertia::render('Ruang_Layanan/cetakSuratRujukan/FormCetakSuratRujukan', [
            'suratRujuk' => $suratRujuk,
            'dataPasien' => $dataPasien,
            'dataAnamnesa' => $dataAnamnesa,
            'unit' => $unit,
            'dokter' => $dokter,
            'diagnosa' => $diagnosa
        ]);
    }

    public function hapusSuratRujukan($idSurat)
    {
        // dd($idSurat);
        try {
            $deleted = SuratRujuk::where('id_surat_rujukan', $idSurat)->delete();
            if (!$deleted) {
                return back()->with('error', 'Surat rujukan tidak ditemukan.');
            }
            return redirect()->back()->with('success', 'Surat rujukan berhasil dihapus.');
        } catch (\Exception $err) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus surat rujukan.');
        }
    }
    public function riwayatPasien($idPoli, $idPasien)
    {
        // dd($idPoli);
        $riwayatPasien = SimpusPasien::where('ID', $idPasien)->with('SimpusLoket.anamnesa', 'SimpusLoket.Diagnosa', 'SimpusLoket.Tindakan', 'SimpusLoket.TindakanLab.parameteruji', 'SimpusLoket.ResepObat.DetailResepObat.MasterObat', 'SimpusLoket.unitprofile', 'SimpusLoket.SimpusPoli', 'SimpusLoket.pelayanan.StatusPulang', 'SimpusLoket.pelayanan.SimpusPoli','SimpusLoket.pelayanan.poliTujuan')->first();

        // $loketId = $riwayatPasien->SimpusLoket;
        // $tindakan

        // $tindakan = SimpusTindakan::with('parameteruji')->get();
       // dd($riwayatPasien);
        //$tindakanLab = DB::table('simpus_tindakan as t')->join('parameter_uji as p')->on('t.kdTindakan', 'p.kode_parameter')->where('t.loketId');
        return Inertia::render('Ruang_Layanan/Umum/riwayatPasien', [
            'riwayatPasien' => $riwayatPasien
        ]);
    }

    public function getCppt($idPoli, $idPasien)
    {
        $riwayatPasien = SimpusPasien::where('ID', $idPasien)->with('SimpusLoket.anamnesa', 'SimpusLoket.Diagnosa', 'SimpusLoket.Tindakan', 'SimpusLoket.TindakanLab.parameteruji', 'SimpusLoket.ResepObat.DetailResepObat.MasterObat', 'SimpusLoket.unitprofile', 'SimpusLoket.SimpusPoli', 'SimpusLoket.pelayanan')->first();
        // dd($riwayatPasien);
        return Inertia::render('Ruang_Layanan/Umum/cppt', [
            'riwayatPasien' => $riwayatPasien
        ]);
    }

    public function getPelayanan($idLoket, $idPelayanan)
    {
        $pelayanan = SimpusPelayanan::with(['SimpusLoket', 'SimpusPoli', 'StatusPulang', 'poliTujuan'])
            ->where('loketId', $idLoket)
            // ->orWhere('pelIdSebelum', $idPelayanan)
            ->orderBy('createdDate', 'asc')
            ->get();
        $cekAkhir = SimpusPelayanan::where('loketId', $idLoket)->orderBy('createdDate', 'desc')->first();
        Log::info('Data pelayanan:', [
            'idLoket' => $idLoket,
            'idPelayanan' => $idPelayanan,
            'pelayanan' => $pelayanan,
            'cekAkhirPelayanan' => $cekAkhir,
        ]);
        return response()->json(
            [
                'pelayanan' => $pelayanan,
                'cekAkhirPelayanan' => $cekAkhir
            ]
        );
    }
    public function hapusRujuk($idpelayanan)
    {
        $rujuk = SimpusPelayanan::where('idpelayanan', $idpelayanan)->first();
        $pel = $rujuk->pelIdSebelum;
        $lok = $rujuk->loketId;
        $batal['tujuanPoli'] = null;
        $batal['kdStatusPulang'] = null;
        $batal['tenagaMedis'] = null;
        $batal['sudahDilayani'] = null;
        SimpusPelayanan::where('idpelayanan', $pel)->update($batal);
        DB::table('simpus_ranap')->where('pelayananId',  $idpelayanan)->delete();

        $rujuk->delete();
        return response()->json(
            [
                'sukses'
            ]
        );
    }
    public function getUKK($idLoket)
    {
        $jenisUkk = masterJenisUkk::get();
        $ukk = Ukk::where('loketId', $idLoket)->first();
        return response()->json(
            [
                'jenisUkk' => $jenisUkk,
                'ukk' => $ukk
            ]
        );
    }

    public function simpanUkk(Request $request, $idLoket)
    {
        // dd(idLoket);
        $ukk = Ukk::where('loketId', $idLoket)->first();
        if (!$ukk) {
            $dataUkk = Ukk::create([
                'ukk_id' => Str::uuid(),
                'loketId' => $idLoket,
                'pekerjaan' => $request->pekerjaan,
                'jenisUKK' => $request->jenis_ukk,
                'tipeKerja' => $request->tipe_kerja,
                'tempatKerja' => $request->tempat_kerja,
                'lamaKerja' => $request->lama_kerja,
                'createdBy' => Auth()->user()->username
            ]);

            SimpusLoket::where('idLoket', $idLoket)->update([
                'ukkId' => $dataUkk->ukk_id
            ]);
        } else {
            $ukk->update([
                'loketId' => $idLoket,
                'pekerjaan' => $request->pekerjaan,
                'jenisUKK' => $request->jenis_ukk,
                'tipeKerja' => $request->tipe_kerja,
                'tempatKerja' => $request->tempat_kerja,
                'lamaKerja' => $request->lama_kerja,
                'modifiedDate' => now(),
                'modifiedBy' => Auth()->user()->username
            ]);
        }
        return redirect()->back();
    }
    public function batalBerobatJalan($idLoket, $idpelayanan)
    {
        $pelayanan = SimpusPelayanan::where('idPelayanan', $idpelayanan)->first();

        if (!$pelayanan) {
            return response()->json([
                'status' => false,
                'message' => 'Data pelayanan tidak ditemukan'
            ], 404);
        }

        $pelayanan->update([
            'tujuanPoli' => null,
            'kdStatusPulang' => null,
            'tenagaMedis' => null,
            'sudahDilayani' => 2,
            'endTime' => null
        ]);

        return response()->json([
            'idLoket' => $idLoket,
            'idPelayanan' => $idpelayanan,
            'message' => 'Berhasil batal berobat jalan'
        ]);
    }


    public function simpanGizi(Request $request, $idLoket)
    {
        //  dd($request->all());
        $gizi = DB::table('simpus_gizi')->where('loketId', $idLoket)->first();
        $data = [
            'energi' => $request->energi,
            'protein' => $request->protein,
            'lemak' => $request->lemak,
            'karbohidrat' => $request->karbohidrat,
            'biokimia' => $request->biokimia,
            'sulitMenelan' => $request->sulitMenelan,
            'sulitMengunyah' => $request->sulitMengunyah,
            'mual' => $request->mual,
            'konstipasi' => $request->konstipasi,
            'nafsuMakan' => $request->nafsuMakan,
            'polaMakan' => $request->polaMakan,
            'pantanganMakanan' => $request->pantangan,
            'riwayatPersonal' => $request->riwayatPersonal,
            'suplemenGizi' => $request->suplemen,
            'diet' => $request->diet,
            'tujuanEdukasi' => $request->tujuanEdukasi,
            'kontrolUlang' => $request->kontrolUlang,
            'tenagaMedisGizi' => $request->TenagaMedisAskep
        ];
        if (!$gizi) {
            DB::table('simpus_gizi')->insert(array_merge($data, [
                'idGizi' => Str::uuid(),
                'loketId' => $idLoket,
                'createdDate' => now(),
                'createdBy' => Auth::user()->username
            ]));
        }
        DB::table('simpus_gizi')->where('idgizi', $gizi->idGizi)->update(array_merge($data, [

            'modifiedDate' => now(),
            'modifiedBy' => Auth::user()->username
        ]));

        return redirect()->back();
    }
    public function simpanSanitasi(Request $request, $idPelayanan)
    {
        //dd($request->all());
        $sanitasi = DB::table('simpus_sanitasi')->where('pelayananId', $idPelayanan)->exists();
        if (!$sanitasi) {
            DB::table('simpus_sanitasi')->insert([
                'pelayananId' => $idPelayanan,
                'interfeksi' => $request->input('interfeksi'),
                'keluargaBinaan' => $request->input('keluarga_binaan'),
                'keluargaRisti' => $request->input('keluarga_risti'),
                'tindakanSaran' => $request->input('tindakan_saran'),
                'hasilWawancara' => $request->input('hasil_wawancara'),
                'createdBy' => Auth::user()->username,
                'createdDate' => now(),
            ]);

        }
        DB::table('simpus_sanitasi')->update([
            'pelayananId' => $idPelayanan,
            'interfeksi' => $request->input('interfeksi'),
            'keluargaBinaan' => $request->input('keluarga_binaan'),
            'keluargaRisti' => $request->input('keluarga_risti'),
            'tindakanSaran' => $request->input('tindakan_saran'),
            'hasilWawancara' => $request->input('hasil_wawancara'),
            'modifiedBy' => Auth::user()->username,
            'modifiedDate' => now(),
        ]);

        SimpusPelayanan::where('idPelayanan', $idPelayanan)->update([
            'sudahDilayani' => 1
        ]);
        return redirect()->back();
    }

    public function popUpFormRujukLanjut()
    {
        $spesialis = DB::table('simpus_spesialis')->get();
        $subspesialis = DB::table('simpus_subspesialis')->get();
        $spesialisSarana = DB::table('simpus_spesialissarana')->get();

        return response()->json(
            [
                'spesialis' => $spesialis,
                'subspesialis' => $subspesialis,
                'spesialisSarana' => $spesialisSarana,
            ]
        );
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
