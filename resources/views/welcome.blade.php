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
                    Selamat datang di {{ config('app.name') }}!
                </h2>
            </div>
        </header>
        <div class="py-4 full-screen d-flex align-items-center">
            <div class="container">
                <!-- Pengantar -->
                <div class="row mb-4">
                    <h3 class="fw-semibold">Pengantar</h3>
                    <p>Selamat datang di aplikasi KosakataKu. Kamu bisa melatih dan memperbanyak kata yang berguna untuk
                        pembelajaran bahasa yang kamu inginkan. Mari kita mulai 😊</p>
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

                <!-- Kontak Dukungan -->
                <div class="row mb-5">
                    <h3 class="fw-semibold">Kontak Dukungan</h3>
                    <p>Kalau kamu memerlukan bantuan lebih lanjut, coba minta bantuan orang tuamu atau jangan takut
                        untuk
                        menghubungi
                        ke email di michael.isaiah.02@gmail.com
                        atau WhatsApp <a
                            href="https://wa.me/6289669045879?text=Hai,%20aku%20membutuhkan bantuan%20menggunakan%20aplikasi%20KosakataKu😊"
                            class="btn-link">+6289669045879</a>.</p>
                </div>

                <!-- Footer -->
                <div class="row mb-5">
                    <h1 class="fw-semibold text-center mb-4">Selamat Latihan!</h1>
                    <p class="fs-2 text-center pb-3">Klik di <button class="btn btn-sm btn-primary fs-3 py-0 px-1 m-0"
                            id="copy-link">sini</button>
                        untuk
                        menyalin dan bagikan ke teman terdekatmu supaya mereka tidak ketinggalan cara
                        belajar kata
                        baru.</p>
                </div>
            </div>
        </div>
    </main>
    @vite(['resources/js/app.js'])
</body>

</html>
