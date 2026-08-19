<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        // 1) Validasi input dasar + reCAPTCHA
        $data = Validator::make($request->all(), [
            'username'             => ['required', 'string'],
            'password'             => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'string'],
        ])->validate();

        // 2) Verifikasi reCAPTCHA ke Google
        try {
            $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $data['g-recaptcha-response'],
                'remoteip' => $request->ip(),
            ])->json();
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'captcha' => 'Gagal menghubungi layanan reCAPTCHA. Coba lagi.',
            ]);
        }

        if (!($resp['success'] ?? false)) {
            throw ValidationException::withMessages([
                'captcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ]);
        }

        // 3) Attempt login dengan username/password
        if (!Auth::attempt(['username' => $data['username'], 'password' => $data['password']])) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        // 4) Regenerate session & ikat sesi ke user_id (untuk panel owner: status online)
        $request->session()->regenerate();
        try {
            DB::table('sessions')
                ->where('id', $request->session()->getId())
                ->update([
                    'user_id'       => Auth::id(),
                    'last_activity' => time(),
                ]);
        } catch (\Throwable $e) {
            // lanjutkan walau update sessions gagal
        }

        $user = Auth::user();

        // 5) Password expiry: 15 hari sejak "forgotten_password_time" (fallback: created_on)
        $changedTs   = $user->forgotten_password_time ?: ($user->created_on ?? time());
        $lastChanged = Carbon::createFromTimestamp((int) $changedTs);
        $expired     = $lastChanged->diffInDays(now()) >= 15;

        // simpan flag di session untuk dibaca di Inertia share()
        session(['force_password_change' => $expired]);

        // 6) Tentukan redirect berdasarkan prioritas role
        //    (pastikan nama rute di bawah ini ada di routes kamu)
        $roles = collect($user->roleNames())->map(fn($r) => strtolower($r));

        $to = route('login'); // fallback
        if ($roles->contains('owner') || $roles->contains('kapus')) {
            $to = route('dashboard');
        } elseif ($roles->contains('loket')) {
            $to = route('loket.index');
        } elseif ($roles->contains('pelayanan')) {
            $to = route('ruang-layanan.poli');
        } elseif ($roles->contains('admin')) {
            $to = route('laporan.loket'); // sesuaikan jika ada landing lain utk admin
        } elseif ($roles->contains('laborat')) {
            $to = route('laborat.index');
        }

        // 7) Balikkan JSON agar frontend bisa redirect & munculkan popup ganti password jika perlu
        return response()->json([
            'ok'                      => true,
            'redirect'                => $to,
            'require_password_change' => $expired,
        ]);
    }
}
