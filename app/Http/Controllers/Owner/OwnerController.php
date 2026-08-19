<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    // GET /api/owner/roles
    public function roles()
    {
        $roles = DB::table('groups')
            ->select('id','name','description')
            ->orderBy('name')->get();

        return response()->json($roles);
    }

    public function storeUser(Request $request)
{
    // Validasi
    $data = $request->validate([
        'username'      => ['required','string','max:100','unique:users,username'],
        'password'      => ['required','string','min:6'],
        'first_name'    => ['nullable','string','max:50'],
        'last_name'     => ['nullable','string','max:50'],
        'email'         => ['nullable','email','max:100'],
        'puskesmas_id'  => ['required','integer','exists:unit_profiles,unit_id'],
        'roles'         => ['array'], // array of role names
        'roles.*'       => ['string','exists:groups,name'],
    ]);

    // Insert ke users (mapping puskesmas_id -> users.unit)
    $userId = DB::table('users')->insertGetId([
        'ip_address'   => $request->ip(),
        'username'     => $data['username'],
        'password'     => Hash::make($data['password']),
        'email'        => $data['email'] ?? null,
        'first_name'   => $data['first_name'] ?? null,
        'last_name'    => $data['last_name'] ?? null,
        'created_on'   => time(),  // mengikuti skema lama kamu (int unsigned)
        'active'       => 1,
        'unit'         => (int)$data['puskesmas_id'], // FK ke puskesmas.PUSK_ID
    ]);

    // Assign roles
    if (!empty($data['roles'])) {
        $roleIds = DB::table('groups')->whereIn('name', $data['roles'])->pluck('id','name'); // name=>id
        $rows = [];
        foreach ($data['roles'] as $rn) {
            $gid = $roleIds[$rn] ?? null;
            if ($gid) {
                $rows[] = [
                    'user_id'    => $userId,
                    'group_id'   => (int)$gid,
                    'nama_group' => $rn,
                ];
            }
        }
        if ($rows) DB::table('users_groups')->insert($rows);
    }

    return response()->json(['ok' => true, 'id' => $userId], 201);
}

    // GET /api/owner/puskesmas
public function puskesmas()
{
    $rows = DB::table('unit_profiles')
        ->select('unit_id as id','nama_unit as nama')
        ->orderBy('nama_unit')
        ->get();

    return response()->json($rows);
}


    // GET /api/owner/users?search=&puskesmas_id=&role=&page=&page_size=
