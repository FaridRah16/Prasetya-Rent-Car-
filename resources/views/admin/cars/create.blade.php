@extends('layouts.admin')

@section('title', 'Tambah Mobil')
@section('page-title', 'Tambah Mobil Baru')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.cars.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Dasar</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Mobil <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Toyota Avanza"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('brand') is-invalid @enderror" 
                                   name="brand" 
                                   value="{{ old('brand') }}"
                                   placeholder="Contoh: Toyota"
                                   required>
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="MPV" {{ old('type') == 'MPV' ? 'selected' : '' }}>MPV</option>
                                <option value="SUV" {{ old('type') == 'SUV' ? 'selected' : '' }}>SUV</option>
                                <option value="Sedan" {{ old('type') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                <option value="Hatchback" {{ old('type') == 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="Minibus" {{ old('type') == 'Minibus' ? 'selected' : '' }}>Minibus</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('year') is-invalid @enderror" 
                                   name="year" 
                                   value="{{ old('year', date('Y')) }}"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Warna <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('color') is-invalid @enderror" 
                                   name="color" 
                                   value="{{ old('color') }}"
                                   placeholder="Contoh: Putih"
                                   required>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Plat <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('plate_number') is-invalid @enderror" 
                                   name="plate_number" 
                                   value="{{ old('plate_number') }}"
                                   placeholder="Contoh: B 1234 XYZ"
                                   required>
                            @error('plate_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Kursi <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('seats') is-invalid @enderror" 
                                   name="seats" 
                                   value="{{ old('seats', 5) }}"
                                   min="1"
                                   max="20"
                                   required>
                            @error('seats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Transmisi <span class="text-danger">*</span></label>
                            <select class="form-select @error('transmission') is-invalid @enderror" name="transmission" required>
                                <option value="">Pilih Transmisi</option>
                                <option value="Manual" {{ old('transmission') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                <option value="Automatic" {{ old('transmission') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="CVT" {{ old('transmission') == 'CVT' ? 'selected' : '' }}>CVT</option>
                            </select>
                            @error('transmission')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bahan Bakar <span class="text-danger">*</span></label>
                            <select class="form-select @error('fuel') is-invalid @enderror" name="fuel" required>
                                <option value="">Pilih Bahan Bakar</option>
                                <option value="Bensin" {{ old('fuel') == 'Bensin' ? 'selected' : '' }}>Bensin</option>
                                <option value="Diesel" {{ old('fuel') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Hybrid" {{ old('fuel') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="Listrik" {{ old('fuel') == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                            </select>
                            @error('fuel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cash"></i> Harga & Status</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa per Hari (Rp) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('price_per_day') is-invalid @enderror" 
                                   name="price_per_day" 
                                   value="{{ old('price_per_day') }}"
                                   min="0"
                                   step="1000"
                                   placeholder="Contoh: 350000"
                                   required>
                            @error('price_per_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Foto Mobil</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Foto Utama (Opsional)</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(event)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" style="display: none; margin-top: 10px;">
                        <img id="preview" src="#" alt="Preview" style="max-width: 300px; border-radius: 10px;" class="img-fluid">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Galeri Foto Mobil</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Foto Tambahan (Bisa pilih beberapa sekaligus)</label>
                        <input type="file" 
                               class="form-control @error('gallery') is-invalid @enderror" 
                               name="gallery[]" 
                               accept="image/*"
                               multiple
                               onchange="previewGallery(event)">
                        @error('gallery')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('gallery.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB per file)</small>
                    </div>

                    <!-- Gallery Preview Container -->
                    <div id="galleryPreview" class="row g-2 mt-2" style="display: none;">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Deskripsi</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              name="description" 
                              rows="4" 
                              placeholder="Deskripsi singkat tentang mobil ini (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Simpan Mobil
                </button>
                <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Tips</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Pastikan nomor plat unik dan belum terdaftar</li>
                    <li>Upload foto mobil untuk tampilan yang lebih menarik</li>
                    <li>Isi deskripsi untuk memberikan informasi tambahan</li>
                    <li>Set status "Maintenance" jika mobil sedang perbaikan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('preview');
            output.src = reader.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function previewGallery(event) {
        const container = document.getElementById('galleryPreview');
        container.innerHTML = '';
        container.style.display = 'flex';
        
        if (event.target.files) {
            Array.from(event.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3 text-center';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded border';
                    img.style.maxHeight = '85px';
                    img.style.objectFit = 'contain';
                    
                    col.appendChild(img);
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endsection
