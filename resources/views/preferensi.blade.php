<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Pengaturan Latihan Kosakata
        </h2>
    </x-slot>

    <div class="py-1 min-vh-75 d-flex align-items-center">
        <div class="container">
            <form action="{{ route('latihan.store') }}" method="POST">
                @csrf
                <div class="container py-3" id="preferensi">
                    <div class="row my-3 justify-content-center">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75">
                                <label for="bahasa" class="form-label text-center fs-3">Bahasa</label>
                                <select id="bahasa" name="id_bahasa" class="form-select form-select-lg"
                                    aria-label="bahasa">
                                    <option>Pilih Bahasa</option>
                                    @foreach ($languages as $language)
                                        <option value="{{ $language->id }}"
                                            @if (old('id_bahasa') == $language->id) selected @endif>{{ $language->indonesia }}
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
                    <div class="row mb-3 justify-content-center">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="row w-75">
                                <label for="tingkat_kesulitan" class="form-label text-center fs-3">Tingkat
                                    Kesulitan</label>
                                <select id="tingkat_kesulitan" name="id_tingkat_kesulitan"
                                    class="form-select form-select-lg text-capitalize" aria-label="tingkat_kesulitan">
                                    <option selected>Pilih Tingkat Kesulitan</option>
                                    @foreach ($difficulties as $difiiculty)
                                        <option value="{{ $difiiculty->id }}"
                                            @if (old('id_tingkat_kesulitan') == $difiiculty->id) selected @endif class="text-capitalize">
                                            {{ $difiiculty->tingkat_kesulitan }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_tingkat_kesulitan')" class="mt-2 ms-3"></x-input-error>
                            </div>
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
</x-app-layout>
