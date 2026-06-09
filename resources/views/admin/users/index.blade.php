@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Akun Pengguna')

@section('content')
<div class="table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Daftar Pengguna</h6>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm rounded-3">
            <i class="bi bi-plus-circle me-1"></i>Tambah Pengguna
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
<<<<<<< HEAD
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Bergabung</th><th>Aksi</th></tr>
=======
                <tr><th>#</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Bergabung</th><th>Aksi</th></tr>
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
<<<<<<< HEAD
                    <td><small class="text-muted">{{ $loop->iteration }}</small></td>
=======
                    <td><small class="text-muted">{{ $user->id }}</small></td>
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
                    <td>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </td>
                    <td><small>{{ $user->email }}</small></td>
                    <td><small>{{ $user->phone ?? '—' }}</small></td>
                    <td><small>{{ $user->created_at->format('d M Y') }}</small></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="btn btn-sm btn-outline-primary rounded-3">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="p-3 border-top">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
