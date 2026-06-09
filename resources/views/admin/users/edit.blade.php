@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Akun Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-4"><i class="bi bi-pencil me-2 text-primary"></i>Edit: {{ $user->name }}</h6>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control rounded-3"
                        value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>
                <hr>
                <p class="small text-muted">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control rounded-3" minlength="8">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="8">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bi bi-save me-2"></i>Perbarui
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
