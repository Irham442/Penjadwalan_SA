<h3>Tambah Ruangan Baru</h3>
<form action="{{ route('admin.ruangan.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
        <input type="text" name="nama_ruangan" id="nama_ruangan" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="kapasitas" class="form-label">Kapasitas (Jumlah Kursi)</label>
        <input type="number" name="kapasitas" id="kapasitas" class="form-control" required min="1">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.ruangan.index') }}" class="btn btn-secondary">Batal</a>
</form>