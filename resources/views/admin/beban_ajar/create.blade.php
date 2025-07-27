<h3>Tambah Beban Ajar Baru</h3>
<form action="{{ route('admin.beban-ajar.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="id_guru" class="form-label">Guru Pengajar</label>
        <select name="id_guru" id="id_guru" class="form-select" required>
            <option value="">-- Pilih Guru --</option>
            @foreach ($gurus as $guru)
                <option value="{{ $guru->id_guru }}">{{ $guru->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="id_mapel" class="form-label">Mata Pelajaran</label>
        <select name="id_mapel" id="id_mapel" class="form-select" required>
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach ($mapels as $mapel)
                <option value="{{ $mapel->id_mapel }}">{{ $mapel->kode_mapel }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="id_kelas" class="form-label">Kelas</label>
        <select name="id_kelas" id="id_kelas" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach ($kelases as $kelas)
                <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="jumlah_jam_seminggu" class="form-label">Jumlah Jam per Minggu</label>
        <input type="number" name="jumlah_jam_seminggu" id="jumlah_jam_seminggu" class="form-control" required min="1">
    </div>
    <div class="mb-3">
        <label for="jam_per_blok" class="form-label">Jam per Blok / Pertemuan</label>
        <input type="number" name="jam_per_blok" id="jam_per_blok" class="form-control" required min="1" value="1">
        <small class="form-text text-muted">Contoh: jika pelajaran ini 2 jam setiap pertemuan, isi dengan angka 2.</small>
    </div>
    <div class="mb-3">
        <label for="id_hari_waktu" class="form-label">Kunci Jadwal di Waktu (Opsional)</label>
        <select name="id_hari_waktu" id="id_hari_waktu" class="form-select">
            <option value="">-- Biarkan Algoritma yang Menentukan --</option>
            @foreach ($waktus as $waktu)
                <option value="{{ $waktu->id }}">
                    {{ $waktu->hari }}, {{ date('H:i', strtotime($waktu->jam_mulai)) }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-secondary">Batal</a>
</form>