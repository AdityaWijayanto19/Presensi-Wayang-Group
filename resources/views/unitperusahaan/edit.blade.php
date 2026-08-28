<form action="/unitperusahaan/{{ $unitperusahaan->unit }}/update"
      method="POST"
      id="formUnitperusahaan"
      enctype="multipart/form-data">

    @csrf

    {{-- =====================================================
         UNIT
    ====================================================== --}}
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
                              fill="none" />

                        <path d="M8 9l5 5v7h-5v-4m0 4h-5v-7l5 -5m1 1v-6a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v17h-8" />
                        <path d="M13 7l0 .01" />
                        <path d="M17 7l0 .01" />
                        <path d="M17 11l0 .01" />
                        <path d="M17 15l0 .01" />

                    </svg>

                </span>

                <input type="text"
                       name="unit"
                       id="unit"
                       class="form-control"
                       value="{{ $unitperusahaan->unit }}"
                       placeholder="Nama Unit"
                       autocomplete="off">

            </div>

        </div>

    </div>



    {{-- =====================================================
         PERUSAHAAN
    ====================================================== --}}
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
                       value="{{ $unitperusahaan->perusahaan }}"
                       placeholder="Nama Perusahaan"
                       autocomplete="off">

            </div>

        </div>

    </div>



    {{-- =====================================================
         JAM MASUK
    ====================================================== --}}
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
                       value="{{ $unitperusahaan->jam_masuk }}"
                       required>

            </div>

        </div>

    </div>



    {{-- =====================================================
         BUTTON SIMPAN
    ====================================================== --}}
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

                    Perbarui

                </button>

            </div>

        </div>

    </div>

</form>