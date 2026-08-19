<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterViewLog extends Model
{
    protected $table = 'center_view_log';
    public $timestamps = false;

    protected $fillable = [
        'loketId','pelayananId','tgl_pelayanan','kd_pasien','kd_penyakit',
        'gender_pasien','tgllahir_pasien','cara_bayar','kasus_baru',
        'status_code','status_msg','pusk_id'
    ];
}
