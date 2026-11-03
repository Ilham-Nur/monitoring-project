const find = (selector, root = document) => root.querySelector(selector);
const findAll = (selector, root = document) => [...root.querySelectorAll(selector)];

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('modal-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('modal-open');
    modal.setAttribute('aria-hidden', 'true');
    if (!find('.modal-overlay.modal-open')) document.body.style.overflow = '';
}

function showToast(type, message, duration = 4500) {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const titles = { success: 'Berhasil', error: 'Terjadi Kesalahan', warning: 'Perhatian', info: 'Informasi' };
    const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<div class="toast-icon-wrapper"><i class="bi ${icons[type] || icons.info}"></i></div><div class="toast-content"><div class="toast-title">${titles[type] || titles.info}</div><div class="toast-message"></div></div><button class="toast-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button><div class="toast-progress" style="--duration:${duration / 1000}s"></div>`;
    find('.toast-message', toast).textContent = message;
    const remove = () => { toast.classList.add('removing'); setTimeout(() => toast.remove(), 300); };
    find('.toast-close', toast).addEventListener('click', remove);
    container.appendChild(toast);
    setTimeout(remove, duration);
}

function toggleCompanyField(select) {
    const group = document.getElementById(select.dataset.companyTarget);
    if (!group) return;
    group.hidden = select.value !== 'client';
    const input = find('input', group);
    if (input) input.required = select.value === 'client';
}

function stageRow(prefix, index, stage = {}) {
    const row = document.createElement('div');
    row.className = 'stage-builder-row';
    row.dataset.stageRow = '';
    row.innerHTML = `<span class="drag-number">${index + 1}</span><input class="form-control" name="stages[${index}][name]" placeholder="Nama tahapan" required><div class="duration-input"><input class="form-control" type="number" name="stages[${index}][duration_days]" min="1" value="1" required><span>hari</span></div><button type="button" class="btn-action btn-action-delete" data-remove-stage-row aria-label="Hapus tahapan"><i class="bi bi-trash3"></i></button>`;
    find(`[name="stages[${index}][name]"]`, row).value = stage.name || '';
    find(`[name="stages[${index}][duration_days]"]`, row).value = stage.duration_days || 1;
    return row;
}

