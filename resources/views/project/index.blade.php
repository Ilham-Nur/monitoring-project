@extends('layout.main')
@section('title', 'Project - '.config('app.name'))
@section('breadcrumb', 'Project')
@section('content')
<div class="page-header"><div><h1 class="page-title">Daftar Project</h1><p class="page-subtitle">Pantau jadwal, progres tahapan, dan dokumen project.</p></div>@if(auth()->user()->isAdmin())<button class="btn btn-primary" data-open-modal="project-create-modal"><i class="bi bi-plus-lg"></i> Tambah Project</button>@endif</div>
<div class="card">
    <div class="card-header"><div class="card-header-left"><h2 class="card-title">Project</h2><p class="card-subtitle">{{ $projects->total() }} project dapat Anda akses</p></div></div>
    <form class="table-toolbar" method="GET"><div class="toolbar-filters"><div class="search-field"><i class="bi bi-search"></i><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, atau klien"></div><select class="form-select" name="status"><option value="">Semua status</option><option value="active" @selected(request('status') === 'active')>Berjalan</option><option value="completed" @selected(request('status') === 'completed')>Selesai</option></select><button class="btn btn-outline btn-sm" type="submit"><i class="bi bi-funnel"></i> Terapkan</button><a class="btn btn-outline btn-sm" href="{{ route('projects.index') }}"><i class="bi bi-arrow-counterclockwise"></i> Reset</a></div></form>
    <div class="table-responsive"><table class="data-table"><thead><tr><th>Project</th><th>Klien</th><th>PIC</th><th>Periode</th><th>Progres</th><th>Status</th><th class="th-aksi">Aksi</th></tr></thead><tbody>
    @forelse($projects as $project)
        <tr>
            <td><strong>{{ $project->name }}</strong><span class="table-secondary mono">{{ $project->code }} · {{ $project->type->name }}</span></td>
            <td><strong>{{ $project->client->company_name }}</strong><span class="table-secondary">{{ $project->client->name }}</span></td>
            <td><div class="avatar-stack">@foreach($project->pics->take(3) as $pic)<span class="avatar-xs" title="{{ $pic->name }}">{{ strtoupper(substr($pic->name,0,1)) }}</span>@endforeach</div><span class="table-secondary">{{ $project->pics->pluck('name')->join(', ') }}</span></td>
            <td>{{ $project->start_date->translatedFormat('d M Y') }}<span class="table-secondary">s.d. {{ $project->end_date->translatedFormat('d M Y') }}</span></td>
            <td><div class="progress-inline"><div class="progress-track"><span style="width:{{ $project->progressPercentage() }}%"></span></div><strong>{{ $project->progressPercentage() }}%</strong></div><span class="table-secondary">{{ $project->completed_stages_count }}/{{ $project->stages_count }} tahapan</span></td>
            <td><span class="badge {{ $project->status==='completed'?'badge-success':'badge-info' }}">{{ $project->statusLabel() }}</span></td>
            <td><div class="action-group"><a class="btn-action btn-action-view" href="{{ route('projects.show',$project) }}" title="Detail"><i class="bi bi-eye-fill"></i></a>@if(auth()->user()->isAdmin())<button type="button" class="btn-action btn-action-edit" data-edit-project data-id="{{ $project->id }}" data-code="{{ $project->code }}" data-name="{{ $project->name }}" data-client="{{ $project->client_id }}" data-description="{{ $project->description }}" data-start="{{ $project->start_date->toDateString() }}" data-pics='@json($project->pics->pluck("id"))' title="Edit"><i class="bi bi-pencil-fill"></i></button><button type="button" class="btn-action btn-action-delete" data-confirm-delete="{{ route('projects.destroy',$project) }}" data-confirm-message="Hapus project {{ $project->name }}? Semua tahapan, dokumen, komentar, dan riwayat akan dihapus permanen." title="Hapus"><i class="bi bi-trash3-fill"></i></button>@endif</div></td>
        </tr>
    @empty<tr><td colspan="7"><div class="empty-state compact"><i class="bi bi-kanban"></i><p>Project tidak ditemukan.</p></div></td></tr>@endforelse
    </tbody></table></div>
    <div class="table-footer"><span class="table-info">{{ $projects->total() }} project</span>{{ $projects->links() }}</div>
</div>
@endsection
@if(auth()->user()->isAdmin())
@push('modals')
<div class="modal-overlay" id="project-create-modal" aria-hidden="true"><div class="modal modal-lg"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-kanban-fill"></i> Tambah Project</h3><button class="modal-close" data-close-modal="project-create-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" action="{{ route('projects.store') }}">@csrf<div class="modal-body">@include('project.partials.form',['prefix'=>'create','includeType'=>true])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="project-create-modal">Batal</button><button type="submit" class="btn btn-primary">Buat Project</button></div></form></div></div>
<div class="modal-overlay" id="project-edit-modal" aria-hidden="true"><div class="modal modal-lg"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-pencil-fill"></i> Edit Project</h3><button class="modal-close" data-close-modal="project-edit-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" id="project-edit-form">@csrf @method('PUT')<div class="modal-body">@include('project.partials.form',['prefix'=>'edit','includeType'=>false])</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="project-edit-modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div></form></div></div>
@endpush
@endif
