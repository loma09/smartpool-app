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
            <div class="value" id="admin-rain-today">{{ $stats['rain_today'] }}</div>
            <div class="label">Deteksi Hujan Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#fef3c7">
                <i class="bi bi-droplet-half" style="color:#d97706"></i>
            </div>
            <div class="value" id="admin-chlor-today">{{ $stats['chlorine_today'] }}</div>
            <div class="label">Kaporit Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box mb-2" style="background:#dcfce7">
                <i class="bi bi-activity" style="color:#16a34a"></i>
            </div>
            <div class="value" id="admin-avg-ntu">{{ $stats['avg_turbidity'] ? number_format($stats['avg_turbidity'], 1) : '—' }}</div>
            <div class="label">Rata-rata NTU (24j)</div>
        </div>
    </div>
</div>

{{-- Status terkini --}}
@if($latest)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="table-card p-3">
            <h6 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-broadcast me-2 text-success"></i>Status Sensor Terkini</span>
                <small class="text-muted fw-normal d-flex align-items-center gap-1">
                    <span class="status-dot online" id="admin-poll-dot"></span>
                    <span id="admin-poll-status">Live</span>
                </small>
            </h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded-3 bg-light text-center">
                        <div class="small text-muted">Device</div>
                        <strong id="admin-device">{{ $latest->device->device_id ?? '—' }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 text-center" id="admin-turbidity-card">
                        <div class="small text-muted">Kekeruhan</div>
                        <strong id="admin-turbidity-value">{{ number_format($latest->turbidity_value, 1) }} NTU</strong>
                        <div><span class="badge mt-1" id="admin-turbidity-badge">{{ $latest->turbidity_label }}</span></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 text-center" id="admin-rain-card">
                        <div class="small text-muted">Hujan</div>
                        <strong id="admin-rain-value">{{ $latest->rain_value }} ADC</strong>
                        <div>
                            <span class="badge mt-1" id="admin-rain-badge">
                                {{ $latest->rain_detected ? 'Hujan' : 'Tidak Hujan' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded-3 bg-light text-center">
                        <div class="small text-muted">Update Terakhir</div>
                        <strong id="admin-updated-at">{{ $latest->created_at->diffForHumans() }}</strong>
                        <div class="small text-muted" id="admin-updated-time">{{ $latest->created_at->format('H:i:s') }}</div>
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
            <tbody id="admin-readings-tbody">
                @foreach($recentReadings as $r)
                <tr>
                    <td><small>{{ $r->created_at->format('d/m H:i:s') }}</small></td>
                    <td><span class="badge bg-dark rounded-pill">{{ $r->device->device_id ?? '—' }}</span></td>
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
<script>
const ADMIN_POLL_URL      = "{{ route('admin.dashboard.poll') }}";
const ADMIN_POLL_INTERVAL = 5000; // 5 detik

function setPollStatus(ok) {
    const dot    = document.getElementById('admin-poll-dot');
    const status = document.getElementById('admin-poll-status');
    if (!dot) return;
    dot.className      = `status-dot ${ok ? 'online' : 'offline'}`;
    status.textContent = ok ? 'Live' : 'Reconnecting...';
}

async function adminPoll() {
    try {
        const res  = await fetch(ADMIN_POLL_URL, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (!data.success) return;

        // Stats
        document.getElementById('admin-rain-today').textContent  = data.stats.rain_today;
        document.getElementById('admin-chlor-today').textContent = data.stats.chlorine_today;
        document.getElementById('admin-avg-ntu').textContent     = data.stats.avg_turbidity ?? '—';

        // Status sensor terkini
        if (data.latest) {
            document.getElementById('admin-turbidity-value').textContent = data.latest.turbidity_value + ' NTU';
            document.getElementById('admin-rain-value').textContent      = data.latest.rain_value + ' ADC';
            document.getElementById('admin-updated-at').textContent      = data.latest.updated_at;
            document.getElementById('admin-updated-time').textContent    = data.latest.updated_time;

            const tBadge = document.getElementById('admin-turbidity-badge');
            tBadge.textContent = data.latest.turbidity_label;
            tBadge.className   = `badge bg-${data.latest.turbidity_color} mt-1`;

            const rBadge = document.getElementById('admin-rain-badge');
            rBadge.textContent = data.latest.rain_detected ? 'Hujan' : 'Tidak Hujan';
            rBadge.className   = `badge mt-1 ${data.latest.rain_detected ? 'bg-primary' : 'bg-secondary'}`;

            const rainCard = document.getElementById('admin-rain-card');
            rainCard.className = `p-3 rounded-3 text-center ${data.latest.rain_detected ? 'bg-primary bg-opacity-10' : 'bg-light'}`;
        }

        // Tabel 10 pembacaan terbaru
        if (data.recent_readings) {
            const tbody = document.getElementById('admin-readings-tbody');
            tbody.innerHTML = data.recent_readings.map(r => `
                <tr>
                    <td><small>${r.time}</small></td>
                    <td><span class="badge bg-dark rounded-pill">${r.device_id}</span></td>
                    <td>${r.turbidity_value}</td>
                    <td><span class="badge bg-${r.turbidity_color}">${r.turbidity_label}</span></td>
                    <td>${r.rain_value}</td>
                    <td>${r.rain_detected
                        ? '<i class="bi bi-cloud-rain-fill text-primary"></i>'
                        : '<i class="bi bi-sun text-warning"></i>'}</td>
                </tr>
            `).join('');
        }

        setPollStatus(true);
    } catch (e) {
        setPollStatus(false);
    }
}

setInterval(adminPoll, ADMIN_POLL_INTERVAL);
</script>
@endpush