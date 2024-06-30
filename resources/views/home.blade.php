<!DOCTYPE html>
<html lang="en">

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
    <title>Document</title>
    @vite(['resources/sass/app.scss'])
</head>

<body class="vh-100 ">
    <div class="container py-3">
        <div class="row">
            <h1>Speech To Text</h1>
        </div>
        <div class="row my-3">
            <h2 class="text-center" id="randomWord"></h2>
            <h2 class="text-center">=</h2>
            <h2 class="text-center" id="translatedWord"></h2>
        </div>
        <div class="row my-3">
            <audio id="spelledWord" src="" controls></audio>
        </div>
        <div class="row">
            <button class="btn btn-info d-flex justify-content-center" id="spellingBtn">
                <div class="icon my-auto">
                    <img src="{{ asset('img/bars.svg') }}" alt="bars" id="recIcon"
                        style="display: none; height: 40px;" />
                </div>
                <p class="mt-3 ms-2">Mulai ucapkan</p>
            </button>
        </div>
        <div class="row">
            <h3 id="resultWord"></h3>
            <h3 id="example"></h3>
        </div>
    </div>
    @vite(['resources/js/app.js'])
</body>

</html>
