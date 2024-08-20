<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3 mt-header">
            KosakataKu
        </h2>
    </x-slot>

    <div class="container-sm container-fluid">
        <div class="py-4 full-screen row align-items-center justify-content-center mx-md-0 mx-3 pb-5">
            <div class="row bg-light shadow-sm rounded-3 bg-opacity-75">
                <div class="px-4 py-3 text-dark">
                    Kamu sudah login! Selamat datang di KosakataKu. Silakan pilih menu <a
                        href="{{ route('latihan.index') }}">latihan</a> untuk memulai latihan atau pilih menu yang lain
                    di atas.
                </div>
            </div>
            <div class="row bg-light shadow-sm rounded-3 bg-opacity-75">
                <div class="px-4 py-3 text-dark">
                    Jumlah orang yang terdaftar di KosakataKu saat ini adalah <span
                        class="fw-semibold">{{ $jumlahPenggunaKosakataku }}</span> orang.
                </div>
            </div>
            <div class="row bg-light shadow-sm rounded-3 bg-opacity-75">
                <div class="px-4 py-3 text-dark">
                    Dari semua orang yang latihan, bahasa yang paling banyak dilatih adalah bahasa <span
                        class="fw-semibold">{{ $bahasaPalingBanyak }}</span>. Sebanyak <span
                        class="fw-semibold">{{ $jumlahLatihanBahasa }}</span>
                    kali latihan.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
