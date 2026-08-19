<?php

namespace App\Models\RuangLayanan;

use Illuminate\Database\Eloquent\Model;

class UnitProfile extends Model
{
    protected $table = 'unit_profiles';
    protected $primaryKey = 'unit_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'satker_id',
        'satker_id_lama',
        'kode_puskesmas',
        'nama_unit',
        'unor',
        'alamat',
        'telp',
        'fax',
        'email',
        'site',
        'kategori',
        'kode_pos',
        'no_prov',
        'no_kab',
        'no_kec',
        'no_kel',
        'rt',
        'rw',
        'lat',
        'lng',
        'jabatan_pimpinan',
        'nip_pimpinan',
        'nama_pimpinan',
        'key_header',
        'pusk_rawat',
        'bip_uraian',
        'secret_keys',
        'org_id',
        'client_secret',
        'client_id',
        'token',
        'response_organization',
        'last_update',
    ];
}
