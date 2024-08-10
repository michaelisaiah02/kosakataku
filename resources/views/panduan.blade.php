<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Panduan Aplikasi KosakataKu
        </h2>
        <nav id="navbarPanduan" class="navbar bg-body-tertiary fixed-bottom px-3 mb-3 d-flex justify-content-evenly">
            <h2 class="navbar-brand my-auto">Panduan</h2>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link" href="#pengantar">Awal</a>
                </li>
                <li class="nav-item dropup-center dropup">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Daftar Panduan</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#pengantar">Pengantar</a></li>
                        <li><a class="dropdown-item" href="#video-tutorial">Video Tutorial</a></li>
                        <li><a class="dropdown-item" href="#langkah">Langkah-Langkah</a></li>
                        <li><a class="dropdown-item" href="#faq">FAQ</a></li>
                        <li><a class="dropdown-item" href="#tips">Tips dan Trik</a></li>
                        <li><a class="dropdown-item" href="#kontak">Kontak Dukungan</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#kontak">Akhir</a>
                </li>
            </ul>
        </nav>
    </x-slot>

    <div class="py-4 full-screen d-flex align-items-center">
        <div data-bs-spy="scroll" data-bs-target="#navbar-panduan" data-bs-root-margin="0px 0px -40%"
            data-bs-smooth-scroll="true" class="scrollspy-example p-3 rounded-2 container" tabindex="0">
            <!-- Pengantar -->
            <div class="row mb-4" id="pengantar">
                <h3 class="fw-semibold">Pengantar</h3>
                <p>Selamat datang di aplikasi KosakataKu. Halaman ini adalah panduan agar kamu dapat menggunakan
                    aplikasi yang membuatmu bisa menguasai kata-kata baru.</p>
            </div>

            <!-- Tutorial Video -->
            <div class="row mb-4" id="video-tutorial">
                <h3 class="fw-semibold">Video Tutorial</h3>
                <p>Berikut adalah video tutorial yang bisa membantu kamu tahu cara menggunakan pembelajaran ini:</p>
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/ZTPNKISi23Q" allowfullscreen></iframe>
                </div>
            </div>

            <!-- Langkah-langkah Belajar -->
            <div class="row mb-4" id="langkah">
                <h3 class="fw-semibold">Langkah-Langkah Belajar di KosakataKu</h3>
                <p>1. Setelah login kamu bisa klik menu latihan untuk memulai latihan.</p>
                <img src="img/panduan/1.png" class="img-fluid" alt="Menu Latihan">
                <p>2. Nanti pilih dulu pengaturan latihan yang mau dilatih mulai dari bahasa, kategori, dan tingkat
                    kesulitan.</p>
                <img src="img/panduan/2.png" class="img-fluid" alt="Pengaturan Latihan">
                <p>3. Kamu juga bisa memilih bantuan suaranya mau suara laki-laki atau perempuan (tidak wajib).</p>
                <img src="img/panduan/3.png" class="img-fluid" alt="Pilihan Bantuan Suara">
                <p>4. Kalau sudah memilih pengaturan tinggal klik tombol mulai latihan nanti latihan akan langsung
                    dimulai.</p>
                <img src="img/panduan/4.png" class="img-fluid" alt="Mulai Latihan">
                <p>5. Tunggu sebentar sistem sedang menyiapkan soal latihannya.</p>
                <img src="img/panduan/5.png" class="img-fluid" alt="Memuat Latihan">
                <p>6. Setelah soal latihan muncul, kamu bisa mulai ucapkan kata yang muncul di layar dengan cara klik
                    mulai ucapkan.</p>
                <img src="img/panduan/6.png" class="img-fluid" alt="Latihan Mulai">
                <p>7. Waktu kamu 2 detik untuk mengucapkan kata.</p>
                <img src="img/panduan/7.png" class="img-fluid" alt="Mengucapkan Kata">
                <p>8. Tunggu sistem memeriksa ucapanmu.</p>
                <img src="img/panduan/8.png" class="img-fluid" alt="Pemeriksaan Ucapan">
                <p>9. Kalau benar, kamu bisa melihat contoh kalimatnya. Klik tombol selanjutnya untuk lanjut ke kata
                    berikutnya atau klik tombol selesai untuk menyelesaikan latihan.</p>
                <img src="img/panduan/9.png" class="img-fluid" alt="Pengucapan Benar">
                <p>10. Kalau salah, kamu bisa mengucapkan ulang dengan klik mulai ucapkan.</p>
                <img src="img/panduan/10.png" class="img-fluid" alt="Pengucapan Salah">
                <p>11. Kalau kamu menyelesaikan latihan nanti akan muncul hasil latihanmu.</p>
                <img src="img/panduan/11.png" class="img-fluid" alt="Hasil Latihan">
                <p>12. Kamu bisa lihat semua hasil latihanmu di menu riwayat.</p>
                <img src="img/panduan/12.png" class="img-fluid" alt="Menu Riwayat">
                <p>13. Kamu bisa lihat hasil latihan yang lebih lengkap dengan klik detail.</p>
                <img src="img/panduan/13.png" class="img-fluid" alt="Detail Latihan">
            </div>

            <!-- FAQ -->
            <div class="row mb-4 text-light" id="faq">
                <h3 class="fw-semibold">FAQ (Frequently Asked Questions)</h3>
                <p><strong>Q: Bagaimana cara masuk ke halaman profil?</strong></p>
                <p>A: Kamu bisa klik nama kamu pada menu yang ada di atas halaman nanti muncul menu profil.</p>
                <p><strong>Q: Bagaimana cara mengubah email?</strong></p>
                <p>A: Kamu bisa ubah emailmu di halaman <a class="link-warning"
                        href="{{ route('profile.edit') }}">Profil</a>.</p>
                <p><strong>Q: Kenapa aku harus verifikasi email?</strong></p>
                <p>A: Supaya sistem bisa memastikan yang memakai pembelajaran ini bukan robot</a>.</p>
                <p><strong>Q: Apa yang terjadi kalau akun dihapus?</strong></p>
                <p>A: Semua latihan yang kamu kerjakan hilang dan kamu harus buat akun lagi agar bisa memulai
                    pembelajaran baru.</p>
            </div>

            <!-- Tips dan Trik -->
            <div class="row mb-4 text-light" id="tips">
                <h3 class="fw-semibold">Tips dan Trik</h3>
                <p>Beberapa tips untuk membantumu menggunakan pembelajaran ini dengan lebih efektif:</p>
                <ul class="list-group list-group-flush list-group-item-dark rounded-3">
                    <li class="list-group-item">1. Kalau kamu sudah yakin mengucapkan dengan benar tapi tidak terdengar,
                        coba ucapkan kata
                        lebih lambat.</li>
                    <li class="list-group-item">2. Jangan memuat ulang halaman saat latihan karena kata yang muncul
                        langsung dianggap salah.
                    </li>
                    <li class="list-group-item">3. Pastikan lingkungan sekitar tidak berisik agar suaramu terdengar
                        dengan jelas.</li>
                    <li class="list-group-item">4. Kalau kamu salah dan mengulangi terus nanti ada batas maksimalnya
                        sesuai tingkat kesulitan
                        yang kamu pilih kalau tercapai tidak akan masuk ke penilaian walaupun sudah benar pengucapan
                        selanjutnya.</li>
                    <li class="list-group-item">5. Kalau memang susah pengucapannya kamu bisa klik lewati kata.</li>
                </ul>
            </div>

            <!-- Kontak Dukungan -->
            <div class="row mb-4 text-light pb-5" id="kontak">
                <h3 class="fw-semibold">Kontak Dukungan</h3>
                <p>Jika kamu memerlukan bantuan lebih lanjut, coba minta bantuan orang tuamu atau jangan ragu untuk
                    menghubungi
                    ke email di michael.isaiah.02@gmail.com
                    atau WhatsApp <a
                        href="https://wa.me/6289669045879?text=Hai,%20aku%20membutuhkan bantuan%20menggunakan%20aplikasi%20KosakataKu😊"
                        target="blank" class="btn-link link-warning">+62 89669045879</a></p>
            </div>
        </div>
    </div>
</x-app-layout>
