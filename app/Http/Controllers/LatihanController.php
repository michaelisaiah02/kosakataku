<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use App\Models\Kategori;
use App\Models\Latihan;
use App\Models\TingkatKesulitan;
use Illuminate\Http\Request;

class LatihanController extends Controller
{
    protected $latihan;
    public function __construct()
    {
        $this->latihan = Latihan::where('id_user', auth()->user()->id)->where('selesai', false);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->latihan->exists()) {
            return redirect()->route('latihan.edit', $this->latihan->first()->id);
        }
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
        $request->merge([
            'id_user' => auth()->id()
        ]);
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
        if ($this->latihan->exists()) {
            return redirect()->route('latihan.edit', $latihan->id)->with('error', 'Kamu belum menyelesaikan latihan, semangat!');
        }
        return view('hasil', [
            'latihan' => $latihan->load('bahasa', 'kategori', 'tingkatKesulitan'),
            'nilai' => $latihan->jumlah_benar / $latihan->jumlah_kata * 100
        ])->with('success', 'Selamat telah menyelesaikan latihan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Latihan $latihan)
    {
        if ($latihan->selesai == true) {
            return redirect()->route('latihan.index')->with('error', 'Latihan sebelumnya sudah selesai, kamu bisa melihat hasilnya di riwayat.');
        }
        return view('latihan', [
            'latihan' => $latihan,
            'bahasa' => $latihan->bahasa()->first(),
            'kategori' => $latihan->kategori()->first()->kategori,
            'tingkat_kesulitan' => $latihan->tingkatKesulitan()->first()->tingkat_kesulitan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Latihan $latihan)
    {
        $latihan->update([
            'jumlah_kata' => $request->jumlah_kata,
            'jumlah_benar' => $request->jumlah_benar,
            'list' => $request->list,
            'selesai' => true
        ]);

        return redirect()->route('latihan.show', $latihan->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Latihan $latihan)
    {
        //
    }
}
