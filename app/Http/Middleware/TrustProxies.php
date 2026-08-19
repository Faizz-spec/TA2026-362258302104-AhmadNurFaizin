<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Percaya semua proxy (aman untuk dev/tunnel)
    protected $proxies = '*';

    // Pakai semua header X-Forwarded-* yang dikirim Cloudflare
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
