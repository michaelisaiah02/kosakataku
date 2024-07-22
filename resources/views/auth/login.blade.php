<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" class="form-control mt-1" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="'Kata Sandi'" />
            <x-text-input id="password" class="form-control mt-1" type="password" name="password" required
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3 d-flex justify-content-between align-items-center">
            <input id="remember_me" type="checkbox" class="form-check-input me-2" name="remember">
            <label for="remember_me" class="form-check-label user-select-none">
                Ingat saya
            </label>
            <a class="btn btn-link ms-auto p-0" href="{{ route('register') }}">
                Belum punya akun?
            </a>
        </div>

        <div class="d-flex align-items-center justify-content-end mt-4">
            @if (Route::has('password.request'))
                <a class="btn btn-link me-auto p-0" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
            <button class="btn btn-primary">Login</button>
        </div>
    </form>
</x-guest-layout>
