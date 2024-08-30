<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3 mt-header">
            Pengaturan Latihan Kosakata
        </h2>
    </x-slot>

    <div class="py-1 full-screen d-flex align-items-center">
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
                                    <option value="">Pilih Kategori</option>
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

                    <div class="row mb-3 justify-content-center" x-show="selectedLanguage">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row justify-content-center mb-md-3 mb-1" id="bantuanSuara">
                                <div class="col-12 text-center">
                                    <p for="bantuan_suara" class="fs-3">Bantuan Suara</p>
                                </div>
                                <div class="row justify-content-evenly">
                                    <div class="col-auto d-flex flex-column align-items-center" id="laki">
                                        <input type="radio" class="btn-check" name="bantuan_suara" id="opsi1"
                                            autocomplete="off" value="pria" x-bind:disabled="!voiceOptions.male">
                                        <label :class="'btn btn-outline-primary btn-lg bantuan-suara my-auto'"
                                            x-show="voiceOptions.male" for="opsi1">Laki - Laki</label>
                                        <div x-show="!voiceOptions.male"
                                            class="alert alert-link user-select-none text-center text-muted my-auto">
                                            Suara laki-laki belum tersedia
                                        </div>
                                    </div>
                                    <div class="col-auto d-flex flex-column align-items-center" id="perempuan">
                                        <input type="radio" class="btn-check" name="bantuan_suara" id="opsi2"
                                            autocomplete="off" value="wanita" x-bind:disabled="!voiceOptions.female">
                                        <label :class="'btn my-auto btn-outline-danger btn-lg bantuan-suara'"
                                            x-show="voiceOptions.female" for="opsi2">Perempuan</label>
                                        <div x-show="!voiceOptions.female"
                                            class="alert alert-info text-center text-primary my-auto">
                                            Suara perempuan belum tersedia
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row py-3 justify-content-center">
                        <button type="submit" class="btn btn-lg btn-primary d-flex justify-content-center w-50">
                            Mulai Latihan
                        </button>
                    </div>
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
</x-app-layout>
