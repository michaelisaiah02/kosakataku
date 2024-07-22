<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Riwayat Latihan
        </h2>
    </x-slot>
    {{-- @dd($histories) --}}
    <div class="py-4 min-vh-75 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col d-flex align-items-center">
                    <p>Bahasa yang sering dipelajari</p>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <p>:</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>{{ $bahasaSeringDipelajari }}</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>Bahasa yang paling banyak benar (<i class="bi bi-check text-success"></i>)</p>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <p>:</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>{{ $bahasaPalingBanyakBenar }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex align-items-center">
                    <p>Latihan paling lama</p>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <p>:</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>{{ $latihanPalingLama }}</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>Bahasa yang paling banyak salah (<i class="bi bi-x text-danger"></i>)</p>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <p>:</p>
                </div>
                <div class="col d-flex align-items-center">
                    <p>{{ $bahasaPalingBanyakSalah }}</p>
                </div>
            </div>
            <div class="row mx-3 fs-3">Daftar Latihan</div>
            <div class="table-responsive">
                <table id="tabelRiwayat" class="table table-striped-columns table-primary table-hover">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col">Bahasa</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Kesulitan</th>
                            <th scope="col" class="text-center">Nilai</th>
                            <th scope="col" class="text-end">Tanggal</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($histories as $history)
                            <tr class="align-middle">
                                <td class="text-nowrap">{{ $history->bahasa->indonesia }}</td>
                                <td class="text-nowrap">{{ $history->kategori->indonesia }}</td>
                                <td class="text-nowrap text-capitalize">
                                    {{ $history->tingkatKesulitan->tingkat_kesulitan }}</td>
                                <td class="text-center">
                                    {{ round(($history->jumlah_benar / $history->jumlah_kata) * 100) }}</td>
                                <td class="text-end text-nowrap">
                                    {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y') }}
                                </td>
                                <td class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-sm btn-info text-nowrap"
                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-id="{{ $history->id }}">
                                        <i class="bi bi-ticket-detailed"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="detailModalLabel">Detail Latihan</h1>
                    <h1 class="ms-auto modal-title fs-5 tanggal" id="tanggal">Rabu, 17 Juli 2024 - 19:53:31</h1>
                    <button type="button" class="btn-close ms-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <p>Bahasa</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="bahasa" class="text-capitalize"></p>
                            </div>
                            <div class="col">
                                <p>Jumlah Benar</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="benar"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p>Kategori</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="kategori" class="text-capitalize"></p>
                            </div>
                            <div class="col">
                                <p>Jumlah Kata</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="kata"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p>Tingkat Kesulitan</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="kesulitan" class="text-capitalize"></p>
                            </div>
                            <div class="col">
                                <p>Waktu Latihan</p>
                            </div>
                            <div class="col-auto">
                                <p>:</p>
                            </div>
                            <div class="col">
                                <p id="waktu"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table id="tabelDetail"
                                    class="table table-sm table-striped-columns table-primary table-hover">
                                    <thead>
                                        <tr class="align-middle">
                                            <th scope="col">Kata</th>
                                            <th scope="col">Terjemahan</th>
                                            <th scope="col" class="text-center">Percobaan</th>
                                            <th scope="col" class="text-center text-nowrap">Waktu (Detik)</th>
                                            <th scope="col" class="text-center text-nowrap">Benar (<i
                                                    class="bi bi-check text-success"></i>/<i
                                                    class="bi bi-x text-danger"></i>)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider" id="tabelDetailBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite(['resources/js/riwayat.js'])
</x-app-layout>
