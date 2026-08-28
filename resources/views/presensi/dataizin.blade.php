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
                        Data Izin Karyawan
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
                            <div class="row mb-3">

                                <div class="col-12">

                                    <form action="/presensi/dataizin" method="GET">

                                        {{-- ========================= --}}
                                        {{-- Filter Tanggal --}}
                                        {{-- ========================= --}}
                                        <div class="row mb-3">

                                            <div class="col-12">

                                                <div class="input-icon">

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
                                                            class="icon icon-tabler icon-tabler-calendar-time">

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
                                                        autocomplete="off"
                                                        placeholder="Cari Data Izin"
                                                        value="{{ Request('tanggal') }}">

                                                </div>

                                            </div>

                                        </div>

                                        {{-- ========================= --}}
                                        {{-- Filter Nama, Unit & Jenis --}}
                                        {{-- ========================= --}}
                                        <div class="row">

                                            <div class="col-4">

                                                <input type="text"
                                                    name="nama_karyawan"
                                                    class="form-control"
                                                    placeholder="Cari Nama Karyawan"
                                                    value="{{ Request('nama_karyawan') }}"
                                                    autocomplete="off">

                                            </div>

                                            <div class="col-3">

                                                <select name="unit"
                                                    class="form-select">

                                                    <option value="">
                                                        Semua Unit
                                                    </option>

                                                    @foreach ($unitperusahaan as $u)

                                                        <option
                                                            {{ Request('unit') == $u->unit ? 'selected' : '' }}
                                                            value="{{ $u->unit }}">

                                                            {{ $u->perusahaan }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="col-3">

                                                <select name="jenis_izin"
                                                    class="form-select">

                                                    <option value="">
                                                        Semua Jenis Izin
                                                    </option>

                                                    <option value="i"
                                                        {{ Request('jenis_izin') == 'i' ? 'selected' : '' }}>

                                                        Izin

                                                    </option>

                                                    <option value="s"
                                                        {{ Request('jenis_izin') == 's' ? 'selected' : '' }}>

                                                        Sakit

                                                    </option>

                                                </select>

                                            </div>

                                            <div class="col-2">

                                                <button type="submit"
                                                    class="btn btn-primary w-100">

                                                    Cari Data

                                                </button>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>

                            {{-- ================================================== --}}
                            {{-- Tabel Data Izin --}}
                            {{-- ================================================== --}}
                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>No.</th>
                                        <th>Tanggal Izin</th>
                                        <th>NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Unit Perusahaan</th>
                                        <th>Jenis Izin</th>
                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse ($dataizin as $d)
                                    
                                        <tr>

                                            <td>
                                                {{ ($dataizin->currentPage() - 1) * $dataizin->perPage() + $loop->iteration }}
                                            </td>

                                            <td>
                                                {{ date('d-m-Y', strtotime($d->tgl_izin)) }}
                                            </td>

                                            <td>{{ $d->nik }}</td>

                                            <td>{{ $d->nama_lengkap }}</td>

                                            <td>{{ $d->jabatan }}</td>

                                            <td>{{ $d->perusahaan }}</td>

                                            <td>

                                                {{ $d->jenis_izin == 'i' ? 'Izin' : 'Sakit' }}

                                            </td>

                                            <td>

                                                <a href="/presensi/showfile/{{ $d->file }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-success">

                                                    Lihat File

                                                </a>

                                                <form action="/presensi/dataizin/{{ $d->id }}/delete"
                                                    method="POST"
                                                    class="d-inline">

                                                    @csrf

                                                    <button type="submit"
                                                        class="btn btn-sm btn-danger delete-confirm">

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2">

                                                            <path d="M4 7h16" />
                                                            <path d="M10 11v6" />
                                                            <path d="M14 11v6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12" />
                                                            <path d="M9 7v-3h6v3" />

                                                        </svg>

                                                    </button>

                                                </form>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="8"
                                                class="text-center text-muted">

                                                Data izin tidak ditemukan

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                            {{-- ================================================== --}}
                            {{-- Pagination --}}
                            {{-- ================================================== --}}
                            <div class="mt-3">

                                {{ $dataizin->appends(request()->all())->links('vendor.pagination.bootstrap-5') }}

                            </div>

                        </div>

                    </div>

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

    });

    $(function () {

        // ==================================================
        // Auto Submit Filter Tanggal
        // ==================================================
        $('input[name="tanggal"]').change(function () {

            $(this).closest('form').submit();

        });

        // ==================================================
        // Auto Submit Filter Unit
        // ==================================================
        $('select[name="unit"]').change(function () {

            $(this).closest('form').submit();

        });

        // ==================================================
        // Auto Submit Filter Jenis Izin
        // ==================================================
        $('select[name="jenis_izin"]').change(function () {

            $(this).closest('form').submit();

        });

    });

    // ==================================================
    // Konfirmasi Hapus
    // ==================================================
    $(".delete-confirm").click(function (e) {

        var form = $(this).closest("form");

        e.preventDefault();

        Swal.fire({

            title: 'Yakin data ini akan dihapus?',
            text: "Data yang sudah dihapus tidak bisa dikembalikan!",
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

    });

</script>

@endpush