<?php
// app/Http/Middleware/HandleInertiaRequests.php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => function () {
                    $u = Auth::user();
                    if (!$u)
                        return null;

                    // Puskesmas dari unit_profiles (unit_id)
                    $pusk = DB::table('unit_profiles')
                        ->select('unit_id as id', 'nama_unit as nama')
                        ->where('unit_id', $u->unit)
                        ->first();

                    // Password last changed (fallback created_on)
                    $changedTs = $u->forgotten_password_time ?: ($u->created_on ?? time());
                    $changed = Carbon::createFromTimestamp((int) $changedTs);

                    // Wajib ganti jika >= 15 hari (boleh override via session flag dari LoginController)
                    $mustChange = session('force_password_change', $changed->diffInDays(now()) >= 15);

                    // Roles user (lowercase) dan abilities (union dari rolemap)
                    $roles = array_map('strtolower', $u->roleNames() ?? []);
                    $rolemap = Config::get('rolemap', []); // fitur => [roles...]
                    $abilities = collect($rolemap)
                        ->filter(
                            fn($allowed) =>
                            collect($allowed)->map(fn($r) => strtolower($r))->intersect($roles)->isNotEmpty()
                        )
                        ->keys()->values()->all();

                    return [
                        'id' => $u->id,
                        'username' => $u->username,
                        'firstName' => $u->first_name,
                        'lastName' => $u->last_name,
                        'roles' => $roles,
                        'abilities' => $abilities,
                        'puskesmas' => $pusk ?: null,
                        'mustChangePassword' => $mustChange,
                        'passwordLastChanged' => $changed->format('d-m-Y H:i'),
                    ];
                },
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ]);
    }
}
