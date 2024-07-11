<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Latihan Kosakata
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row my-3">
                <h2 class="text-center" id="randomWord"></h2>
                <h2 class="text-center">=</h2>
                <h2 class="text-center" id="translatedWord"></h2>
            </div>
            <div class="row my-3" id="correctSpellingAudio">
            </div>
            <div class="row" id="spellingSection">
                <button type="button" class="btn btn-info d-flex justify-content-center my-3" id="spellingBtn">
                    <div id="offMic">
                        <p class="mt-3 ms-2">Mulai ucapkan</p>
                    </div>
                    <div id="onMic">
                        <div class="icon my-auto">
                            <img src="{{ asset('img/bars.svg') }}" alt="bars" id="recIcon"
                                style="display: block; height: 40px;" />
                        </div>
                        <p class="mt-3 ms-2">Mendengarkan... tekan tombol ini jika sudah selesai</p>
                    </div>
                </button>
                <h3 id="spelledWordLabel" class="text-center mt-3 mb-1">Kata yang terdengar: </h3>
                <h3 id="spelledWord" class="text-center mt-1 mb-3"></h3>
            </div>
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        Contoh Kalimat
                    </div>
                    <div class="card-body">
                        <div id="exampleCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" id="exampleSentencesCarousel">
                                <!-- Carousel items will be inserted here -->
                            </div>
                            <a class="carousel-control-prev" href="#exampleCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#exampleCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="nextSection">
                <button class="btn btn-info d-flex justify-content-center" id="nextBtn">
                    <p class="mt-3 ms-2">Lanjut</p>
                    <div class="icon my-auto">
                        <img src="{{ asset('img/arrow-right.svg') }}" alt="arrow-right"
                            style="display: block; height: 20px;" />
                    </div>
                </button>
            </div>
        </div>
    </div>
    @vite(['resources/js/latihan.js'])
</x-app-layout>
