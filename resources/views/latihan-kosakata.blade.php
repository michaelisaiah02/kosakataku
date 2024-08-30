<x-app-layout>
    <x-slot name="header">
        <div class="row justify-content-between py-3 mt-header">
            <div class="col-md-auto col-12 mb-md-0 mb-2 text-md-start text-center">
                <h2 class="fw-semibold text-dark p-0 m-0">
                    Latihan Kosakata
                </h2>
            </div>
            <div class="col col-md-auto d-flex gap-3" id="skipSection">
                <button class="btn btn-sm btn-danger d-flex justify-content-center" id="skipBtn">
                    <p class="my-auto">Lewati Kata Ini</p>
                    <div class="icon my-auto">
                        <i class="bi bi-skip-end"></i>
                    </div>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 full-screen d-flex align-items-center">
        <div class="container">
            <div class="row mb-3">
                <h2 class="text-center" id="kata"></h2>
                <p class="text-center" id="ejaan"></p>
                <div id="translatedIcon">
                    <h2 class="text-center"><i class="bi bi-translate"></i></h2>
                    <h2 class="text-center"><i class="bi bi-arrow-down"></i></h2>
                </div>
                <h2 class="text-center" id="translatedWord"></h2>
            </div>
            <div class="d-flex justify-content-center align-items-center full-screen" id="loading">
                <div class="row justify-content-center text-center">
                    <h1 class="pb-3">Tunggu sebentar...</h1>
                    <div class="spinner-border text-primary" style="width: 10rem; height: 10rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3" id="correctSpellingAudio">
            </div>
            <div class="row mb-3 justify-content-center" id="spellingSection">
                <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center my-3 w-50"
                    id="spellingBtn">
                    <div id="offMic">
                        <p class="mt-3 ms-2">Mulai ucapkan</p>
                    </div>
                    <div id="onMic">
                        <div class="icon my-auto d-flex justify-content-center">
                            <img src="{{ asset('img/bars.svg') }}" alt="bars" id="recIcon"
                                style="display: block; height: 40px;" />
                        </div>
                        <p class="mt-3 ms-2">Mendengarkan...</p>
                    </div>
                </button>
            </div>
            <div class="row mb-3" id="spelledSection">
                <h3 id="spelledWordLabel" class="text-center mt-3 mb-1">Kata yang terdengar: </h3>
                <h3 id="spelledWord" class="text-center mt-1 mb-3"></h3>
            </div>
            <div class="row mb-3" id="exampleSentenceSection">
                <div class="card p-0 rounded-5">
                    <div class="card-header bg-primary text-light rounded-top-5 text-center">
                        Contoh Kalimat
                    </div>
                    <div class="card-body">
                        <div id="exampleCarousel" class="carousel carousel-dark slide" data-ride="carousel">
                            <div class="carousel-inner" id="exampleSentencesCarousel">
                                <!-- Contoh kalimat akan ditampilkan di sini -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#exampleCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#exampleCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center" id="trueSection">
                <div class="col d-flex justify-content-center" id="finishBtn">
                    <button class="btn btn-info" x-data @click="window.saveResults(true)">
                        <p class="my-auto me-2">Selesai</p>
                        <div class="icon my-auto">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </button>
                </div>
                <div class="col d-flex justify-content-center" id="nextBtn">
                    <button class="btn btn-info">
                        <p class="my-auto me-2">Lanjut</p>
                        <div class="icon my-auto">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <form id="latihanForm"
        action="{{ route('latihan.update', ['latihan' => $latihan->id, 'jenisLatihan' => 'kosakata']) }}"
        method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="jumlah_benar" id="jumlah_benar" value="">
        <input type="hidden" name="list" id="list" value="">
    </form>
    <div id="data" data-id-latihan="{{ $latihan->id }}" data-id-bahasa="{{ $bahasa->id }}"
        data-bahasa="{{ $bahasa->inggris }}" data-kategori="{{ $kategori->inggris }}"
        data-bantuan-suara="{{ $latihan->bantuan_suara }}"></div>
    @vite(['resources/js/latihan-kosakata.js'])
</x-app-layout>
