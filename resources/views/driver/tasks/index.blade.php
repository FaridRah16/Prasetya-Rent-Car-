@extends('layouts.driver')

@section('title', 'Tugas Saya')
@section('page-title', 'Tugas Aktif Saya')

@section('content')
@if($tasks->count() > 0)
    <div class="row g-4">
        @foreach($tasks as $task)
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center text-white">
                            <h5 class="mb-0">Booking #{{ $task->id }}</h5>
                            @if($task->status === 'confirmed')
                                <span class="badge bg-light text-dark">Menunggu Dimulai</span>
                            @else
                                <span class="badge bg-warning text-dark">Sedang Berlangsung</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Customer Info -->
                        <h6><i class="bi bi-person-fill text-primary"></i> Customer</h6>
                        <p class="mb-3">
                            <strong>{{ $task->user->name }}</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-telephone"></i> {{ $task->user->phone }}
                            </small>
                        </p>

                        <!-- Car Info -->
                        <h6><i class="bi bi-car-front-fill text-primary"></i> Mobil</h6>
                        <p class="mb-3">
                            <strong>{{ $task->car->name }}</strong><br>
                            <small class="text-muted">{{ $task->car->plate_number }}</small>
                        </p>

                        <!-- Schedule -->
                        <h6><i class="bi bi-calendar-event text-primary"></i> Jadwal</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Mulai</small>
                                <p class="mb-0 fw-bold">{{ $task->start_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Selesai</small>
                                <p class="mb-0 fw-bold">{{ $task->end_date->format('d M Y') }}</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <h6><i class="bi bi-geo-alt-fill text-primary"></i> Lokasi</h6>
                        <div class="alert alert-light mb-3">
                            <strong>Penjemputan:</strong><br>
                            {{ $task->pickup_location }}
                            <hr class="my-2">
                            <strong>Pengantaran:</strong><br>
                            {{ $task->dropoff_location }}
                        </div>

                        <!-- Notes -->
                        @if($task->notes)
                            <h6><i class="bi bi-chat-left-text text-primary"></i> Catatan</h6>
                            <p class="text-muted small mb-3">{{ $task->notes }}</p>
                        @endif

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            @if($task->status === 'confirmed')
                                <form method="POST" action="{{ route('driver.tasks.start', $task->id) }}" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-play-circle"></i> Mulai Tugas
                                    </button>
                                </form>
                            @elseif($task->status === 'ongoing')
                                <form method="POST" 
                                      action="{{ route('driver.tasks.complete', $task->id) }}"
                                      onsubmit="return confirm('Yakin tugas sudah selesai?')"
                                      class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-circle"></i> Selesaikan Tugas
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('driver.tasks.show', $task->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3">Tidak Ada Tugas Aktif</h4>
            <p class="text-muted mb-0">Tugas baru akan muncul di sini ketika admin menugaskan Anda</p>
        </div>
    </div>
@endif
@endsection
