@extends('layouts.admin.tabler')

@section('content')

{{-- =====================================================
     PAGE HEADER
===================================================== --}}

<div class="page-header d-print-none">

    <div class="container-xl">

        <div class="row g-2 align-items-center">

            <div class="col">

                <div class="page-pretitle">
                    WAG - Presensi Digital
                </div>

                <h2 class="page-title">
                    Data Unit Perusahaan
                </h2>

            </div>

        </div>

    </div>

</div>



{{-- =====================================================
     PAGE BODY
===================================================== --}}

<div class="page-body">

    <div class="container-xl">

        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-body">

                        {{-- =====================================================
                             ALERT
                        ===================================================== --}}

                        <div class="row">

                            <div class="col-12">

                                @if (Session::get('success'))

                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>

                                @endif

                                @if (Session::get('error'))

                                    <div class="alert alert-danger">
                                        {{ Session::get('error') }}
                                    </div>

                                @endif

                            </div>

                        </div>



                        {{-- =====================================================
                             BUTTON TAMBAH DATA
                        ===================================================== --}}

                        <div class="row">

                            <div class="col-12">

                                <a href="#"
                                   class="btn btn-primary"
                                   id="btnTambahunitperusahaan">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="24"
                                         height="24"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2"
                                         stroke-linecap="round"
                                         stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none"/>

                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>

                                        <path d="M16 19h6"/>

                                        <path d="M19 16v6"/>

                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4"/>

                                    </svg>

                                    Tambah Data Unit Perusahaan

                                </a>

                            </div>

                        </div>



                        {{-- =====================================================
                             TABEL DATA
                        ===================================================== --}}

                        <div class="row mt-3">

                            <div class="col-12">

                                <table class="table table-bordered">

                                    <thead>

                                        <tr>

                                            <th>No</th>
                                            <th>Unit</th>
                                            <th>Perusahaan</th>
                                            <th>Jam Masuk</th>
                                            <th>Actions</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach ($unitperusahaan as $u)

                                            <tr>

                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $u->unit }}</td>

                                                <td>{{ $u->perusahaan }}</td>

                                                <td>
                                                    {{ $u->jam_masuk ? date('H:i', strtotime($u->jam_masuk)) : '-' }}
                                                </td>

                                                <td>

                                                    {{-- Edit --}}
                                                    <a href="#"
                                                       class="edit btn btn-info btn-sm"
                                                       unit="{{ $u->unit }}">

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             width="24"
                                                             height="24"
                                                             viewBox="0 0 24 24"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             stroke-width="2"
                                                             stroke-linecap="round"
                                                             stroke-linejoin="round"
                                                             class="icon icon-tabler icons-tabler-outline icon-tabler-edit-circle">

                                                            <path stroke="none"
                                                                  d="M0 0h24v24H0z"
                                                                  fill="none"/>

                                                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3"/>

                                                            <path d="M16 5l3 3"/>

                                                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6"/>

                                                        </svg>

                                                    </a>

                                                    {{-- Delete --}}
                                                    <form action="/unitperusahaan/{{ $u->unit }}/delete"
                                                          method="POST"
                                                          class="d-inline">

                                                        @csrf

                                                        <button type="submit"
                                                                class="delete-confirm btn btn-danger btn-sm">

                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 width="24"
                                                                 height="24"
                                                                 viewBox="0 0 24 24"
                                                                 fill="none"
                                                                 stroke="currentColor"
                                                                 stroke-width="2"
                                                                 stroke-linecap="round"
                                                                 stroke-linejoin="round"
                                                                 class="icon icon-tabler icons-tabler-outline icon-tabler-trash">

                                                                <path stroke="none"
                                                                      d="M0 0h24v24H0z"
                                                                      fill="none"/>

                                                                <line x1="4" y1="7" x2="20" y2="7"/>

                                                                <line x1="10" y1="11" x2="10" y2="17"/>

                                                                <line x1="14" y1="11" x2="14" y2="17"/>

                                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>

                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>

                                                            </svg>

                                                        </button>

                                                    </form>

                                                </td>

                                            </tr>

                                        @endforeach

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

{{-- =====================================================
     MODAL TAMBAH DATA UNIT PERUSAHAAN
===================================================== --}}

