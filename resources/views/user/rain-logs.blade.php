@extends('layouts.app')
@section('title', 'Log Hujan')
@section('page-title', 'Riwayat Deteksi Hujan')

@section('content')
<div class="table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-cloud-rain me-2 text-primary"></i>Log Hujan</h6>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm rounded-3"
                value="{{ request('date') }}" style="width:auto">
            <button class="btn btn-sm btn-primary rounded-3">Filter</button>
            @if(request('date'))
                <a href="{{ request()->url() }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
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
                    <td><span class="badge bg-dark rounded-pill">{{ $log->device_id }}</span></td>
                    <td><span class="badge bg-primary rounded-pill">{{ $log->rain_value }}</span></td>
                    <td>
                        @if($log->cover_closed)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Menutup</span>
                        @else
                            <span class="badge bg-secondary">Tidak</span>
                        @endif
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
        <div class="p-3 border-top">
            {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
