@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil & Keamanan')

@section('content')
<div class="row g-3">

    {{-- Edit Profil --}}
    <div class="col-12 col-lg-6">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Edit Profil</h6>
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control rounded-3"
                        value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" class="form-control rounded-3 bg-light"
                        value="{{ $user->email }}" disabled>
                    <small class="text-muted">Email tidak dapat diubah.</small>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control rounded-3"
                        value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>
                <button type="submit" class="btn btn-primary rounded-3">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    {{-- Update Password --}}
    <div class="col-12 col-lg-6">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-lock me-2 text-danger"></i>Ubah Password</h6>
            <form method="POST" action="{{ route('user.password.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-control rounded-3" required>
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control rounded-3" required minlength="8">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control rounded-3" required minlength="8">
                </div>
                <button type="submit" class="btn btn-danger rounded-3">
                    <i class="bi bi-shield-lock me-2"></i>Perbarui Password
                </button>
            </form>
        </div>

        {{-- Info Akun --}}
        <div class="table-card p-4 mt-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-secondary"></i>Info Akun</h6>
            <table class="table table-sm mb-0">
                <tr>
                    <td class="text-muted small">Role</td>
                    <td><span class="badge bg-primary">{{ ucfirst($user->role) }}</span></td>
                </tr>
                <tr>
                    <td class="text-muted small">Bergabung</td>
                    <td><small>{{ $user->created_at->format('d M Y') }}</small></td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
