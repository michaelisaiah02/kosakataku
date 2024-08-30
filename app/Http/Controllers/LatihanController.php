<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use App\Models\Latihan;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LatihanController extends Controller
{
    protected $latihan;
    public function __construct()
    {
        $this->latihan = Latihan::where('id_user', auth()->user()->id)->where('selesai', false);
    }

    public function beranda()
    {
        // Ambil bahasa yang paling banyak dilatih dan jumlah total latihannya
        $bahasaPalingBanyakDilatih = Latihan::select('id_bahasa', DB::raw('count(*) as latihan_count'))
            ->groupBy('id_bahasa')
            ->orderBy('latihan_count', 'desc')
            ->first();

        // Nama bahasa dan jumlah latihan
        if ($bahasaPalingBanyakDilatih) {
            $bahasa = Bahasa::find($bahasaPalingBanyakDilatih->id_bahasa);
            $bahasaPalingBanyak = $bahasa ? $bahasa->indonesia : 'Tidak ada data';
            $jumlahLatihanBahasa = $bahasaPalingBanyakDilatih->latihan_count;
        } else {
            $bahasaPalingBanyak = 'Tidak ada data';
            $jumlahLatihanBahasa = 0;
        }

        // Jumlah pengguna yang pernah latihan (tetap sama)
        $jumlahPenggunaKosakataku = Latihan::distinct('id_user')->count('id_user');

        return view('beranda', compact('jumlahPenggunaKosakataku', 'bahasaPalingBanyak', 'jumlahLatihanBahasa'));
    }

    public function panduan()
    {
        return view('panduan');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->latihan->exists()) {
            if (empty($this->latihan->first()->list_latihan_kosakata)) {
                return redirect()->route('latihan', [$this->latihan->first()->id, 'kosakata']);
            } else {
                return redirect()->route('latihan', [$this->latihan->first()->id, 'artikata']);
            }
        }
        $languages = Bahasa::all()->sortBy('indonesia');
        $categories = Kategori::all()->sortBy('indonesia');

        return view('preferensi', [
            'languages' => $languages,
            'categories' => $categories
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
            ],
            [
                'id_bahasa.required' => 'Pilih bahasa yang mau dilatih!',
                'id_kategori.required' => 'Pilih kategori kosakata yang mau dilatih!',
            ]
        );
        $request->merge([
            'id_user' => auth()->id()
        ]);
        // Validasi dan simpan pengaturan latihan ke database
        $latihan = Latihan::create($request->all());

        // Redirect ke halaman edit untuk memulai latihan
        return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'kosakata']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Latihan $latihan)
    {
        if ($this->latihan->exists()) {
            if (empty($latihan->list_latihan_kosakata)) {
                return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'kosakata'])->with('error', 'Kamu belum menyelesaikan latihan, semangat!');
            }
            if (empty($latihan->list_latihan_artikata)) {
                return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'artikata'])->with('error', 'Kamu belum menyelesaikan latihan, semangat!');
            }
        }
        return view('hasil', [
            'latihan' => $latihan->load('bahasa', 'kategori')
        ])->with('success', 'Selamat telah menyelesaikan latihan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function latihan(Latihan $latihan, $jenisLatihan)
    {
        if ($latihan->selesai == true) {
            return redirect()->route('latihan.index')->with('error', 'Latihan sebelumnya sudah selesai, kamu bisa melihat hasilnya di riwayat.');
        }
        if ($jenisLatihan == "kosakata") {
            if (empty($latihan->list_latihan_kosakata)) {
                return view('latihan-kosakata', [
                    'latihan' => $latihan,
                    'bahasa' => $latihan->bahasa()->first(),
                    'kategori' => $latihan->kategori()->first()
                ]);
            } else {
                return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'artikata']);
            }
        }
        if ($jenisLatihan == "artikata") {
            if (empty($latihan->list_latihan_kosakata)) {
                return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'kosakata']);
            } else {
                if (empty($latihan->list_latihan_artikata)) {
                    return view('latihan-artikata', [
                        'latihan' => $latihan,
                    ]);
                } else {
                    return redirect()->route('latihan.show', $latihan->id);
                }
            }
        }
    }

    private function generateSoalArtiKata($listLatihanKosakata)
    {
        $data = json_decode($listLatihanKosakata, true);
        $soal = [];

        // Mengacak urutan data
        shuffle($data);

        foreach ($data as $item) {
            $tipesoal = rand(0, 1); // 0 untuk kata ke terjemahan, 1 untuk terjemahan ke kata
            $pilihan = $this->getPilihan($data, $item, $tipesoal);

            if ($tipesoal == 0) {
                $soal[] = [
                    'pertanyaan' => $item['kata'],
                    'jawaban_benar' => $item['terjemahan'],
                    'pilihan' => $pilihan,
                    'tipe' => 'kata_ke_terjemahan'
                ];
            } else {
                $soal[] = [
                    'pertanyaan' => $item['terjemahan'],
                    'jawaban_benar' => $item['kata'],
                    'pilihan' => $pilihan,
                    'tipe' => 'terjemahan_ke_kata'
                ];
            }
        }

        return $soal;
    }

    private function getPilihan($data, $currentItem, $tipesoal)
    {
        $pilihan = [];
        $pilihan[] = $tipesoal == 0 ? $currentItem['terjemahan'] : $currentItem['kata'];

        while (count($pilihan) < 3) {
            $randomItem = $data[array_rand($data)];
            $pilihanBaru = $tipesoal == 0 ? $randomItem['terjemahan'] : $randomItem['kata'];

            if (!in_array($pilihanBaru, $pilihan) && $pilihanBaru != $currentItem['terjemahan'] && $pilihanBaru != $currentItem['kata']) {
                $pilihan[] = $pilihanBaru;
            }
        }

        shuffle($pilihan);
        return $pilihan;
    }

    public function soalArtikata(Latihan $latihan)
    {
        $soalArtiKata = $this->generateSoalArtiKata($latihan->list_latihan_kosakata);
        return response()->json($soalArtiKata);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Latihan $latihan, $jenisLatihan)
    {
        if ($jenisLatihan == "kosakata") {
            $latihan->update([
                'jumlah_pengucapan_benar' => $request->jumlah_benar,
                'list_latihan_kosakata' => $request->list
            ]);
            // dd($latihan);
            return redirect()->route('latihan', ['latihan' => $latihan->id, 'jenisLatihan' => 'artikata']);
        }
        if ($jenisLatihan == "artikata") {
            // dd($request->all());
            $latihan->update([
                'jumlah_artikata_benar' => $request->jumlah_benar,
                'list_latihan_artikata' => $request->list,
                'selesai' => true
            ]);
            return redirect()->route('latihan.show', $latihan->id);
        }
    }

    public function riwayat()
    {
        $histories = Latihan::where('id_user', auth()->user()->id)
            ->where('selesai', true)
            ->with('bahasa', 'kategori')
            ->get() ?? null;

        if ($histories->isEmpty()) {
            return view('riwayat', ['info' => 'Kamu belum pernah latihan kosakata!']);
        }

        $jumlah_kata = 5;

        // Jumlah latihan
        $jumlahLatihan = $histories->count();

        // Bahasa yang pertama kali dipelajari
        $bahasaPertamaDipelajari = $histories->sortBy('created_at')->first();

        // Bahasa yang terakhir dipelajari
        $bahasaTerakhirDipelajari = $histories->sortByDesc('created_at')->first();

        // Bahasa yang sering dipelajari
        $bahasaSeringDipelajari = $histories->groupBy('id_bahasa')->map(function ($bahasa) {
            return $bahasa->count();
        })->sortDesc()->take(1);

        $bahasaSeringDipelajariIndex = $bahasaSeringDipelajari->keys()->first();
        $bahasaSeringDipelajariCount = $bahasaSeringDipelajari->first();
        $bahasaSeringDipelajariName = Bahasa::find($bahasaSeringDipelajariIndex)->indonesia;

        // Bahasa yang paling banyak benar (per latihan)
        $bahasaPalingBanyakBenar = $histories->mapToGroups(function ($history) use ($jumlah_kata) {
            $benarPersentase = $history->jumlah_benar / $jumlah_kata * 100;
            return [$history->id_bahasa => ['persentase' => $benarPersentase, 'id' => $history->id]];
        })->map(function ($persentase) {
            return $persentase->sortByDesc('persentase')->first();
        })->sortDesc()->take(1);

        $bahasaPalingBanyakBenarId = $bahasaPalingBanyakBenar->keys()->first();
        $bahasaPalingBanyakBenarData = $bahasaPalingBanyakBenar->first();
        $bahasaPalingBanyakBenarLatihanId = $bahasaPalingBanyakBenarData['id'];
        $bahasaPalingBanyakBenarCount = $bahasaPalingBanyakBenarData['persentase'];
        $bahasaPalingBanyakBenarName = Bahasa::find($bahasaPalingBanyakBenarId)->indonesia;

        // Latihan paling lama
        $latihanPalingLama = $histories->sortByDesc(function ($history) {
            return strtotime($history->updated_at) - strtotime($history->created_at);
        })->first();

        $latihanPalingLamaDuration = strtotime($latihanPalingLama->updated_at) - strtotime($latihanPalingLama->created_at);
        $latihanPalingLamaHours = floor($latihanPalingLamaDuration / 3600);
        $latihanPalingLamaMinutes = floor(($latihanPalingLamaDuration % 3600) / 60);
        $latihanPalingLamaSeconds = $latihanPalingLamaDuration % 60;
        $latihanPalingLamaFormatted = ($latihanPalingLamaHours == 0 ? "" : $latihanPalingLamaHours . " Jam ") . ($latihanPalingLamaMinutes == 0 ?  "" : $latihanPalingLamaMinutes . " Menit ") . $latihanPalingLamaSeconds . " Detik";

        // Bahasa yang paling banyak salah (per latihan)
        $bahasaPalingBanyakSalah = $histories->mapToGroups(function ($history) use ($jumlah_kata) {
            $salahPersentase = ($jumlah_kata - $history->jumlah_benar) / $jumlah_kata * 100;
            return [$history->id_bahasa => ['persentase' => $salahPersentase, 'id' => $history->id]];
        })->map(function ($persentase) {
            return $persentase->sortByDesc('persentase')->first();
        })->sortDesc()->take(1);

        $bahasaPalingBanyakSalahId = $bahasaPalingBanyakSalah->keys()->first();
        $bahasaPalingBanyakSalahData = $bahasaPalingBanyakSalah->first();
        $bahasaPalingBanyakSalahLatihanId = $bahasaPalingBanyakSalahData['id'];
        $bahasaPalingBanyakSalahCount = $bahasaPalingBanyakSalahData['persentase'];
        $bahasaPalingBanyakSalahName = Bahasa::find($bahasaPalingBanyakSalahId)->indonesia;

        return view('riwayat', [
            'histories' => $histories,
            'jumlahKata' => $jumlah_kata,
            'jumlahLatihan' => $jumlahLatihan,
            'bahasaPertamaDipelajari' => $bahasaPertamaDipelajari,
            'bahasaTerakhirDipelajari' => $bahasaTerakhirDipelajari,
            'bahasaSeringDipelajari' => [
                'bahasa' => $bahasaSeringDipelajariName,
                'jumlah' => $bahasaSeringDipelajariCount
            ],
            'bahasaPalingBanyakBenar' => [
                'id' => $bahasaPalingBanyakBenarLatihanId,
                'bahasa' => $bahasaPalingBanyakBenarName,
                'jumlah' => $bahasaPalingBanyakBenarCount
            ],
            'latihanPalingLama' =>  [
                'id' => $latihanPalingLama->id,
                'bahasa' => $latihanPalingLama->bahasa->indonesia,
                'durasi' => $latihanPalingLamaFormatted
            ],
            'bahasaPalingBanyakSalah' => [
                'id' => $bahasaPalingBanyakSalahLatihanId,
                'bahasa' => $bahasaPalingBanyakSalahName,
                'jumlah' => $bahasaPalingBanyakSalahCount
            ]
        ]);
    }

    public function detailRiwayat($id)
    {
        $latihan = Latihan::with('bahasa', 'kategori')->findOrfail($id);
        return response()->json($latihan);
    }
}
