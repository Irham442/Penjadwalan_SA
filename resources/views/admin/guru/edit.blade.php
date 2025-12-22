<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card border-0 shadow-sm" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Data Guru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.guru.update', $guru->id_guru) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $guru->nama) }}">
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP</label>
                            <input type="number" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip', $guru->nip) }}">
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NUPTK</label>
                            <input type="number" class="form-control" name="nuptk" value="{{ old('nuptk', $guru->nuptk) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select" name="jenis_kelamin">
                                <option value="L" {{ $guru->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $guru->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $guru->phone) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Kepegawaian</label>
                        <select class="form-select" name="status_kepegawaian">
                            <option value="PNS" {{ $guru->status_kepegawaian == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ $guru->status_kepegawaian == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="GTT" {{ $guru->status_kepegawaian == 'GTT' ? 'selected' : '' }}>GTT</option>
                            <option value="GTY" {{ $guru->status_kepegawaian == 'GTY' ? 'selected' : '' }}>GTY</option>
                            <option value="Honorer" {{ $guru->status_kepegawaian == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>