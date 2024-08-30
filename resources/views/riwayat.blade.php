<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3 mt-header">
            Riwayat Latihan
        </h2>
    </x-slot>
    {{-- @dd($errors) --}}
    @if (isset($info))
        <div class="py-4 full-screen d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="alert alert-info fs-3 text-center text-danger col-auto shadow-sm">
                        {{ $info }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="py-4 full-screen d-flex align-items-center">
            <div class="container-sm container-fluid">
                <div class="row mb-3 justify-content-center mb-md-3">
                    <div class="p-4 text-dark">
                        Kamu sudah latihan <span class="fw-semibold">{{ $jumlahLatihan }}</span> kali.
                    </div>
                </div>
                <div class="row justify-content-center mb-md-3">
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-flag"></i>
                    </div>
                    <div class="col-md d-flex col-5 align-items-center">
                        <p class="p-0 m-0">Bahasa yang pertama dipelajari</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#detailModalPengucapan" data-id="{{ $bahasaPertamaDipelajari->id }}">
                                {{ $bahasaPertamaDipelajari->bahasa->indonesia }}
                            </button>
                        </p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">Bahasa yang terakhir dipelajari</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#detailModalPengucapan" data-id="{{ $bahasaTerakhirDipelajari->id }}">
                                {{ $bahasaTerakhirDipelajari->bahasa->indonesia }}
                            </button>
                        </p>
                    </div>
                </div>
                <div class="row justify-content-center mb-md-3">
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div class="col-md d-flex col-5 align-items-center">
                        <p class="p-0 m-0">Bahasa yang sering dipelajari</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            {{ $bahasaSeringDipelajari['bahasa'] }}
                            ({{ $bahasaSeringDipelajari['jumlah'] }} Kali)
                        </p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-check text-success"></i>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">Bahasa yang banyak benar pengucapannya</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#detailModalPengucapan" data-id="{{ $bahasaPalingBanyakBenar['id'] }}">
                                {{ $bahasaPalingBanyakBenar['bahasa'] }}
                            </button>
                            ({{ round($bahasaPalingBanyakBenar['jumlah']) }}% Benar)
                        </p>
                    </div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">Latihan paling lama</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#detailModalPengucapan" data-id="{{ $latihanPalingLama['id'] }}">
                                {{ $latihanPalingLama['bahasa'] }}
                            </button>
                            ({{ $latihanPalingLama['durasi'] }})
                        </p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center py-3 py-md-0">
                        <i class="bi bi-x text-danger"></i>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">Bahasa yang banyak salah pengucapannya</p>
                    </div>
                    <div class="col-md-auto col-1 d-flex align-items-center">
                        <p class="p-0 m-0">:</p>
                    </div>
                    <div class="col-md col-5 d-flex align-items-center">
                        <p class="p-0 m-0">
                            <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#detailModalPengucapan" data-id="{{ $bahasaPalingBanyakSalah['id'] }}">
                                {{ $bahasaPalingBanyakSalah['bahasa'] }}
                            </button>
                            ({{ round($bahasaPalingBanyakSalah['jumlah']) }}% Salah)
                        </p>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="latihan" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pengucapan-tab" data-bs-toggle="tab"
                            data-bs-target="#pengucapan-tab-pane" type="button" role="tab"
                            aria-controls="pengucapan-tab-pane" aria-selected="true">Pengucapan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="artikata-tab" data-bs-toggle="tab"
                            data-bs-target="#artikata-tab-pane" type="button" role="tab"
                            aria-controls="artikata-tab-pane" aria-selected="false">Arti Kata</button>
                    </li>
                </ul>
                <div class="tab-content" id="latihanContent">
                    <div class="tab-pane fade show active" id="pengucapan-tab-pane" role="tabpanel"
                        aria-labelledby="pengucapan-tab" tabindex="0">
                        <div class="row mx-md-1 mx-0 mt-3 fs-3">
                            <h3>
                                <i class="bi bi-journal-text"></i> Daftar Latihan Pengucapan Kata
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table id="tabelRiwayatPengucapan"
                                class="table table-striped-columns table-primary table-hover">
                                <thead>
                                    <tr class="align-middle">
                                        <th scope="col"><i class="bi bi-globe2 icon-pc-only"></i> Bahasa</th>
                                        <th scope="col"><i class="bi bi-tags icon-pc-only"></i> Kategori</th>
                                        <th scope="col" class="text-center"><i
                                                class="bi bi-award icon-pc-only"></i> Nilai
                                        </th>
                                        <th scope="col" class="text-end"><i class="bi bi-clock icon-pc-only"></i>
                                            Waktu Latihan
                                        </th>
                                        <th scope="col" class="text-end"><i
                                                class="bi bi-calendar icon-pc-only"></i>
                                            Tanggal
                                        </th>
                                        <th scope="col" class="text-center"><i
                                                class="bi bi-gear icon-pc-only"></i> Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @foreach ($histories as $history)
                                        <tr class="align-middle">
                                            <td class="text-nowrap">{{ $history->bahasa->indonesia }}</td>
                                            <td class="text-nowrap">{{ $history->kategori->indonesia }}</td>
                                            <td class="text-center">
                                                {{ round(($history->jumlah_pengucapan_benar / $jumlahKata) * 100) }}
                                            </td>
                                            <td class="text-end text-nowrap">
                                                @php
                                                    $diff = \Carbon\Carbon::parse($history->updated_at)->diff(
                                                        \Carbon\Carbon::parse($history->created_at),
                                                    );
                                                    $formattedDiff = $diff->format(
                                                        ($diff->h == 0 ? '' : '%H:') .
                                                            ($diff->i == 0 ? '' : '%I:') .
                                                            '%S',
                                                    );
                                                @endphp
                                                {{ $formattedDiff }}
                                            </td>
                                            <td class="text-end text-nowrap">
                                                {{ \Carbon\Carbon::parse($history->created_at)->setTimezone('Asia/Jakarta')->format('d M Y') }}
                                            </td>
                                            <td class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-sm btn-info text-nowrap"
                                                    data-bs-toggle="modal" data-bs-target="#detailModalPengucapan"
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
                    <div class="tab-pane fade" id="artikata-tab-pane" role="tabpanel" aria-labelledby="artikata-tab"
                        tabindex="0">
                        <div class="row mx-md-1 mx-0 mt-3 fs-3">
                            <h3>
                                <i class="bi bi-journal-text"></i> Daftar Latihan Arti Kata
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table id="tabelRiwayatArtiKata"
                                class="table table-striped-columns table-primary table-hover">
                                <thead>
                                    <tr class="align-middle">
                                        <th scope="col"><i class="bi bi-globe2 icon-pc-only"></i> Bahasa</th>
                                        <th scope="col"><i class="bi bi-tags icon-pc-only"></i> Kategori</th>
                                        <th scope="col" class="text-center"><i
                                                class="bi bi-award icon-pc-only"></i> Nilai
                                        </th>
                                        <th scope="col" class="text-end"><i class="bi bi-clock icon-pc-only"></i>
                                            Waktu Latihan
                                        </th>
                                        <th scope="col" class="text-end"><i
                                                class="bi bi-calendar icon-pc-only"></i>
                                            Tanggal
                                        </th>
                                        <th scope="col" class="text-center"><i
                                                class="bi bi-gear icon-pc-only"></i> Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @foreach ($histories as $history)
                                        <tr class="align-middle">
                                            <td class="text-nowrap">{{ $history->bahasa->indonesia }}</td>
                                            <td class="text-nowrap">{{ $history->kategori->indonesia }}</td>
                                            <td class="text-center">
                                                {{ round(($history->jumlah_artikata_benar / $jumlahKata) * 100) }}</td>
                                            <td class="text-end text-nowrap">
                                                @php
                                                    $diff = \Carbon\Carbon::parse($history->updated_at)->diff(
                                                        \Carbon\Carbon::parse($history->created_at),
                                                    );
                                                    $formattedDiff = $diff->format(
                                                        ($diff->h == 0 ? '' : '%H:') .
                                                            ($diff->i == 0 ? '' : '%I:') .
                                                            '%S',
                                                    );
                                                @endphp
                                                {{ $formattedDiff }}
                                            </td>
                                            <td class="text-end text-nowrap">
                                                {{ \Carbon\Carbon::parse($history->created_at)->setTimezone('Asia/Jakarta')->format('d M Y') }}
                                            </td>
                                            <td class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-sm btn-info text-nowrap"
                                                    data-bs-toggle="modal" data-bs-target="#detailModalPengucapan"
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
            </div>
        </div>
        <div class="modal fade" id="detailModalPengucapan" tabindex="-1"
            aria-labelledby="detailModalPengucapanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-lg-down">
                <div class="modal-content">
                    <div class="modal-header" id="riwayatModalHeader">
                        <h1 class="modal-title fs-5" id="detailModalPengucapanLabel"><i
                                class="bi bi-info-circle"></i> Detail
                            Latihan</h1>
                        <h1 class="ms-auto modal-title fs-5 tanggal" id="tanggal"></h1>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="riwayatModalBody">
                        <div class="container-fluid">
                            <div class="row justify-content-center mb-md-3 mb-0">
                                <div class="col-md-auto col-1 d-flex align-items-center py-md-0 py-3">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Bahasa</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="bahasa" class="text-capitalize p-0 m-0"></p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Jumlah Pengucapan Benar</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="benar" class="p-0 m-0"></p>
                                </div>
                            </div>
                            <div class="row justify-content-center mb-md-3 mb-0">
                                <div class="col-md-auto col-1 d-flex align-items-center py-md-0 py-3">
                                    <i class="bi bi-tag"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Kategori</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="kategori" class="text-capitalize p-0 m-0"></p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Waktu Latihan</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="waktu" class="p-0 m-0"></p>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="table-responsive">
                                    <table id="tabelDetail"
                                        class="table table-sm table-striped-columns table-primary table-hover">
                                        <thead>
                                            <tr class="align-middle">
                                                <th scope="col">Kata</th>
                                                <th scope="col">Cara Baca</th>
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
        <div class="modal fade" id="detailModalArtiKata" tabindex="-1" aria-labelledby="detailModalArtiKataLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-lg-down">
                <div class="modal-content">
                    <div class="modal-header" id="riwayatModalHeader">
                        <h1 class="modal-title fs-5" id="detailModalArtiKataLabel"><i class="bi bi-info-circle"></i>
                            Detail
                            Latihan</h1>
                        <h1 class="ms-auto modal-title fs-5 tanggal" id="tanggal"></h1>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="riwayatModalBody">
                        <div class="container-fluid">
                            <div class="row justify-content-center mb-md-3 mb-0">
                                <div class="col-md-auto col-1 d-flex align-items-center py-md-0 py-3">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Bahasa</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="bahasa" class="text-capitalize p-0 m-0"></p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Jumlah Pengucapan Benar</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="benar" class="p-0 m-0"></p>
                                </div>
                            </div>
                            <div class="row justify-content-center mb-md-3 mb-0">
                                <div class="col-md-auto col-1 d-flex align-items-center py-md-0 py-3">
                                    <i class="bi bi-tag"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Kategori</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="kategori" class="text-capitalize p-0 m-0"></p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p class="p-0 m-0">Waktu Latihan</p>
                                </div>
                                <div class="col-md-auto col-1 d-flex align-items-center">
                                    <p class="p-0 m-0">:</p>
                                </div>
                                <div class="col-md col-5 d-flex align-items-center">
                                    <p id="waktu" class="p-0 m-0"></p>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="table-responsive">
                                    <table id="tabelDetail"
                                        class="table table-sm table-striped-columns table-primary table-hover">
                                        <thead>
                                            <tr class="align-middle">
                                                <th scope="col">Kata</th>
                                                <th scope="col">Cara Baca</th>
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
    @endif
</x-app-layout>
