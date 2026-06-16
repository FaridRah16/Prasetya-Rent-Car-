@extends('layouts.admin')

@section('title', 'Kelola Pemesanan')
@section('page-title', 'Kelola Pemesanan')

@section('content')
<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari ID atau Nama Customer..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="payment_status">
                        <option value="">Status Pembayaran</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Daftar Pemesanan</h5>
    </div>
    <div class="card-body">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Mobil</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <div>{{ $booking->user->name }}</div>
                                    <small class="text-muted">{{ $booking->user->email }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $booking->car->name }}</div>
                                    <small class="text-muted">{{ $booking->car->plate_number }}</small>
                                </td>
                                <td>
                                    <div>{{ $booking->start_date->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $booking->total_days }} hari</small>
                                </td>
                                <td>
                                    <strong class="text-primary">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Pending
                                        </span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-info">
                                            <i class="bi bi-check-circle"></i> Dikonfirmasi
                                        </span>
                                    @elseif($booking->status === 'ongoing' && $booking->delivery_proof)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i> Menunggu Selesai
                                        </span>
                                    @elseif($booking->status === 'ongoing')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-car-front"></i> Berlangsung
                                        </span>
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-all"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->payment_status === 'paid')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Belum Bayar
                                        </span>
                                        @if($booking->payment_proof)
                                            <br><small class="text-info">
                                                <i class="bi bi-image"></i> Bukti ada
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Belum Ada Pemesanan</h5>
                <p class="text-muted">Pemesanan dari customer akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
