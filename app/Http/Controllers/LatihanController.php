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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate(
            [
                'id_bahasa' => 'required|exists:bahasa,id',
                'id_kategori' => 'required|exists:kategori,id',
                'id_tingkat_kesulitan' => 'required|exists:tingkat_kesulitan,id',
            ],
            [
                'id_bahasa.exists' => 'Pilih bahasa yang mau dilatih!',
                'id_kategori.exists' => 'Pilih kategori kosakata yang mau dilatih!',
                'id_tingkat_kesulitan.exists' => 'Pilih kesulitan yang mau dipilih!',
            ]
        );
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
            'latihan' => $latihan->load('bahasa', 'kategori', 'tingkatKesulitan')
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
            'tingkat_kesulitan' => $latihan->tingkatKesulitan()->first()
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

    public function riwayat()
    {
        $histories = Latihan::where('id_user', auth()->user()->id)
            ->where('selesai', true)
            ->with('bahasa', 'kategori', 'tingkatKesulitan')
            ->get();

        // 1. Bahasa yang sering dipelajari
        $bahasaSeringDipelajari = $histories->groupBy('id_bahasa')->map(function ($bahasa) {
            return $bahasa->count();
        })->sortDesc()->take(5);

        $bahasaSeringDipelajariId = $bahasaSeringDipelajari->keys()->first();
        $bahasaSeringDipelajariCount = $bahasaSeringDipelajari->first();
        $bahasaSeringDipelajariName = Bahasa::find($bahasaSeringDipelajariId)->indonesia;

        // 2. Bahasa yang paling banyak benar (per latihan)
        $bahasaPalingBanyakBenar = $histories->mapToGroups(function ($history) {
            $benarPersentase = $history->jumlah_benar / $history->jumlah_kata * 100;
            return [$history->id_bahasa => $benarPersentase];
        })->map(function ($persentase) {
            return $persentase->avg();
        })->sortDesc()->take(1);

        $bahasaPalingBanyakBenarId = $bahasaPalingBanyakBenar->keys()->first();
        $bahasaPalingBanyakBenarCount = $bahasaPalingBanyakBenar->first();
        $bahasaPalingBanyakBenarName = Bahasa::find($bahasaPalingBanyakBenarId)->indonesia;

        // 3. Latihan paling lama
        $latihanPalingLama = $histories->sortByDesc(function ($history) {
            return strtotime($history->updated_at) - strtotime($history->created_at);
        })->first();

        $latihanPalingLamaDuration = strtotime($latihanPalingLama->updated_at) - strtotime($latihanPalingLama->created_at);
        $latihanPalingLamaHours = floor($latihanPalingLamaDuration / 3600);
        $latihanPalingLamaMinutes = floor(($latihanPalingLamaDuration % 3600) / 60);
        $latihanPalingLamaSeconds = $latihanPalingLamaDuration % 60;
        $latihanPalingLamaFormatted = ($latihanPalingLamaHours == 0 ? "" : $latihanPalingLamaHours . " Jam ") . ($latihanPalingLamaMinutes == 0 ?  "" : $latihanPalingLamaMinutes . " Menit ") . $latihanPalingLamaSeconds . " Detik";

        // 4. Bahasa yang paling banyak salah (per latihan)
        $bahasaPalingBanyakSalah = $histories->mapToGroups(function ($history) {
            $salahPersentase = ($history->jumlah_kata - $history->jumlah_benar) / $history->jumlah_kata * 100;
            return [$history->id_bahasa => $salahPersentase];
        })->map(function ($persentase) {
            return $persentase->avg();
        })->sortDesc()->take(1);

        $bahasaPalingBanyakSalahId = $bahasaPalingBanyakSalah->keys()->first();
        $bahasaPalingBanyakSalahCount = $bahasaPalingBanyakSalah->first();
        $bahasaPalingBanyakSalahName = Bahasa::find($bahasaPalingBanyakSalahId)->indonesia;

        return view('riwayat', [
            'histories' => $histories,
            'bahasaSeringDipelajari' => $bahasaSeringDipelajariName . " (" . $bahasaSeringDipelajariCount . " Kali)",
            'bahasaPalingBanyakBenar' => $bahasaPalingBanyakBenarName . " (" . round($bahasaPalingBanyakBenarCount) . "%)",
            'latihanPalingLama' =>  $latihanPalingLamaFormatted . " (" . $latihanPalingLama->bahasa->indonesia . ")",
            'bahasaPalingBanyakSalah' => $bahasaPalingBanyakSalahName . " (" . round($bahasaPalingBanyakSalahCount) . "%)",
        ]);
    }

    public function detailRiwayat($id)
    {
        $latihan = Latihan::with('bahasa', 'kategori', 'tingkatKesulitan')->findOrfail($id);
        return response()->json($latihan);
    }
}
