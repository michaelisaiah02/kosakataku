<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/sass/app.scss', 'resources/css/app.css'])
</head>

<body class="font-sans text-dark bg-light utama">
    <nav class="navbar navbar-expand-md navbar-light bg-light border-bottom shadow-sm navbarApp">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('beranda') }}">
                <x-application-logo width="35" height="35" />
            </a>

            <!-- Hamburger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navigation Links -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            Registrasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <header class="bg-light shadow-sm">
            <div class="container px-3">
                <h2 class="fw-semibold text-dark py-3">
                    Selamat datang di {{ config('app.name', 'Laravel') }}!
                </h2>
            </div>
        </header>
        <div class="py-4 min-vh-75 d-flex align-items-center">
            <div class="container">
                <!-- Pengantar -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Pengantar</h3>
                    <p>Selamat datang di aplikasi KosakataKu. Halaman ini adalah panduan agar kamu dapat menggunakan
                        aplikasi yang membuatmu bisa menguasai kosakata baru.</p>
                </div>

                <!-- Langkah-langkah Awal -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Langkah-langkah Awal</h3>
                    <p>
                        Klik atau tekan link ini <a class="link-primary" href="{{ route('register') }}">Registrasi</a>
                        atau link yang ada di atas layar untuk mendaftar.
                    </p>
                    <p>
                        Kalau sudah pernah mendaftar, klik atau tekan link ini <a class="link-primary"
                            href="{{ route('login') }}">Login</a>
                        untuk masuk. Gunakan email dan kata sandi yang telah kamu daftarkan.
                    </p>
                    <p>
                        Setelah registrasi atau login, silakan menjelajahi pembelajaran ini atau kamu juga bisa melihat
                        panduan untuk memulai pembelajaran.
                    </p>
                </div>


                <!-- Fitur Utama -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Fitur Utama</h3>
                    <p>Aplikasi kami memiliki berbagai fitur untuk membantu Anda:</p>
                    <ul>
                        <li>Fitur 1: </li>
                        <li>Fitur 2: Deskripsi fitur 2</li>
                        <li>Fitur 3: Deskripsi fitur 3</li>
                    </ul>
                </div>

                <!-- FAQ -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">FAQ (Frequently Asked Questions)</h3>
                    <p><strong>Q: Bagaimana cara reset kata sandi?</strong></p>
                    <p>A: Anda dapat reset kata sandi dengan mengklik 'Lupa Kata Sandi' pada halaman login.</p>
                    <p><strong>Q: Bagaimana cara menghubungi dukungan pelanggan?</strong></p>
                    <p>A: Anda dapat menghubungi kami melalui email di support@contoh.com.</p>
                </div>

                <!-- Tutorial Video -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Tutorial Video</h3>
                    <p>Berikut adalah video tutorial yang dapat membantu Anda memahami cara menggunakan aplikasi:</p>
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/xyz123" frameborder="0"
                        allowfullscreen></iframe>
                </div>

                <!-- Tips dan Trik -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Tips dan Trik</h3>
                    <p>Beberapa tips untuk membantu Anda menggunakan aplikasi dengan lebih efektif:</p>
                    <ul>
                        <li>Tip 1: Deskripsi tip 1</li>
                        <li>Tip 2: Deskripsi tip 2</li>
                        <li>Tip 3: Deskripsi tip 3</li>
                    </ul>
                </div>

                <!-- Kontak Dukungan -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Kontak Dukungan</h3>
                    <p>Jika kamu memerlukan bantuan lebih lanjut, coba minta bantuan orang tuamu atau jangan ragu untuk
                        menghubungi
                        ke email di michael.isaiah.02@gmail.com
                        atau WhatsApp <a
                            href="https://wa.me/6289669045879?text=Hai,%20aku%20membutuhkan bantuan%20menggunakan%20aplikasi%20KosakataKu😊"
                            class="btn-link">+62 89669045879</a></p>
                </div>
            </div>
        </div>
    </main>
    @vite(['resources/js/app.js'])
</body>

</html>
