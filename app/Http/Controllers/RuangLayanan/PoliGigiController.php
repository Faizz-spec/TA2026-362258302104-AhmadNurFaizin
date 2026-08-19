<?php

namespace App\Http\Controllers\RuangLayanan;

use App\Http\Controllers\Controller;
use App\Models\RuangLayanan\DataMasterUnitDetail;
use App\Models\RuangLayanan\MasterAlergi;
use App\Models\RuangLayanan\MasterDiagnosaKasus;
use App\Models\RuangLayanan\MasterDokter;
use App\Models\RuangLayanan\SimpusAlergiData;
use App\Models\RuangLayanan\SimpusAnamnesa;
use App\Models\RuangLayanan\SimpusDataDiagnosa;
use App\Models\RuangLayanan\SimpusDetailResepObat;
use App\Models\RuangLayanan\SimpusDiagnosa;
use App\Models\RuangLayanan\SimpusKesadaran;
use App\Models\RuangLayanan\SimpusMasterObat;
use App\Models\RuangLayanan\SimpusPelayanan;
use App\Models\RuangLayanan\SimpusResepObat;
use App\Models\RuangLayanan\SimpusTindakan;
use App\Models\RuangLayanan\StatusPulang;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;

class PoliGigiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $DataUnit = DataMasterUnitDetail::with('DataMasterUnit')
            ->where('no_kec', 18)
            ->orderBy('id_kategori')
            ->get();

        $DataPasien = DB::table('simpus_pelayanan as pel')
            ->join('simpus_loket as l', 'pel.loketId', '=', 'l.idLoket')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
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
            ->where('l.kdPoli', '002')
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
                'l.idLoket'
            )
            ->get();
        // dd($DataPasien);

        return Inertia::render('Ruang_Layanan/Gigi/pasien_poli', [
            'DataUnit' => $DataUnit,
            'DataPasien' => $DataPasien,
        ]);

    }
    public function pelayanan($id)
    {
        $DataPasien = DB::table('simpus_loket as l')
            ->join('simpus_pasien as p', 'l.pasienId', '=', 'p.ID')
            ->join('simpus_poli_fktp as poli', 'poli.kdPoli', '=', 'l.kdPoli')
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
            ->where('l.kdPoli', operator: 002)
            ->where('idLoket', $id)
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
                'p.alamat',
                'p.no_rt',
                'p.no_rw',
                'p.jenis_klmin',
                'l.umur',
                'l.umur_bulan',
                'umur_hari',
                'l.tglKunjungan',
                'l.idLoket',
                'l.kdPoli'
            )
            ->get();

        $DataAnamnesa = SimpusAnamnesa::where('loketId', $DataPasien[0]->idLoket)->first();
        $DataKesadaran = SimpusKesadaran::get();
        $DiagnosaKasus = MasterDiagnosaKasus::get();
        $MasterAlergi = MasterAlergi::get();
        $SimpusDataDiagnosa = SimpusDataDiagnosa::with(['SimpusPoliFKTP', 'MasterDiagnosaKasus'])->where('kdPoli', '002')->where('loketId', $DataPasien[0]->idLoket)->get();
        $SimpusTindakan = SimpusTindakan::where('loketId', $DataPasien[0]->idLoket)->get();
        $AlergiPasien = SimpusAlergiData::with(['alergiObat', 'alergiMakanan'])->where('pasienId', $DataPasien[0]->ID)->get();
        $StatusPulang = StatusPulang::all();
        $SimpusResepObat = SimpusResepObat::with('SimpusDetailResepObat.MasterObat')->where('loketId', $DataPasien[0]->idLoket)->get();
        $MasterObat = SimpusMasterObat::all();
        $TenagaMedisAskep = MasterDokter::whereIn('profesi_id', [19, 20])->get();
        $DiagnosaKeperawatan = SimpusDiagnosa::where('kategori', 1)->get();
        $DataRujuk = SimpusPelayanan::with(['SimpusLoket', 'SimpusPoli', 'StatusPulang'])->where('loketId', $DataPasien[0]->idLoket)->orderBy('createdDate', 'desc')->get();
        return Inertia::render('Ruang_Layanan/Gigi/pelayanan', [
            'DataPasien' => $DataPasien,
            'DataAnamnesa' => $DataAnamnesa,
            'DataKesadaran' => $DataKesadaran,
            'DiagnosaKasus' => $DiagnosaKasus,
            'MasterAlergi' => $MasterAlergi,
            'SimpusDataDiagnosa' => $SimpusDataDiagnosa,
            'SimpusTindakan' => $SimpusTindakan,
            'AlergiPasien' => $AlergiPasien[0],
            'StatusPulang' => $StatusPulang,
            'SimpusResepObat' => $SimpusResepObat,
            'MasterObat' => $MasterObat,
            'TenagaMedisAskep' => $TenagaMedisAskep,
            'DiagnosaKeperawatan' => $DiagnosaKeperawatan,
            'DataRujuk' => $DataRujuk
        ]);
    }

    public function setAnamnesaSubjective(Request $request)
    {
        // dd($request->all());
        SimpusAnamnesa::where('loketid', $request->idLoket)
            ->update([
                'tglAnamnesa' => $request->tgl_anamnesa,
                'keluhan' => $request->keluhan_utama,
                'keluhanTambahan' => $request->keluhan_tambahan,
                'riwayatPenyakitSekarang' => $request->riwayat_penyakit_sekarang,
                'riwayatPenyakitDahulu' => $request->riwayat_penyakit_dahulu,
                'riwayatPenyakitKeluarga' => $request->riwayat_penyakit_keluarga,
                'terapiYangPernahDijalani' => $request->tindakan,
                'obatSeringDigunakan' => $request->obat_digunakan,
                'obatSeringDikonsumsi' => $request->obat_dikonsumsi
            ]);
        $AlergiPasien = SimpusAlergiData::where('pasienId', $request->idPasien)->first();
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
    public function setAnamnesaObjective(Request $request)
    {
        // dd($request->all());

        $anamnesa = SimpusAnamnesa::where('idAnamnesa', $request->idAnamnesa)->first();
        $dataUpdate = [
            'keadaanUmum' => $request->keadaan_umum ?? $anamnesa->keadaanUmum,
            'kdSadar' => $request->kesadaran ?? $anamnesa->kdSadar,
            'imt' => $request->imt ?? $anamnesa->imt,
            'imtKet' => $request->imtKet ?? $anamnesa->imtKet,
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
        SimpusAnamnesa::where('idAnamnesa', $request->idAnamnesa)->update($dataUpdate);
        return redirect()->back();

    }
    public function setDiagnosaMedis(Request $request)
    {
        //dd($request->all());
        SimpusDataDiagnosa::create([
            'kdDiagnosa' => $request->kode_diagnosa,
            'nmDiagnosa' => $request->nama_diagnosa,
            'diagnosaKasus' => $request->kunjungan_khusus,
            'keterangan' => $request->keterangan_kunjungan,
            'kdPoli' => $request->kdPoli,
            'loketId' => $request->loketId,
            'pelayananId' => $request->pelayananId,
        ]);
        return redirect()->back();
    }
    public function setPlanningTindakan(Request $request)
    {
        dd($request->all());
        SimpusTindakan::create([
            'idPelayanan' => 1,
            'loketId' => $request->loketId,
            'kdTindakan' => $request->kode_tindakan,
            'nmTindakan' => $request->nama_tindakan,
            'nmTindakanInd' => $request->nama_tindakan_indonesia,
            'keterangan' => $request->keterangan_tindakan,
            'kdPoli' => $request->kdPoli,
            'ketGigi' => $request->keterangangigi
        ]);
        return redirect()->back();
    }

    public function removeDiagnosaMedis(string $id)
    {
        // dd($id);
        $diagnosa = SimpusDataDiagnosa::find($id);
        if ($diagnosa) {
            $diagnosa->delete();
        }
        return redirect()->back();

    }

    public function removePlanningTindakan(string $id)
    {
        $TIndakan = SimpusTindakan::find($id);
        if ($TIndakan) {
            $TIndakan->delete();
        }
        return redirect()->back();
    }
    public function setPlanningPengobatan(Request $request)
    {
        //dd($request->all());
        SimpusResepObat::create([
            'id_resep' => Str::uuid(),
            'kode_resep' => Str::random(10),
            'loketId' => $request->loketId,
            'pelayananId' => 1,
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
    public function setPlanningPengobatandetail(Request $request)
    {
        // dd($request->all());
        SimpusDetailResepObat::create([
            'id_resep_detail' => Str::uuid(),
            'resep_id' => $request->resep_id,
            'poli' => $request->poli,
            'obat_id' => $request->obat_id,
            'jumlah' => $request->jumlah_obat,
            'jumlah_puyer' => $request->jumlah_puyer,
            'dosis_pakai' => $request->dosis_pakai,
            'tiapJam' => $request->dosis_pakai_jam,
            'waktu' => is_array($request->waktu) ? implode(',', $request->waktu) : $request->waktu,
            'kondisi' => is_array($request->kondisi) ? implode(',', $request->kondisi) : $request->kondisi,
        ]);
        return redirect()->back();


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
