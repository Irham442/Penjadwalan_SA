{{-- Di dalam resources/views/admin/beban_ajar/index.blade.php --}}
<h3>Manajemen Beban Ajar</h3>
<p>Di sini Anda bisa mengatur guru mana mengajar mapel apa di kelas mana.</p>
<a href="{{ route('admin.beban-ajar.create') }}" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Beban Ajar Baru
</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Guru</th>
            <th>Mata Pelajaran</th>
            <th>Kelas</th>
            <th>Jam/Minggu</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($bebanAjars as $bebanAjar)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $bebanAjar->guru->nama ?? 'N/A' }}</td>
                <td>{{ $bebanAjar->mapel->nama_mapel ?? 'N/A' }}</td>
                <td>{{ $bebanAjar->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $bebanAjar->jumlah_jam_seminggu }}</td>
                <td>
                    <a href="{{ route('admin.beban-ajar.edit', $bebanAjar->id) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <form action="{{ route('admin.beban-ajar.destroy', $bebanAjar->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data beban ajar.</td>
            </tr>
        @endforelse
    </tbody>
</table>