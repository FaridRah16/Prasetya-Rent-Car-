@extends('layouts.driver')

@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Tugas Selesai</h5>
    </div>
    <div class="card-body">
        @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Mobil</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td><strong>#{{ $task->id }}</strong></td>
                                <td>
                                    {{ $task->user->name }}<br>
                                    <small class="text-muted">{{ $task->user->phone }}</small>
                                </td>
                                <td>
                                    {{ $task->car->name }}<br>
                                    <small class="text-muted">{{ $task->car->plate_number }}</small>
                                </td>
                                <td>
                                    {{ $task->start_date->format('d M Y') }}<br>
                                    <small class="text-muted">{{ $task->total_days }} hari</small>
                                </td>
                                <td>
                                    @if($task->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('driver.tasks.show', $task->id) }}" 
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
                {{ $tasks->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Belum Ada Riwayat</h5>
                <p class="text-muted">Tugas yang sudah selesai akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
