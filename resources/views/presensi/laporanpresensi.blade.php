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
                        Laporan Presensi
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

            <div class="row justify-content-center">

                <div class="col-lg-10">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <form action="/presensi/cetaklaporan" method="POST">

                                @csrf

                                {{-- ================================================== --}}
                                {{-- Bulan --}}
                                {{-- ================================================== --}}
                                <div class="row justify-content-center">

                                    <div class="col-12">

                                        <div class="form-group">

                                            <select name="bulan"
                                                id="bulan"
                                                class="form-select">

                                                <option value="">
                                                    Bulan
                                                </option>

                                                @for ($i = 1; $i <= 12; $i++)

                                                    <option value="{{ $i }}"
                                                        {{ date('m') == $i ? 'selected' : '' }}>

                                                        {{ $namabulan[$i] }}

                                                    </option>

                                                @endfor

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                {{-- ================================================== --}}
                                {{-- Tahun --}}
                                {{-- ================================================== --}}
                                <div class="row mt-2">

                                    <div class="col-12">

                                        <div class="form-group">

                                            <select name="tahun"
                                                id="tahun"
                                                class="form-select">

                                                <option value="">
                                                    Tahun
                                                </option>

                                                @php
                                                    $tahunmulai = 2025;
                                                    $tahunskrg = date('Y');
                                                @endphp

                                                @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++)

                                                    <option value="{{ $tahun }}"
                                                        {{ date('Y') == $tahun ? 'selected' : '' }}>

                                                        {{ $tahun }}

                                                    </option>

                                                @endfor

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                {{-- ================================================== --}}
                                {{-- Unit Perusahaan --}}
                                {{-- ================================================== --}}
                                <div class="row mt-2">

                                    <div class="col-12">

                                        <div class="form-group">

                                            <select name="unit"
                                                id="unit"
                                                class="form-select">

                                                <option value="">
                                                    Pilih Unit Perusahaan
                                                </option>

                                                @foreach ($unit as $u)

                                                    <option value="{{ $u->unit }}">
                                                        {{ $u->perusahaan }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                {{-- ================================================== --}}
                                {{-- Karyawan --}}
                                {{-- ================================================== --}}
                                <div class="row mt-2">

                                    <div class="col-12">

                                        <div class="form-group">

                                            <select name="nik"
                                                id="nik"
                                                class="form-select">

                                                <option value="">
                                                    Pilih Karyawan
                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                {{-- ================================================== --}}
                                {{-- Button Download --}}
                                {{-- ================================================== --}}
                                <div class="row mt-2">

                                    <div class="col-12">

                                        <div class="form-group">

                                            <button type="submit"
                                                name="cetak"
                                                class="btn btn-primary w-100">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-printer">

                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                    <path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" />

                                                </svg>

                                                Download PDF!

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </form>

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
        // Get Karyawan Berdasarkan Unit
        // ==================================================
        $("#unit").change(function () {

            var unit = $(this).val();

            $.ajax({

                type: 'POST',

                url: '/getkaryawanbyunit',

                data: {
                    _token: "{{ csrf_token() }}",
                    unit: unit
                },

                cache: false,

                success: function (res) {

                    $("#nik").empty();

                    $("#nik").append(
                        '<option value="">Pilih Karyawan</option>'
                    );

                    $.each(res, function (index, item) {

                        $("#nik").append(
                            '<option value="' + item.nik + '">' +
                            item.nama_lengkap +
                            '</option>'
                        );

                    });

                }

            });

        });

    });

</script>

@endpush