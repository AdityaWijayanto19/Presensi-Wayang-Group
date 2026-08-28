@extends('layouts.presensi')

@section('header')

    <div class="appHeader bg-coklat text-light">
        <div class="pageTitle">Data Work From Home</div>
        <div class="right"></div>
    </div>

@endsection

@section('content')

    {{-- Alert --}}
    <div class="flex mt-[70px]">
        <div class="w-full px-2">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp

            @if (Session::get('success'))
                <div class="bg-[#34c759] text-white border border-[#34c759] text-[13px] rounded-md py-1.5 px-4" id="alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif

            @if (Session::get('error'))
                <div class="bg-[#ec4433] text-white border border-[#ec4433] text-[13px] rounded-md py-1.5 px-4">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>

    {{-- Data Work From Home --}}
    <div class="flex">
        <div class="w-full px-2">
            @forelse ($datawfh as $d)
                <ul class="listview image-listview">
                    <li>
                        <div class="item">
                            <div class="in">
                                <div class="flex items-center gap-2.5 w-full">
                                    <ion-icon name="home-outline" class="text-yellow-500 text-[22px]"></ion-icon>
                                    <div class="flex-1 min-w-0">
                                        <b>{{ date('d-m-Y', strtotime($d->tgl_wfh)) }}</b>
                                        <br>
                                        <small>
                                            <a href="/presensi/showfilewfh/{{ $d->file_form }}" target="_blank" class="text-blue-600 no-underline">
                                                Form WFH
                                            </a>
                                        </small>
                                        <br>
                                        <small>
                                            <a href="/presensi/showfilewfh/{{ $d->file_laporan }}" target="_blank" class="text-blue-600 no-underline">
                                                Laporan WFH
                                            </a>
                                        </small>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1 items-end">
                                    <form action="/presensi/deletewfh/{{ $d->id }}" method="POST" class="form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white border-0 p-0">
                                            <ion-icon name="trash-outline" class="text-xs"></ion-icon>
                                        </button>
                                    </form>
                                    <span class="inline-flex items-center justify-center rounded-full bg-green-500 text-white text-[9px] px-2 py-0.5">
                                        Uploaded
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            @empty
                <div class="mt-[120px] text-center text-gray-400">
                    <ion-icon name="document-text-outline" class="text-[70px] text-gray-300"></ion-icon>
                    <h4 class="mt-2 text-coklat">Belum Ada Data WFH</h4>
                    <small>Data WFH akan muncul di sini</small>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Floating Action Button --}}
    <div class="fab-button bottom-right" style="bottom: 80px; right: 35px;">
        <span>Tambah Data</span>
        <a href="/presensi/buatwfh" class="fab bg-coklat text-white">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>

    <script>
        setTimeout(function () {
            let alert = document.getElementById('alert-success');
            if (alert) { alert.style.display = 'none'; }
        }, 3000);

        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data WFH akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7a5234',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) { form.submit(); }
                });
            });
        });
    </script>

@endsection
