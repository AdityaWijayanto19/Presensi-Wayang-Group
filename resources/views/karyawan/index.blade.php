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
                        Data Karyawan
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
                            {{-- Alert --}}
                            {{-- ================================================== --}}
                            @if ($errors->any())

                                <div class="alert alert-danger">

                                    <ul class="mb-0">

                                        @foreach ($errors->all() as $error)

                                            <li>{{ $error }}</li>

                                        @endforeach

                                    </ul>

                                </div>

                            @endif

                            @if (Session::get('error'))

                                <div class="alert alert-danger">

                                    {{ Session::get('error') }}

                                </div>

                            @endif

                            {{-- ================================================== --}}
                            {{-- Button Tambah --}}
                            {{-- ================================================== --}}
                            <div class="mb-3">

                                <a href="#"
                                    class="btn btn-primary"
                                    id="btnTambahkaryawan">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icon-tabler-user-plus">

                                        <path stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none" />

                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />

                                        <path d="M16 19h6" />

                                        <path d="M19 16v6" />

                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />

                                    </svg>

                                    Tambah Data Karyawan

                                </a>

                            </div>

                            {{-- ================================================== --}}
                            {{-- Filter --}}
                            {{-- ================================================== --}}
                            <form
                                action="/karyawan"
                                method="GET">

                                <div class="row g-2 mb-3">

                                    <div class="col-md-6">

                                        <input
                                            type="text"
                                            name="nama_karyawan"
                                            id="nama_karyawan"
                                            class="form-control"
                                            placeholder="Cari Karyawan"
                                            value="{{ Request('nama_karyawan') }}"
                                            autocomplete="off">

                                    </div>

                                    <div class="col-md-4">

                                        <select
                                            name="unit"
                                            id="unit_search"
                                            class="form-select">

                                            <option value="">
                                                Semua Unit
                                            </option>

                                            @foreach ($unitperusahaan as $u)

                                                <option
                                                    value="{{ $u->unit }}"
                                                    {{ Request('unit') == $u->unit ? 'selected' : '' }}>

                                                    {{ $u->unit }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="col-md-2">

                                        <button
                                            type="submit"
                                            class="btn btn-primary w-100">

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

                                                <circle
                                                    cx="10.5"
                                                    cy="10.5"
                                                    r="7.5" />

                                                <line
                                                    x1="21"
                                                    y1="21"
                                                    x2="15.8"
                                                    y2="15.8" />

                                            </svg>

                                            Cari Data

                                        </button>

                                    </div>

                                </div>

                            </form>

                            {{-- ================================================== --}}
                            {{-- Table --}}
                            {{-- ================================================== --}}
                            <div class="table-responsive">

                                <table class="table table-bordered table-hover align-middle">

                                    <thead>

                                        <tr>

                                            <th>No</th>

                                            <th>NIK</th>

                                            <th>Nama</th>

                                            <th>Jabatan</th>

                                            <th>No. HP</th>

                                            <th>Foto</th>

                                            <th>Unit Perusahaan</th>

                                            <th width="170">
                                                Actions
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>
                                                                            
                                    @foreach ($karyawan as $k)
                                    
                                        @php
                                            $path = asset('storage/uploads/karyawan/' . $k->foto);
                                        @endphp
                                    
                                        <tr>
                                    
                                            <td>
                                                {{ $loop->iteration + $karyawan->firstItem() - 1 }}
                                            </td>
                                    
                                            <td>
                                                {{ $k->nik }}
                                            </td>
                                    
                                            <td>
                                                {{ $k->nama_lengkap }}
                                            </td>
                                    
                                            <td>
                                                {{ $k->jabatan }}
                                            </td>
                                    
                                            <td>
                                                {{ $k->no_hp }}
                                            </td>
                                    
                                            {{-- ================================================== --}}
                                            {{-- Foto --}}
                                            {{-- ================================================== --}}
                                            <td>
                                    
                                                @if ($k->foto == 'nophoto.png')
                                    
                                                    <img
                                                        src="{{ asset('assets/img/nophoto.png') }}"
                                                        class="avatar foto-karyawan"
                                                        style="cursor:pointer;"
                                                        alt="Foto Default">
                                    
                                                @else
                                    
                                                    <img
                                                        src="{{ $path }}?v={{ time() }}"
                                                        class="avatar foto-karyawan"
                                                        style="cursor:pointer;"
                                                        alt="{{ $k->nama_lengkap }}">
                                    
                                                @endif
                                    
                                            </td>
                                    
                                            <td>
                                                {{ $k->perusahaan }}
                                            </td>
                                    
                                            {{-- ================================================== --}}
                                            {{-- Actions --}}
                                            {{-- ================================================== --}}
                                            <td>
                                    
                                                <div class="btn-list flex-nowrap">
                                    
                                                    {{-- ================= Edit ================= --}}
                                                    <a href="#"
                                                        class="btn btn-info btn-sm edit"
                                                        nik="{{ $k->nik }}"
                                                        page="{{ request()->get('page',1) }}">
                                    
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            width="18"
                                                            height="18"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon">
                                    
                                                            <path stroke="none"
                                                                d="M0 0h24v24H0z"
                                                                fill="none"/>
                                    
                                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    
                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415"/>
                                    
                                                            <path d="M16 5l3 3"/>
                                    
                                                        </svg>
                                    
                                                    </a>
                                    
                                                    {{-- ================= Reset Password ================= --}}
                                                    <form action="/karyawan/{{ $k->nik }}/resetpassword"
                                                        method="POST"
                                                        class="d-inline">
                                    
                                                        @csrf
                                    
                                                        <button
                                                            type="submit"
                                                            class="btn btn-warning btn-sm reset-password-confirm">
                                    
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                width="18"
                                                                height="18"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="icon">
                                    
                                                                <path stroke="none"
                                                                    d="M0 0h24v24H0z"
                                                                    fill="none"/>
                                    
                                                                <path d="M3.06 13a9 9 0 1 0 .49 -4.087"/>
                                    
                                                                <path d="M3 4.001v5h5"/>
                                    
                                                                <path d="M11 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                                    
                                                            </svg>
                                    
                                                        </button>
                                    
                                                    </form>
                                    
                                                    {{-- ================= Delete ================= --}}
                                                    <form action="/karyawan/{{ $k->nik }}/delete"
                                                        method="POST"
                                                        class="d-inline">
                                    
                                                        @csrf
                                    
                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger btn-sm delete-confirm">
                                    
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                width="18"
                                                                height="18"
                                                                viewBox="0 0 24 24"
                                                                fill="currentColor"
                                                                class="icon">
                                    
                                                                <path stroke="none"
                                                                    d="M0 0h24v24H0z"
                                                                    fill="none"/>
                                    
                                                                <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007zm-10 4a1 1 0 0 0 -1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0 -1 -1m4 0a1 1 0 0 0 -1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0 -1 -1"/>
                                    
                                                                <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005z"/>
                                    
                                                            </svg>
                                    
                                                        </button>
                                    
                                                    </form>
                                    
                                                </div>
                                    
                                            </td>
                                    
                                        </tr>
                                    
                                    @endforeach
                                    
                                    </tbody>
                                    
                                    </table>
                                    
                                    </div>
                                    
                                    <div class="mt-3">
                                    
                                        {{ $karyawan->links('vendor.pagination.bootstrap-5') }}
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    </div>
                                    
                                    {{-- ================================================== --}}
                                    {{-- Modal Tambah Karyawan --}}
                                    {{-- ================================================== --}}
                                    <div
                                        class="modal modal-blur fade"
                                        id="modal-inputkaryawan"
                                        tabindex="-1"
                                        aria-hidden="true">
                                    
                                        <div class="modal-dialog modal-dialog-centered">
                                    
                                            <div class="modal-content">
                                    
                                                <div class="modal-header">
                                    
                                                    <h5 class="modal-title">
                                                        Tambah Data Karyawan
                                                    </h5>
                                    
                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal">
                                                    </button>
                                    
                                                </div>
                                    
                                                <div class="modal-body">

                <form
                    action="/karyawan/store"
                    method="POST"
                    id="formKaryawan"
                    enctype="multipart/form-data">

                    @csrf

                    {{-- NIK --}}
                    <div class="input-icon mb-3">

                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="icon"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <path stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"/>

                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2"/>
                                <path d="M8 13h1v3h-1"/>
                                <path d="M12 13v3"/>
                                <path d="M15 13h1v3h-1"/>

                            </svg>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            name="nik"
                            id="nik"
                            placeholder="NIK"
                            autocomplete="off">

                    </div>

                    {{-- Nama --}}
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
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>

                                </svg>

                        </span>

                        <input
                            type="text"
                            class="form-control"
                            name="nama_lengkap"
                            id="nama_lengkap"
                            placeholder="Nama Lengkap"
                            autocomplete="off">

                    </div>

                    {{-- Jabatan --}}
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
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-device-analytics">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1l0 -10"/>
                                    <path d="M7 20l10 0"/>
                                    <path d="M9 16l0 4"/>
                                    <path d="M15 16l0 4"/>
                                    <path d="M8 12l3 -3l2 2l3 -3"/>

                                </svg>

                        </span>

                        <input
                            type="text"
                            class="form-control"
                            name="jabatan"
                            id="jabatan"
                            placeholder="Jabatan"
                            autocomplete="off">

                    </div>

                    {{-- Unit --}}
                    <div class="mb-3">

                        <select
                            name="unit"
                            id="unit"
                            class="form-select">

                            <option value="">
                                Pilih Unit
                            </option>

                            @foreach ($unitperusahaan as $u)

                                <option value="{{ $u->unit }}">

                                    {{ $u->unit }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Nomor HP --}}
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
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-phone-plus">

                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
                                    <path d="M15 6h6m-3 -3v6"/>

                                </svg>

                        </span>

                        <input
                            type="text"
                            class="form-control"
                            name="no_hp"
                            id="no_hp"
                            placeholder="No. HP"
                            autocomplete="off">

                    </div>

                    {{-- Upload Foto --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Upload Foto
                        </label>

                        <input
                            type="file"
                            name="foto"
                            class="form-control">

                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Simpan Data

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

{{-- ================================================== --}}
{{-- Modal Edit --}}
{{-- ================================================== --}}
<div
    class="modal modal-blur fade"
    id="modal-editkaryawan"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Edit Data Karyawan
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div
                class="modal-body"
                id="loadeditform">

                {{-- Form edit akan dimuat melalui AJAX --}}

            </div>

        </div>

    </div>

</div>

@endsection

@push('myscript')

<script>

$(function () {

    // =====================================================
    // Modal Tambah Karyawan
    // =====================================================

    $("#btnTambahkaryawan").click(function () {

        $("#modal-inputkaryawan").modal("show");

    });


    // =====================================================
    // Modal Edit Karyawan
    // =====================================================

    $(".edit").click(function () {

        let nik = $(this).attr("nik");
        let page = $(this).attr("page");

        $.ajax({

            type: "POST",

            url: "/karyawan/edit",

            data: {
                _token: "{{ csrf_token() }}",
                nik: nik,
                page: page
            },

            cache: false,

            success: function (respond) {

                $("#loadeditform").html(respond);

                $("#modal-editkaryawan").modal("show");

            }

        });

    });


    // =====================================================
    // Preview Foto
    // =====================================================

    $(document).on("click", ".foto-karyawan", function () {

        Swal.fire({

            imageUrl: $(this).attr("src"),

            imageAlt: "Foto Karyawan",

            showConfirmButton: false,

            showCloseButton: true,

            width: "520px",

            backdrop: false

        });

    });


    // =====================================================
    // Reset Password
    // =====================================================

    $(".reset-password-confirm").click(function (e) {

        e.preventDefault();

        let form = $(this).closest("form");

        Swal.fire({

            title: "Reset Password?",

            text: "Password karyawan akan direset menjadi 12345.",

            icon: "warning",

            showCancelButton: true,

            confirmButtonColor: "#3085d6",

            cancelButtonColor: "#d33",

            confirmButtonText: "Ya, Reset!",

            cancelButtonText: "Batal",

            backdrop: false

        }).then((result) => {

            if (result.isConfirmed) {

                form.submit();

            }

        });

    });


    // =====================================================
    // Delete Karyawan
    // =====================================================

    $(".delete-confirm").click(function (e) {

        e.preventDefault();

        let form = $(this).closest("form");

        Swal.fire({

            title: "Yakin data ini akan dihapus?",

            text: "Data karyawan beserta riwayat presensinya akan dihapus permanen.",

            icon: "warning",

            showCancelButton: true,

            confirmButtonColor: "#3085d6",

            cancelButtonColor: "#d33",

            confirmButtonText: "Hapus Data",

            cancelButtonText: "Batal",

            backdrop: false

        }).then((result) => {

            if (result.isConfirmed) {

                form.submit();

            }

        });

    });


    // =====================================================
    // Validasi Form Tambah
    // =====================================================

    $("#formKaryawan").submit(function () {

        let nik = $("#nik").val();

        let nama = $("#nama_lengkap").val();

        let jabatan = $("#jabatan").val();

        let unit = $("#formKaryawan").find("#unit").val();

        let no_hp = $("#no_hp").val();


        if (nik == "") {

            Swal.fire({

                icon: "warning",

                title: "Oops...",

                text: "NIK tidak boleh kosong.",

                backdrop: false

            });

            $("#nik").focus();

            return false;

        }


        if (nama == "") {

            Swal.fire({

                icon: "warning",

                title: "Oops...",

                text: "Nama lengkap tidak boleh kosong.",

                backdrop: false

            });

            $("#nama_lengkap").focus();

            return false;

        }


        if (jabatan == "") {

            Swal.fire({

                icon: "warning",

                title: "Oops...",

                text: "Jabatan tidak boleh kosong.",

                backdrop: false

            });

            $("#jabatan").focus();

            return false;

        }


        if (unit == "") {

            Swal.fire({

                icon: "warning",

                title: "Oops...",

                text: "Unit perusahaan harus dipilih.",

                backdrop: false

            });

            $("#unit").focus();

            return false;

        }


        if (no_hp == "") {

            Swal.fire({

                icon: "warning",

                title: "Oops...",

                text: "Nomor HP tidak boleh kosong.",

                backdrop: false

            });

            $("#no_hp").focus();

            return false;

        }

    });

});

</script>

@endpush