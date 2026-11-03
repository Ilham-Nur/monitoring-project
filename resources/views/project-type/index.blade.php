@extends('layout.main')

@section('title', 'Tipe Project - '.config('app.name'))
@section('breadcrumb', 'Tipe & Tahapan')

@section('content')
    <div class="page-header">
        <div><h1 class="page-title">Master Tipe Project</h1><p class="page-subtitle">Atur urutan tahapan dan durasi rencana dalam hari kalender.</p></div>
        <button class="btn btn-primary" data-open-modal="type-create-modal"><i class="bi bi-plus-lg"></i> Tambah Tipe Project</button>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-header-left"><h2 class="card-title">Daftar Tipe Project</h2><p class="card-subtitle">Perubahan master hanya digunakan oleh project baru</p></div></div>
        <div class="table-responsive"><table class="data-table"><thead><tr><th>Nama</th><th>Tahapan</th><th>Total Durasi</th><th>Digunakan</th><th>Status</th><th class="th-aksi">Aksi</th></tr></thead><tbody>
        @forelse ($projectTypes as $type)
            <tr>
                <td><strong>{{ $type->name }}</strong><span class="table-secondary">{{ $type->description }}</span></td>
                <td><div class="stage-chip-list">@foreach ($type->stages as $stage)<span class="stage-chip"><b>{{ $stage->sort_order }}</b>{{ $stage->name }} <small>{{ $stage->duration_days }} hari</small></span>@endforeach</div></td>
                <td><strong>{{ $type->stages->sum('duration_days') }} hari</strong></td>
                <td>{{ $type->projects_count }} project</td>
                <td><span class="badge {{ $type->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $type->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><div class="action-group">
                    <button type="button" class="btn-action btn-action-edit" data-edit-type data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-description="{{ $type->description }}" data-active="{{ $type->is_active ? '1' : '0' }}" data-stages='@json($type->stages->map->only(["name", "duration_days"]))' title="Edit"><i class="bi bi-pencil-fill"></i></button>
                    @if ($type->projects_count === 0)<button type="button" class="btn-action btn-action-delete" data-confirm-delete="{{ route('project-types.destroy', $type) }}" data-confirm-message="Hapus tipe project {{ $type->name }} beserta master tahapannya?" title="Hapus"><i class="bi bi-trash3-fill"></i></button>@endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty-state compact"><i class="bi bi-diagram-3"></i><p>Belum ada tipe project.</p></div></td></tr>
        @endforelse
        </tbody></table></div>
    </div>
@endsection

@push('modals')
    <div class="modal-overlay" id="type-create-modal" aria-hidden="true"><div class="modal modal-lg" role="dialog" aria-modal="true"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-diagram-3-fill"></i> Tambah Tipe Project</h3><button class="modal-close" data-close-modal="type-create-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" action="{{ route('project-types.store') }}">@csrf<div class="modal-body">@include('project-type.partials.form', ['prefix' => 'create'])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="type-create-modal">Batal</button><button class="btn btn-primary" type="submit">Simpan Tipe Project</button></div></form></div></div>
    <div class="modal-overlay" id="type-edit-modal" aria-hidden="true"><div class="modal modal-lg" role="dialog" aria-modal="true"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-pencil-fill"></i> Edit Tipe Project</h3><button class="modal-close" data-close-modal="type-edit-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" id="type-edit-form">@csrf @method('PUT')<div class="modal-body">@include('project-type.partials.form', ['prefix' => 'edit'])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="type-edit-modal">Batal</button><button class="btn btn-primary" type="submit">Simpan Perubahan</button></div></form></div></div>
@endpush
