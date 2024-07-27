<section class="mb-4">
    <header>
        <h2 class="h5 fw-bold text-dark">
            Hapus Akun
        </h2>

        <p class="mt-2 text-muted">
            Jika kamu hapus akunmu, semua data akan hilang selamanya. Tekan tombol di bawah untuk menghapus akunmu.
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        Hapus Akun
    </button>

    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="confirm-user-deletionLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirm-user-deletionLabel">
                            Apa kamu yakin ingin menghapus akunmu?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Jika kamu hapus akunmu, semua data akan hilang selamanya. Masukkan kata sandimu untuk
                            memastikan kamu ingin menghapus akunmu.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input id="password" name="password" type="password" class="form-control"
                                placeholder="Kata sandi yang sekarang...">
                            @if ($errors->userDeletion->get('password'))
                                <div class="text-danger mt-2">
                                    {{ $errors->userDeletion->get('password')[0] }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
