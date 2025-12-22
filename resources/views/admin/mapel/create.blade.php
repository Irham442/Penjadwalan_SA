<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mapel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card border-0 shadow-sm" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Tambah Mata Pelajaran</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.mapel.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Kode Mapel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror" name="kode_mapel" value="{{ old('kode_mapel') }}" placeholder="Contoh: MTK, BIO">
                        @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Mapel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror" name="nama_mapel" value="{{ old('nama_mapel') }}">
                        @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori">
                            <option value="Muatan Nasional">Muatan Nasional</option>
                            <option value="Muatan Kewilayahan">Muatan Kewilayahan</option>
                            <option value="Muatan Kejuruan">Muatan Kejuruan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-info text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>