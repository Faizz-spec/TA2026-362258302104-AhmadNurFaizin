<?php
namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        $allowed = [];
        foreach ($roles as $r) foreach (explode(',', $r) as $p) $allowed[] = trim(strtolower($p));

        if (count(array_intersect($allowed, $user->roleNames())) > 0) return $next($request);

        abort(403);
    }
}
