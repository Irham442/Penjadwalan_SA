{{-- Di dalam resources/views/admin/beban_ajar/edit.blade.php --}}
<h3>Edit Beban Ajar</h3>

<form action="{{ route('admin.beban-ajar.update', $bebanAjar->id) }}" method="POST">
    @csrf
    @method('PUT') {{-- Method untuk update --}}

    {{-- Field Guru --}}
    <div class="mb-3">
        <label for="id_guru" class="form-label">Guru Pengajar</label>
        <select name="id_guru" id="id_guru" class="form-select" required>
            @foreach ($gurus as $guru)
                <option value="{{ $guru->id_guru }}" {{ $bebanAjar->id_guru == $guru->id_guru ? 'selected' : '' }}>
                    {{ $guru->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Field Mapel --}}
    <div class="mb-3">
        <label for="id_mapel" class="form-label">Mata Pelajaran</label>
        <select name="id_mapel" id="id_mapel" class="form-select" required>
            @foreach ($mapels as $mapel)
                <option value="{{ $mapel->id_mapel }}" {{ $bebanAjar->id_mapel == $mapel->id_mapel ? 'selected' : '' }}>
                    {{ $mapel->nama_mapel }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Field Kelas --}}
    <div class="mb-3">
        <label for="id_kelas" class="form-label">Kelas</label>
        <select name="id_kelas" id="id_kelas" class="form-select" required>
             @foreach ($kelases as $kelas)
                <option value="{{ $kelas->id_kelas }}" {{ $bebanAjar->id_kelas == $kelas->id_kelas ? 'selected' : '' }}>
                    {{ $kelas->nama_kelas }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Field Jam/Minggu --}}
    <div class="mb-3">
        <label for="jumlah_jam_seminggu" class="form-label">Jumlah Jam per Minggu</label>
        <input type="number" name="jumlah_jam_seminggu" id="jumlah_jam_seminggu" class="form-control" required min="1" value="{{ $bebanAjar->jumlah_jam_seminggu }}">
    </div>
    
    {{-- Field Jam/Blok --}}
    <div class="mb-3">
        <label for="jam_per_blok" class="form-label">Jam per Blok / Pertemuan</label>
        <input type="number" name="jam_per_blok" id="jam_per_blok" class="form-control" required min="1" value="{{ $bebanAjar->jam_per_blok }}">
    </div>

    {{-- Field Jam/Blok --}}
    <div class="mb-3">
        <label for="jam_per_blok" class="form-label">Jam per Blok / Pertemuan</label>
        <input type="number" name="jam_per_blok" id="jam_per_blok" class="form-control" required min="1" value="{{ $bebanAjar->jam_per_blok }}">
    </div>

    {{-- ▼▼▼ TAMBAHKAN BLOK KODE INI DI SINI ▼▼▼ --}}
    <div class="mb-3">
        <label for="id_hari_waktu" class="form-label">Kunci Jadwal di Waktu (Opsional)</label>
        <select name="id_hari_waktu" id="id_hari_waktu" class="form-select">
            {{-- Opsi default untuk tidak mengunci waktu --}}
            <option value="">-- Biarkan Algoritma yang Menentukan --</option>
            
            {{-- Loop untuk menampilkan semua pilihan waktu --}}
            @foreach ($waktus as $waktu)
                <option value="{{ $waktu->id }}" {{ $bebanAjar->id_hari_waktu == $waktu->id ? 'selected' : '' }}>
                    {{ $waktu->hari }}, {{ date('H:i', strtotime($waktu->jam_mulai)) }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Pilih waktu spesifik jika pelajaran ini harus ada di jam tertentu.</small>
    </div>
    {{-- ▲▲▲ AKHIR DARI BLOK KODE BARU ▲▲▲ --}}

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-secondary">Batal</a>
</form>