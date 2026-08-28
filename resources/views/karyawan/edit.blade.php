<form action="/karyawan/{{ $karyawan->nik }}/update"
      method="POST"
      id="formKaryawan"
      enctype="multipart/form-data">

    @csrf

    <input
        type="hidden"
        name="page"
        value="{{ $page }}">


    {{-- =====================================================
         DATA IDENTITAS
    ===================================================== --}}

    {{-- NIK --}}
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
                         class="icon icon-tabler icons-tabler-outline icon-tabler-file-barcode">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2"/>
                        <path d="M8 13h1v3h-1l0 -3"/>
                        <path d="M12 13v3"/>
                        <path d="M15 13h1v3h-1l0 -3"/>

                    </svg>

                </span>

                <input
                    type="text"
                    readonly
                    name="nik"
                    id="nik"
                    class="form-control"
                    value="{{ $karyawan->nik }}"
                    placeholder="NIK"
                    autocomplete="off">

            </div>

        </div>

    </div>


    {{-- Nama Lengkap --}}
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
                         class="icon icon-tabler icons-tabler-outline icon-tabler-user">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>

                    </svg>

                </span>

                <input
                    type="text"
                    name="nama_lengkap"
                    id="nama_lengkap"
                    class="form-control"
                    value="{{ $karyawan->nama_lengkap }}"
                    placeholder="Nama Lengkap"
                    autocomplete="off">

            </div>

        </div>

    </div>


    {{-- Jabatan --}}
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
                    name="jabatan"
                    id="jabatan"
                    class="form-control"
                    value="{{ $karyawan->jabatan }}"
                    placeholder="Jabatan"
                    autocomplete="off">

            </div>

        </div>

    </div>


    {{-- =====================================================
         UNIT PERUSAHAAN
    ===================================================== --}}

    <div class="row mb-3">

        <div class="col-12">

            <select
                name="unit"
                id="unit"
                class="form-select">

                <option value="">Pilih Unit</option>

                @foreach ($unitperusahaan as $u)

                    <option
                        value="{{ $u->unit }}"
                        {{ $karyawan->unit == $u->unit ? 'selected' : '' }}>

                        {{ $u->unit }}

                    </option>

                @endforeach

            </select>

        </div>

    </div>


    {{-- =====================================================
         DATA KONTAK
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
                         class="icon icon-tabler icons-tabler-outline icon-tabler-phone-plus">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
                        <path d="M15 6h6m-3 -3v6"/>

                    </svg>

                </span>

                <input
                    type="text"
                    name="no_hp"
                    id="no_hp"
                    class="form-control"
                    value="{{ $karyawan->no_hp }}"
                    placeholder="No. HP"
                    autocomplete="off">

            </div>

        </div>

    </div>


    {{-- =====================================================
         FOTO PROFIL
    ===================================================== --}}

    <div class="row mt-2">

        <div class="col-12">

            <div class="form-label">

                Upload Foto

            </div>

            <input
                type="file"
                name="foto"
                class="form-control">

            <input
                type="hidden"
                name="foto_lama"
                value="{{ $karyawan->foto }}">

        </div>

    </div>


    {{-- =====================================================
         PASSWORD
    ===================================================== --}}

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
                         class="icon icon-tabler icon-tabler-key">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0"/>
                        <path d="M15 9h.01"/>

                    </svg>

                </span>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Kosongkan jika password tidak diubah">

            </div>

        </div>

    </div>


    {{-- =====================================================
         TOMBOL SIMPAN
    ===================================================== --}}

    <div class="row mt-3">

        <div class="col-12">

            <div class="form-group">

                <button
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
                         class="icon icon-tabler icons-tabler-outline icon-tabler-device-desktop-down">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M13.5 16h-9.5a1 1 0 0 1 -1 -1v-10a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v7.5"/>
                        <path d="M7 20h5"/>
                        <path d="M9 16v4"/>
                        <path d="M19 16v6"/>
                        <path d="M22 19l-3 3l-3 -3"/>

                    </svg>

                    Perbarui Data

                </button>

            </div>

        </div>

    </div>

</form>