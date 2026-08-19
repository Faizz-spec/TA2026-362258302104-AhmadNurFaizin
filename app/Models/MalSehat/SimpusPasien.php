<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class SimpusPasien extends Model
{
    protected $table = 'simpus_pasien';   // nama tabel
    protected $primaryKey = 'ID';         // kolom primary key
    public $timestamps = false;           // tabel tidak pakai created_at & updated_at

    // kolom yang bisa diisi (kalau perlu insert/update)
    protected $fillable = [
        'NO_MR', 'NIK', 'NAMA_LGKP', 'ALAMAT',
        'NO_KEL', 'PHONE', 'noKartu', 'ACTIVE'
    ];
}
