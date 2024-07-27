<section>
    <header>
        <h2 class="h5 fw-bold text-dark">
            Ubah Kata Sandi
        </h2>

        <p class="mt-2 text-muted">
            Masukkan kata sandi lama dan kata sandi baru untuk mengubah kata sandi akunmu.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Kata Sandi yang Sekarang</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control"
                autocomplete="current-password">
            @if ($errors->updatePassword->get('current_password'))
                <div class="text-danger mt-2">
                    {{ $errors->updatePassword->get('current_password')[0] }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="form-control"
                autocomplete="new-password">
            @if ($errors->updatePassword->get('password'))
                <div class="text-danger mt-2">
                    {{ $errors->updatePassword->get('password')[0] }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control" autocomplete="new-password">
            @if ($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger mt-2">
                    {{ $errors->updatePassword->get('password_confirmation')[0] }}
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>

            @if (session('status') === 'password-updated')
                <p class="text-success mt-2">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
