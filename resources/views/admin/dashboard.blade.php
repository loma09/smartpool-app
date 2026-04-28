@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#e0f2fe">
                <i class="bi bi-people" style="color:#0284c7"></i>
            </div>
            <div class="value">{{ $stats['total_users'] }}</div>
            <div class="label">Total Pengguna</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#ede9fe">
                <i class="bi bi-cloud-rain" style="color:#7c3aed"></i>
            </div>
            <div class="value">{{ $stats['rain_today'] }}</div>
            <div class="label">Deteksi Hujan Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#fef3c7">
                <i class="bi bi-droplet-half" style="color:#d97706"></i>
            </div>
            <div class="value">{{ $stats['chlorine_today'] }}</div>
            <div class="label">Kaporit Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#dcfce7">
                <i class="bi bi-activity" style="color:#16a34a"></i>
            </div>
            <div class="value">{{ $stats['avg_turbidity'] ? number_format($stats['avg_turbidity'], 1) : '—' }}</div>
            <div class="label">Rata-rata NTU (24j)</div>
        </div>
    </div>
</div>

{{-- Status terkini --}}
@if($latest)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="table-card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-broadcast me-2 text-success"></i>Status Sensor Terkini</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded-3 bg-light text-center">
                        <div class="small text-muted">Device</div>
                        <strong>{{ $latest->device_id }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 text-center bg-{{ $latest->turbidity_color }} bg-opacity-10">
                        <div class="small text-muted">Kekeruhan</div>
                        <strong>{{ number_format($latest->turbidity_value, 1) }} NTU</strong>
                        <div><span class="badge bg-{{ $latest->turbidity_color }} mt-1">{{ $latest->turbidity_label }}</span></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 text-center {{ $latest->rain_detected ? 'bg-primary bg-opacity-10' : 'bg-light' }}">
                        <div class="small text-muted">Hujan</div>
                        <strong>{{ $latest->rain_value }} ADC</strong>
                        <div>
                            <span class="badge {{ $latest->rain_detected ? 'bg-primary' : 'bg-secondary' }} mt-1">
                                {{ $latest->rain_detected ? 'Hujan' : 'Tidak Hujan' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 bg-light text-center">
                        <div class="small text-muted">Update Terakhir</div>
                        <strong>{{ $latest->created_at->diffForHumans() }}</strong>
                        <div class="small text-muted">{{ $latest->created_at->format('H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabel pembacaan terbaru --}}
<div class="table-card">
    <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i>10 Pembacaan Sensor Terakhir</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Device</th>
                    <th>Turbidity (NTU)</th>
                    <th>Status Air</th>
                    <th>Rain ADC</th>
                    <th>Hujan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentReadings as $r)
                <tr>
                    <td><small>{{ $r->created_at->format('d/m H:i:s') }}</small></td>
                    <td><span class="badge bg-dark rounded-pill">{{ $r->device_id }}</span></td>
                    <td>{{ number_format($r->turbidity_value, 1) }}</td>
                    <td><span class="badge bg-{{ $r->turbidity_color }}">{{ $r->turbidity_label }}</span></td>
                    <td>{{ $r->rain_value }}</td>
                    <td>
                        @if($r->rain_detected)
                            <i class="bi bi-cloud-rain-fill text-primary"></i>
                        @else
                            <i class="bi bi-sun text-warning"></i>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>setTimeout(() => location.reload(), 30000);</script>
@endpush
