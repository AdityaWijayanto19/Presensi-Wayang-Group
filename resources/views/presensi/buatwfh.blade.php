@extends('layouts.presensi')

@section('header')

    <div class="appHeader bg-coklat text-light">
        <div class="left">
            <a href="/presensi/wfh" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Kirim Dokumen Work From Home</div>
        <div class="right"></div>
    </div>

@endsection

@section('content')

    <div class="flex mt-[70px]">
        <div class="w-full px-2">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp

            @if(Session::get('success'))
                <div class="bg-[#34c759] text-white border border-[#34c759] text-[13px] rounded-md py-1.5 px-4">{{ $messagesuccess }}</div>
            @endif

            @if(Session::get('error'))
                <div class="bg-[#ec4433] text-white border border-[#ec4433] text-[13px] rounded-md py-1.5 px-4">{{ $messageerror }}</div>
            @endif

            <form method="POST" action="/presensi/storewfh" id="form_wfh" autocomplete="off" enctype="multipart/form-data">
                @csrf

                {{-- Tanggal WFH --}}
                <div class="flex flex-wrap -mx-2 mt-2">
                    <div class="w-full px-2">
                        <div class="form-group">
                            <input type="text" class="form-control datepicker" placeholder="Tanggal Work From Home" name="tgl_wfh" id="tgl_wfh" required>
                        </div>
                    </div>
                </div>

                {{-- Upload Form WFH --}}
                <div class="form-group mt-2">
                    <label class="text-base text-black font-medium">Form WFH</label>
                    <small class="text-red-500 block -mt-1 mb-2">* Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 4 MB)</small>
                    <input type="file" name="file_form" id="file_form" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                </div>

                {{-- Upload Laporan WFH --}}
                <div class="form-group mt-2">
                    <label class="text-base text-black font-medium">Laporan WFH</label>
                    <small class="text-red-500 block -mt-1 mb-2">* Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 4 MB)</small>
                    <input type="file" name="file_laporan" id="file_laporan" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                </div>

                {{-- Submit --}}
                <div class="form-group mt-2">
                    <button class="btn btn-primary w-full">Kirim Data WFH</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('myscript')

<script>
    $(document).ready(function () {
        flatpickr(".datepicker", { dateFormat: "Y-m-d" });

        $("#form_wfh").submit(function (e) {
            e.preventDefault();

            var tgl_wfh = $("#tgl_wfh").val();
            var file_form = $("#file_form")[0].files[0];
            var file_laporan = $("#file_laporan")[0].files[0];

            if (tgl_wfh == "") {
                Swal.fire({ title: 'Error!', icon: 'warning', text: 'Tanggal Work From Home harus diisi!', confirmButtonColor: '#7a5234' });
                return false;
            } else if (!file_form) {
                Swal.fire({ title: 'Error!', icon: 'warning', text: 'Form WFH harus diupload!', confirmButtonColor: '#7a5234' });
                return false;
            } else if (!file_laporan) {
                Swal.fire({ title: 'Error!', icon: 'warning', text: 'Laporan WFH harus diupload!', confirmButtonColor: '#7a5234' });
                return false;
            } else if (file_form.size > 4 * 1024 * 1024) {
                Swal.fire({ title: 'Error!', icon: 'warning', text: 'Ukuran Form WFH maksimal 4 MB!', confirmButtonColor: '#7a5234' });
                return false;
            } else if (file_laporan.size > 4 * 1024 * 1024) {
                Swal.fire({ title: 'Error!', icon: 'warning', text: 'Ukuran Laporan WFH maksimal 4 MB!', confirmButtonColor: '#7a5234' });
                return false;
            }

            Swal.fire({
                title: 'Kirim Data WFH?',
                text: 'Pastikan data yang dikirim sudah benar!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7a5234',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { $("#form_wfh")[0].submit(); }
            });
        });
    });
</script>

@endpush
