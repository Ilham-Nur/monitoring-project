@extends('layout.main')
@section('title', $project->name.' - '.config('app.name'))
@section('breadcrumb', 'Detail Project')

@section('content')
<div class="page-header project-page-header">
    <div><a class="back-link" href="{{ route('projects.index') }}"><i class="bi bi-arrow-left"></i> Kembali ke daftar project</a><div class="title-row"><h1 class="page-title">{{ $project->name }}</h1><span class="badge {{ $project->status==='completed'?'badge-success':'badge-info' }}">{{ $project->statusLabel() }}</span></div><p class="page-subtitle"><span class="mono">{{ $project->code }}</span> · {{ $project->type->name }}</p></div>
    @if(auth()->user()->isAdmin())<button class="btn btn-outline" data-open-modal="stage-create-modal"><i class="bi bi-plus-lg"></i> Tambah Tahapan</button>@endif
</div>

<section class="project-hero card">
    <div class="project-progress-block"><div class="progress-ring" style="--progress:{{ $project->progressPercentage() }}"><span>{{ $project->progressPercentage() }}%</span></div><div><span class="eyebrow">Progres keseluruhan</span><h2>{{ $project->completed_stages_count }} dari {{ $project->stages_count }} tahapan selesai</h2><div class="progress-track large"><span style="width:{{ $project->progressPercentage() }}%"></span></div></div></div>
    <div class="project-meta-grid">
        <div class="meta-item"><span>Klien</span><strong>{{ $project->client->company_name }}</strong><small>{{ $project->client->name }}</small></div>
        <div class="meta-item"><span>PIC Project</span><strong>{{ $project->pics->pluck('name')->join(', ') }}</strong></div>
        <div class="meta-item"><span>Mulai</span><strong>{{ $project->start_date->translatedFormat('d F Y') }}</strong></div>
        <div class="meta-item"><span>Target Selesai</span><strong>{{ $project->end_date->translatedFormat('d F Y') }}</strong><small>{{ $project->end_date->diffForHumans() }}</small></div>
    </div>
    @if($project->description)<p class="project-description">{{ $project->description }}</p>@endif
</section>

