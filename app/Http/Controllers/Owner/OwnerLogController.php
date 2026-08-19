<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerLogController extends Controller
{
    public function loketDeletes(Request $req)
    {
        $page     = max(1, (int) $req->query('page', 1));
        $pageSize = max(1, min(100, (int) $req->query('page_size', 25)));

        $search        = trim((string) $req->query('search', ''));
        $puskesmasId   = $req->query('puskesmas_id', null);
        $kdPoli        = $req->query('kd_poli', null);
        $dateFrom      = $req->query('date_from', null);
        $dateTo        = $req->query('date_to', null);
        $tglKunjFrom   = $req->query('tgl_kunjungan_from', null);
        $tglKunjTo     = $req->query('tgl_kunjungan_to', null);

        $q = DB::table('log_loket_delete as l')
            ->leftJoin('users as u', 'u.username', '=', 'l.deleteBy')
            ->leftJoin('unit_profiles as up', 'up.unit_id', '=', 'l.puskId')
            ->selectRaw('
                l.id, l.loketId, l.pasienId, l.deleteBy, l.deleteDate, l.kdPoli,
                l.agent, l.ip_address, l.platform, l.tglKunjungan, l.puskId,
                up.nama_unit as puskesmas_nama,
                CONCAT(IFNULL(u.first_name, ""), " ", IFNULL(u.last_name, "")) as deleteBy_name
            ');

        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('l.deleteBy', 'like', "%{$search}%")
                  ->orWhere('l.ip_address', 'like', "%{$search}%")
                  ->orWhere('l.agent', 'like', "%{$search}%");
            });
        }

        if ($puskesmasId !== null && $puskesmasId !== '') {
            $q->where('l.puskId', (int) $puskesmasId);
        }

        if ($kdPoli !== null && $kdPoli !== '') {
            $q->where('l.kdPoli', $kdPoli);
        }

        if ($dateFrom)    $q->where('l.deleteDate', '>=', $dateFrom);
        if ($dateTo)      $q->where('l.deleteDate', '<=', $dateTo);
        if ($tglKunjFrom) $q->where('l.tglKunjungan', '>=', $tglKunjFrom);
        if ($tglKunjTo)   $q->where('l.tglKunjungan', '<=', $tglKunjTo);

        $total = (clone $q)->count();

        $rows = $q->orderByDesc('l.deleteDate')
            ->forPage($page, $pageSize)
            ->get();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total'     => $total,
                'page'      => $page,
                'page_size' => $pageSize,
            ],
        ]);
    }
}
