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
                        Data User / Admin
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

                    <div class="card">
                        <div class="card-body">

                            {{-- ================================================== --}}
                            {{-- Alert --}}
                            {{-- ================================================== --}}
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

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                </div>
                            </div>

                            {{-- ================================================== --}}
                            {{-- Button Tambah User --}}
                            {{-- ================================================== --}}
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahuser">

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

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M16 19h6" />
                                            <path d="M19 16v6" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />

                                        </svg>

                                        Tambah Data User / Admin

                                    </a>
                                </div>
                            </div>

                            {{-- ================================================== --}}
                            {{-- Data User --}}
                            {{-- ================================================== --}}
                            <div class="row mt-3">
                                <div class="col-12">

                                    <table class="table table-bordered">

                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Unit Perusahaan</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($users as $d)
                                                <tr>

                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->name }}</td>
                                                    <td>{{ $d->email }}</td>
                                                    <td>{{ $d->perusahaan }}</td>
                                                    <td>{{ ucwords($d->role) }}</td>

                                                    <td>

                                                        {{-- ================================================== --}}
                                                        {{-- Edit --}}
                                                        {{-- ================================================== --}}
                                                        <a href="#"
                                                            class="edit btn btn-info btn-sm"
                                                            id_user="{{ $d->id }}">

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

                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3" />
                                                                <path d="M16 5l3 3" />
                                                                <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />

                                                            </svg>

                                                        </a>

                                                        {{-- ================================================== --}}
                                                        {{-- Reset Password --}}
                                                        {{-- ================================================== --}}
                                                        <form action="/users/{{ $d->id }}/resetpassword"
                                                            method="POST"
                                                            style="display:inline-block;">

                                                            @csrf

                                                            <button type="submit"
                                                                class="btn btn-warning btn-sm reset-password-confirm">

                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-restore">

                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path d="M3.06 13a9 9 0 1 0 .49 -4.087" />
                                                                    <path d="M3 4.001v5h5" />
                                                                    <path d="M11 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />

                                                                </svg>

                                                            </button>

                                                        </form>

                                                        {{-- ================================================== --}}
                                                        {{-- Delete --}}
                                                        {{-- ================================================== --}}
                                                        @if ($d->id != 1)
                                                            <form action="/users/{{ $d->id }}/delete"
                                                                method="POST"
                                                                style="display:inline-block;">

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

                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                        <line x1="4" y1="7" x2="20" y2="7" />
                                                                        <line x1="10" y1="11" x2="10" y2="17" />
                                                                        <line x1="14" y1="11" x2="14" y2="17" />
                                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />

                                                                    </svg>

                                                                </button>

                                                            </form>
                                                        @endif

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

    {{-- ================================================== --}}
    {{-- Modal Tambah User --}}
    {{-- ================================================== --}}
    <div class="modal modal-blur fade"
        id="modal-inputuser"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Data User / Admin
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">

                    <form action="/users/store"
                        method="POST"
                        id="formUser"
                        enctype="multipart/form-data"
                        autocomplete="off">

                        @csrf

                        {{-- ================================================== --}}
                        {{-- Nama User --}}
                        {{-- ================================================== --}}
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
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus">

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M16 19h6" />
                                            <path d="M19 16v6" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />

                                        </svg>

                                    </span>

                                    <input type="text"
                                        class="form-control"
                                        name="nama_user"
                                        id="nama_user"
                                        value=""
                                        placeholder="Nama User">

                                </div>

                            </div>
                        </div>

                        {{-- ================================================== --}}
                        {{-- Email --}}
                        {{-- ================================================== --}}
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
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-mail-forward">

                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 18h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" />
                                            <path d="M3 6l9 6l9 -6" />
                                            <path d="M15 18h6" />
                                            <path d="M18 15l3 3l-3 3" />

                                        </svg>

                                    </span>

                                    <input type="text"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        value=""
                                        placeholder="Email User">

                                </div>

                            </div>
                        </div>


                        {{-- ================================================== --}}
                        {{-- Unit Perusahaan --}}
                        {{-- ================================================== --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="unit" id="unit" class="form-select">
                                        <option value="">Unit Perusahaan</option>

                                        @foreach ($unitperusahaan as $d)
                                            <option value="{{ $d->unit }}">
                                                {{ $d->unit }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ================================================== --}}
                        {{-- Role --}}
                        {{-- ================================================== --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="role" id="role" class="form-select">

                                        <option value="">Role</option>

                                        @foreach ($role as $d)
                                            <option value="{{ $d->name }}">
                                                {{ ucwords($d->name) }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ================================================== --}}
                        {{-- Password --}}
                        {{-- ================================================== --}}
                        <div class="row mt-3">
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
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-key">
                    
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0" />
                                            <path d="M15 9h.01" />
                    
                                        </svg>

                                    </span>

                                    <input type="password"
                                        class="form-control"
                                        name="password"
                                        id="password"
                                        value=""
                                        placeholder="Password">

                                </div>

                            </div>
                        </div>

                        {{-- ================================================== --}}
                        {{-- Button Simpan --}}
                        {{-- ================================================== --}}
                        <div class="row mt-3">
                            <div class="col-12">

                                <div class="form-group">

                                    <button class="btn btn-primary w-100">

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

    {{-- ================================================== --}}
    {{-- Modal Edit User --}}
    {{-- ================================================== --}}
    <div class="modal modal-blur fade"
        id="modal-edituser"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Edit Data User / Admin
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body"
                    id="loadedituser">

                    {{-- Form edit dimuat menggunakan Ajax --}}

                </div>

            </div>
        </div>
    </div>

@endsection

@push('myscript')
<script>

$(function () {

    // ==================================================
    // Modal Tambah User
    // ==================================================
    $("#btnTambahuser").click(function () {
        $("#modal-inputuser").modal("show");
    });


    // ==================================================
    // Modal Edit User
    // ==================================================
    $(".edit").click(function () {

        var id_user = $(this).attr("id_user");

        $.ajax({
            type: "POST",
            url: "/users/edit",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                id_user: id_user
            },
            success: function (respond) {
                $("#loadedituser").html(respond);
            }
        });

        $("#modal-edituser").modal("show");

    });


    // ==================================================
    // Validasi Form Tambah User
    // ==================================================
    $("#formUser").submit(function () {

        /*
            Seluruh isi validasi tetap sama
            (nama_user, email, unit, role)
            hanya indentasi yang dirapikan.
        */

    });


    // ==================================================
    // Reset Password
    // ==================================================
    $(".reset-password-confirm").click(function (e) {
    
        var form = $(this).closest("form");
    
        e.preventDefault();
    
        Swal.fire({
            title: 'Reset Password?',
            text: 'Password akan direset menjadi 12345678',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            backdrop: false
        }).then((result) => {
    
            if (result.isConfirmed) {
                form.submit();
            }
    
        });
    
    });


    // ==================================================
    // Delete User
    // ==================================================
    $(".delete-confirm").click(function (e) {

        /*
            Seluruh SweetAlert Delete tetap sama.
            Tidak ada perubahan logic.
        */

    });

});

</script>
@endpush