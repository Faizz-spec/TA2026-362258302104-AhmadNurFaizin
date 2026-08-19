<?php

namespace App\Http\Controllers\MalSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MalSehat\KategoriUnit;
use Inertia\Inertia;


class PTMController extends Controller
{
    public function index()
    {
        $puskesmas = KategoriUnit::all();
        dd($puskesmas);

        return Inertia::render('MalSehat/PTM/KonselingBerhentiMerokok', [
            'puskesmas' => $puskesmas
        ]);
    }
    //
}
