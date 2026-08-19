<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class MasterDokter extends Model
{
    protected $table = 'master_dokter';
    public $timestamps = false;
    protected $primaryKey = 'idDokter';
    public function profesi()
    {
        return $this->belongsTo(NakesProfesi::class, 'profesi_id', 'id_profesi');
    }
}
