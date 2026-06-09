@extends('layouts.app')
@section('title', 'Edit Device')
@section('page-title', 'Edit Device')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">

        {{-- Form Edit Device --}}
        <div class="table-card p-4 mb-4">
            <form method="POST" action="{{ route('admin.devices.update', $device) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pemilik (User)</label>
                    <select name="user_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $device->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Device ID</label>
                    <input type="text" name="device_id" class="form-control rounded-3"
                        value="{{ old('device_id', $device->device_id) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Device</label>
                    <input type="text" name="name" class="form-control rounded-3"
                        value="{{ old('name', $device->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="location" class="form-control rounded-3"
                        value="{{ old('location', $device->location) }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="is_active" class="form-select rounded-3">
                        <option value="1" {{ $device->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$device->is_active ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.devices') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>

        {{-- API Key Section --}}
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-key me-2 text-warning"></i>API Key ESP32</h6>

            @if($device->apiKey)
                <div class="mb-3">
                    <label class="form-label fw-semibold small">API Key Aktif</label>
                    <div class="input-group">
                        <input type="text" id="apiKeyInput" class="form-control rounded-start-3 font-monospace"
                            value="{{ $device->apiKey->api_key }}" readonly>
                        <button class="btn btn-outline-secondary rounded-end-3" onclick="copyApiKey()" title="Copy">
                            <i class="bi bi-clipboard" id="copyIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">
                        Terakhir digunakan: {{ $device->apiKey->last_used_at ? $device->apiKey->last_used_at->diffForHumans() : 'Belum pernah' }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.devices.generate-key', $device) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning rounded-3"
                            onclick="return confirm('Generate API key baru? Key lama akan tidak berlaku.')">
                            <i class="bi bi-arrow-clockwise me-1"></i>Generate Ulang
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.devices.delete-key', $device) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger rounded-3"
                            onclick="return confirm('Hapus API key ini?')">
                            <i class="bi bi-trash me-1"></i>Hapus Key
                        </button>
                    </form>
                </div>
            @else
                <p class="text-muted small">Belum ada API key untuk device ini.</p>
                <form method="POST" action="{{ route('admin.devices.generate-key', $device) }}">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-3">
                        <i class="bi bi-plus-lg me-1"></i>Generate API Key
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
function copyApiKey() {
    const input = document.getElementById('apiKeyInput');
    navigator.clipboard.writeText(input.value);
    const icon = document.getElementById('copyIcon');
    icon.className = 'bi bi-check text-success';
    setTimeout(() => icon.className = 'bi bi-clipboard', 2000);
}
</script>
@endpush

@endsection