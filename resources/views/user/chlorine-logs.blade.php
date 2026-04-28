@extends('layouts.app')
@section('title', 'Log Kaporit')
@section('page-title', 'Riwayat Penambahan Kaporit')

@section('content')
<div class="table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-droplet-half me-2 text-warning"></i>Log Kaporit</h6>
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
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Device</th>
                    <th>Kekeruhan (NTU)</th>
                    <th>Status Air</th>
                    <th>Kaporit (ml)</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small class="text-muted">{{ $log->id }}</small></td>
                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                    <td><span class="badge bg-dark rounded-pill">{{ $log->device_id }}</span></td>
                    <td><strong>{{ number_format($log->turbidity_value, 1) }}</strong></td>
                    <td>
                        @if($log->turbidity_status === 'sangat_keruh')
                            <span class="badge bg-danger">Sangat Keruh</span>
                        @else
                            <span class="badge bg-warning text-dark">Keruh</span>
                        @endif
                    </td>
                    <td>{{ $log->chlorine_amount_ml }} ml</td>
                    <td>
                        @if($log->chlorine_added)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil</span>
                        @else
                            <span class="badge bg-danger">Gagal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>Tidak ada log kaporit
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
