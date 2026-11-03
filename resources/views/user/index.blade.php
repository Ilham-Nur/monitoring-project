@extends('layout.main')

@section('title', 'Pengguna - '.config('app.name'))
@section('breadcrumb', 'Pengguna')

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Manajemen Pengguna</h1><p class="page-subtitle">Kelola akun Admin, PIC project, dan klien.</p></div>
        <button class="btn btn-primary" data-open-modal="user-create-modal"><i class="bi bi-person-plus-fill"></i> Tambah Pengguna</button>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-header-left"><h2 class="card-title">Daftar Pengguna</h2><p class="card-subtitle">Satu akun klien mewakili satu perusahaan</p></div></div>
        <form class="table-toolbar" method="GET">
            <div class="toolbar-filters">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau perusahaan"></div>
                <select class="form-select" name="role"><option value="">Semua peran</option><option value="admin" @selected(request('role') === 'admin')>Administrator</option><option value="pic" @selected(request('role') === 'pic')>PIC Project</option><option value="client" @selected(request('role') === 'client')>Klien</option></select>
                <button class="btn btn-outline btn-sm" type="submit"><i class="bi bi-funnel"></i> Terapkan</button>
                <a class="btn btn-outline btn-sm" href="{{ route('users.index') }}"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
        <div class="table-responsive"><table class="data-table"><thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Perusahaan</th><th>Status</th><th class="th-aksi">Aksi</th></tr></thead><tbody>
        @forelse ($users as $user)
            <tr>
                <td><div class="user-cell"><div class="avatar-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div><strong>{{ $user->name }}</strong></div></td>
                <td>{{ $user->email }}</td>
                <td><span class="role-badge role-{{ $user->role }}">{{ $user->roleLabel() }}</span></td>
                <td>{{ $user->company_name ?? '—' }}</td>
                <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }}"><i class="bi bi-circle-fill"></i>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><div class="action-group">
                    <button type="button" class="btn-action btn-action-edit" data-edit-user data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-role="{{ $user->role }}" data-company="{{ $user->company_name }}" data-active="{{ $user->is_active ? '1' : '0' }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                    @if (auth()->id() !== $user->id)<button type="button" class="btn-action btn-action-delete" data-confirm-delete="{{ route('users.destroy', $user) }}" data-confirm-message="Hapus pengguna {{ $user->name }} secara permanen?" title="Hapus"><i class="bi bi-trash3-fill"></i></button>@endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty-state compact"><i class="bi bi-people"></i><p>Pengguna tidak ditemukan.</p></div></td></tr>
        @endforelse
        </tbody></table></div>
        <div class="table-footer"><span class="table-info">{{ $users->total() }} pengguna</span>{{ $users->links() }}</div>
    </div>
@endsection

@push('modals')
    <div class="modal-overlay" id="user-create-modal" aria-hidden="true"><div class="modal modal-md" role="dialog" aria-modal="true"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-person-plus-fill"></i> Tambah Pengguna</h3><button class="modal-close" data-close-modal="user-create-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" action="{{ route('users.store') }}">@csrf<div class="modal-body">@include('user.partials.form', ['prefix' => 'create'])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="user-create-modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div></form></div></div>
    <div class="modal-overlay" id="user-edit-modal" aria-hidden="true"><div class="modal modal-md" role="dialog" aria-modal="true"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-pencil-fill"></i> Edit Pengguna</h3><button class="modal-close" data-close-modal="user-edit-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" id="user-edit-form">@csrf @method('PUT')<div class="modal-body">@include('user.partials.form', ['prefix' => 'edit', 'editing' => true])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="user-edit-modal">Batal</button><button class="btn btn-primary" type="submit">Simpan Perubahan</button></div></form></div></div>
@endpush
