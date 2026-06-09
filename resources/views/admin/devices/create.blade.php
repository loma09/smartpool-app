@extends('layouts.app')
@section('title', 'Tambah Device')
@section('page-title', 'Tambah Device Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="table-card p-4">
            <form method="POST" action="{{ route('admin.devices.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pemilik (User)</label>
                    <select name="user_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Device ID</label>
                    <input type="text" name="device_id" class="form-control rounded-3"
                        placeholder="e.g. ESP32-001" value="{{ old('device_id') }}" required>
                    <small class="text-muted">ID unik untuk ESP32</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Device</label>
                    <input type="text" name="name" class="form-control rounded-3"
                        placeholder="e.g. Kolam Utama" value="{{ old('name') }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="location" class="form-control rounded-3"
                        placeholder="e.g. Lantai 1" value="{{ old('location') }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bi bi-plus-lg me-1"></i>Simpan Device
                    </button>
                    <a href="{{ route('admin.devices') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection