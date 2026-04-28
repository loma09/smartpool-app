@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Kolam')

@section('content')
<div class="row g-3 mb-4">

    {{-- ESP32 Status --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="icon-box" style="background:#e0f2fe">
                    <i class="bi bi-cpu" style="color:#0284c7"></i>
                </div>
                <span class="badge {{ $esp32Online ? 'bg-success' : 'bg-danger' }} rounded-pill">
                    {{ $esp32Online ? 'Online' : 'Offline' }}
                </span>
            </div>
            <div class="value">ESP32</div>
            <div class="label">
                <span class="status-dot {{ $esp32Online ? 'online' : 'offline' }}"></span>
                {{ $esp32Online ? 'Perangkat terhubung' : 'Tidak ada koneksi' }}
            </div>
        </div>
    </div>

    {{-- Turbidity --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="icon-box" style="background:#fef3c7">
                    <i class="bi bi-droplet-half" style="color:#d97706"></i>
                </div>
                @if($latest)
                    <span class="badge bg-{{ $latest->turbidity_color }} rounded-pill">
                        {{ $latest->turbidity_label }}
                    </span>
                @endif
            </div>
            <div class="value">{{ $latest ? number_format($latest->turbidity_value, 1) : '—' }} <small class="fs-6 fw-normal text-muted">NTU</small></div>
            <div class="label">Kekeruhan air kolam</div>
        </div>
    </div>

    {{-- Sensor Hujan --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="icon-box" style="background:#ede9fe">
                    <i class="bi bi-cloud-rain" style="color:#7c3aed"></i>
                </div>
                @if($latest)
                    <span class="badge {{ $latest->rain_detected ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                        {{ $latest->rain_detected ? 'Hujan' : 'Cerah' }}
                    </span>
                @endif
            </div>
            <div class="value">{{ $latest ? $latest->rain_value : '—' }}</div>
            <div class="label">Nilai ADC sensor hujan</div>
        </div>
    </div>

    {{-- Statistik hari ini --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="icon-box mb-2" style="background:#dcfce7">
                <i class="bi bi-bar-chart" style="color:#16a34a"></i>
            </div>
            <div class="row g-0 text-center mt-1">
                <div class="col-6 border-end">
                    <div class="value" style="font-size:1.2rem">{{ $stats['rain_count'] }}</div>
                    <div class="label" style="font-size:.7rem">Hujan (24j)</div>
                </div>
                <div class="col-6">
                    <div class="value" style="font-size:1.2rem">{{ $stats['chlorine_count'] }}</div>
                    <div class="label" style="font-size:.7rem">Kaporit (24j)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Log Hujan Terbaru --}}
    <div class="col-12 col-lg-6">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-700"><i class="bi bi-cloud-rain me-2 text-primary"></i>Log Hujan Terbaru</h6>
                <a href="{{ route('user.rain-logs') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Nilai ADC</th>
                            <th>Penutup</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rainLogs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('d/m H:i') }}</small></td>
                            <td><span class="badge bg-primary rounded-pill">{{ $log->rain_value }}</span></td>
                            <td>
                                @if($log->cover_closed)
                                    <span class="badge bg-success">Menutup</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data hujan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Log Kaporit Terbaru --}}
    <div class="col-12 col-lg-6">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-700"><i class="bi bi-droplet-half me-2 text-warning"></i>Log Kaporit Terbaru</h6>
                <a href="{{ route('user.chlorine-logs') }}" class="btn btn-sm btn-outline-warning rounded-pill">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kekeruhan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chlorLogs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('d/m H:i') }}</small></td>
                            <td><small>{{ number_format($log->turbidity_value,1) }} NTU</small></td>
                            <td><small>{{ $log->chlorine_amount_ml }} ml</small></td>
                            <td>
                                @if($log->chlorine_added)
                                    <span class="badge bg-success">Ditambahkan</span>
                                @else
                                    <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data kaporit</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh data setiap 30 detik
    setTimeout(() => location.reload(), 30000);
</script>
@endpush
