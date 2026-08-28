@extends('layouts.admin.tabler')

@section('content')

{{-- ====================================================== --}}
{{-- DASHBOARD HEADER --}}
{{-- ====================================================== --}}

<div class="page-header d-print-none">

    <div class="container-xl">

        <div class="row g-2 align-items-center">

            <div class="col">

                <div class="page-pretitle">
                    WAG - Presensi Digital
                </div>

                <h2 class="page-title">
                    Dashboard Administrator
                </h2>

                <br>

            </div>

        </div>

    </div>

</div>



{{-- ====================================================== --}}
{{-- DASHBOARD BODY --}}
{{-- ====================================================== --}}

<div class="page-body">

    <div class="container-xl">

        <div class="row g-3">



            {{-- ====================================================== --}}
            {{-- JUMLAH KARYAWAN --}}
            {{-- ====================================================== --}}

            <div class="col-md-6 col-xl-3">

                <a href="/karyawan" class="text-decoration-none text-reset">

                    <div class="card card-sm card-link">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-auto">

                                    <span class="bg-info text-white avatar">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-users">

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>

                                            <path d="M5 7a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>

                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>

                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>

                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>

                                        </svg>

                                    </span>

                                </div>

                                <div class="col">

                                    <div class="font-weight-medium">
                                        {{ $jmlkaryawan }}
                                    </div>

                                    <div class="text-muted">
                                        Jumlah Karyawan
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            </div>



            {{-- ====================================================== --}}
            {{-- KARYAWAN HADIR HARI INI --}}
            {{-- ====================================================== --}}

            <div class="col-md-6 col-xl-3">

                <a href="/presensi/monitoring"
                   class="text-decoration-none text-reset">

                    <div class="card card-sm-link">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-auto">

                                    <span class="bg-success text-white avatar">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint">

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>

                                            <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3"/>

                                            <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6"/>

                                            <path d="M12 11v2a14 14 0 0 0 2.5 8"/>

                                            <path d="M8 15a18 18 0 0 0 1.8 6"/>

                                            <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95"/>

                                        </svg>

                                    </span>

                                </div>

                                <div class="col">

                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlhadir ?? 0 }}
                                    </div>

                                    <div class="text-muted">
                                        Karyawan Hadir Hari Ini
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            </div>


            {{-- ====================================================== --}}
            {{-- KARYAWAN WFH HARI INI --}}
            {{-- ====================================================== --}}
            
            <div class="col-md-6 col-xl-3">
            
                <a href="/presensi/datawfh"
                   class="text-decoration-none text-reset">
            
                    <div class="card card-sm card-link">
            
                        <div class="card-body">
            
                            <div class="row align-items-center">
            
                                <div class="col-auto">
            
                                    <span class="bg-primary text-white avatar">
            
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round">
            
                                            <path stroke="none"
                                                  d="M0 0h24v24H0z"
                                                  fill="none"/>
            
                                            <path d="M5 12l-2 0l9 -9l9 9l-2 0"/>
            
                                            <path d="M5 12v7a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1 -1v-7"/>
            
                                        </svg>
            
                                    </span>
            
                                </div>
            
                                <div class="col">
            
                                    <div class="font-weight-medium">
                                        {{ $rekapwfh->jmlwfh ?? 0 }}
                                    </div>
            
                                    <div class="text-muted">
                                        Karyawan WFH Hari Ini
                                    </div>
            
                                </div>
            
                            </div>
            
                        </div>
            
                    </div>
            
                </a>
            
            </div>  


            {{-- ====================================================== --}}
            {{-- KARYAWAN IZIN / SAKIT HARI INI --}}
            {{-- ====================================================== --}}

            <div class="col-md-6 col-xl-3">

                <a href="/presensi/dataizin?tanggal={{ date('Y-m-d') }}"
                   class="text-decoration-none text-reset">

                    <div class="card card-sm-link">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-auto">

                                    <span class="bg-warning text-white avatar">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-user-off">

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>

                                            <path d="M8.18 8.189a4.01 4.01 0 0 0 2.616 2.627m3.507 -.545a4 4 0 1 0 -5.59 -5.552"/>

                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4c.412 0 .81 .062 1.183 .178m2.633 2.618c.12 .38 .184 .785 .184 1.204v2"/>

                                            <path d="M3 3l18 18"/>

                                        </svg>

                                    </span>

                                </div>

                                <div class="col">

                                    <div class="font-weight-medium">
                                        {{ $rekapizin->jmlizin ?? 0 }}
                                    </div>

                                    <div class="text-muted">
                                        Karyawan Izin/Sakit Hari Ini
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            </div>

            {{-- ====================================================== --}}
            {{-- KARYAWAN TERLAMBAT HARI INI --}}
            {{-- ====================================================== --}}

            <div class="col-md-6 col-xl-3">

                <a href="/presensi/monitoring"
                   class="text-decoration-none text-reset">

                    <div class="card card-sm-link">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-auto">

                                    <span class="bg-danger text-white avatar">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-alert-octagon">

                                            <path stroke="none"
                                                  d="M0 0h24v24H0z"
                                                  fill="none" />

                                            <path d="M12.802 2.165l5.575 2.389c.48 .206 .863 .589 1.07 1.07l2.388 5.574c.22 .512 .22 1.092 0 1.604l-2.389 5.575c-.206 .48 -.589 .863 -1.07 1.07l-5.574 2.388c-.512 .22 -1.092 .22 -1.604 0l-5.575 -2.389a2.036 2.036 0 0 1 -1.07 -1.07l-2.388 -5.574a2.036 2.036 0 0 1 0 -1.604l2.389 -5.575c.206 -.48 .589 -.863 1.07 -1.07l5.574 -2.388a2.036 2.036 0 0 1 1.604 0" />

                                            <path d="M12 8v4" />

                                            <path d="M12 16h.01" />

                                        </svg>

                                    </span>

                                </div>

                                <div class="col">

                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmltelat ?? 0 }}
                                    </div>

                                    <div class="text-muted">
                                        Karyawan Terlambat Hari Ini
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            </div>



            {{-- ====================================================== --}}
            {{-- KARYAWAN LEMBUR HARI INI --}}
            {{-- ====================================================== --}}

            <div class="col-md-6 col-xl-3">

                <a href="/presensi/datalembur"
                   class="text-decoration-none text-reset">

                    <div class="card card-sm card-link">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-auto">

                                    <span class="bg-danger text-white avatar">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-clock">

                                            <path stroke="none"
                                                  d="M0 0h24v24H0z"
                                                  fill="none" />

                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />

                                            <path d="M12 7v5l3 3" />

                                        </svg>

                                    </span>

                                </div>

                                <div class="col">

                                    <div class="font-weight-medium">
                                        {{ $rekaplembur->jmllembur ?? 0 }}
                                    </div>

                                    <div class="text-muted">
                                        Karyawan Lembur Hari Ini
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            </div>


        </div>

    </div>

</div>

@endsection