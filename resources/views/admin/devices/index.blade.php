@extends('layouts.app')
@section('title', 'Kelola Device')
@section('page-title', 'Kelola Device')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.devices.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i>Tambah Device
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Device ID</th>
                    <th>Nama</th>
                    <th>Lokasi</th>
                    <th>Pemilik</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                <tr>
                    <td><small class="text-muted">{{ $loop->iteration }}</small></td>
                    <td><span class="badge bg-dark rounded-pill">{{ $device->device_id }}</span></td>
                    <td><strong>{{ $device->name }}</strong></td>
                    <td><small class="text-muted">{{ $device->location ?? '—' }}</small></td>
                    <td>{{ $device->user->name ?? '—' }}</td>
                    <td>
                        @if($device->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="d-inline"
                            onsubmit="return confirm('Hapus device ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-3">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-cpu display-6 d-block mb-2"></i>Belum ada device
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($devices->hasPages())
        <div class="p-3 border-top">{{ $devices->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection