{{-- ================================================== --}}
{{-- Sidebar --}}
{{-- ================================================== --}}
<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">

    <div class="container-fluid">

        {{-- ================================================== --}}
        {{-- Sidebar Toggle --}}
        {{-- ================================================== --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        {{-- ================================================== --}}
        {{-- Logo --}}
        {{-- ================================================== --}}
        <h1 class="navbar-brand navbar-brand-autodark">

            <a href="/panel/dashboardadmin">

                <img src="{{ asset('assets/img/login/logo_aplikasi_admin_nyamping.png') }}" width="110"
                    height="32" alt="Tabler" class="navbar-brand-image">

            </a>

        </h1>

        {{-- ================================================== --}}
        {{-- Mobile User Menu --}}
        {{-- ================================================== --}}
        <div class="navbar-nav flex-row d-lg-none">

            <div class="nav-item dropdown">

                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">

                    <span class="avatar avatar-sm"
                        style="background-image: url('{{ asset('assets/img/admin_icon.png') }}')">
                    </span>

                    <div class="d-none d-xl-block ps-2">

                        <div>
                            {{ Auth::guard('user')->user()->name }}
                        </div>

                        <div class="mt-1 small text-muted">
                            Administrator
                        </div>

                    </div>

                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                    <a href="/admin/settings/permissions" class="dropdown-item">
                        Pengaturan
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/proseslogoutadmin" class="dropdown-item">
                        Logout
                    </a>

                </div>

            </div>

        </div>

        {{-- ================================================== --}}
        {{-- Sidebar Menu --}}
        {{-- ================================================== --}}
        <div class="collapse navbar-collapse" id="sidebar-menu">

            <ul class="navbar-nav pt-lg-3">

                {{-- ================================================== --}}
                {{-- Menu Dashboard --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['panel/dashboardadmin']) ? 'active' : '' }}"
                        href="/panel/dashboardadmin">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Dashboard
                        </span>

                    </a>

                </li>

                {{-- ================================================== --}}
                {{-- Menu Data Master --}}
                {{-- ================================================== --}}
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle {{ request()->is(['karyawan', 'unitperusahaan']) ? 'show' : '' }}"
                        href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                                <path d="M16 5.25l-8 4.5" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Data Master
                        </span>

                    </a>

                    <div class="dropdown-menu {{ request()->is(['karyawan', 'unitperusahaan']) ? 'show' : '' }}">

                        <div class="dropdown-menu-columns">

                            <div class="dropdown-menu-column">

                                <a class="dropdown-item {{ request()->is(['karyawan']) ? 'active' : '' }}"
                                    href="/karyawan">

                                    Karyawan

                                </a>

                                <a class="dropdown-item {{ request()->is(['unitperusahaan']) ? 'active' : '' }}"
                                    href="/unitperusahaan">

                                    Unit Perusahaan

                                </a>

                            </div>

                        </div>

                    </div>

                </li>

                {{-- ================================================== --}}
                {{-- Menu Monitoring Presensi --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['presensi/monitoring']) ? 'active' : '' }}"
                        href="/presensi/monitoring">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-device-desktop-analytics">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1l0 -10" />
                                <path d="M7 20h10" />
                                <path d="M9 16v4" />
                                <path d="M15 16v4" />
                                <path d="M9 12v-4" />
                                <path d="M12 12v-1" />
                                <path d="M15 12v-2" />
                                <path d="M12 12v-1" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Monitoring Presensi
                        </span>

                    </a>

                </li>

                {{-- ================================================== --}}
                {{-- Menu Data Izin Karyawan --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['presensi/dataizin']) ? 'active' : '' }}"
                        href="/presensi/dataizin">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-license">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M15 21h-9a3 3 0 0 1 -3 -3v-1h10v2a2 2 0 0 0 4 0v-14a2 2 0 1 1 2 2h-2m2 -4h-11a3 3 0 0 0 -3 3v11" />
                                <path d="M9 7l4 0" />
                                <path d="M9 11l4 0" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Data Izin Karyawan
                        </span>

                    </a>

                </li>

                {{-- ================================================== --}}
                {{-- Menu Data Lembur Karyawan --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['presensi/datalembur']) ? 'active' : '' }}"
                        href="/presensi/datalembur">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icon-tabler-clock-hour-4">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 7v5l3 3" />
                                <path d="M12 21a9 9 0 1 0 0 -18a9 9 0 0 0 0 18" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Data Lembur Karyawan
                        </span>

                    </a>

                </li>


                {{-- ================================================== --}}
                {{-- Menu Data WFH Karyawan --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['presensi/datawfh']) ? 'active' : '' }}"
                        href="/presensi/datawfh">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icon-tabler-home">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h3v-6h4v6h3a2 2 0 0 0 2 -2v-7" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Data WFH Karyawan
                        </span>
                        @php
                            $totalPending = ($pendingWfhAdminCount ?? 0) + ($pendingLaporanAdminCount ?? 0);
                        @endphp
                        <span id="adminWfhBadge" class="ms-auto"
                            style="display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border-radius:9999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;line-height:1;{{ $totalPending > 0 ? '' : 'display:none;' }}">{{ $totalPending }}</span>

                    </a>

                </li>


                {{-- ================================================== --}}
                {{-- Menu Laporan Presensi --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['presensi/laporan']) ? 'active' : '' }}"
                        href="/presensi/laporan">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icon-tabler-file-isr">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M6 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7" />
                                <path d="M3 15l3 -3l3 3" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            Laporan Presensi
                        </span>

                    </a>

                </li>

                {{-- ================================================== --}}
                {{-- Menu User / Admin --}}
                {{-- ================================================== --}}
                <li class="nav-item">

                    <a class="nav-link {{ request()->is(['panel/users']) ? 'active' : '' }}" href="/panel/users">

                        <span class="nav-link-icon d-md-none d-lg-inline-block">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icon-tabler-user-exclamation">

                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4c.348 0 .686 .045 1.008 .128" />
                                <path d="M19 16v3" />
                                <path d="M19 22v.01" />

                            </svg>

                        </span>

                        <span class="nav-link-title">
                            User / Admin
                        </span>

                    </a>

                </li>

            </ul>

        </div>

    </div>

</aside>
