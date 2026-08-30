@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">WAG - Presensi Digital</div>
                    <h2 class="page-title">Data WFH Karyawan</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            {{-- Filter --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <form action="/presensi/datawfh" method="GET">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            class="icon icon-tabler icon-tabler-calendar-time">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                            <path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                            <path d="M15 3v4" />
                                                            <path d="M7 3v4" />
                                                            <path d="M3 11h16" />
                                                            <path d="M18 16.496v1.504l1 1" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" class="form-control" id="tanggal" name="tanggal"
                                                        autocomplete="off" placeholder="Cari Tanggal WFH"
                                                        value="{{ Request('tanggal') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <input type="text" name="nama_karyawan" class="form-control"
                                                    placeholder="Cari Nama" value="{{ Request('nama_karyawan') }}"
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-2">
                                                <select name="unit" class="form-select">
                                                    <option value="">Semua Unit</option>
                                                    @foreach ($unitperusahaan as $u)
                                                        <option {{ Request('unit') == $u->unit ? 'selected' : '' }}
                                                            value="{{ $u->unit }}">{{ $u->perusahaan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select name="status" class="form-select">
                                                    <option value="">Semua Status</option>
                                                    <option value="pending_atasan"
                                                        {{ Request('status') == 'pending_atasan' ? 'selected' : '' }}>
                                                        Menunggu
                                                        Atasan</option>
                                                    <option value="pending_admin"
                                                        {{ Request('status') == 'pending_admin' ? 'selected' : '' }}>
                                                        Menunggu
                                                        Admin</option>
                                                    <option value="approved"
                                                        {{ Request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                                                    </option>
                                                    <option value="rejected"
                                                        {{ Request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                                                    </option>
                                                    <option value="unpaid"
                                                        {{ Request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <button type="submit" class="btn btn-primary w-100">Cari Data</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal</th>
                                            <th>NIK / Nama</th>
                                            <th>Jabatan • Posisi</th>
                                            <th>Unit</th>
                                            <th>Atasan</th>
                                            <th>Status</th>
                                            <th>Pengajuan</th>
                                            <th>Laporan</th>
                                            <th style="min-width:200px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="wfhTableBody">
                                        @include('presensi.datawfh-rows')
                                    </tbody>
                                </table>
                            </div>
                            <div id="wfhPagination" class="mt-3">
                                {{ $datawfh->appends(request()->all())->links('vendor.pagination.bootstrap-5') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal (reuse presensi style) --}}
    <div id="adminPreviewBackdrop"
        style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(20,12,6,0.55); backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:16px;">
        <div
            style="background:#fff; border-radius:20px; width:100%; max-width:640px; max-height:85vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f0ece8;">
                <div>
                    <div id="adminModalTitle" style="font-size:14px; font-weight:700;">Preview</div>
                    <div id="adminModalSubtitle" style="font-size:11px; color:#a8a29e;"></div>
                </div>
                <button type="button" id="adminModalClose"
                    style="width:36px; height:36px; border-radius:999px; background:#f5f5f4; border:1px solid #e7e5e4;">✕</button>
            </div>
            <div id="adminModalBody"
                style="flex:1; overflow:auto; background:#fafaf9; min-height:320px; display:flex; flex-direction:column;">
                <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:32px;">Memuat...
                </div>
            </div>
            <div style="display:flex; gap:8px; padding:14px 20px; border-top:1px solid #f0ece8; justify-content:flex-end;">
                <a id="adminModalDownload" href="#" download class="btn btn-outline-secondary">Download</a>
                <a id="adminModalOpenTab" href="#" target="_blank" class="btn btn-primary">Buka di Tab Baru</a>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $('#tanggal').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('input[name="tanggal"]').change(function() {
                $(this).closest('form').submit();
            });
            $('select[name="unit"], select[name="status"]').change(function() {
                $(this).closest('form').submit();
            });

            // Admin preview
            const backdrop = document.getElementById('adminPreviewBackdrop');
            const body = document.getElementById('adminModalBody');
            const titleEl = document.getElementById('adminModalTitle');
            const subtitleEl = document.getElementById('adminModalSubtitle');
            const downloadEl = document.getElementById('adminModalDownload');
            const openTabEl = document.getElementById('adminModalOpenTab');
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-preview-admin');
                if (!btn) return;
                e.preventDefault();
                const url = btn.dataset.url;
                const filename = btn.dataset.filename || '';
                const label = btn.dataset.label || filename;
                titleEl.textContent = label;
                subtitleEl.textContent = filename;
                downloadEl.href = url;
                openTabEl.href = url;
                backdrop.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                const ext = (filename.split('.').pop() || '').toLowerCase();
                body.innerHTML = '';
                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    const img = document.createElement('img');
                    img.src = url;
                    img.style.width = '100%';
                    img.style.height = 'auto';
                    img.style.objectFit = 'contain';
                    body.appendChild(img);
                } else if (ext === 'pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = url;
                    iframe.style.width = '100%';
                    iframe.style.height = '480px';
                    iframe.style.border = '0';
                    body.appendChild(iframe);
                } else {
                    body.innerHTML =
                        '<div style="padding:32px; text-align:center;"><p>Preview tidak tersedia untuk .' +
                        ext + '</p><p><a href="' + url +
                        '" target="_blank" class="btn btn-primary">Buka di Tab Baru</a></p></div>';
                }
            });
            document.getElementById('adminModalClose').addEventListener('click', () => {
                backdrop.style.display = 'none';
                document.body.style.overflow = '';
            });
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    backdrop.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });

            // === EVENT DELEGATION: Reject buttons (survives AJAX table update) ===
            document.getElementById('wfhTableBody').addEventListener('click', function(e) {
                // Reject admin WFH
                var btnReject = e.target.closest('.btn-reject-admin');
                if (btnReject) {
                    e.preventDefault();
                    var id = btnReject.dataset.id;
                    Swal.fire({
                        title: 'Tolak WFH?',
                        input: 'textarea',
                        inputPlaceholder: 'Alasan penolakan...',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Tolak',
                        inputValidator: v => {
                            if (!v || v.trim().length < 5) return 'Minimal 5 karakter';
                        }
                    }).then(res => {
                        if (res.isConfirmed) {
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/presensi/datawfh/' + id + '/reject';
                            var csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            var reason = document.createElement('input');
                            reason.type = 'hidden';
                            reason.name = 'rejected_reason';
                            reason.value = res.value;
                            form.appendChild(csrf);
                            form.appendChild(reason);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                    return;
                }

                // Reject laporan admin
                var btnRejectLaporan = e.target.closest('.btn-reject-laporan-admin');
                if (btnRejectLaporan) {
                    e.preventDefault();
                    var idL = btnRejectLaporan.dataset.id;
                    Swal.fire({
                        title: 'Tolak Laporan WFH?',
                        input: 'textarea',
                        inputPlaceholder: 'Alasan penolakan laporan...',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Tolak Laporan',
                        inputValidator: v => {
                            if (!v || v.trim().length < 5) return 'Minimal 5 karakter';
                        }
                    }).then(res => {
                        if (res.isConfirmed) {
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/presensi/datawfh/' + idL + '/reject-laporan-admin';
                            var csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            var reason = document.createElement('input');
                            reason.type = 'hidden';
                            reason.name = 'rejected_reason';
                            reason.value = res.value;
                            form.appendChild(csrf);
                            form.appendChild(reason);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                    return;
                }

                // Delete confirm
                var btnDelete = e.target.closest('.delete-confirm');
                if (btnDelete) {
                    e.preventDefault();
                    var form = btnDelete.closest('form');
                    Swal.fire({
                        title: 'Yakin data ini akan dihapus?',
                        text: "Data WFH yang sudah dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Hapus Data',
                        backdrop: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                    return;
                }
            });

            // === REALTIME POLLING ADMIN DATA WFH ===
            (function() {
                let lastCheck = new Date().toISOString();

                function getCurrentFilters() {
                    var params = new URLSearchParams(window.location.search);
                    var filters = {};
                    if (params.get('nama_karyawan')) filters.nama_karyawan = params.get('nama_karyawan');
                    if (params.get('unit')) filters.unit = params.get('unit');
                    if (params.get('tanggal')) filters.tanggal = params.get('tanggal');
                    if (params.get('status')) filters.status = params.get('status');
                    if (params.get('page')) filters.page = params.get('page');
                    return filters;
                }

                function fetchTableData() {
                    var filters = getCurrentFilters();
                    var qs = new URLSearchParams(filters).toString();
                    fetch('/api/realtime/admin/wfh-data' + (qs ? '?' + qs : ''), {
                            credentials: 'same-origin'
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            var tbody = document.getElementById('wfhTableBody');
                            var pagination = document.getElementById('wfhPagination');
                            if (tbody && data.html) tbody.innerHTML = data.html;
                            if (pagination && data.pagination) pagination.innerHTML = data.pagination;
                        }).catch(function() {});
                }

                function pollAdminData() {
                    fetch('/api/realtime/admin', {
                            credentials: 'same-origin'
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            // Update sidebar badge
                            var badgeEl = document.getElementById('adminWfhBadge');
                            var total = (data.pending_wfh || 0) + (data.pending_laporan || 0);
                            if (badgeEl) {
                                if (total > 0) {
                                    badgeEl.textContent = total;
                                    badgeEl.style.display = 'inline-flex';
                                } else {
                                    badgeEl.style.display = 'none';
                                }
                            }
                            // Check if data changed → AJAX table update
                            fetch('/api/realtime/admin/wfh-check?last_check=' + encodeURIComponent(
                                    lastCheck), {
                                    credentials: 'same-origin'
                                })
                                .then(function(r) {
                                    return r.json();
                                })
                                .then(function(check) {
                                    if (check.updated_data || check.new_data) {
                                        lastCheck = new Date().toISOString();
                                        fetchTableData();
                                    }
                                }).catch(function() {});
                        }).catch(function() {});
                }

                pollAdminData();
                setInterval(pollAdminData, 5000);

                // AJAX Pagination click
                document.getElementById('wfhPagination').addEventListener('click', function(e) {
                    var link = e.target.closest('a');
                    if (!link) return;
                    e.preventDefault();
                    var apiUrl = link.href.replace('/presensi/datawfh', '/api/realtime/admin/wfh-data');
                    fetch(apiUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            var tbody = document.getElementById('wfhTableBody');
                            var pagination = document.getElementById('wfhPagination');
                            if (tbody && data.html) tbody.innerHTML = data.html;
                            if (pagination && data.pagination) pagination.innerHTML = data
                                .pagination;
                            window.history.pushState({}, '', link.href);
                        }).catch(function() {});
                });
            })();
        });
    </script>
@endpush
