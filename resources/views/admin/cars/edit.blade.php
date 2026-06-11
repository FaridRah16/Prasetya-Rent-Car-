@extends('layouts.admin')

@section('title', 'Edit Mobil')
@section('page-title', 'Edit Mobil: ' . $car->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.cars.update', $car->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                   value="{{ old('name', $car->name) }}"
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
                                   value="{{ old('brand', $car->brand) }}"
                                   required>
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="MPV" {{ old('type', $car->type) == 'MPV' ? 'selected' : '' }}>MPV</option>
                                <option value="SUV" {{ old('type', $car->type) == 'SUV' ? 'selected' : '' }}>SUV</option>
                                <option value="Sedan" {{ old('type', $car->type) == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                <option value="Hatchback" {{ old('type', $car->type) == 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="Minibus" {{ old('type', $car->type) == 'Minibus' ? 'selected' : '' }}>Minibus</option>
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
                                   value="{{ old('year', $car->year) }}"
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
                                   value="{{ old('color', $car->color) }}"
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
                                   value="{{ old('plate_number', $car->plate_number) }}"
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
                                   value="{{ old('seats', $car->seats) }}"
                                   min="1"
                                   max="20"
                                   required>
                            @error('seats')
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
                                   value="{{ old('price_per_day', $car->price_per_day) }}"
                                   min="0"
                                   step="1000"
                                   required>
                            @error('price_per_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="available" {{ old('status', $car->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                                <option value="rented" {{ old('status', $car->status) == 'rented' ? 'selected' : '' }}>Disewa</option>
                                <option value="maintenance" {{ old('status', $car->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                    @if($car->image)
                        <div class="mb-3">
                            <label class="form-label">Foto Saat Ini:</label>
                            <div>
                                <img src="{{ asset('storage/' . $car->image) }}" 
                                     alt="{{ $car->name }}" 
                                     style="max-width: 300px; border-radius: 10px;" 
                                     class="img-fluid">
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Upload Foto Baru (Opsional)</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(event)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" style="display: none;">
                        <label class="form-label">Preview Foto Baru:</label>
                        <img id="preview" src="#" alt="Preview" style="max-width: 300px; border-radius: 10px;" class="img-fluid">
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
                              placeholder="Deskripsi singkat tentang mobil ini (opsional)">{{ old('description', $car->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Update Mobil
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
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Info Mobil</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">ID Mobil</small>
                    <p class="mb-0 fw-bold">#{{ $car->id }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ditambahkan</small>
                    <p class="mb-0">{{ $car->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <small class="text-muted">Update Terakhir</small>
                    <p class="mb-0">{{ $car->updated_at->format('d M Y H:i') }}</p>
                </div>
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
</script>
@endsection
