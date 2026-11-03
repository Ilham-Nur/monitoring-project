<div class="form-grid">
    <div class="form-group"><label class="form-label" for="{{ $prefix }}-type-name">Nama Tipe Project <span class="required">*</span></label><input class="form-control" id="{{ $prefix }}-type-name" name="name" required></div>
    <div class="form-group"><label class="form-label">Status</label><label class="form-check check-card"><input type="checkbox" class="check-input" id="{{ $prefix }}-type-active" name="is_active" value="1" checked><span class="check-label">Aktif dan dapat dipilih</span></label></div>
    <div class="form-group form-group-full"><label class="form-label" for="{{ $prefix }}-type-description">Deskripsi</label><textarea class="form-textarea" id="{{ $prefix }}-type-description" name="description" rows="2"></textarea></div>
</div>
<div class="section-divider"><div><h4>Tahapan Default</h4><p>Tanggal rencana dihitung berurutan berdasarkan durasi berikut.</p></div><button type="button" class="btn btn-outline btn-sm" data-add-stage-row="{{ $prefix }}"><i class="bi bi-plus-lg"></i> Tambah Tahapan</button></div>
<div class="stage-builder" id="{{ $prefix }}-stage-builder" data-stage-builder="{{ $prefix }}">
    <div class="stage-builder-row" data-stage-row><span class="drag-number">1</span><input class="form-control" name="stages[0][name]" placeholder="Nama tahapan" required><div class="duration-input"><input class="form-control" type="number" name="stages[0][duration_days]" min="1" value="1" required><span>hari</span></div><button type="button" class="btn-action btn-action-delete" data-remove-stage-row><i class="bi bi-trash3"></i></button></div>
</div>