// GET /api/owner/users?search=&puskesmas_id=&role=&page=&page_size=
public function users(Request $request)
{
    $page      = max(1, (int)$request->query('page', 1));
    $pageSize  = min(200, max(1, (int)$request->query('page_size', 25)));
    $search    = trim((string)$request->query('search', ''));
    $roleName  = trim((string)$request->query('role', ''));
    $puskId    = $request->query('puskesmas_id');

    $q = DB::table('users as u')
    ->leftJoin('unit_profiles as up', 'up.unit_id', '=', 'u.unit')
    ->select(
        'u.id','u.username','u.first_name','u.last_name','u.email','u.last_login',
        DB::raw('up.unit_id as puskesmas_id'),
        DB::raw('up.nama_unit as puskesmas_nama')
    );

    if ($search !== '') {
        $q->where(function ($w) use ($search) {
            $w->where('u.username','like',"%{$search}%")
              ->orWhere('u.email','like',"%{$search}%")
              ->orWhere('u.first_name','like',"%{$search}%")
              ->orWhere('u.last_name','like',"%{$search}%");
        });
    }

if ($puskId !== null && $puskId !== '') {
    $q->where('up.unit_id', $puskId);
}

    if ($roleName !== '') {
        $q->join('users_groups as ugf', 'ugf.user_id', '=', 'u.id')
          ->join('groups as gf', 'gf.id', '=', 'ugf.group_id')
          ->where('gf.name', $roleName);
    }

    $total = (clone $q)->count();

    $rows = $q->orderBy('u.username')
        ->forPage($page, $pageSize)
        ->get();

    $userIds = $rows->pluck('id')->all();

    // roles per user
    $roleRows = DB::table('users_groups as ug')
        ->join('groups as g', 'g.id', '=', 'ug.group_id')
        ->whereIn('ug.user_id', $userIds)
        ->select('ug.user_id','g.name')
        ->get()
        ->groupBy('user_id');

    // ==== AGREGASI SESSIONS ====
    $sessAgg = DB::table('sessions')
        ->select(
            'user_id',
            DB::raw('MAX(last_activity) as last_activity'),
            DB::raw('COUNT(*) as session_count')
        )
        ->whereIn('user_id', $userIds)
        ->whereNotNull('user_id')
        ->groupBy('user_id')
        ->get()
        ->keyBy('user_id');

    $onlineWindow = 5; // menit
    $nowEpoch     = time();

    $data = $rows->map(function ($r) use ($roleRows, $sessAgg, $onlineWindow, $nowEpoch, $request) {
        $roles = array_values(($roleRows->get($r->id)?->pluck('name')->unique()->all()) ?? []);

        $agg = $sessAgg->get($r->id);
        $isLoggedIn  = false;
        $isOnline    = false;
        $lastSeenIso = null;

        if ($agg) {
            $isLoggedIn = ((int)$agg->session_count) > 0;
            $lastAct    = (int)$agg->last_activity;
            if ($lastAct > 0) {
                $isOnline    = ($nowEpoch - $lastAct) <= ($onlineWindow * 60);
                $lastSeenIso = \Illuminate\Support\Carbon::createFromTimestamp($lastAct)->toIso8601String();
            }
        } else {
            // fallback halus utk current user
            if (Auth::id() === (int)$r->id) {
                $sessRow = DB::table('sessions')->where('id', $request->session()->getId())->first();
                if ($sessRow) {
                    $isLoggedIn = true;
                    $lastAct    = (int)$sessRow->last_activity;
                    $isOnline   = ($nowEpoch - $lastAct) <= ($onlineWindow * 60);
                    $lastSeenIso= \Illuminate\Support\Carbon::createFromTimestamp($lastAct)->toIso8601String();
                }
            }
        }

        return [
            'id'         => (int)$r->id,
            'username'   => $r->username,
            'first_name' => $r->first_name,
            'last_name'  => $r->last_name,
            'email'      => $r->email,
            'puskesmas'  => [
                'id'   => $r->puskesmas_id,
                'nama' => $r->puskesmas_nama,
            ],
            'roles'        => $roles,
            'last_seen'    => $lastSeenIso,
            'is_online'    => $isOnline,
            'is_logged_in' => $isLoggedIn,
        ];
    })->values();

    return response()->json([
        'data' => $data,
        'meta' => [
            'total'      => $total,
            'page'       => $page,
            'page_size'  => $pageSize,
            'online_min' => $onlineWindow,
        ],
    ]);
}


    // PATCH /api/owner/users/{id}/roles
    public function updateRoles($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'add'    => ['array'],
            'remove' => ['array'],
        ]);
        $validator->validate();

        $add    = array_values(array_unique(array_map('strval', $request->input('add', []))));
        $remove = array_values(array_unique(array_map('strval', $request->input('remove', []))));

        $me = Auth::user();
        if ((int)$id === (int)$me->id && in_array('owner', $remove, true)) {
            return response()->json(['ok' => false, 'message' => 'Tidak boleh mencabut role owner dari akun sendiri.'], 422);
        }

        $roles = DB::table('groups')
            ->whereIn('name', array_unique(array_merge($add, $remove)))
            ->pluck('id','name'); // name => id

        DB::beginTransaction();
        try {
            // add
            if (!empty($add)) {
                foreach ($add as $rn) {
                    $gid = $roles[$rn] ?? null;
                    if (!$gid) continue;

                    $exists = DB::table('users_groups')
                        ->where('user_id', $id)
                        ->where('group_id', $gid)
                        ->exists();

                    if (!$exists) {
                        DB::table('users_groups')->insert([
                            'user_id'    => (int)$id,
                            'group_id'   => (int)$gid,
                            'nama_group' => $rn,
                        ]);
                    }
                }
            }
            // remove
            if (!empty($remove)) {
                $gids = [];
                foreach ($remove as $rn) if (isset($roles[$rn])) $gids[] = (int)$roles[$rn];
                if ($gids) {
                    DB::table('users_groups')
                        ->where('user_id', $id)
                        ->whereIn('group_id', $gids)
                        ->delete();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok'=>false,'message'=>'Gagal menyimpan role','error'=>$e->getMessage()],500);
        }

        $newRoles = DB::table('users_groups as ug')
            ->join('groups as g','g.id','=','ug.group_id')
            ->where('ug.user_id',$id)
            ->pluck('g.name')->unique()->values()->all();

        return response()->json(['ok'=>true,'roles'=>$newRoles]);
    }
public function updatePasswordChanged($id, Request $request)
{
    // HANYA owner/admin boleh (opsional, sesuaikan kebijakanmu)
    // if (!Auth::user() || !Auth::user()->hasRole('owner')) abort(403);

    $data = $request->validate([
        'mode'      => ['required', 'in:now,date,force'],
        'date'      => ['nullable', 'date'],    // wajib kalau mode=date
        'days_ago'  => ['nullable','integer','min:0','max:36500'], // opsional fallback
    ]);

    // Tentukan timestamp final
    $ts = null;
    switch ($data['mode']) {
        case 'now':
            $ts = now('Asia/Jakarta')->timestamp; // simpan tetap sebagai epoch (UTC-agnostik)
            break;

        case 'date':
            // Jika frontend kirim YYYY-MM-DD (atau dengan jam), parse jadi epoch
            // Default timezone Asia/Jakarta biar enak input
            if (empty($data['date'])) {
                return response()->json(['ok'=>false,'message'=>'Tanggal wajib diisi untuk mode=date'], 422);
            }
            $ts = \Illuminate\Support\Carbon::parse($data['date'], 'Asia/Jakarta')->timestamp;
            break;

        case 'force':
            // Paksa “wajib ganti” => set 16 hari yang lalu
            $days = $data['days_ago'] ?? 16;
            $ts = now('Asia/Jakarta')->subDays($days)->timestamp;
            break;
    }

    // Update kolom
    $updated = DB::table('users')
        ->where('id', (int)$id)
        ->update(['forgotten_password_time' => $ts]);

    if (!$updated) {
        return response()->json(['ok'=>false,'message'=>'User tidak ditemukan atau tidak ada perubahan'], 404);
    }

    // (Opsional) agar UI langsung reflect, hapus cache flag khusus
    Cache::forget("user:last_seen:{$id}");

    return response()->json([
        'ok' => true,
        'user_id' => (int)$id,
        'forgotten_password_time' => $ts,
        'formatted' => \Illuminate\Support\Carbon::createFromTimestamp($ts)->format('d-m-Y H:i'),
    ]);
}

    // POST /api/owner/users/{id}/force-logout
    public function forceLogout($id)
    {
        // TANPA DB: tandai user ini diblokir via cache
        Cache::put("user:blocked:{$id}", true, now()->addDay()); // TTL 1 hari (sesuaikan)

        // (opsional) hapus last_seen agar di UI langsung turun statusnya
        Cache::forget("user:last_seen:{$id}");

        return response()->json(['ok' => true]);
    }

    public function destroyUser($id, Request $request)
{
    $targetId = (int)$id;
    $meId = (int)Auth::id();

    // 1) Cegah hapus diri sendiri
    if ($targetId === $meId) {
        return response()->json([
            'ok' => false,
            'message' => 'Tidak boleh menghapus akun sendiri.'
        ], 422);
    }

    // 2) Cek user ada?
    $user = DB::table('users')->where('id', $targetId)->first();
    if (!$user) {
        return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
    }

    // 3) Kalau target punya role 'owner', pastikan bukan owner terakhir
    $targetRoles = DB::table('users_groups as ug')
        ->join('groups as g', 'g.id', '=', 'ug.group_id')
        ->where('ug.user_id', $targetId)
        ->pluck('g.name')
        ->map(fn($n) => strtolower($n))
        ->values();

    $isOwner = $targetRoles->contains('owner');
    if ($isOwner) {
        $ownerCount = DB::table('users_groups as ug')
            ->join('groups as g', 'g.id', '=', 'ug.group_id')
            ->where('g.name', 'owner')
            ->distinct()
            ->count('ug.user_id');

        if ($ownerCount <= 1) {
            return response()->json([
                'ok' => false,
                'message' => 'Tidak bisa menghapus: ini adalah user owner terakhir.'
            ], 422);
        }
    }

    DB::beginTransaction();
    try {
        // 4) Bersihkan relasi/pivot & sesi
        DB::table('users_groups')->where('user_id', $targetId)->delete();
        DB::table('sessions')->where('user_id', $targetId)->delete();

        // (opsional) kalau ada tabel lain yang refer user_id, bersihkan di sini juga

        // 5) Hapus user
        DB::table('users')->where('id', $targetId)->delete();

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'ok' => false,
            'message' => 'Gagal menghapus user.',
            'error' => $e->getMessage(),
        ], 500);
    }

    // Bersihkan cache status online (kalau ada)
    Cache::forget("user:blocked:{$targetId}");
    Cache::forget("user:last_seen:{$targetId}");

    return response()->json(['ok' => true]);
}

}
