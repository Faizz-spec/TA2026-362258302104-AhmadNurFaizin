<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranLangsungDetailController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Controller aktif dan terhubung.']);
    }
}
