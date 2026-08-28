@extends('layouts.admin.tabler')

@section('content')

    {{-- ================================================== --}}
    {{-- Page Header --}}
    {{-- ================================================== --}}
    <div class="page-header d-print-none">

        <div class="container-xl">

            <div class="row g-2 align-items-center">

                <div class="col">

                    <div class="page-pretitle">
                        WAG - Presensi Digital
                    </div>

                    <h2 class="page-title">
                        Monitoring Presensi
                    </h2>

                </div>

            </div>

        </div>

    </div>

    {{-- ================================================== --}}
    {{-- Page Body --}}
    {{-- ================================================== --}}
    <div class="page-body">

        <div class="container-xl">

            <div class="row">

                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            {{-- ================================================== --}}
                            {{-- Filter --}}
                            {{-- ================================================== --}}
                            <div class="row">

                                <div class="col-12">

                                    {{-- ========================= --}}
                                    {{-- Tanggal --}}
                                    {{-- ========================= --}}
                                    <div class="input-icon mb-3">

                                        <span class="input-icon-addon">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-time">

                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                <path d="M15 3v4" />
                                                <path d="M7 3v4" />
                                                <path d="M3 11h16" />
                                                <path d="M18 16.496v1.504l1 1" />

                                            </svg>

                                        </span>

                                        <input type="text"
                                            class="form-control"
                                            id="tanggal"
                                            name="tanggal"
                                            placeholder="Pilih Tanggal Presensi"
                                            autocomplete="off"
                                            value="{{ date('Y-m-d') }}">

                                    </div>

                                    {{-- ========================= --}}
                                    {{-- Filter Pencarian --}}
                                    {{-- ========================= --}}
                                    <div class="row mb-3">

                                        <div class="col-md-5">

                                            <input type="text"
                                                class="form-control"
                                                id="nama_karyawan"
                                                placeholder="Cari Nama Karyawan"
                                                autocomplete="off">

                                        </div>

                                        <div class="col-md-5">

                                            <select class="form-select"
                                                id="unit">

                                                <option value="">
                                                    Semua Unit
                                                </option>

                                                @foreach ($unitperusahaan as $u)

                                                    <option value="{{ $u->unit }}">
                                                        {{ $u->unit }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-md-2">

                                            <button type="button"
                                                class="btn btn-primary"
                                                id="btnCari">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <circle cx="10.5" cy="10.5" r="7.5" />
                                                    <line x1="21" y1="21" x2="15.8" y2="15.8" />

                                                </svg>

                                                Cari Data

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- ================================================== --}}
                            {{-- Tabel Monitoring --}}
                            {{-- ================================================== --}}
                            <div class="row">

                                <div class="col-12">

                                    <table class="table table-striped table-hover">

                                        <thead>

                                            <tr>

                                                <th>No.</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Unit Perusahaan</th>
                                                <th>Masuk</th>
                                                <th>Foto Masuk</th>
                                                <th>Pulang</th>
                                                <th>Foto Pulang</th>
                                                <th>Keterangan Presensi</th>
                                                <th>Lokasi</th>
                                                <th>Lembur</th>

                                            </tr>

                                        </thead>

                                        <tbody id="loadpresensi">

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ================================================== --}}
    {{-- Modal Peta --}}
    {{-- ================================================== --}}
    <div class="modal modal-blur fade"
        id="modal-tampilkanpeta"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Lokasi Presensi Karyawan
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body" id="loadmap">

                    {{-- Map akan dimuat menggunakan AJAX --}}

                </div>

            </div>

        </div>

    </div>

@endsection

@push('myscript')

<script>

    $(function () {

        // ==================================================
        // Datepicker
        // ==================================================
        $('#tanggal').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // ==================================================
        // Load Data Presensi
        // ==================================================
        function loadpresensi() {

            var tanggal = $('#tanggal').val();
            var nama_karyawan = $('#nama_karyawan').val();
            var unit = $('#unit').val();

            $.ajax({

                type: 'POST',

                url: '/getpresensi',

                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal: tanggal,
                    nama_karyawan: nama_karyawan,
                    unit: unit
                },

                cache: false,

                success: function (respond) {

                    $("#loadpresensi").html(respond);

                }

            });

        }

        // ==================================================
        // Filter Berdasarkan Tanggal
        // ==================================================
        $("#tanggal").change(function () {

            loadpresensi();

        });

        // ==================================================
        // Button Cari
        // ==================================================
        $("#btnCari").click(function () {

            loadpresensi();

        });

        // ==================================================
        // Load Pertama Kali
        // ==================================================
        loadpresensi();

        // ==================================================
        // Preview Foto Presensi
        // ==================================================
        $(document).on('click', '.foto-monitoring', function () {

            Swal.fire({

                imageUrl: $(this).attr('src'),
                imageAlt: 'Foto Presensi',
                showConfirmButton: false,
                showCloseButton: true,
                width: '480px',
                backdrop: false

            });

        });

    });

</script>

@endpush