@extends('layouts.app')
@section('title', 'Log Hujan')
@section('page-title', 'Riwayat Deteksi Hujan')

@section('content')
<div class="table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-cloud-rain me-2 text-primary"></i>Semua Log Hujan</h6>
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="device_id" class="form-select form-select-sm rounded-3" style="width:auto">
                <option value="">Semua Device</option>
                @foreach($devices as $d)
                    <option value="{{ $d->id }}" {{ request('device_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->device_id }})
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" class="form-control form-control-sm rounded-3"
                value="{{ request('date') }}" style="width:auto">
            <button class="btn btn-sm btn-primary rounded-3">Filter</button>
            @if(request('date') || request('device_id'))
                <a href="{{ route('admin.rain-logs') }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
            @endif
            <a href="{{ route('admin.rain-logs.export', request()->only('date', 'device_id')) }}"
               class="btn btn-sm btn-success rounded-3">
                <i class="bi bi-download me-1"></i>Download CSV
            </a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Device</th>
                    <th>Nilai ADC</th>
                    <th>Penutup Otomatis</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small class="text-muted">{{ $log->id }}</small></td>
                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                    <td><span class="badge bg-dark rounded-pill">{{ $log->device->device_id ?? '—' }}</span></td>
                    <td><span class="badge bg-primary rounded-pill">{{ $log->rain_value }}</span></td>
                    <td>
                        <span class="badge {{ $log->cover_closed ? 'bg-success' : 'bg-secondary' }}">
                            {{ $log->cover_closed ? 'Menutup' : 'Tidak' }}
                        </span>
                    </td>
                    <td><small class="text-muted">{{ $log->notes ?? '—' }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada log hujan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="p-3 border-top">{{ $logs->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