<section class="card timeline-card">
    <div class="card-header">
        <div class="card-header-left">
            <h2 class="card-title">Timeline Project</h2>
            <p class="card-subtitle">Setiap blok tanggal menunjukkan perbandingan rencana dan realisasi dalam hari kalender.</p>
        </div>
        <div class="timeline-legend">
            <span><i class="legend-plan"></i>Plan (Rencana)</span>
            <span><i class="legend-action-in"></i>Action (Dalam Plan)</span>
            <span><i class="legend-action-early"></i>Early Start</span>
            <span><i class="legend-action-late"></i>Late Finish</span>
        </div>
    </div>
    @if($project->stages->isNotEmpty())
    @php
        $timelineMonths = $timelineDates->groupBy(fn ($date) => $date->format('Y-m'));
        $monthStartColumn = 2;
    @endphp
    <div class="gantt-scroll">
        <div class="gantt" style="--timeline-days:{{ $timelineDates->count() }}">
        <div class="gantt-row gantt-month-row">
            <div class="gantt-label gantt-description-header">Deskripsi Pekerjaan</div>
            @foreach($timelineMonths as $monthDates)
                <div class="gantt-month" style="grid-column:{{ $monthStartColumn }} / span {{ $monthDates->count() }}">{{ $monthDates->first()->translatedFormat('F Y') }}</div>
                @php
                    $monthStartColumn += $monthDates->count();
                @endphp
            @endforeach
        </div>
        <div class="gantt-row gantt-date-row">
            <div class="gantt-label">Tanggal</div>
            @foreach($timelineDates as $date)
                <div class="gantt-day {{ $date->isWeekend()?'weekend':'' }}"><span>{{ $date->format('d') }}</span></div>
            @endforeach
        </div>
        @foreach($project->stages as $stage)
            @php
                $actualEnd = $stage->actual_end ?? ($stage->actual_start ? ($stage->actual_start->isFuture() ? $stage->actual_start : today()) : null);
            @endphp
            <div class="gantt-row gantt-data-row" data-open-modal="stage-modal-{{ $stage->id }}">
                <div class="gantt-label"><strong>{{ $stage->sort_order }}. {{ $stage->name }}</strong><small>{{ $stage->duration_days }} hari · {{ $stage->is_completed?'Selesai':$stage->timingLabel() }}</small></div>
                @foreach($timelineDates as $date)
                    @php
                        $isInPlan = $date->betweenIncluded($stage->planned_start, $stage->planned_end);
                        $isInAction = $stage->actual_start && $actualEnd && $date->betweenIncluded($stage->actual_start, $actualEnd);
                        $blockClass = null;
                        $blockTitle = null;

                        if ($isInAction && $date->lessThan($stage->planned_start)) {
                            $blockClass = 'timeline-block-action-early';
                            $blockTitle = 'Early Start — realisasi sebelum plan';
                        } elseif ($isInAction && $date->greaterThan($stage->planned_end)) {
                            $blockClass = 'timeline-block-action-late';
                            $blockTitle = 'Late Finish — realisasi melewati plan';
                        } elseif ($isInAction && $isInPlan) {
                            $blockClass = 'timeline-block-action-in';
                            $blockTitle = 'Action — realisasi dalam plan';
                        } elseif ($isInPlan) {
                            $blockClass = 'timeline-block-plan';
                            $blockTitle = 'Plan — tanggal rencana';
                        }
                    @endphp
                    <div class="timeline-date-cell {{ $date->isWeekend()?'weekend':'' }}">
                        @if($blockClass)<span class="timeline-day-block {{ $blockClass }}" title="{{ $blockTitle }} · {{ $date->translatedFormat('d M Y') }}"></span>@endif
                    </div>
                @endforeach
            </div>
        @endforeach
        </div>
    </div>
    @else<div class="empty-state"><i class="bi bi-calendar-x"></i><h3>Belum ada tahapan</h3><p>Tambahkan tahapan agar timeline dapat dihitung.</p></div>@endif
</section>

<div class="detail-layout">
    <section class="card" data-project-stages>
        <div class="card-header"><div class="card-header-left"><h2 class="card-title">Tahapan Project</h2><p class="card-subtitle">Klik tahapan untuk melihat dokumen dan komentar</p></div></div>
        <div class="stage-flow">
        @foreach($project->stages as $stage)
            <button class="stage-flow-item {{ $stage->is_completed?'completed':'' }} {{ !$stage->is_completed && $stage->timingStatus()==='overdue'?'overdue':'' }}" id="stage-{{ $stage->id }}" data-open-modal="stage-modal-{{ $stage->id }}">
                <span class="stage-flow-number">@if($stage->is_completed)<i class="bi bi-check-lg"></i>@else{{ $stage->sort_order }}@endif</span>
                <span class="stage-flow-content"><strong>{{ $stage->name }}</strong><small>Rencana {{ $stage->planned_start->translatedFormat('d M') }}–{{ $stage->planned_end->translatedFormat('d M Y') }}</small></span>
                <span class="badge {{ $stage->is_completed?'badge-success':($stage->timingStatus()==='overdue'?'badge-danger':'badge-secondary') }}">{{ $stage->is_completed?'Selesai':$stage->timingLabel() }}</span><i class="bi bi-chevron-right"></i>
            </button>
        @endforeach
        </div>
    </section>
    <aside class="card activity-card">
        <div class="card-header"><div class="card-header-left"><h2 class="card-title">Aktivitas Terbaru</h2><p class="card-subtitle">Riwayat perubahan project</p></div></div>
        <div class="activity-list" tabindex="0" role="region" aria-label="Daftar aktivitas terbaru">@forelse($project->activities as $activity)<div class="activity-item"><span class="activity-dot"></span><div><strong>{{ $activity->user?->name ?? 'Sistem' }}</strong><p>{{ $activity->description }}</p><small>{{ $activity->created_at->diffForHumans() }}</small></div></div>@empty<div class="empty-compact">Belum ada aktivitas.</div>@endforelse</div>
    </aside>
</div>
@endsection

