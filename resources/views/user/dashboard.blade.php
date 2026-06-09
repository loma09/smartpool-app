@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Kolam')

@section('content')

{{-- Pilih Device --}}
@if($devices->count() > 0)
<div class="d-flex align-items-center gap-3 mb-4">
    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="fw-semibold text-muted small">Device:</label>
        <select name="device_id" class="form-select form-select-sm rounded-3" style="width:auto" onchange="this.form.submit()">
            @foreach($devices as $d)
                <option value="{{ $d->id }}" {{ isset($device) && $device->id == $d->id ? 'selected' : '' }}>
                    {{ $d->name }} ({{ $d->device_id }})
                </option>
            @endforeach
        </select>
    </form>
    @if(isset($device))
        <small class="text-muted">{{ $device->location ?? '' }}</small>
        {{-- Indikator auto-refresh --}}
        <small class="text-muted ms-auto d-flex align-items-center gap-1">
            <span class="status-dot online" id="poll-dot"></span>
            <span id="poll-status">Live</span>
        </small>
    @endif
</div>
@endif

{{-- Tidak ada device --}}
@if($devices->count() == 0)
<div class="table-card p-5 text-center">
    <i class="bi bi-cpu display-4 text-muted d-block mb-3"></i>
    <h5 class="fw-bold">Belum Ada Device</h5>
    <p class="text-muted">Hubungi admin untuk menambahkan device ke akun kamu.</p>
</div>

@else

<div class="row g-3 mb-4">
    {{-- ESP32 Status --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="icon-box" style="background:#e0f2fe">
                    <i class="bi bi-cpu" style="color:#0284c7"></i>
                </div>
                <span class="badge rounded-pill" id="esp32-badge">
                    {{ $esp32Online ? 'Online' : 'Offline' }}
                </span>
            </div>
            <div class="value">ESP32</div>
            <div class="label">
                <span class="status-dot" id="esp32-dot"></span>
                <span id="esp32-label">{{ $esp32Online ? 'Perangkat terhubung' : 'Tidak ada koneksi' }}</span>
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
                    <span class="badge rounded-pill" id="turbidity-badge">{{ $latest->turbidity_label }}</span>
                @endif
            </div>
            <div class="value" id="turbidity-value">{{ $latest ? number_format($latest->turbidity_value, 1) : '—' }} <small class="fs-6 fw-normal text-muted">NTU</small></div>
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
                    <span class="badge rounded-pill" id="rain-badge">{{ $latest->rain_detected ? 'Hujan' : 'Cerah' }}</span>
                @endif
            </div>
            <div class="value" id="rain-value">{{ $latest ? $latest->rain_value : '—' }}</div>
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
                    <div class="value" style="font-size:1.2rem" id="stat-rain">{{ $stats['rain_count'] }}</div>
                    <div class="label" style="font-size:.7rem">Hujan (24j)</div>
                </div>
                <div class="col-6">
                    <div class="value" style="font-size:1.2rem" id="stat-chlor">{{ $stats['chlorine_count'] }}</div>
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
                <h6 class="mb-0"><i class="bi bi-cloud-rain me-2 text-primary"></i>Log Hujan Terbaru</h6>
                <a href="{{ route('user.rain-logs', ['device_id' => $device->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
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
                    <tbody id="rain-logs-tbody">
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
                <h6 class="mb-0"><i class="bi bi-droplet-half me-2 text-warning"></i>Log Kaporit Terbaru</h6>
                <a href="{{ route('user.chlorine-logs', ['device_id' => $device->id]) }}" class="btn btn-sm btn-outline-warning rounded-pill">Lihat Semua</a>
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
                    <tbody id="chlor-logs-tbody">
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

@endif

@endsection

@push('scripts')
<script>
@isset($device)
const POLL_URL    = "{{ route('user.dashboard.poll') }}";
const DEVICE_ID   = "{{ $device->id }}";
const POLL_INTERVAL = 5000; // 5 detik

function updateEsp32(online) {
    const badge = document.getElementById('esp32-badge');
    const dot   = document.getElementById('esp32-dot');
    const label = document.getElementById('esp32-label');
    if (!badge) return;
    badge.textContent = online ? 'Online' : 'Offline';
    badge.className   = `badge rounded-pill ${online ? 'bg-success' : 'bg-danger'}`;
    dot.className     = `status-dot ${online ? 'online' : 'offline'}`;
    label.textContent = online ? 'Perangkat terhubung' : 'Tidak ada koneksi';
}

function updateSensorCards(latest) {
    if (!latest) return;

    // Turbidity
    document.getElementById('turbidity-value').innerHTML =
        `${latest.turbidity_value} <small class="fs-6 fw-normal text-muted">NTU</small>`;
    const tBadge = document.getElementById('turbidity-badge');
    if (tBadge) {
        tBadge.textContent = latest.turbidity_label;
        tBadge.className   = `badge rounded-pill bg-${latest.turbidity_color}`;
    }

    // Rain
    document.getElementById('rain-value').textContent = latest.rain_value;
    const rBadge = document.getElementById('rain-badge');
    if (rBadge) {
        rBadge.textContent = latest.rain_detected ? 'Hujan' : 'Cerah';
        rBadge.className   = `badge rounded-pill ${latest.rain_detected ? 'bg-primary' : 'bg-secondary'}`;
    }
}

function updateStats(stats) {
    document.getElementById('stat-rain').textContent  = stats.rain_count;
    document.getElementById('stat-chlor').textContent = stats.chlorine_count;
}

function updateRainLogs(logs) {
    const tbody = document.getElementById('rain-logs-tbody');
    if (!tbody) return;
    if (logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Belum ada data hujan</td></tr>';
        return;
    }
    tbody.innerHTML = logs.map(l => `
        <tr>
            <td><small>${l.time}</small></td>
            <td><span class="badge bg-primary rounded-pill">${l.rain_value}</span></td>
            <td><span class="badge ${l.cover_closed ? 'bg-success' : 'bg-secondary'}">${l.cover_closed ? 'Menutup' : '—'}</span></td>
        </tr>
    `).join('');
}

function updateChlorLogs(logs) {
    const tbody = document.getElementById('chlor-logs-tbody');
    if (!tbody) return;
    if (logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada data kaporit</td></tr>';
        return;
    }
    tbody.innerHTML = logs.map(l => `
        <tr>
            <td><small>${l.time}</small></td>
            <td><small>${l.turbidity_value} NTU</small></td>
            <td><small>${l.chlorine_amount_ml} ml</small></td>
            <td><span class="badge ${l.chlorine_added ? 'bg-success' : 'bg-danger'}">${l.chlorine_added ? 'Ditambahkan' : 'Gagal'}</span></td>
        </tr>
    `).join('');
}

function setPollStatus(ok) {
    const dot    = document.getElementById('poll-dot');
    const status = document.getElementById('poll-status');
    if (!dot) return;
    dot.className     = `status-dot ${ok ? 'online' : 'offline'}`;
    status.textContent = ok ? 'Live' : 'Reconnecting...';
}

async function poll() {
    try {
        const res  = await fetch(`${POLL_URL}?device_id=${DEVICE_ID}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            updateEsp32(data.esp32_online);
            updateSensorCards(data.latest);
            updateStats(data.stats);
            updateRainLogs(data.rain_logs);
            updateChlorLogs(data.chlor_logs);
            setPollStatus(true);
        }
    } catch (e) {
        setPollStatus(false);
    }
}

// Mulai polling
setInterval(poll, POLL_INTERVAL);
@endisset
</script>
@endpush