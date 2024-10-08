<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3 mt-header">
            Hasil Latihan Kosakata
        </h2>
    </x-slot>

    <div class="py-4 full-screen d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="card shadow-sm rounded-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="fw-semibold m-0">Selamat telah menyelesaikan latihan!</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 justify-content-center">
                                <div class="col-8 col-md-5">
                                    <strong>Bahasa yang Dipelajari</strong>
                                </div>
                                <div class="col col-md-3">
                                    : {{ $latihan->bahasa->indonesia }}
                                </div>
                            </div>
                            <div class="row mb-3 justify-content-center">
                                <div class="col-8 col-md-5">
                                    <strong>Kategori</strong>
                                </div>
                                <div class="col col-md-3">
                                    : {{ $latihan->kategori->indonesia }}
                                </div>
                            </div>
                            <div class="row mb-3 justify-content-center">
                                <div class="col-8 col-md-5">
                                    <strong>Jumlah Pengucapan yang Benar</strong>
                                </div>
                                <div class="col col-md-3">
                                    : {{ $latihan->jumlah_pengucapan_benar }}
                                </div>
                            </div>
                            <div class="row mb-4 justify-content-center">
                                <div class="col-8 col-md-5">
                                    <strong>Jumlah Arti Kata yang Benar</strong>
                                </div>
                                <div class="col col-md-3">
                                    : {{ $latihan->jumlah_artikata_benar }}
                                </div>
                            </div>
                            <div class="row justify-content-center mb-2">
                                <hr class="border border-primary border-3 opacity-50 m-0">
                                <h3 class="text-center my-2">Nilai Latihanmu</h3>
                                <hr class="border border-primary border-3">
                                <div class="col text-center">
                                    <h4>Pengucapan</h4>
                                </div>
                                <div class="col text-center">
                                    <h4>Arti Kata</h4>
                                </div>
                            </div>
                            <div class="row justify-content-evenly mb-4">
                                <div class="col-auto" x-data="{
                                    nilai: {{ round(($latihan->jumlah_pengucapan_benar / 5) * 100) }}
                                }">
                                    <button disabled class="btn btn-lg rounded-circle text-center fs-1" id="nilai"
                                        x-bind:class="nilai >= 80 ? 'btn-success' : nilai >= 50 ? 'btn-warning' : 'btn-danger'">
                                        <span x-text="nilai"></span>
                                    </button>
                                </div>
                                <div class="col-auto" x-data="{
                                    nilai: {{ round(($latihan->jumlah_artikata_benar / 5) * 100) }}
                                }">
                                    <button disabled class="btn btn-lg rounded-circle text-center fs-1" id="nilai"
                                        x-bind:class="nilai >= 80 ? 'btn-success' : nilai >= 50 ? 'btn-warning' : 'btn-danger'">
                                        <span x-text="nilai"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-auto">
                                <a href="{{ route('beranda') }}" class="btn btn-info">
                                    <i class="bi bi-house-door-fill"></i> Kembali Ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
