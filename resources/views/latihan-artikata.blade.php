<x-app-layout>
    <x-slot name="header">
        <div class="row justify-content-between py-3 mt-header">
            <div class="col-md-auto col-12 mb-md-0 mb-2 text-md-start text-center">
                <h2 class="fw-semibold text-dark p-0 m-0">
                    Latihan Arti Kata
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 full-screen d-flex align-items-center">
        <div class="container">
            <div class="row mb-3">
                <h2 class="text-center" id="kata">Soal kata pakai elemen ini</h2>
            </div>
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-center">
                    <div class="row justify-content-center mb-md-3 mb-1" id="bantuanSuara">
                        <div class="col-12 text-center">
                            <p for="jawaban" class="fs-4">Pilih Jawabanmu</p>
                        </div>
                        <div class="row justify-content-evenly mb-3">
                            <div class="col-md col-12 d-flex flex-column align-items-center">
                                <input type="radio" class="btn-check" name="options" id="option1"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary btn-lg my-md-auto mb-2 w-100"
                                    for="option1"></label>
                            </div>
                            <div class="col-md col-12 d-flex flex-column align-items-center">
                                <input type="radio" class="btn-check" name="options" id="option2"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary btn-lg my-md-auto mb-2 w-100"
                                    for="option2"></label>
                            </div>
                            <div class="col-md col-12 d-flex flex-column align-items-center">
                                <input type="radio" class="btn-check" name="options" id="option3"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary btn-lg my-md-auto mb-2 w-100"
                                    for="option3"></label>
                            </div>
                        </div>
                        <div class="row" x-data>
                            <div class="col-12 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary" id="checkBtn">Cek
                                    Jawaban</button>
                                <button type="button" class="btn btn-primary" id="nextBtn">Selanjutnya</button>
                                <button type="button" class="btn btn-primary" id="finishBtn">Selesai</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="latihanForm"
        action="{{ route('latihan.update', ['latihan' => $latihan->id, 'jenisLatihan' => 'artikata']) }}"
        method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="jumlah_benar" id="jumlah_benar" value="">
        <input type="hidden" name="list" id="list" value="">
    </form>
    <div id="data" data-id-latihan="{{ $latihan->id }}"></div>
    @vite(['resources/js/latihan-artikata.js'])
</x-app-layout>
