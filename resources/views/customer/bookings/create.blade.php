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
    
    .map-coords {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    #mapModal .modal-body {
        padding: 0;
    }
    
    #map {
        height: 450px;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        #map {
            height: 350px;
        }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
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
                                         onclick="selectCar({{ $car->id }}, {{ $car->price_per_day }}, @js($car->name))">
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

            <!-- Step 2: Tanggal, Waktu & Lokasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Step 2: Tanggal, Waktu & Lokasi</h5>
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
                            <label class="form-label">Jam Penjemputan <span class="text-danger">*</span></label>
                            <input type="time" 
                                   class="form-control @error('pickup_time') is-invalid @enderror" 
                                   name="pickup_time" 
                                   id="pickup_time"
                                   value="{{ old('pickup_time', '08:00') }}" 
                                   required>
                            @error('pickup_time')
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
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jam Pengembalian <span class="text-danger">*</span></label>
                            <input type="time" 
                                   class="form-control @error('return_time') is-invalid @enderror" 
                                   name="return_time" 
                                   id="return_time"
                                   value="{{ old('return_time', '08:00') }}" 
                                   required>
                            @error('return_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pickup Location -->
                        <div class="col-md-12">
                            <label class="form-label">Lokasi Penjemputan <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('pickup_location') is-invalid @enderror" 
                                   name="pickup_location" 
                                   id="pickup_location"
                                   value="{{ old('pickup_location') }}"
                                   placeholder="Contoh: Jl. Sudirman No. 10, Jakarta"
                                   required>
                            @error('pickup_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="pickup_lat" id="pickup_lat" value="{{ old('pickup_lat') }}">
                            <input type="hidden" name="pickup_lng" id="pickup_lng" value="{{ old('pickup_lng') }}">
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm flex-fill" onclick="openMap('pickup')">
                                    <i class="bi bi-geo-alt-fill"></i> Pilih di Peta
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="openGoogleMaps('pickup')" id="pickup_gmaps_btn" style="display:none">
                                    <i class="bi bi-map"></i> Google Maps
                                </button>
                            </div>
                            <div class="map-coords mt-1" id="pickup_coords_display"></div>
                        </div>

                        <!-- Dropoff Location -->
                        <div class="col-md-12">
                            <label class="form-label">Lokasi Pengantaran <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('dropoff_location') is-invalid @enderror" 
                                   name="dropoff_location" 
                                   id="dropoff_location"
                                   value="{{ old('dropoff_location') }}"
                                   placeholder="Contoh: Bandara Soekarno-Hatta"
                                   required>
                            @error('dropoff_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="dropoff_lat" id="dropoff_lat" value="{{ old('dropoff_lat') }}">
                            <input type="hidden" name="dropoff_lng" id="dropoff_lng" value="{{ old('dropoff_lng') }}">
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm flex-fill" onclick="openMap('dropoff')">
                                    <i class="bi bi-geo-alt-fill"></i> Pilih di Peta
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="openGoogleMaps('dropoff')" id="dropoff_gmaps_btn" style="display:none">
                                    <i class="bi bi-map"></i> Google Maps
                                </button>
                            </div>
                            <div class="map-coords mt-1" id="dropoff_coords_display"></div>
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
                    @php $withDriver = old('driver_id') || $errors->has('driver_id'); @endphp
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="driver_option" id="no_driver" value="no" @unless($withDriver) checked @endunless>
                        <label class="form-check-label" for="no_driver">
                            <strong>Tanpa Driver</strong> - Saya akan menyetir sendiri
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="driver_option" id="with_driver" value="yes" @if($withDriver) checked @endif>
                        <label class="form-check-label" for="with_driver">
                            <strong>Dengan Driver</strong> - Saya ingin menggunakan driver
                        </label>
                    </div>

                    <div id="driver_selection" style="display: {{ $withDriver ? 'block' : 'none' }};">
                        @if($drivers->count() > 0)
                            <select class="form-select @error('driver_id') is-invalid @enderror" name="driver_id" id="driver_id">
                                <option value="">Pilih Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->user_id }}" @selected(old('driver_id') == $driver->user_id)>
                                        {{ $driver->user->name }} - SIM: {{ $driver->license_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
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

<!-- Map Picker Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-geo-alt"></i> Pilih Lokasi di Peta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height:450px;width:100%;min-height:450px;"></div>
                <div class="p-3 bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Ketuk pada peta untuk menandai lokasi
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" id="mapAddress" placeholder="Alamat akan terisi otomatis...">
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmMapLocation()">
                    <i class="bi bi-check-circle"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    let selectedCarPrice = {{ $selectedCarId ? $cars->find($selectedCarId)?->price_per_day ?? 0 : 0 }};
    let selectedCarName = @js($selectedCarId ? ($cars->find($selectedCarId)?->name ?? '') : '');
    let map, marker, currentMapTarget = 'pickup';

    // Initialize summary if car is preselected
    if (selectedCarPrice > 0) {
        updateSummary();
    }

    function selectCar(carId, price, name) {
        selectedCarPrice = price;
        selectedCarName = name;
        const radio = document.getElementById('car' + carId);
        if (radio) radio.checked = true;
        document.querySelectorAll('.car-preview').forEach(el => el.classList.remove('selected'));
        radio.closest('.car-preview').classList.add('selected');
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

    // Date/time change listeners
    ['start_date', 'end_date', 'pickup_time', 'return_time'].forEach(id => {
        document.getElementById(id).addEventListener('change', updateSummary);
    });

    function updateSummary() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const pickupTime = document.getElementById('pickup_time').value || '00:00';
        const returnTime = document.getElementById('return_time').value || '00:00';

        let duration = 0;
        if (startDate && endDate && pickupTime && returnTime) {
            const pickup = new Date(startDate + 'T' + pickupTime);
            const returnD = new Date(endDate + 'T' + returnTime);
            if (returnD > pickup) {
                const hours = (returnD - pickup) / (1000 * 60 * 60);
                duration = Math.ceil(hours / 24);
                if (duration < 1) duration = 1;
            }
        }

        const total = duration * selectedCarPrice;
        document.getElementById('summary_car').textContent = selectedCarName || '-';
        document.getElementById('summary_duration').textContent = duration > 0 ? duration + ' hari' : '- hari';
        document.getElementById('summary_price').textContent = 'Rp ' + selectedCarPrice.toLocaleString('id-ID');
        document.getElementById('summary_total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // ========== INTERACTIVE MAP PICKER (Leaflet + OpenStreetMap) ==========
    // openMap hanya mencatat target lalu menampilkan modal.
    // Pembuatan/penyegaran peta ditangani oleh listener `shown.bs.modal` permanen
    // di bawah, sehingga PASTI berjalan setiap kali modal dibuka (tidak bergantung
    // pada timing pemasangan listener).
    function openMap(target) {
        currentMapTarget = target;
        const modalEl = document.getElementById('mapModal');
        // Pindahkan modal menjadi anak langsung <body> agar lepas dari elemen induk
        // (form/.main-content) yang bisa membuat position:fixed-nya salah posisi &
        // backdrop menutupi modal → modal melayang ke atas dan tidak bisa diklik.
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        // getOrCreateInstance (bukan `new` tiap klik) agar tidak ada instance modal
        // ganda yang meninggalkan backdrop nyangkut → layar beku & harus refresh.
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    // Membuat objek peta (sekali) lalu menyegarkannya. Dipanggil dari listener shown.bs.modal.
    function initOrRefreshMap() {
        const target = currentMapTarget;
        const existingLat = document.getElementById(target + '_lat').value;
        const existingLng = document.getElementById(target + '_lng').value;
        const center = (existingLat && existingLng)
            ? [parseFloat(existingLat), parseFloat(existingLng)]
            : [-6.2088, 106.8456]; // Default: Jakarta

        // Buat peta sekali saja; pada pembukaan berikutnya cukup invalidateSize.
        if (!map) {
            map = L.map('map');

            // Tile utama: OpenStreetMap. Jika gagal dimuat (diblokir jaringan/ISP),
            // otomatis beralih ke CARTO agar peta tidak pernah blank abu-abu.
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            });
            let fellBackToCarto = false;
            osmLayer.on('tileerror', function () {
                if (fellBackToCarto) return;
                fellBackToCarto = true;
                map.removeLayer(osmLayer);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map);
            });
            osmLayer.addTo(map);

            // Click/tap to place marker
            map.on('click', function(e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);

                // Reverse geocode untuk mengisi alamat (best-effort).
                // Nominatim publik punya rate limit / bisa menolak request; jika gagal
                // (HTTP error, diblokir, atau tanpa hasil) jatuh ke fallback koordinat
                // agar field alamat tidak pernah kosong.
                const fallback = e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${e.latlng.lat}&lon=${e.latlng.lng}&format=json`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(r => {
                        if (!r.ok) throw new Error('geocode failed: ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        document.getElementById('mapAddress').value = data.display_name || fallback;
                    })
                    .catch(() => {
                        document.getElementById('mapAddress').value = fallback;
                    });
            });
        }

        // Reset state agar marker/alamat dari target sebelumnya tidak bocor ke target ini.
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        document.getElementById('mapAddress').value = '';
        map.setView(center, 13);

        // PENTING: modal Bootstrap punya animasi, sehingga saat peta pertama kali dibuat
        // ukuran kontainernya sering belum final → tile tampil abu-abu/kosong & klik meleset.
        // invalidateSize() memaksa Leaflet menghitung ulang ukuran & memuat tile.
        map.invalidateSize();
        setTimeout(function () { map.invalidateSize(); }, 300);

        // Jika target ini sudah punya koordinat, tampilkan marker & alamat yang tersimpan.
        if (existingLat && existingLng) {
            marker = L.marker(center).addTo(map);
            document.getElementById('mapAddress').value =
                document.getElementById(target + '_location').value || '';
        }
    }

    // Listener PERMANEN: dipasang sekali saat skrip dimuat. Bootstrap memicu event ini
    // setiap kali modal selesai tampil, sehingga peta selalu dibangun/disegarkan.
    document.getElementById('mapModal').addEventListener('shown.bs.modal', initOrRefreshMap);

    function confirmMapLocation() {
        if (!marker) {
            alert('Silakan ketuk pada peta untuk memilih lokasi');
            return;
        }

        const lat = marker.getLatLng().lat.toFixed(7);
        const lng = marker.getLatLng().lng.toFixed(7);
        const address = document.getElementById('mapAddress').value || (lat + ', ' + lng);

        // Fill in form fields
        document.getElementById(currentMapTarget + '_lat').value = lat;
        document.getElementById(currentMapTarget + '_lng').value = lng;
        document.getElementById(currentMapTarget + '_location').value = address;

        // Update display
        updateCoordsDisplay(currentMapTarget);

        bootstrap.Modal.getInstance(document.getElementById('mapModal')).hide();
    }

    // Open Google Maps in new tab for viewing
    function openGoogleMaps(target) {
        const lat = document.getElementById(target + '_lat').value;
        const lng = document.getElementById(target + '_lng').value;
        if (lat && lng) {
            window.open(`https://www.google.com/maps?q=${lat},${lng}&z=18`, '_blank');
        }
    }

    // Update coordinates display + show Google Maps button
    function updateCoordsDisplay(target) {
        const lat = document.getElementById(target + '_lat').value.trim();
        const lng = document.getElementById(target + '_lng').value.trim();
        const display = document.getElementById(target + '_coords_display');
        const gmapsBtn = document.getElementById(target + '_gmaps_btn');
        
        if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
            display.innerHTML = 
                '<i class="bi bi-geo-alt-fill text-success"></i> Koordinat: ' + lat + ', ' + lng +
                ' &middot; <a href="https://www.google.com/maps?q=' + lat + ',' + lng + '" target="_blank" rel="noopener noreferrer" class="text-primary">Lihat di Google Maps</a>';
            if (gmapsBtn) gmapsBtn.style.display = '';
        } else {
            display.innerHTML = '';
            if (gmapsBtn) gmapsBtn.style.display = 'none';
        }
    }

    // Show existing coords on page load
    ['pickup', 'dropoff'].forEach(function(target) {
        updateCoordsDisplay(target);
    });
</script>
@endsection
