/**
 * AdminSphere — script.js
 * Premium Admin Dashboard — jQuery Logic
 * =============================================================
 */

$(function () {

  /* =============================================================
     DATA DUMMY (30 rows)
     Ganti dengan AJAX / @foreach Laravel di production
     ============================================================= */
  var userData = [
    { id: 1,  nama: 'Ahmad Fauzi',        email: 'ahmad.fauzi@mail.com',    role: 'Super Admin', status: 'Active',   tanggal: '2024-01-05' },
    { id: 2,  nama: 'Budi Santoso',       email: 'budi.santoso@mail.com',   role: 'Admin',       status: 'Active',   tanggal: '2024-01-12' },
    { id: 3,  nama: 'Citra Dewi',         email: 'citra.dewi@mail.com',     role: 'Editor',      status: 'Pending',  tanggal: '2024-02-03' },
    { id: 4,  nama: 'Dian Pratama',       email: 'dian.pratama@mail.com',   role: 'Viewer',      status: 'Active',   tanggal: '2024-02-18' },
    { id: 5,  nama: 'Eko Wahyudi',        email: 'eko.wahyudi@mail.com',    role: 'Editor',      status: 'Inactive', tanggal: '2024-03-01' },
    { id: 6,  nama: 'Fitri Handayani',    email: 'fitri.h@mail.com',        role: 'Admin',       status: 'Active',   tanggal: '2024-03-14' },
    { id: 7,  nama: 'Galih Nugroho',      email: 'galih.nugroho@mail.com',  role: 'Viewer',      status: 'Banned',   tanggal: '2024-04-02' },
    { id: 8,  nama: 'Hana Pertiwi',       email: 'hana.pertiwi@mail.com',   role: 'Editor',      status: 'Active',   tanggal: '2024-04-15' },
    { id: 9,  nama: 'Irwan Kusuma',       email: 'irwan.kusuma@mail.com',   role: 'Admin',       status: 'Pending',  tanggal: '2024-05-06' },
    { id: 10, nama: 'Juli Astuti',        email: 'juli.astuti@mail.com',    role: 'Viewer',      status: 'Active',   tanggal: '2024-05-22' },
    { id: 11, nama: 'Kevin Manurung',     email: 'kevin.m@mail.com',        role: 'Editor',      status: 'Active',   tanggal: '2024-06-01' },
    { id: 12, nama: 'Lina Marlena',       email: 'lina.m@mail.com',         role: 'Admin',       status: 'Inactive', tanggal: '2024-06-10' },
    { id: 13, nama: 'Mario Siahaan',      email: 'mario.s@mail.com',        role: 'Viewer',      status: 'Active',   tanggal: '2024-06-28' },
    { id: 14, nama: 'Nina Rahayu',        email: 'nina.rahayu@mail.com',    role: 'Editor',      status: 'Pending',  tanggal: '2024-07-05' },
    { id: 15, nama: 'Oscar Hidayat',      email: 'oscar.h@mail.com',        role: 'Admin',       status: 'Active',   tanggal: '2024-07-19' },
    { id: 16, nama: 'Putri Amalina',      email: 'putri.a@mail.com',        role: 'Viewer',      status: 'Banned',   tanggal: '2024-08-03' },
    { id: 17, nama: 'Qori Ramadhan',      email: 'qori.r@mail.com',         role: 'Editor',      status: 'Active',   tanggal: '2024-08-20' },
    { id: 18, nama: 'Rizki Firmansyah',   email: 'rizki.f@mail.com',        role: 'Admin',       status: 'Active',   tanggal: '2024-09-01' },
    { id: 19, nama: 'Sari Indah',         email: 'sari.indah@mail.com',     role: 'Viewer',      status: 'Inactive', tanggal: '2024-09-15' },
    { id: 20, nama: 'Taufik Hidayat',     email: 'taufik.h@mail.com',       role: 'Editor',      status: 'Active',   tanggal: '2024-10-04' },
    { id: 21, nama: 'Umi Kalsum',         email: 'umi.kalsum@mail.com',     role: 'Viewer',      status: 'Pending',  tanggal: '2024-10-18' },
    { id: 22, nama: 'Vicky Prasetyo',     email: 'vicky.p@mail.com',        role: 'Admin',       status: 'Active',   tanggal: '2024-11-02' },
    { id: 23, nama: 'Wulan Sari',         email: 'wulan.s@mail.com',        role: 'Editor',      status: 'Active',   tanggal: '2024-11-20' },
    { id: 24, nama: 'Xenia Putri',        email: 'xenia.p@mail.com',        role: 'Viewer',      status: 'Banned',   tanggal: '2024-12-01' },
    { id: 25, nama: 'Yoga Perdana',       email: 'yoga.p@mail.com',         role: 'Editor',      status: 'Active',   tanggal: '2024-12-15' },
    { id: 26, nama: 'Zahra Puspita',      email: 'zahra.puspita@mail.com',  role: 'Admin',       status: 'Pending',  tanggal: '2025-01-08' },
    { id: 27, nama: 'Andre Sihombing',    email: 'andre.s@mail.com',        role: 'Viewer',      status: 'Active',   tanggal: '2025-01-22' },
    { id: 28, nama: 'Bella Natasya',      email: 'bella.n@mail.com',        role: 'Editor',      status: 'Inactive', tanggal: '2025-02-05' },
    { id: 29, nama: 'Chandra Gunawan',    email: 'chandra.g@mail.com',      role: 'Admin',       status: 'Active',   tanggal: '2025-02-18' },
    { id: 30, nama: 'Dewi Susilawati',    email: 'dewi.sus@mail.com',       role: 'Viewer',      status: 'Active',   tanggal: '2025-03-03' },
  ];

  /* =============================================================
     STATE
     ============================================================= */
  var state = {
    data:        userData.slice(),   // working copy
    filtered:    [],
    currentPage: 1,
    rowsPerPage: 10,
    sortCol:     null,
    sortDir:     'asc',
    search:      '',
    filterRole:  '',
    filterStatus:'',
    deleteTarget: null,              // row to be deleted
    editTarget:  null,               // row being edited
    nextId:      31,                 // auto-increment for new rows
  };

  /* =============================================================
     INIT
     ============================================================= */
  function init() {
    updateStats();
    filterData();
  }

  /* =============================================================
     STAT CARDS
     ============================================================= */
  function updateStats() {
    var total   = state.data.length;
    var active  = state.data.filter(function (r) { return r.status === 'Active'; }).length;
    var pending = state.data.filter(function (r) { return r.status === 'Pending'; }).length;
    var banned  = state.data.filter(function (r) { return r.status === 'Banned'; }).length;

    animateCount('#stat-total',   total);
    animateCount('#stat-active',  active);
    animateCount('#stat-pending', pending);
    animateCount('#stat-banned',  banned);
  }

  function animateCount(selector, target) {
    var $el  = $(selector);
    var from = parseInt($el.text()) || 0;
    $({ val: from }).animate({ val: target }, {
      duration: 600,
      easing: 'swing',
      step: function () { $el.text(Math.floor(this.val)); },
      complete: function () { $el.text(target); }
    });
  }

  /* =============================================================
     BADGE HELPERS
     ============================================================= */
  function statusBadge(status) {
    var map = {
      'Active':   { cls: 'badge-success', icon: 'bi-check-circle-fill' },
      'Inactive': { cls: 'badge-secondary', icon: 'bi-dash-circle-fill' },
      'Pending':  { cls: 'badge-warning', icon: 'bi-hourglass-split' },
      'Banned':   { cls: 'badge-danger', icon: 'bi-slash-circle-fill' },
    };
    var b = map[status] || { cls: 'badge-secondary', icon: 'bi-question-circle' };
    return '<span class="badge ' + b.cls + '"><i class="bi ' + b.icon + '"></i> ' + status + '</span>';
  }

  function roleBadge(role) {
    return '<span class="role-tag">' + role + '</span>';
  }

  function avatarLetter(nama) {
    return nama ? nama.charAt(0).toUpperCase() : '?';
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    var d = new Date(dateStr);
    var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }

  /* =============================================================
     FILTER & SORT
     ============================================================= */
  function filterData() {
    var search = state.search.toLowerCase().trim();
    var role   = state.filterRole;
    var status = state.filterStatus;

    state.filtered = state.data.filter(function (r) {
      var matchSearch = !search || r.nama.toLowerCase().includes(search) || r.email.toLowerCase().includes(search);
      var matchRole   = !role   || r.role === role;
      var matchStatus = !status || r.status === status;
      return matchSearch && matchRole && matchStatus;
    });

    sortData();
    state.currentPage = 1;
    renderTable();
    renderPagination();
  }

  function sortData() {
    if (!state.sortCol) return;
    state.filtered.sort(function (a, b) {
      var valA = state.sortCol === 'nama'    ? a.nama    :
                 state.sortCol === 'tanggal' ? a.tanggal : '';
      var valB = state.sortCol === 'nama'    ? b.nama    :
                 state.sortCol === 'tanggal' ? b.tanggal : '';
      if (valA < valB) return state.sortDir === 'asc' ? -1 : 1;
      if (valA > valB) return state.sortDir === 'asc' ?  1 : -1;
      return 0;
    });
  }

  /* =============================================================
     RENDER TABLE
     ============================================================= */
  function renderTable() {
    var $tbody  = $('#table-body');
    var perPage = state.rowsPerPage;
    var page    = state.currentPage;
    var start   = (page - 1) * perPage;
    var end     = start + perPage;
    var pageData = state.filtered.slice(start, end);
    var total    = state.filtered.length;

    $tbody.empty();

    if (pageData.length === 0) {
      $tbody.html(
        '<tr><td colspan="7">' +
          '<div class="empty-state">' +
            '<div class="empty-icon"><i class="bi bi-inbox"></i></div>' +
            '<div class="empty-title">Tidak ada data ditemukan</div>' +
            '<div class="empty-desc">Coba ubah kata kunci pencarian atau filter Anda.</div>' +
          '</div>' +
        '</td></tr>'
      );
    } else {
      $.each(pageData, function (i, row) {
        var no = start + i + 1;
        var tr = $('<tr data-id="' + row.id + '">');
        tr.html(
          '<td class="td-no"><span class="mono">' + no + '</span></td>' +
          '<td>' +
            '<div class="td-user">' +
              '<div class="td-avatar">' + avatarLetter(row.nama) + '</div>' +
              '<div>' +
                '<div class="td-name">' + escHtml(row.nama) + '</div>' +
                '<div class="td-email-sub">' + escHtml(row.email) + '</div>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td class="td-email-col"><span style="font-size:12.5px;color:var(--text-secondary)">' + escHtml(row.email) + '</span></td>' +
          '<td>' + roleBadge(row.role) + '</td>' +
          '<td>' + statusBadge(row.status) + '</td>' +
          '<td class="td-date">' + formatDate(row.tanggal) + '</td>' +
          '<td>' +
            '<div class="action-btns">' +
              '<button class="btn-action btn-action-view"  data-action="view"   title="Lihat Detail"><i class="bi bi-eye-fill"></i></button>' +
              '<button class="btn-action btn-action-edit"  data-action="edit"   title="Edit Data"><i class="bi bi-pencil-fill"></i></button>' +
              '<button class="btn-action btn-action-delete" data-action="delete" title="Hapus"><i class="bi bi-trash3-fill"></i></button>' +
            '</div>' +
          '</td>'
        );
        $tbody.append(tr);
      });
    }

    // Update info
    var showing = pageData.length;
    var infoText = total === 0
      ? 'Tidak ada data'
      : 'Menampilkan ' + (start + 1) + '–' + (start + showing) + ' dari ' + total + ' data';
    $('#table-info').text(infoText);
  }

  function escHtml(str) {
    return $('<div>').text(str || '').html();
  }

  /* =============================================================
     PAGINATION
     ============================================================= */
  function renderPagination() {
    var $pag    = $('#pagination');
    var total   = state.filtered.length;
    var perPage = state.rowsPerPage;
    var pages   = Math.ceil(total / perPage);
    var current = state.currentPage;

    $pag.empty();
    if (pages <= 1) return;

    // Prev
    var $prev = $('<button class="page-btn">&lsaquo;</button>');
    if (current === 1) $prev.attr('disabled', true);
    $prev.on('click', function () {
      if (current > 1) { state.currentPage--; renderTable(); renderPagination(); }
    });
    $pag.append($prev);

    // Page numbers with ellipsis
    var range = buildPageRange(current, pages);
    $.each(range, function (_, pg) {
      if (pg === '...') {
        $pag.append('<span class="page-ellipsis">&hellip;</span>');
      } else {
        var $btn = $('<button class="page-btn">' + pg + '</button>');
        if (pg === current) $btn.addClass('active');
        $btn.on('click', function () {
          state.currentPage = pg;
          renderTable();
          renderPagination();
        });
        $pag.append($btn);
      }
    });

    // Next
    var $next = $('<button class="page-btn">&rsaquo;</button>');
    if (current === pages) $next.attr('disabled', true);
    $next.on('click', function () {
      if (current < pages) { state.currentPage++; renderTable(); renderPagination(); }
    });
    $pag.append($next);
  }

  function buildPageRange(current, total) {
    if (total <= 7) {
      return Array.from({ length: total }, function (_, i) { return i + 1; });
    }
    var range = [];
    range.push(1);
    if (current > 3) range.push('...');
    for (var i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
      range.push(i);
    }
    if (current < total - 2) range.push('...');
    range.push(total);
    return range;
  }

  /* =============================================================
     MODAL SYSTEM
     ============================================================= */
  function openModal(modalId) {
    var $overlay = $('#' + modalId);
    $overlay.attr('aria-hidden', 'false').addClass('modal-open');
    $('body').css('overflow', 'hidden');

    // Focus first input
    setTimeout(function () {
      $overlay.find('input, select, textarea').first().trigger('focus');
    }, 240);
  }

  function closeModal(modalId) {
    var $overlay = $('#' + modalId);
    $overlay.attr('aria-hidden', 'true').removeClass('modal-open');
    // Re-enable scroll only when no other modals open
    if ($('.modal-overlay.modal-open').length === 0) {
      $('body').css('overflow', '');
    }
  }

  function closeAllModals() {
    $('.modal-overlay.modal-open').each(function () {
      $(this).attr('aria-hidden', 'true').removeClass('modal-open');
    });
    $('body').css('overflow', '');
  }

  // Close by overlay click
  $(document).on('click', '.modal-overlay', function (e) {
    if ($(e.target).hasClass('modal-overlay')) {
      closeAllModals();
    }
  });

  // Close by [data-close-modal] button
  $(document).on('click', '[data-close-modal]', function () {
    var target = $(this).data('close-modal');
    closeModal(target);
  });

  // Close by ESC
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') closeAllModals();
  });

  /* =============================================================
     TOAST SYSTEM
     ============================================================= */
  var toastIcons = {
    success: 'bi-check-circle-fill',
    error:   'bi-x-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    info:    'bi-info-circle-fill',
  };
  var toastTitles = {
    success: 'Berhasil',
    error:   'Terjadi Kesalahan',
    warning: 'Peringatan',
    info:    'Informasi',
  };

  function showToast(type, message, duration) {
    duration = duration || 4000;
    var icon  = toastIcons[type]  || 'bi-bell-fill';
    var title = toastTitles[type] || 'Notifikasi';

    var $toast = $('<div class="toast toast-' + type + '" role="alert">' +
      '<div class="toast-icon-wrapper"><i class="bi ' + icon + '"></i></div>' +
      '<div class="toast-content">' +
        '<div class="toast-title">' + title + '</div>' +
        '<div class="toast-message">' + message + '</div>' +
      '</div>' +
      '<button class="toast-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>' +
      '<div class="toast-progress" style="--duration:' + (duration / 1000) + 's"></div>' +
    '</div>');

    $('#toast-container').append($toast);

    // Auto remove
    var timer = setTimeout(function () { removeToast($toast); }, duration);

    // Manual close
    $toast.find('.toast-close').on('click', function () {
      clearTimeout(timer);
      removeToast($toast);
    });
  }

  function removeToast($toast) {
    $toast.addClass('removing');
    setTimeout(function () { $toast.remove(); }, 320);
  }

  /* =============================================================
     FORM VALIDATION
     ============================================================= */
  function validateForm(formId) {
    var $form = $('#' + formId);
    var valid = true;

    // Clear previous errors
    $form.find('.form-control, .form-select, .form-textarea').removeClass('is-invalid');

    // Required fields
    $form.find('[required], .validate-required').each(function () {
      if ($(this).val().trim() === '') {
        $(this).addClass('is-invalid');
        valid = false;
      }
    });

    // Email fields
    $form.find('input[type="email"]').each(function () {
      var email = $(this).val().trim();
      if (email && !isValidEmail(email)) {
        $(this).addClass('is-invalid');
        valid = false;
      }
      if (!email) {
        $(this).addClass('is-invalid');
        valid = false;
      }
    });

    return valid;
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function resetForm(formId) {
    var $form = $('#' + formId);
    $form[0].reset();
    $form.find('.form-control, .form-select, .form-textarea').removeClass('is-invalid');
  }

  /* =============================================================
     ACTION — VIEW
     ============================================================= */
  function openViewModal(id) {
    var row = findById(id);
    if (!row) return;

    var $body = $('#modal-view-body');
    $body.html(
      '<div class="detail-user-header">' +
        '<div class="detail-avatar">' + avatarLetter(row.nama) + '</div>' +
        '<div>' +
          '<div class="detail-user-name">' + escHtml(row.nama) + '</div>' +
          '<div class="detail-user-email">' + escHtml(row.email) + '</div>' +
        '</div>' +
      '</div>' +
      '<div class="detail-grid">' +
        '<div class="detail-item"><div class="detail-label">ID Pengguna</div><div class="detail-value">#' + String(row.id).padStart(4, '0') + '</div></div>' +
        '<div class="detail-item"><div class="detail-label">Role</div><div class="detail-value">' + roleBadge(row.role) + '</div></div>' +
        '<div class="detail-item"><div class="detail-label">Status</div><div class="detail-value">' + statusBadge(row.status) + '</div></div>' +
        '<div class="detail-item"><div class="detail-label">Tanggal Dibuat</div><div class="detail-value">' + formatDate(row.tanggal) + '</div></div>' +
        '<div class="detail-item" style="grid-column:1/-1"><div class="detail-label">Alamat Email</div><div class="detail-value">' + escHtml(row.email) + '</div></div>' +
      '</div>'
    );

    openModal('modal-view');
  }

  /* =============================================================
     ACTION — EDIT
     ============================================================= */
  function openEditModal(id) {
    var row = findById(id);
    if (!row) return;

    state.editTarget = id;

    $('#e-id').val(row.id);
    $('#e-nama').val(row.nama).removeClass('is-invalid');
    $('#e-email').val(row.email).removeClass('is-invalid');
    $('#e-role').val(row.role).removeClass('is-invalid');
    $('#e-status').val(row.status);

    openModal('modal-edit');
  }

  $('#btn-simpan-edit').on('click', function () {
    if (!validateForm('form-edit')) {
      showToast('error', 'Harap lengkapi semua field yang wajib diisi.');
      return;
    }

    var id = parseInt($('#e-id').val());
    var row = findById(id);
    if (!row) return;

    row.nama   = $('#e-nama').val().trim();
    row.email  = $('#e-email').val().trim();
    row.role   = $('#e-role').val();
    row.status = $('#e-status').val();

    closeModal('modal-edit');
    updateStats();
    filterData();
    showToast('success', 'Data pengguna <strong>' + escHtml(row.nama) + '</strong> berhasil diperbarui.');
  });

  /* =============================================================
     ACTION — DELETE
     ============================================================= */
  function openDeleteModal(id) {
    var row = findById(id);
    if (!row) return;

    state.deleteTarget = id;
    $('#delete-target-name').text(row.nama);
    openModal('modal-delete');
  }

  $('#btn-confirm-delete').on('click', function () {
    if (!state.deleteTarget) return;

    var row = findById(state.deleteTarget);
    var nama = row ? row.nama : '—';

    state.data = state.data.filter(function (r) { return r.id !== state.deleteTarget; });
    state.deleteTarget = null;

    closeModal('modal-delete');
    updateStats();
    filterData();
    showToast('success', 'Pengguna <strong>' + escHtml(nama) + '</strong> telah dihapus dari sistem.');
  });

  /* =============================================================
     ACTION — TAMBAH
     ============================================================= */
  $('#btn-tambah-user').on('click', function () {
    resetForm('form-tambah');
    openModal('modal-tambah');
  });

  $('#btn-simpan-tambah').on('click', function () {
    if (!validateForm('form-tambah')) {
      showToast('error', 'Harap lengkapi semua field yang wajib diisi.');
      return;
    }

    var newRow = {
      id:      state.nextId++,
      nama:    $('#t-nama').val().trim(),
      email:   $('#t-email').val().trim(),
      role:    $('#t-role').val(),
      status:  $('#t-status').val(),
      tanggal: new Date().toISOString().split('T')[0],
    };

    state.data.unshift(newRow);
    closeModal('modal-tambah');
    resetForm('form-tambah');
    updateStats();
    filterData();
    showToast('success', 'Pengguna <strong>' + escHtml(newRow.nama) + '</strong> berhasil ditambahkan.');
  });

  /* =============================================================
     TABLE — EVENT DELEGATION (action buttons)
     ============================================================= */
  $(document).on('click', '[data-action]', function () {
    var action = $(this).data('action');
    var id     = parseInt($(this).closest('tr').data('id'));

    if      (action === 'view')   openViewModal(id);
    else if (action === 'edit')   openEditModal(id);
    else if (action === 'delete') openDeleteModal(id);
  });

  /* =============================================================
     TABLE — SEARCH, FILTER, ROWS PER PAGE
     ============================================================= */
  var searchTimer;
  $('#table-search').on('input', function () {
    clearTimeout(searchTimer);
    var val = $(this).val();
    searchTimer = setTimeout(function () {
      state.search = val;
      filterData();
    }, 280);
  });

  $('#filter-role').on('change', function () {
    state.filterRole = $(this).val();
    filterData();
  });

  $('#filter-status').on('change', function () {
    state.filterStatus = $(this).val();
    filterData();
  });

  $('#rows-per-page').on('change', function () {
    state.rowsPerPage = parseInt($(this).val());
    state.currentPage = 1;
    filterData();
  });

  $('#btn-reset-filter').on('click', function () {
    state.search      = '';
    state.filterRole  = '';
    state.filterStatus= '';
    state.currentPage = 1;
    $('#table-search').val('');
    $('#filter-role').val('');
    $('#filter-status').val('');
    filterData();
    showToast('info', 'Filter berhasil direset.');
  });

  /* =============================================================
     TABLE — SORTING
     ============================================================= */
  $(document).on('click', '.sortable', function () {
    var col = $(this).data('col');

    // Toggle direction
    if (state.sortCol === col) {
      state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
    } else {
      state.sortCol = col;
      state.sortDir = 'asc';
    }

    // Update header classes
    $('.sortable').removeClass('sort-asc sort-desc');
    $(this).addClass(state.sortDir === 'asc' ? 'sort-asc' : 'sort-desc');

    sortData();
    renderTable();
    renderPagination();
  });

  /* =============================================================
     SIDEBAR TOGGLE
     ============================================================= */
  var isMobile = function () { return $(window).width() <= 768; };

  $('#sidebar-toggle-btn').on('click', function () {
    if (isMobile()) {
      $('#sidebar').toggleClass('mobile-open');
      $('#sidebar-overlay').toggleClass('show');
    } else {
      $('body').toggleClass('sidebar-collapsed');
    }
  });

  $('#sidebar-close-btn').on('click', function () {
    $('#sidebar').removeClass('mobile-open');
    $('#sidebar-overlay').removeClass('show');
  });

  $('#sidebar-overlay').on('click', function () {
    $('#sidebar').removeClass('mobile-open');
    $(this).removeClass('show');
  });

  // Sidebar nav active
  $(document).on('click', '.nav-link', function (e) {
    e.preventDefault();
    $('.nav-item').removeClass('active');
    $(this).closest('.nav-item').addClass('active');

    if (isMobile()) {
      $('#sidebar').removeClass('mobile-open');
      $('#sidebar-overlay').removeClass('show');
    }
  });

  // Resize handler
  $(window).on('resize', function () {
    if (!isMobile()) {
      $('#sidebar').removeClass('mobile-open');
      $('#sidebar-overlay').removeClass('show');
    }
  });

  /* =============================================================
     PROFILE DROPDOWN
     ============================================================= */
  $('#profile-trigger').on('click', function (e) {
    e.stopPropagation();
    var isOpen = $('#profile-dropdown').hasClass('show');

    // Close all dropdowns first
    closeAllDropdowns();

    if (!isOpen) {
      $('#profile-dropdown').addClass('show');
      $('#profile-trigger').addClass('open').attr('aria-expanded', 'true');
    }
  });

  $(document).on('click', function () {
    closeAllDropdowns();
  });

  $('#profile-dropdown').on('click', function (e) {
    e.stopPropagation();
  });

  function closeAllDropdowns() {
    $('#profile-dropdown').removeClass('show');
    $('#profile-trigger').removeClass('open').attr('aria-expanded', 'false');
  }

  /* =============================================================
     SAMPLE FORM — SUBMIT & VALIDATION
     ============================================================= */
  $('#sample-form').on('submit', function (e) {
    e.preventDefault();

    var $form = $(this);
    var valid = true;

    // Clear errors
    $form.find('.form-control, .form-select, .form-textarea').removeClass('is-invalid');

    // Validate
    var nama = $('#f-nama').val().trim();
    var email = $('#f-email').val().trim();
    var role  = $('#f-role').val();

    if (!nama)  { $('#f-nama').addClass('is-invalid');  valid = false; }
    if (!email || !isValidEmail(email)) { $('#f-email').addClass('is-invalid'); valid = false; }
    if (!role)  { $('#f-role').addClass('is-invalid');  valid = false; }

    // File validation
    var fileEl = document.getElementById('f-file');
    if (fileEl && fileEl.files && fileEl.files.length === 0) {
      $('#file-error').text('File dokumen wajib diunggah.').show();
      valid = false;
    } else {
      $('#file-error').hide();
    }

    if (!valid) {
      showToast('error', 'Harap perbaiki field yang tidak valid sebelum menyimpan.');
      return;
    }

    // Success
    showToast('success', 'Data form berhasil disimpan!');
    this.reset();
    $('#file-preview').empty().hide();
    $('.file-placeholder').text('Klik untuk pilih file atau drag & drop');
  });

  $('#btn-reset-form').on('click', function () {
    $('#sample-form')[0].reset();
    $('#sample-form .form-control, #sample-form .form-select').removeClass('is-invalid');
    $('#file-preview').empty().hide();
    $('.file-placeholder').text('Klik untuk pilih file atau drag & drop');
    showToast('info', 'Form telah direset.');
  });

  /* =============================================================
     FILE UPLOAD VALIDATION
     ============================================================= */
  var ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
  var ALLOWED_EXT   = ['pdf', 'jpg', 'jpeg', 'png'];
  var MAX_SIZE_MB   = 2;

  $('#f-file').on('change', function () {
    handleFileSelect(this.files);
  });

  // Drag & drop
  var $fileLabel = $('.file-label');

  $fileLabel.on('dragover', function (e) {
    e.preventDefault();
    $(this).closest('.file-upload-wrapper').addClass('drag-over');
  });
  $fileLabel.on('dragleave drop', function () {
    $(this).closest('.file-upload-wrapper').removeClass('drag-over');
  });
  $fileLabel.on('drop', function (e) {
    e.preventDefault();
    var files = e.originalEvent.dataTransfer.files;
    handleFileSelect(files);
  });

  function handleFileSelect(files) {
    if (!files || files.length === 0) return;

    var file = files[0];
    var ext  = file.name.split('.').pop().toLowerCase();
    var sizeMB = file.size / 1024 / 1024;
    var $err = $('#file-error');

    // Validate type
    if (!ALLOWED_EXT.includes(ext)) {
      $err.text('Tipe file tidak diizinkan. Hanya PDF, JPG, PNG.').show();
      showToast('error', 'Format file tidak diizinkan!');
      return;
    }

    // Validate size
    if (sizeMB > MAX_SIZE_MB) {
      $err.text('Ukuran file melebihi batas 2MB (' + sizeMB.toFixed(2) + 'MB).').show();
      showToast('error', 'Ukuran file terlalu besar (maks. 2MB).');
      return;
    }

    // Valid — show preview
    $err.hide();
    var sizeStr = sizeMB < 0.1 ? (file.size / 1024).toFixed(1) + ' KB' : sizeMB.toFixed(2) + ' MB';
    var iconCls = ext === 'pdf' ? 'bi-file-earmark-pdf-fill' : 'bi-file-earmark-image-fill';

    var $preview = $('#file-preview');
    $preview.html(
      '<div class="file-preview-item">' +
        '<i class="bi ' + iconCls + ' file-preview-icon"></i>' +
        '<span class="file-preview-name">' + escHtml(file.name) + '</span>' +
        '<span class="file-preview-size">' + sizeStr + '</span>' +
      '</div>'
    ).show();

    $('.file-placeholder').text(file.name);
    showToast('info', 'File <strong>' + escHtml(file.name) + '</strong> siap diunggah.');
  }

  /* =============================================================
     UTILS
     ============================================================= */
  function findById(id) {
    return state.data.find(function (r) { return r.id === id; }) || null;
  }

  /* =============================================================
     KICK OFF
     ============================================================= */
  init();

});
