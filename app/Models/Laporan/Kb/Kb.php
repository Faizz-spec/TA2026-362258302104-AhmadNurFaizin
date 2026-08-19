<?php

namespace App\Models\Laporan\Kb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kb extends Model
{
    // Tetap pakai tabel puskesmas walau nama class Kb
    protected $table = 'puskesmas';
    protected $primaryKey = 'PUSK_ID';
    public $timestamps = false;

    // Dropdown Puskesmas
    public static function getList()
    {
        return self::selectRaw('PUSK_ID as id, PUSK as nama')
            ->where('AKTIF', 1)
            ->orderBy('PUSK')
            ->get();
    }

    // Dropdown Unit (layanan)
    public static function getUnits()
    {
        return DB::table('data_master_unit')
            ->selectRaw('id_kategori as id, kategori as nama')
            ->orderBy('kategori')
            ->get();
    }

    // Dropdown Sub Unit (sub layanan)
    public static function getSubUnits($unitId, $puskId = null)
    {
        return DB::table('data_master_unit_detail')
            ->selectRaw('id_detail as id, nama_unit as nama')
            ->where('id_kategori', $unitId)
            ->whereNotNull('nama_unit')
            ->where('nama_unit', '<>', '')
            ->when(DB::getSchemaBuilder()->hasColumn('data_master_unit_detail', 'status'),
                fn ($q) => $q->where('status', 1))
            ->when($puskId, function ($q) use ($puskId) {
                $q->where(function ($qq) use ($puskId) {
                    $qq->whereNull('id_unit')->orWhere('id_unit', $puskId);
                });
            })
            ->orderBy('nama_unit')
            ->get();
    }
}