@push('modals')
@if(auth()->user()->isAdmin())
<div class="modal-overlay" id="stage-create-modal" aria-hidden="true"><div class="modal modal-md"><div class="modal-header"><h3 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Tahapan</h3><button class="modal-close" data-close-modal="stage-create-modal"><i class="bi bi-x-lg"></i></button></div><form method="POST" action="{{ route('project-stages.store',$project) }}">@csrf<div class="modal-body"><div class="form-group"><label class="form-label">Nama Tahapan</label><input class="form-control" name="name" required></div><div class="form-group"><label class="form-label">Durasi Rencana</label><div class="duration-input"><input class="form-control" type="number" name="duration_days" min="1" value="1" required><span>hari kalender</span></div></div><p class="info-box"><i class="bi bi-info-circle"></i> Tahapan ditambahkan di urutan terakhir dan timeline dihitung ulang.</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-close-modal="stage-create-modal">Batal</button><button class="btn btn-primary" type="submit">Tambah Tahapan</button></div></form></div></div>
@endif

@foreach($project->stages as $stage)
@php
    $canUpdateStage = auth()->user()->can('update', $stage);
@endphp
<div class="modal-overlay" id="stage-modal-{{ $stage->id }}" aria-hidden="true">
    <div class="modal modal-xl stage-detail-modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title"><span class="stage-number-badge">{{ $stage->sort_order }}</span> Detail Proses <strong>{{ $stage->name }}</strong></h3>
                <p class="modal-subtitle">Rencana {{ $stage->planned_start->translatedFormat('d F Y') }} – {{ $stage->planned_end->translatedFormat('d F Y') }}</p>
            </div>
            <button class="modal-close" data-close-modal="stage-modal-{{ $stage->id }}"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body stage-modal-body">
            <div class="stage-summary-strip">
                <div><span>Status</span><strong>{{ $stage->is_completed?'Selesai':$stage->timingLabel() }}</strong></div>
                <div><span>Durasi Rencana</span><strong>{{ $stage->duration_days }} hari</strong></div>
                <div><span>Realisasi</span><strong>{{ $stage->actual_start?->translatedFormat('d M Y') ?? '—' }} – {{ $stage->actual_end?->translatedFormat('d M Y') ?? '—' }}</strong></div>
            </div>

            <div class="stage-modal-content">
                <div class="stage-files-grid {{ $canUpdateStage ? '' : 'single-column' }}">
                    <section class="stage-modal-section stage-uploaded-panel">
                        <div class="section-title">
                            <div><h4>Data Terunggah</h4><p>{{ $stage->documents->count() }} dokumen tersedia untuk diunduh.</p></div>
                        </div>
                        <div class="document-list">
                            @forelse($stage->documents as $document)
                                <div class="document-item">
                                    <span class="document-icon"><i class="bi bi-file-earmark-text-fill"></i></span>
                                    <div class="document-info"><strong>{{ $document->original_name }}</strong><small>{{ number_format($document->file_size/1024,1) }} KB · {{ $document->uploader?->name ?? 'Pengguna' }} · {{ $document->created_at->diffForHumans() }}</small></div>
                                    <a class="btn btn-outline btn-sm" href="{{ route('stage-documents.download',$document) }}"><i class="bi bi-download"></i> Unduh</a>
                                    @can('update',$stage)<button class="btn-action btn-action-delete" data-confirm-delete="{{ route('stage-documents.destroy',$document) }}" data-confirm-message="Hapus dokumen {{ $document->original_name }} secara permanen?"><i class="bi bi-trash3"></i></button>@endcan
                                </div>
                            @empty
                                <div class="empty-compact bordered">Belum ada data yang di-upload.</div>
                            @endforelse
                        </div>
                    </section>

                    @can('update',$stage)
                    <section class="stage-modal-section stage-upload-panel">
                        <div class="section-title"><div><h4>Upload File Baru</h4><p>Unggah dokumen untuk tahapan ini.</p></div></div>
                        <form class="upload-stack" method="POST" action="{{ route('stage-documents.store',$stage) }}" enctype="multipart/form-data">
                            @csrf
                            <input class="form-control" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                            <small>PDF, gambar, Word, atau Excel. Maksimal 20 MB.</small>
                            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-cloud-arrow-up"></i> Simpan File</button>
                        </form>
                    </section>
                    @endcan
                </div>

                <section class="stage-modal-section stage-action-section">
                    <div class="section-title"><div><h4>Pelaksanaan Tahapan</h4><p>Tanggal realisasi dibandingkan langsung dengan blok plan pada timeline.</p></div></div>
                    @can('update',$stage)
                    <form method="POST" action="{{ route('project-stages.update',$stage) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-grid stage-action-grid">
                            @if(auth()->user()->isAdmin())
                                <div class="form-group"><label class="form-label">Nama Tahapan</label><input class="form-control" name="name" value="{{ $stage->name }}" required></div>
                                <div class="form-group"><label class="form-label">Durasi Rencana</label><div class="duration-input"><input class="form-control" type="number" name="duration_days" min="1" value="{{ $stage->duration_days }}" required><span>hari</span></div></div>
                            @endif
                            <div class="form-group"><label class="form-label">Tanggal Mulai</label><input class="form-control" type="date" name="actual_start" value="{{ $stage->actual_start?->toDateString() }}"></div>
                            <div class="form-group"><label class="form-label">Tanggal Selesai</label><input class="form-control" type="date" name="actual_end" value="{{ $stage->actual_end?->toDateString() }}"></div>
                        </div>
                        <div class="inline-actions"><button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-save"></i> Simpan</button></div>
                    </form>
                    @else
                    <div class="stage-readonly-dates">
                        <div><span>Tanggal Mulai</span><strong>{{ $stage->actual_start?->translatedFormat('d F Y') ?? 'Belum diisi' }}</strong></div>
                        <div><span>Tanggal Selesai</span><strong>{{ $stage->actual_end?->translatedFormat('d F Y') ?? 'Belum diisi' }}</strong></div>
                    </div>
                    @endcan
                </section>

                <section class="stage-modal-section stage-comments-section">
                    <div class="section-title"><div><h4>Komentar</h4><p>Diskusi khusus untuk proses {{ $stage->name }}.</p></div></div>
                    <form class="comment-form" method="POST" action="{{ route('stage-comments.store',$stage) }}">
                        @csrf
                        <label class="form-label">Tulis Komentar</label>
                        <textarea class="form-textarea" name="comment" rows="3" placeholder="Tulis komentar kamu di sini..." required></textarea>
                        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-send"></i> Tambah Komentar</button>
                    </form>
                    <div class="comment-list">
                        @forelse($stage->comments as $comment)
                            <div class="comment-item">
                                <div class="avatar-sm">{{ strtoupper(substr($comment->user?->name ?? 'S',0,1)) }}</div>
                                <div><div class="comment-head"><strong>{{ $comment->user?->name ?? 'Pengguna dihapus' }}</strong><span>{{ $comment->user?->roleLabel() }}</span></div><p>{{ $comment->comment }}</p><small>{{ $comment->created_at->translatedFormat('d M Y, H:i') }}</small></div>
                            </div>
                        @empty
                            <div class="empty-compact">Belum ada komentar.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        @can('update',$stage)
        <div class="modal-footer stage-footer">
            @if(auth()->user()->isAdmin())<button class="btn btn-outline danger-outline" type="button" data-confirm-delete="{{ route('project-stages.destroy',$stage) }}" data-confirm-message="Hapus tahapan {{ $stage->name }} beserta dokumen dan komentarnya?"><i class="bi bi-trash3"></i> Hapus Tahapan</button>@endif
            <div class="footer-spacer"></div>
            @if($stage->is_completed)
                <form method="POST" action="{{ route('stage-completions.destroy',$stage) }}">@csrf @method('DELETE')<button class="btn btn-secondary" type="submit"><i class="bi bi-arrow-counterclockwise"></i> Cabut Tanda Selesai</button></form>
            @else
                <form method="POST" action="{{ route('stage-completions.store',$stage) }}">@csrf<button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Tandai Selesai</button></form>
            @endif
        </div>
        @endcan
    </div>
</div>
@endforeach
@endpush
