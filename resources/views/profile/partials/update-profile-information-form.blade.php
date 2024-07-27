<section>
    <header>
        <h2 class="h5 fw-bold text-dark">
            Data Diri
        </h2>

        <p class="mt-2 text-muted">
            Ubah data diri kamu di sini.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input id="name" name="name" type="text" class="form-control"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if ($errors->get('name'))
                <div class="text-danger mt-2">
                    {{ $errors->get('name')[0] }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control"
                value="{{ old('email', $user->email) }}" required autocomplete="email">
            @if ($errors->get('email'))
                <div class="text-danger mt-2">
                    {{ $errors->get('email')[0] }}
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-dark">
                        Kamu belum verifikasi email kamu.
                        <button form="send-verification" class="btn btn-link p-0 text-decoration-none">
                            Klik di sini untuk mengirim ulang verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success">
                            Verifikasi email sudah dikirim ke emailmu.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>

            @if (session('status') === 'profile-updated')
                <p class="text-success mt-2">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
