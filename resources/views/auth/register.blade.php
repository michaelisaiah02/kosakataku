<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="formHandler">
        @csrf

        <div class="text-center fs-3 pt-0 mt-0">Registrasi</div>

        <!-- Name -->
        <div class="mb-3">
            <x-input-label for="name" :value="'Nama'" />
            <x-text-input id="name" class="form-control mt-1" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div x-show="errors.name" class="invalid-feedback">
                <template x-for="error in errors.name">
                    <span x-text="error"></span>
                </template>
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" class="form-control mt-1" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div x-show="errors.email" class="invalid-feedback">
                <template x-for="error in errors.email">
                    <span x-text="error"></span>
                </template>
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="'Kata Sandi'" />
            <x-text-input id="password" class="form-control mt-1" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div x-show="errors.password" class="invalid-feedback">
                <template x-for="error in errors.password">
                    <span x-text="error"></span>
                </template>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <x-input-label for="password_confirmation" :value="'Konfirmasi Kata Sandi'" />
            <x-text-input id="password_confirmation" class="form-control mt-1" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div x-show="errors.password_confirmation" class="invalid-feedback">
                <template x-for="error in errors.password_confirmation">
                    <span x-text="error"></span>
                </template>
            </div>
        </div>

        <!-- CAPTCHA -->
        <div class="mb-3">
            <x-input-label for="captcha" :value="'Captcha'" />
            <div class="mb-3 row justify-content-center">
                <div class="col">
                    <x-text-input id="captcha" class="form-control mt-1" type="text" name="captcha" maxlength="5"
                        placeholder="Isi captcha" required />
                </div>
                <div class="col-auto d-flex justify-content-center">
                    <img src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default">
                </div>
                <div class="col-auto d-flex justify-content-center" x-data="{ rotate: false }">
                    <button type="button" class="btn btn-link link-light" id="reload-captcha"
                        @click="rotate = true; fetchCaptcha()" :class="{ 'spin-animation': rotate }"
                        @animationend="rotate = false">
                        <i class="bi bi-arrow-clockwise" id="captcha-icon"></i>
                    </button>
                </div>
                <div class="form-text text-light">
                    Masukkan huruf dan angka yang kamu lihat!
                </div>
            </div>
            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
            <div x-show="errors.captcha" class="invalid-feedback">
                <template x-for="error in errors.captcha">
                    <span x-text="error"></span>
                </template>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-4" x-data="{ loading: false }">
            <a class="btn btn-link link-light p-0 me-4" href="{{ route('login') }}">
                Sudah pernah mendaftar?
            </a>

            <button type="submit" class="btn btn-primary d-flex align-items-center" @click.prevent="handleSubmit">
                <div x-show="!loading">
                    Registrasi
                </div>
                <div class="spinner-border text-light" role="status" aria-hidden="true" x-show="loading">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </div>
    </form>
</x-guest-layout>

<script>
    function fetchCaptcha() {
        // Logic to reload captcha
        fetch('/captcha/refresh')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                document.querySelector('.captcha-img').src = url;
            })
            .catch(error => console.error('Error refreshing captcha:', error));
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', () => ({
            loading: false,
            errors: {},
            handleSubmit() {
                if (this.validateForm()) {
                    this.loading = true;
                    document.querySelector('form').submit();
                }
            },
            validateForm() {
                this.errors = {};
                let isValid = true;

                document.querySelectorAll('input[required]').forEach((input) => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        this.errors[input.name] = [
                            `${input.labels[0].textContent} harus diisi.`
                        ];
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                return isValid;
            },
        }));
    });
</script>
