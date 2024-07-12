<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use App\Models\Kategori;
use App\Models\Latihan;
use App\Models\TingkatKesulitan;
use Illuminate\Http\Request;

class LatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Bahasa::all();
        $categories = Kategori::all();
        $difficulties = TingkatKesulitan::all();

        return view('preferensi', [
            'languages' => $languages,
            'categories' => $categories,
            'difficulties' => $difficulties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_bahasa' => 'required|exists:bahasa,id',
            'id_kategori' => 'required|exists:kategori,id',
            'id_tingkat_kesulitan' => 'required|exists:tingkat_kesulitan,id',
        ]);
        dd($request->all());
        // Validasi dan simpan pengaturan latihan ke database
        $latihan = Latihan::create($request->all());

        // Redirect ke halaman edit untuk memulai latihan
        return redirect()->route('latihan.edit', $latihan->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Latihan $latihan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Latihan $latihan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Latihan $latihan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Latihan $latihan)
    {
        //
    }
}
