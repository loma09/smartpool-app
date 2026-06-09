@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Akun Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-4"><i class="bi bi-person-plus me-2 text-primary"></i>Form Pengguna Baru</h6>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control rounded-3"
                        value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror"
                        required minlength="8">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control rounded-3" required minlength="8">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
