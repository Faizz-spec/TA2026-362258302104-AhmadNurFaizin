<?php

namespace App\Models\RuangLayanan\SkriningPTM;

use Illuminate\Database\Eloquent\Model;

class AssessmentPTM extends Model
{
    protected $table = 'assessment_ptm';

    protected $fillable = [
        'skrining_ptm_id',
        'masalah_hasil_skrining',
        'ringkasan_temuan',
        'diagnosis_utama',
        'diagnosis_utama_saran',
        'status_klinis',
        'catatan_diagnosis',
        'kategori_risiko',
        'ringkasan_klinis',
        'catatan_assessment',
    ];

    protected $casts = [
        'masalah_hasil_skrining' => 'array',
        'ringkasan_temuan' => 'array',
    ];
}
