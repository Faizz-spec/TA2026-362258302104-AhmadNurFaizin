<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordForceController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'new_password' => 'password baru'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Cek apakah password baru sama dengan password lama
        if (Hash::check($request->input('new_password'), $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Password baru tidak boleh sama dengan password lama.'
            ], 422);
        }

        // Update password
        DB::table('users')->where('id', $user->id)->update([
            'old_password'            => $user->password, // opsional
            'password'                => Hash::make($request->input('new_password')),
            'forgotten_password_time' => time(),
        ]);

        // Bersihkan flag & regenerasi sesi
        $request->session()->forget('force_password_change');
        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }
}
