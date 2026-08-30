@extends('layouts.admin.tabler')

@section('content')
    <div class="page-wrapper">
        <div class="page-header d-print-none mb-3">
            <div class="container-xl">
                <div class="page-pretitle">Pengaturan</div>
                <h2 class="page-title">Izin Browser</h2>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <div class="row">
                    <div class="col-lg-6 justify-content-center mx-auto">
                        @php
                            $perms = [
                                'location' => [
                                    'title' => 'Izinkan Lokasi',
                                    'desc' => 'Untuk presensi otomatis dan pelacakan lokasi WFH',
                                ],
                                'camera' => [
                                    'title' => 'Izinkan Kamera',
                                    'desc' => 'Untuk foto selfie saat presensi masuk/pulang',
                                ],
                                'notifications' => [
                                    'title' => 'Izinkan Notifikasi',
                                    'desc' => 'Untuk notifikasi WFH, pengingat, dan persetujuan',
                                ],
                            ];
                        @endphp

                        @foreach ($perms as $key => $perm)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                class="avatar avatar-md bg-{{ $key === 'notifications' ? 'blue' : ($key === 'location' ? 'green' : 'yellow') }}-lt">
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $perm['title'] }}</div>
                                                <div class="text-muted small">{{ $perm['desc'] }}</div>
                                                <div id="admin-status-{{ $key }}"
                                                    class="small {{ $permissions[$key] ? 'text-green' : 'text-red' }}">
                                                    {{ $permissions[$key] ? 'Aktif' : 'Nonaktif' }}
                                                </div>
                                            </div>
                                        </div>
                                        <label class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-permission"
                                                data-permission="{{ $key }}"
                                                {{ $permissions[$key] ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        document.querySelectorAll('.toggle-permission').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                var permission = this.dataset.permission;
                var isChecked = this.checked;
                var toggleEl = this;
                var action = isChecked ? 'Mengaktifkan' : 'Menonaktifkan';
                var permissionLabels = {
                    location: 'Lokasi',
                    camera: 'Kamera',
                    notifications: 'Notifikasi'
                };
                var label = permissionLabels[permission] || permission;

                Swal.fire({
                    title: action + ' Izin ' + label + '?',
                    text: isChecked ? 'Browser akan meminta izin.' : 'Izin akan dinonaktifkan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ' + action,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/api/admin/permissions/toggle', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({
                                    permission: permission
                                })
                            })
                            .then(r => r.json())
                            .then(data => {
                                var statusEl = document.getElementById('admin-status-' +
                                    permission);
                                if (data.is_enabled) {
                                    statusEl.textContent = 'Aktif';
                                    statusEl.className = 'small text-green';
                                } else {
                                    statusEl.textContent = 'Nonaktif';
                                    statusEl.className = 'small text-red';
                                }
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Izin ' + label + ' berhasil ' + (data
                                        .is_enabled ? 'diaktifkan' : 'dinonaktifkan'
                                    ),
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            })
                            .catch(() => {
                                toggleEl.checked = !toggleEl.checked;
                                Swal.fire('Gagal', 'Terjadi kesalahan. Coba lagi.', 'error');
                            });
                    } else {
                        this.checked = !this.checked;
                    }
                });
            });
        });
    </script>
@endpush
