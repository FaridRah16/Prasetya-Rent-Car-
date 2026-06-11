@extends('layouts.customer')

@section('title', 'Booking Baru')
@section('page-title', 'Booking Mobil Baru')

@section('styles')
<style>
    .car-preview {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        border: 2px solid #e9ecef;
    }
    
    .car-preview.selected {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.1);
    }
    
    .price-summary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2c5282 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        position: sticky;
        top: 20px;
    }
</style>
@endsection

@section('content')
<form method="POST" action="{{ route('customer.bookings.store') }}" id="bookingForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Step 1: Pilih Mobil -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-car-front"></i> Step 1: Pilih Mobil</h5>
                </div>
                <div class="card-body">
                    @if($cars->count() > 0)
                        <div class="row g-3">
                            @foreach($cars as $car)
                                <div class="col-md-6">
                                    <div class="car-preview {{ $selectedCarId == $car->id ? 'selected' : '' }}" 
                                         onclick="selectCar({{ $car->id }}, {{ $car->price_per_day }}, '{{ $car->name }}')">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="car_id" 
                                                   value="{{ $car->id }}" 
                                                   id="car{{ $car->id }}"
                                                   {{ $selectedCarId == $car->id ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label w-100" for="car{{ $car->id }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3" style="width: 80px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-car-front-fill text-white" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $car->name }}</h6>
                                                        <small class="text-muted">{{ $car->brand }} • {{ $car->seats }} Kursi</small>
                                                        <p class="mb-0 fw-bold text-warning">
                                                            Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('car_id')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Tidak ada mobil yang tersedia saat ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Step 2: Tanggal & Lokasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Step 2: Tanggal & Lokasi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ old('start_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ old('end_date') }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Penjemputan <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('pickup_location') is-invalid @enderror" 
                                   name="pickup_location" 
                                   value="{{ old('pickup_location') }}"
                                   placeholder="Contoh: Jl. Sudirman No. 10, Jakarta"
                                   required>
                            @error('pickup_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Pengantaran <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('dropoff_location') is-invalid @enderror" 
                                   name="dropoff_location" 
                                   value="{{ old('dropoff_location') }}"
                                   placeholder="Contoh: Bandara Soekarno-Hatta"
                                   required>
                            @error('dropoff_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pilih Driver (Optional) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Step 3: Pilih Driver (Opsional)</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="driver_option" id="no_driver" value="no" checked>
                        <label class="form-check-label" for="no_driver">
                            <strong>Tanpa Driver</strong> - Saya akan menyetir sendiri
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="driver_option" id="with_driver" value="yes">
                        <label class="form-check-label" for="with_driver">
                            <strong>Dengan Driver</strong> - Saya ingin menggunakan driver
                        </label>
                    </div>

                    <div id="driver_selection" style="display: none;">
                        @if($drivers->count() > 0)
                            <select class="form-select" name="driver_id" id="driver_id">
                                <option value="">Pilih Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->user_id }}">
                                        {{ $driver->user->name }} - SIM: {{ $driver->license_number }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Tidak ada driver yang tersedia saat ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Step 4: Catatan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Step 4: Catatan Tambahan</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" 
                              name="notes" 
                              rows="3" 
                              placeholder="Tambahkan catatan khusus untuk booking Anda (opsional)">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="col-lg-4">
            <div class="price-summary">
                <h5 class="mb-3"><i class="bi bi-receipt"></i> Ringkasan Booking</h5>
                
                <div class="mb-3">
                    <small class="text-white-50">Mobil</small>
                    <p class="mb-0 fw-bold" id="summary_car">-</p>
                </div>

                <div class="mb-3">
                    <small class="text-white-50">Durasi</small>
                    <p class="mb-0 fw-bold" id="summary_duration">- hari</p>
                </div>

                <div class="mb-3">
                    <small class="text-white-50">Harga per Hari</small>
                    <p class="mb-0 fw-bold" id="summary_price">Rp -</p>
                </div>

                <hr style="border-color: rgba(255,255,255,0.3);">

                <div class="mb-4">
                    <small class="text-white-50">Total Pembayaran</small>
                    <h3 class="mb-0" id="summary_total">Rp 0</h3>
                </div>

                <button type="submit" class="btn btn-warning w-100 btn-lg">
                    <i class="bi bi-check-circle"></i> Konfirmasi Booking
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    let selectedCarPrice = {{ $selectedCarId ? $cars->find($selectedCarId)?->price_per_day ?? 0 : 0 }};
    let selectedCarName = '{{ $selectedCarId ? $cars->find($selectedCarId)?->name ?? "" : "" }}';

    // Initialize summary if car is preselected
    if (selectedCarPrice > 0) {
        updateSummary();
    }

    function selectCar(carId, price, name) {
        selectedCarPrice = price;
        selectedCarName = name;
        updateSummary();
    }

    // Driver option toggle
    document.getElementById('with_driver').addEventListener('change', function() {
        document.getElementById('driver_selection').style.display = 'block';
    });

    document.getElementById('no_driver').addEventListener('change', function() {
        document.getElementById('driver_selection').style.display = 'none';
        document.getElementById('driver_id').value = '';
    });

    // Date change listeners
    document.getElementById('start_date').addEventListener('change', updateSummary);
    document.getElementById('end_date').addEventListener('change', updateSummary);

    function updateSummary() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        // Calculate duration
        let duration = 0;
        if (startDate && endDate && endDate >= startDate) {
            duration = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        }

        // Calculate total
        const total = duration * selectedCarPrice;

        // Update summary
        document.getElementById('summary_car').textContent = selectedCarName || '-';
        document.getElementById('summary_duration').textContent = duration > 0 ? duration + ' hari' : '- hari';
        document.getElementById('summary_price').textContent = 'Rp ' + selectedCarPrice.toLocaleString('id-ID');
        document.getElementById('summary_total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection
