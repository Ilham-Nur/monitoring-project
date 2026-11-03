@extends('layout.main')

@section('title', 'Dashboard - '.config('app.name'))
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard Monitoring</h1>
            <p class="page-subtitle">Ringkasan progres, jadwal, dan dokumen project yang dapat Anda akses.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('projects.index') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Project</a>
        @endif
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon"><i class="bi bi-play-circle-fill"></i></div>
            <div class="stat-body"><div class="stat-value">{{ $summary['active'] }}</div><div class="stat-label">Project Aktif</div></div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-body"><div class="stat-value">{{ $summary['completed'] }}</div><div class="stat-label">Project Selesai</div></div>
        </div>
        <div class="stat-card stat-card-red">
            <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="stat-body"><div class="stat-value">{{ $summary['overdue'] }}</div><div class="stat-label">Project Terlambat</div></div>
        </div>
        <div class="stat-card stat-card-amber">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-body"><div class="stat-value">{{ $summary['overdue_stages'] }}</div><div class="stat-label">Tahapan Terlambat</div></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <section class="card dashboard-projects">
            <div class="card-header">
                <div class="card-header-left"><h2 class="card-title">Progres Project</h2><p class="card-subtitle">Project aktif dan target penyelesaiannya</p></div>
                <a href="{{ route('projects.index') }}" class="btn btn-outline btn-sm">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Project</th><th>Klien</th><th>Target</th><th>Progres</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td><strong>{{ $project->name }}</strong><span class="table-secondary">{{ $project->code }}</span></td>
                            <td>{{ $project->client->company_name }}</td>
                            <td>{{ $project->end_date->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="progress-inline"><div class="progress-track"><span style="width: {{ $project->progressPercentage() }}%"></span></div><strong>{{ $project->progressPercentage() }}%</strong></div>
                            </td>
                            <td><a class="btn-action btn-action-view" href="{{ route('projects.show', $project) }}" title="Buka project"><i class="bi bi-arrow-right"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state compact"><i class="bi bi-kanban"></i><p>Belum ada project.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="dashboard-side">
            <section class="card">
                <div class="card-header"><div class="card-header-left"><h2 class="card-title">Deadline Terdekat</h2><p class="card-subtitle">Tahapan yang perlu diperhatikan</p></div></div>
                <div class="compact-list">
                    @forelse ($upcomingStages as $stage)
                        <a href="{{ route('projects.show', $stage->project) }}#stage-{{ $stage->id }}" class="compact-list-item">
                            <span class="compact-icon {{ $stage->planned_end->isPast() ? 'danger' : '' }}"><i class="bi bi-calendar-event"></i></span>
                            <span><strong>{{ $stage->name }}</strong><small>{{ $stage->project->name }} · {{ $stage->planned_end->translatedFormat('d M Y') }}</small></span>
                        </a>
                    @empty
                        <div class="empty-compact">Tidak ada deadline.</div>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <div class="card-header"><div class="card-header-left"><h2 class="card-title">Dokumen Terbaru</h2><p class="card-subtitle">Berkas yang baru diunggah</p></div></div>
                <div class="compact-list">
                    @forelse ($recentDocuments as $document)
                        <a href="{{ route('stage-documents.download', $document) }}" class="compact-list-item">
                            <span class="compact-icon"><i class="bi bi-file-earmark-text"></i></span>
                            <span><strong>{{ $document->original_name }}</strong><small>{{ $document->stage->project->name }} · {{ $document->created_at->diffForHumans() }}</small></span>
                        </a>
                    @empty
                        <div class="empty-compact">Belum ada dokumen.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
@endsection
