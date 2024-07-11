<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold text-dark py-3">
            Pengaturan Latihan Kosakata
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="container">
            <form action="/latihan">
                <div class="container py-3" id="preferensi">
                    <select id="bahasa" name="bahasa" class="form-select" aria-label="bahasa">
                        <option selected>Pilih Bahasa</option>
                        <option value="inggris">Inggris</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                    <select id="kategori" name="kategori" class="form-select" aria-label="kategori">
                        <option selected>Pilih Kategori</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
