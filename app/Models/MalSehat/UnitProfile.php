<?php

namespace App\Models\MalSehat;

use Illuminate\Database\Eloquent\Model;

class UnitProfile extends Model
{
    protected $table = 'data_master_unit_detail';   // nama tabel
    protected $fillable = ['nama_unit', 'id_unit'];  // kolom yang bisa diisi
    public $timestamps = false;           // kalau tabel tidak pakai created_at & updated_at
}
