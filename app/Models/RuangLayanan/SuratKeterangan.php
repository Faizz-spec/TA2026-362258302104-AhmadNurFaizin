<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SuratKeterangan extends Model
{
    protected $table = 'surat_keterangan';

    // primary key
    protected $primaryKey = 'id_surat';

    // kalau tidak pakai created_at & updated_at
    public $timestamps = false;

    // field yang bisa diisi
    protected $fillable = [
        'id_jns_surat',
        'no_surat',
        'keperluan',
        'keterangan',
        'tgl_ijin_awal',
        'tgl_ijin_akhir',
        'tgl_kematian',
        'jam_kematian',
        'ket_kematian',
        'hasil_pemeriksaan',
        'mata_ka_ki',
        'telinga_ka_ki',
        'test_buta_warna',
        'tenagaMedis',
        'id_pelayanan',
        'created_by',
        'modified_date',
    ];

    public function jenisSurat()
    {
        return $this->belongsTo(SuratMaster::class, 'id_jns_surat', 'ID_JNS_SURAT');
    }
    public function SimpusPelayanan()
    {
        return $this->belongsTo(SimpusPelayanan::class, 'id_pelayanan', 'idpelayanan');
    }


}
