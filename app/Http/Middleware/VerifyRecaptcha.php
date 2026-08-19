<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        // Cek hanya untuk POST /login (atau apapun kamu mau)
        if ($request->is('login') && $request->isMethod('post')) {
            $request->validate([
                'g-recaptcha-response' => 'required'
            ]);

            $client = new Client();
            $secret = '6LdYuYQrAAAAALxUtGSdSnXQ3i6f0AFxfKBYhqV7';
            $recaptcha = $request->input('g-recaptcha-response');

            $res = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $recaptcha
                ]
            ]);
            $body = json_decode((string)$res->getBody());

            if (!$body->success) {
                return back()->withErrors([
                    'email' => 'Verifikasi captcha gagal!',
                ]);
            }
        }
        return $next($request);
    }
}
