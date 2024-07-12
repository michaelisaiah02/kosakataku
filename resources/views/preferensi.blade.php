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
                        <label for="bahasa" class="form-label text-center fs-3">Bahasa</label>
                        <select id="bahasa" name="id_bahasa" class="form-select form-select-lg w-50 mb-3"
                            aria-label="bahasa">
                            <option {{ old('bahasa') ? '' : 'selected' }}>Pilih Bahasa</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}">{{ $language->indonesia }}</option>
                            @endforeach
                        </select>
                        @error('id_bahasa')
                            <div class="alert alert-danger text-center">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row mb-3 justify-content-center">
                        <label for="kategori" class="form-label text-center fs-3">Kategori</label>
                        <select id="kategori" name="id_kategori" class="form-select form-select-lg w-50 mb-3"
                            aria-label="kategori">
                            <option selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->indonesia }}</option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    An example danger alert with an icon
                                </div>
                            </div>
                        @enderror
                    </div>
                    <div class="row mb-3 justify-content-center">
                        <label for="tingkat_kesulitan" class="form-label text-center fs-3">Tingkat Kesulitan</label>
                        <select id="tingkat_kesulitan" name="id_tingkat_kesulitan"
                            class="form-select form-select-lg w-50 text-capitalize mb-3" aria-label="tingkat_kesulitan">
                            <option selected>Pilih Tingkat Kesulitan</option>
                            @foreach ($difficulties as $difiiculty)
                                <option value="{{ $difiiculty->id }}" class="text-capitalize">
                                    {{ $difiiculty->tingkat_kesulitan }}</option>
                            @endforeach
                        </select>
                        @error('id_tingkat_kesulitan')
                            <div class="alert alert-danger text-center w-50">
                                {{ $message }}
                            </div>
                        @enderror
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
