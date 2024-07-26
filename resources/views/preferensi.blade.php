<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Pengaturan Latihan Kosakata
        </h2>
    </x-slot>

    <div class="py-1 min-vh-75 d-flex align-items-center">
        <div class="container" x-data="setup()">
            <form action="{{ route('latihan.store') }}" method="POST">
                @csrf
                <div class="container py-3" id="preferensi">
                    <div class="row my-3 justify-content-center">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75">
                                <label for="bahasa" class="form-label text-center fs-3">Bahasa</label>
                                <select id="bahasa" name="id_bahasa" class="form-select form-select-lg"
                                    aria-label="bahasa" x-model="selectedLanguage" @change="updateVoiceOptions">
                                    <option value="">Pilih Bahasa</option>
                                    @foreach ($languages as $language)
                                        <option value="{{ $language->id }}"
                                            data-suara-pria="{{ $language->suara_pria }}"
                                            data-suara-wanita="{{ $language->suara_wanita }}"
                                            @if (old('id_bahasa') == $language->id) selected @endif>
                                            {{ $language->indonesia }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_bahasa')" class="mt-2 ms-3"></x-input-error>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 justify-content-center">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75">
                                <label for="kategori" class="form-label text-center fs-3">Kategori</label>
                                <select id="kategori" name="id_kategori" class="form-select form-select-lg"
                                    aria-label="kategori">
                                    <option selected>Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if (old('id_kategori') == $category->id) selected @endif>{{ $category->indonesia }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_kategori')" class="mt-2 ms-3"></x-input-error>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 justify-content-center">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75">
                                <label for="tingkat_kesulitan" class="form-label text-center fs-3">Tingkat
                                    Kesulitan</label>
                                <select id="tingkat_kesulitan" name="id_tingkat_kesulitan"
                                    class="form-select form-select-lg text-capitalize" aria-label="tingkat_kesulitan">
                                    <option selected>Pilih Tingkat Kesulitan</option>
                                    @foreach ($difficulties as $difficulty)
                                        <option value="{{ $difficulty->id }}"
                                            @if (old('id_tingkat_kesulitan') == $difficulty->id) selected @endif class="text-capitalize">
                                            {{ $difficulty->tingkat_kesulitan }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_tingkat_kesulitan')" class="mt-2 ms-3"></x-input-error>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 justify-content-center" x-show="selectedLanguage">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75 justify-content-between mb-3" id="bantuanSuara">
                                <div class="col-auto d-flex align-items-center" id="laki">
                                    <input type="radio" class="btn-check" name="bantuan_suara" id="opsi1"
                                        autocomplete="off" value="pria" x-bind:disabled="!voiceOptions.male">
                                    <label :class="voiceOptions.male ? 'btn btn-outline-primary bantuan-suara' : ''"
                                        for="opsi1">Laki - Laki</label>
                                </div>
                                <div class="col-auto d-flex align-items-center">
                                    <label for="bantuan_suara" class="fs-3">Bantuan Suara</label>
                                </div>
                                <div class="col-auto d-flex align-items-center" id="perempuan">
                                    <input type="radio" class="btn-check" name="bantuan_suara" id="opsi2"
                                        autocomplete="off" value="wanita" x-bind:disabled="!voiceOptions.female">
                                    <label :class="voiceOptions.female ? 'btn btn-outline-danger bantuan-suara' : ''"
                                        for="opsi2">Perempuan</label>
                                </div>
                            </div>
                        </div>
                        <div class="row w-75 justify-content-center"">
                            <alert x-show="!voiceOptions.female" class="alert alert-info text-center text-primary">
                                Suara
                                perempuan belum
                                tersedia</alert>
                            <alert x-show="!voiceOptions.male" class="alert alert-info text-center text-primary">
                                Suara
                                laki-laki belum
                                tersedia</alert>
                        </div>
                    </div>
                </div>
                <div class="row py-3 justify-content-center">
                    <button type="submit" class="btn btn-lg btn-primary d-flex justify-content-center w-50">
                        Mulai Latihan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setup() {
            return {
                selectedLanguage: '{{ old('id_bahasa', '') }}',
                voiceOptions: {
                    male: true,
                    female: true
                },
                updateVoiceOptions() {
                    const selectedOption = document.querySelector(`#bahasa option[value="${this.selectedLanguage}"]`);
                    const suaraPria = selectedOption ? selectedOption.getAttribute('data-suara-pria') : '';
                    const suaraWanita = selectedOption ? selectedOption.getAttribute('data-suara-wanita') : '';

                    this.voiceOptions.male = suaraPria !== '';
                    this.voiceOptions.female = suaraWanita !== '';

                    // Set default selection based on available options
                    if (!this.voiceOptions.male && this.voiceOptions.female) {
                        document.getElementById('opsi2').checked = true;
                    } else if (this.voiceOptions.male && !this.voiceOptions.female) {
                        document.getElementById('opsi1').checked = true;
                    }
                },
                init() {
                    this.updateVoiceOptions();
                }
            }
        }
    </script>
    @vite(['resources/js/preferensi.js'])
</x-app-layout>