function reindexStageBuilder(builder) {
    findAll('[data-stage-row]', builder).forEach((row, index) => {
        find('.drag-number', row).textContent = index + 1;
        const name = find('input:not([type="number"])', row);
        const days = find('input[type="number"]', row);
        name.name = `stages[${index}][name]`;
        days.name = `stages[${index}][duration_days]`;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    findAll('[data-flash]').forEach((flash) => showToast(flash.dataset.flash, flash.dataset.message));

    const stagesPanel = find('[data-project-stages]');
    const activityPanel = find('.activity-card');
    if (stagesPanel && activityPanel) {
        const syncActivityHeight = () => {
            activityPanel.style.setProperty('--project-stages-height', `${stagesPanel.getBoundingClientRect().height}px`);
        };
        syncActivityHeight();
        new ResizeObserver(syncActivityHeight).observe(stagesPanel);
    }

    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    find('#sidebar-toggle-btn')?.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar?.classList.toggle('mobile-open');
            sidebarOverlay?.classList.toggle('show');
        } else document.body.classList.toggle('sidebar-collapsed');
    });
    const closeSidebar = () => { sidebar?.classList.remove('mobile-open'); sidebarOverlay?.classList.remove('show'); };
    find('#sidebar-close-btn')?.addEventListener('click', closeSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    const profileTrigger = find('#profile-trigger');
    const profileDropdown = find('#profile-dropdown');
    const notificationTrigger = find('#notification-trigger');
    const notificationDropdown = find('#notification-dropdown');
    profileTrigger?.addEventListener('click', (event) => { event.stopPropagation(); notificationDropdown?.classList.remove('show'); profileDropdown?.classList.toggle('show'); });
    notificationTrigger?.addEventListener('click', (event) => { event.stopPropagation(); profileDropdown?.classList.remove('show'); notificationDropdown?.classList.toggle('show'); });
    profileDropdown?.addEventListener('click', (event) => event.stopPropagation());
    notificationDropdown?.addEventListener('click', (event) => event.stopPropagation());
    document.addEventListener('click', () => { profileDropdown?.classList.remove('show'); notificationDropdown?.classList.remove('show'); });

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-modal]');
        if (opener) openModal(opener.dataset.openModal);

        const closer = event.target.closest('[data-close-modal]');
        if (closer) closeModal(closer.dataset.closeModal);

        const deleteButton = event.target.closest('[data-confirm-delete]');
        if (deleteButton) {
            event.stopPropagation();
            find('#confirm-form').action = deleteButton.dataset.confirmDelete;
            find('#confirm-message').textContent = deleteButton.dataset.confirmMessage || 'Data akan dihapus permanen. Lanjutkan?';
            openModal('confirm-modal');
        }

        const removeStage = event.target.closest('[data-remove-stage-row]');
        if (removeStage) {
            const builder = removeStage.closest('[data-stage-builder]');
            if (findAll('[data-stage-row]', builder).length === 1) {
                showToast('warning', 'Minimal satu tahapan harus tersedia.');
                return;
            }
            removeStage.closest('[data-stage-row]').remove();
            reindexStageBuilder(builder);
        }

        const addStage = event.target.closest('[data-add-stage-row]');
        if (addStage) {
            const prefix = addStage.dataset.addStageRow;
            const builder = find(`#${prefix}-stage-builder`);
            builder.appendChild(stageRow(prefix, findAll('[data-stage-row]', builder).length));
        }
    });

    findAll('.modal-overlay').forEach((overlay) => overlay.addEventListener('click', (event) => { if (event.target === overlay) closeModal(overlay.id); }));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') findAll('.modal-overlay.modal-open').forEach((modal) => closeModal(modal.id)); });

    findAll('.user-role-select').forEach((select) => { toggleCompanyField(select); select.addEventListener('change', () => toggleCompanyField(select)); });
    findAll('[data-edit-user]').forEach((button) => button.addEventListener('click', () => {
        find('#user-edit-form').action = `/users/${button.dataset.id}`;
        find('#edit-name').value = button.dataset.name;
        find('#edit-email').value = button.dataset.email;
        find('#edit-role').value = button.dataset.role;
        find('#edit-company').value = button.dataset.company || '';
        find('#edit-active').checked = button.dataset.active === '1';
        toggleCompanyField(find('#edit-role'));
        openModal('user-edit-modal');
    }));

    findAll('[data-edit-project]').forEach((button) => button.addEventListener('click', () => {
        find('#project-edit-form').action = `/projects/${button.dataset.id}`;
        find('#edit-project-code').value = button.dataset.code;
        find('#edit-project-name').value = button.dataset.name;
        find('#edit-project-client').value = button.dataset.client;
        find('#edit-project-description').value = button.dataset.description || '';
        find('#edit-project-start').value = button.dataset.start;
        const selectedPics = JSON.parse(button.dataset.pics || '[]').map(String);
        findAll('option', find('#edit-project-pics')).forEach((option) => { option.selected = selectedPics.includes(option.value); });
        openModal('project-edit-modal');
    }));

    findAll('[data-edit-type]').forEach((button) => button.addEventListener('click', () => {
        find('#type-edit-form').action = `/project-types/${button.dataset.id}`;
        find('#edit-type-name').value = button.dataset.name;
        find('#edit-type-description').value = button.dataset.description || '';
        find('#edit-type-active').checked = button.dataset.active === '1';
        const builder = find('#edit-stage-builder');
        builder.innerHTML = '';
        JSON.parse(button.dataset.stages || '[]').forEach((stage, index) => builder.appendChild(stageRow('edit', index, stage)));
        openModal('type-edit-modal');
    }));

    findAll('form').forEach((form) => form.addEventListener('submit', () => {
        const submit = find('button[type="submit"]', form);
        if (!submit || submit.dataset.noLoading !== undefined) return;
        submit.disabled = true;
        submit.dataset.originalText = submit.innerHTML;
        submit.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Memproses...';
    }));

    if (window.location.hash.startsWith('#stage-')) {
        const stageButton = document.querySelector(window.location.hash);
        if (stageButton?.dataset.openModal) openModal(stageButton.dataset.openModal);
    }
});
