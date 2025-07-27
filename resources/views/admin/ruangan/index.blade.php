<h3>Manajemen Ruangan</h3>
<p>Daftar semua lokasi fisik yang dapat digunakan untuk penjadwalan.</p>
<a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Ruangan Baru
</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Ruangan</th>
            <th>Kapasitas</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($semuaRuangan as $ruangan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $ruangan->nama_ruangan }}</td>
                <td>{{ $ruangan->kapasitas }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data ruangan.</td>
            </tr>
        @endforelse
    </tbody>
</table>