<div class="modal modal-blur fade"
     id="modal-inputunitperusahaan"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Tambah Data Unit Perusahaan
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <form action="/unitperusahaan/store"
                      method="POST"
                      id="formUnitperusahaan"
                      enctype="multipart/form-data">

                    @csrf


                    {{-- =====================================================
                         NAMA UNIT
                    ===================================================== --}}

                    <div class="row">

                        <div class="col-12">

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
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-building-community">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none"/>

                                        <path d="M8 9l5 5v7h-5v-4m0 4h-5v-7l5 -5m1 1v-6a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v17h-8"/>

                                        <path d="M13 7l0 .01"/>

                                        <path d="M17 7l0 .01"/>

                                        <path d="M17 11l0 .01"/>

                                        <path d="M17 15l0 .01"/>

                                    </svg>

                                </span>

                                <input type="text"
                                       name="unit"
                                       id="unit"
                                       class="form-control"
                                       placeholder="Nama Unit"
                                       autocomplete="off">

                            </div>

                        </div>

                    </div>



                    {{-- =====================================================
                         NAMA PERUSAHAAN
                    ===================================================== --}}

                    <div class="row">

                        <div class="col-12">

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
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-buildings">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none"/>

                                        <path d="M4 21v-15c0 -1 1 -2 2 -2h5c1 0 2 1 2 2v15"/>

                                        <path d="M16 8h2c1 0 2 1 2 2v11"/>

                                        <path d="M3 21h18"/>

                                        <path d="M10 12v.01"/>

                                        <path d="M10 16v.01"/>

                                        <path d="M10 8v.01"/>

                                        <path d="M7 12v.01"/>

                                        <path d="M7 16v.01"/>

                                        <path d="M7 8v.01"/>

                                        <path d="M17 12v.01"/>

                                        <path d="M17 16v.01"/>

                                    </svg>

                                </span>

                                <input type="text"
                                       name="perusahaan"
                                       id="perusahaan"
                                       class="form-control"
                                       placeholder="Nama Perusahaan"
                                       autocomplete="off">

                            </div>

                        </div>

                    </div>



                    {{-- =====================================================
                         JAM MASUK
                    ===================================================== --}}

                    <div class="row">

                        <div class="col-12">

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
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none"/>

                                        <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06"/>

                                        <path d="M12 7v5l2 2"/>

                                        <path d="M19 22v.01"/>

                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"/>

                                    </svg>

                                </span>

                                <input type="time"
                                       name="jam_masuk"
                                       id="jam_masuk"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                    </div>



                    {{-- =====================================================
                         BUTTON SIMPAN
                    ===================================================== --}}

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="form-group">

                                <button class="btn btn-primary w-100">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="24"
                                         height="24"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2"
                                         stroke-linecap="round"
                                         stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-message-forward">

                                        <path stroke="none"
                                              d="M0 0h24v24H0z"
                                              fill="none"/>

                                        <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12"/>

                                        <path d="M13 8l3 3l-3 3"/>

                                        <path d="M16 11h-8"/>

                                    </svg>

                                    Simpan

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

{{-- =====================================================
     MODAL EDIT DATA UNIT PERUSAHAAN
===================================================== --}}

<div class="modal modal-blur fade"
     id="modal-editunitperusahaan"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Edit Data Unit Perusahaan
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body"
                 id="loadeditform">

                {{-- Form Edit akan dimuat melalui AJAX --}}

            </div>

        </div>

    </div>

</div>

@endsection



@push('myscript')

<script>

$(function () {

    /*
    |--------------------------------------------------------------------------
    | Modal Tambah Data
    |--------------------------------------------------------------------------
    */

    $("#btnTambahunitperusahaan").click(function () {

        $("#modal-inputunitperusahaan").modal("show");

    });



    /*
    |--------------------------------------------------------------------------
    | Modal Edit Data
    |--------------------------------------------------------------------------
    */

    $(".edit").click(function () {

        let unit = $(this).attr("unit");

        $.ajax({

            type: "POST",

            url: "/unitperusahaan/edit",

            cache: false,

            data: {

                _token: "{{ csrf_token() }}",

                unit: unit

            },

            success: function (respond) {

                $("#loadeditform").html(respond);

            }

        });

        $("#modal-editunitperusahaan").modal("show");

    });



    /*
    |--------------------------------------------------------------------------
    | Konfirmasi Hapus Data
    |--------------------------------------------------------------------------
    */

    $(".delete-confirm").click(function (e) {

        let form = $(this).closest("form");

        e.preventDefault();

        Swal.fire({

            title: "Yakin data ini akan dihapus?",

            text: "Data yang sudah dihapus tidak bisa dikembalikan!",

            icon: "warning",

            showCancelButton: true,

            confirmButtonColor: "#3085d6",

            cancelButtonColor: "#d33",

            confirmButtonText: "Hapus Data",

            backdrop: false

        }).then((result) => {

            if (result.isConfirmed) {

                form.submit();

            }

        });

    });



    /*
    |--------------------------------------------------------------------------
    | Validasi Form Tambah Unit Perusahaan
    |--------------------------------------------------------------------------
    */

    $("#formUnitperusahaan").submit(function () {

        let unit = $("#unit").val();

        let perusahaan = $("#perusahaan").val();

        if (unit == "") {

            Swal.fire({

                title: "Oops!",

                text: "Unit tidak boleh kosong",

                icon: "warning",

                confirmButtonText: "OK",

                backdrop: false

            }).then(() => {

                $("#unit").focus();

            });

            return false;

        }

    });

});

</script>

@endpush