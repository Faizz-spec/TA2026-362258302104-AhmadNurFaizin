<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class SimpusDetailResepObat extends Model
{
    
    protected $table = 'simpus_resep_detail';
    protected $primaryKey = 'id_resep_detail';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_resep_detail',
        'resep_id',
        'poli',
        'obat_id',
        'jumlah',
        'dosis_pakai',
        'tiapJam',
        'sample',
        'waktu',
        'kondisi',
        'setujui',
        'keterangan',
        'nama_pasien',
        'unit_details',
        'tglPengeluaran',
        'cetak',
        'createdDate',
        'createdBy',
        'modifiedDate',
        'modifiedBy',
    ];
    public $timestamps = false;
    public function MasterObat(){
        return $this->belongsTo(SimpusMasterObat::class, 'obat_id', 'OBAT_ID');
    }
      public function ResepObat(){
        return $this->belongsTo(SimpusResepObat::class, 'resep_id', 'id_resep');
    }
}
