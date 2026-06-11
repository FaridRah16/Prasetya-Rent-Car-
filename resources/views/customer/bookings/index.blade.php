@extends('layouts.customer')

@section('title', 'Riwayat Booking')
@section('page-title', 'Riwayat Booking Saya')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Semua Booking</h5>
        <a href="{{ route('customer.bookings.create') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-plus-circle"></i> Booking Baru
        </a>
    </div>
    <div class="card-body">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mobil</th>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <strong>{{ $booking->car->name }}</strong><br>
                                    <small class="text-muted">{{ $booking->car->plate_number }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">Mulai:</small> {{ $booking->start_date->format('d/m/Y') }}<br>
                                    <small class="text-muted">Selesai:</small> {{ $booking->end_date->format('d/m/Y') }}
                                </td>
                                <td>{{ $booking->total_days }} hari</td>
                                <td>
                                    <strong class="text-primary">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-info">Dikonfirmasi</span>
                                    @elseif($booking->status === 'ongoing')
                                        <span class="badge bg-primary">Berlangsung</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
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
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customer.bookings.show', $booking->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
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
                <h5 class="mt-3">Belum Ada Booking</h5>
                <p class="text-muted mb-4">Mulai booking mobil untuk perjalanan Anda</p>
                <a href="{{ route('customer.bookings.create') }}" class="btn btn-warning">
                    <i class="bi bi-plus-circle"></i> Buat Booking Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
