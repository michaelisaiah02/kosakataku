<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3 mt-header">
            Verifikasi Email <span class="fs-3 text-warning">({{ auth()->user()->email }})</span>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden p-3">
                <div class="mb-4 text-sm text-muted">
                    Terima kasih telah mendaftar! Sebelum memulai, bisakah kamu memverifikasi alamat emailmu dengan
                    mengklik link yang baru saja dikirimkan ke emailmu? Jika kamu tidak menerima email tersebut, kamu
                    boleh klik tombol kirim ulang untuk mengirimkan verifikasi ke email yang terdaftar.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-success">
                        Link verifikasi baru telah dikirim ke alamat email yang kamu berikan saat pendaftaran.
                    </div>
                @endif

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div>
                            <button class="btn btn-primary">Kirim Ulang Email Verifikasi</button>
                        </div>
                    </form>

                    <div class="d-flex justify-items-center justify-content-center">
                        <a href="{{ route('profile.edit') }}" class="btn btn-link">Ubah Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
