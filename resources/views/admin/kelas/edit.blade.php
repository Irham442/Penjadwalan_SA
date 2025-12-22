<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card border-0 shadow-sm" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Data Kelas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kelas.update', $kelas->id_kelas) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}">
                        @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tingkat</label>
                            <select class="form-select" name="tingkat">
                                <option value="10" {{ $kelas->tingkat == 10 ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ $kelas->tingkat == 11 ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ $kelas->tingkat == 12 ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" name="kapasitas" value="{{ $kelas->kapasitas }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Ruangan</label>
                        <input type="text" class="form-control" name="lokasi_ruangan" value="{{ $kelas->lokasi_ruangan }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>