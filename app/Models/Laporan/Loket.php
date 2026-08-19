<?php

namespace App\Models\Laporan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Laporan\Pasien;

class Loket extends Model
{
    protected $table = 'simpus_loket';
    protected $primaryKey = 'idLoket'; // ✅ ini sesuaikan kolom primary key di DB
    public $timestamps = false;

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasienId', 'ID');
    }
}
