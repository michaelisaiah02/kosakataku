<x-guest-layout>
    <div class="mb-4 text-muted">
        {{ __('Lupa kata sandi? Tidak masalah. Ketikkan alamat emailmu, nanti akan dikirimkan link reset kata sandi melalui email agar kamu bisa membuat kata sandi baru.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" class="form-control mt-1" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="d-flex align-items-center justify-content-between mt-4">
            <a href="{{ route('login') }}" class="btn btn-link p-0">Kembali</a>
            <button class="btn btn-primary">Reset Kata Sandi</button>
        </div>
    </form>
</x-guest-layout>
