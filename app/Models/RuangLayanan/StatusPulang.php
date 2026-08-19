<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class StatusPulang extends Model
{
    protected $table = 'simpus_statuspulang';

    protected $primaryKey = 'kdStatusPulang';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kdStatusPulang',
        'nmStatusPulang',
        'rawatInap',
    ];
}
