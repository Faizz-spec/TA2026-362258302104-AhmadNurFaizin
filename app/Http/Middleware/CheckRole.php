<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, $rolesCsv)
    {
        // Roles yang diperbolehkan dari parameter middleware: 'owner,admin'
        $allowed = collect(explode(',', (string) $rolesCsv))
            ->map(fn($r) => strtolower(trim($r)))
            ->filter()
            ->values();

        $user = $request->user();
        if (!$user) {
            abort(403, 'anda tidak berhak');
        }

        // Ambil semua role user dari pivot (helper roleNames()).
        // Normalisasi ke lowercase biar konsisten.
        $userRoles = collect(method_exists($user, 'roleNames') ? $user->roleNames() : [])
            ->map(fn($r) => strtolower($r))
            ->unique()
            ->values();

        // Fallback kalau masih ada kolom single 'role' lama
        if ($userRoles->isEmpty() && !empty($user->role)) {
            $userRoles = collect([strtolower($user->role)]);
        }

        // Lolos jika ada irisan minimal 1 role
        $ok = $userRoles->intersect($allowed)->isNotEmpty();

        Log::info('CHECK ROLE MIDDLEWARE', [
            'user_id'      => $user->id ?? null,
            'user_roles'   => $userRoles->all(),
            'allowed'      => $allowed->all(),
            'path'         => $request->path(),
            'method'       => $request->method(),
            'authorized'   => $ok,
        ]);

        if (!$ok) {
            abort(403, 'anda tidak berhak');
        }

        return $next($request);
    }
}
