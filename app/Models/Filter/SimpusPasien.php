<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Model;

class SimpusPasien extends Model
{
    protected $table = 'simpus_pasien';
    protected $primaryKey = 'ID';

    public function loket(){
        return $this->hasMany(SimpusLoket::class, 'pasienId', 'ID');
    }
       public function propinsi()
    {
        return $this->belongsTo(SetupProvinsi::class, 'NO_PROP', 'NO_PROP');
    }

    public function kabupaten()
    {
        return $this->belongsTo(SetupKabupaten::class, ['NO_PROP', 'NO_KAB'], ['NO_PROP', 'NO_KAB']);
    }

    public function kecamatan()
    {
        return $this->belongsTo(SetupKecamatan::class, ['NO_PROP', 'NO_KAB', 'NO_KEC'], ['NO_PROP', 'NO_KAB', 'NO_KEC']);
    }

    public function kelurahan()
    {
        return $this->belongsTo(SetupDesa::class, ['NO_PROP', 'NO_KAB', 'NO_KEC', 'NO_KEL'], ['NO_PROP', 'NO_KAB', 'NO_KEC', 'NO_KEL']);
    }
    //
}
