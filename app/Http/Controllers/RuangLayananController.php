<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RuangLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        return Inertia::render('Ruang_Layanan/index');
        
    }

    public function dataPasienPoli() {
        return Inertia::render('Ruang_Layanan/Umum/pasien_poli');

    }

    public function layanan() {
        return Inertia::render('Ruang_Layanan/Umum/pelayanan');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
