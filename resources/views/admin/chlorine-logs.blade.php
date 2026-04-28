@extends('layouts.app')
@section('title', 'Log Kaporit')
@section('page-title', 'Log Riwayat Kaporit (Admin)')

@section('content')
<div class="table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-droplet-half me-2 text-warning"></i>Semua Log Kaporit</h6>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm rounded-3"
                value="{{ request('date') }}" style="width:auto">
            <button class="btn btn-sm btn-warning rounded-3">Filter</button>
            @if(request('date'))
                <a href="{{ request()->url() }}" class="btn btn-sm btn-outline-secondary rounded-3">Reset</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>#</th><th>Waktu</th><th>Device</th><th>NTU</th><th>Status</th><th>Kaporit (ml)</th><th>Hasil</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small class="text-muted">{{ $log->id }}</small></td>
                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                    <td><span class="badge bg-dark rounded-pill">{{ $log->device_id }}</span></td>
                    <td>{{ number_format($log->turbidity_value, 1) }}</td>
                    <td>
                        <span class="badge {{ $log->turbidity_status === 'sangat_keruh' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $log->turbidity_status === 'sangat_keruh' ? 'Sangat Keruh' : 'Keruh' }}
                        </span>
                    </td>
                    <td>{{ $log->chlorine_amount_ml }}</td>
                    <td>
                        <span class="badge {{ $log->chlorine_added ? 'bg-success' : 'bg-danger' }}">
                            {{ $log->chlorine_added ? 'Berhasil' : 'Gagal' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data kaporit</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="p-3 border-top">{{ $logs->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
