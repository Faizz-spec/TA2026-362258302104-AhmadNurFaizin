<?php

namespace App\Models\RuangLayanan\SkriningPTM;

use Illuminate\Database\Eloquent\Model;

class FaktorRisiko extends Model
{
    protected $table = 'faktor_risiko_ptm';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'skriningID',
        'pelayananId',
        'merokok',
        'status_merokok',
        'btg_rokok',
        'lama_rokok',
        'paparan_rokok',
        'gula',
        'garam',
        'minyak',
        'sayur',
        'aktivitas',
        'alkohol',
        'obat',
        'kesiapan',
        'dukung',
        'r_pribadi_htn',
        'r_pribadi_dm',
        'r_pribadi_stroke',
        'r_pribadi_jantung',
        'r_keluarga_htn',
        'r_keluarga_dm',
        'r_keluarga_stroke',
        'r_keluarga_jantung',
        'skor_faktor_risiko',
        'kategori_faktor_risiko',
        'detail_faktor_risiko'
    ];

    protected $casts = [
        'r_pribadi_htn' => 'boolean',
        'r_pribadi_dm' => 'boolean',
        'r_pribadi_stroke' => 'boolean',
        'r_pribadi_jantung' => 'boolean',
        'r_keluarga_htn' => 'boolean',
        'r_keluarga_dm' => 'boolean',
        'r_keluarga_stroke' => 'boolean',
        'r_keluarga_jantung' => 'boolean',
        'detail_faktor_risiko' => 'array',
    ];
}
