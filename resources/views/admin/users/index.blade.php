@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<!-- Filter & Add Button -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Cari nama, email, atau telepon..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="role">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="driver" {{ request('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-people"></i> Daftar User</h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Role</th>
                            <th>Verifikasi</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>#{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->driver)
                                        <br><small class="text-muted">SIM: {{ $user->driver->license_number }}</small>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'driver')
                                        <span class="badge bg-info">Driver</span>
                                    @else
                                        <span class="badge bg-primary">Customer</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role === 'customer')
                                        @if($user->verification_status === 'verified')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terverifikasi</span>
                                        @elseif($user->verification_status === 'pending')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Belum Terverifikasi</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-info"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" 
                                                  action="{{ route('admin.users.destroy', $user->id) }}" 
                                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Belum Ada User</h5>
                <p class="text-muted mb-4">User akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
