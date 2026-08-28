@extends('layouts.presensi')

@php

    use Illuminate\Support\Facades\Storage;

    $user = Auth::guard('karyawan')->user();
    $nama = explode(' ', $user->nama_lengkap);
    $namaPendek = implode(' ', array_slice($nama, 0, 2));
    $pathFoto = Storage::url('uploads/karyawan/' . $user->foto);

@endphp


@section('content')

{{-- HEADER USER --}}
<div class="section" id="user-section" style="height:220px;background-color:#7a5234;padding:20px;position:relative;margin-top:-60px;padding-top:80px;">

    <a href="#" class="absolute text-white text-[30px] no-underline right-[15px] hover:text-[#bdb4b4]" id="btnlogout">
        <ion-icon name="exit"></ion-icon>
    </a>

    <div class="mt-5 flex">
        <div class="avatar">
            @if ($user->foto != null)
                <img src="{{ url($pathFoto) }}?v={{ time() }}" alt="avatar" class="w-16 h-16 object-cover object-[center_15%] rounded-full">
            @else
                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="w-16 h-16 object-cover object-[center_15%] rounded-full">
            @endif
        </div>
        <div class="ml-[30px] leading-[2px]">
            <h2 class="text-white" id="user-name">{{ $namaPendek }}</h2>
            <span class="text-white" id="user-role">{{ Auth::guard('karyawan')->user()->jabatan }}</span>
        </div>
    </div>

</div>



{{-- REKAP PRESENSI --}}
<div class="section px-4" id="presence-section" style="margin-top:-30px;width:100%;background-color:#e9ecef;border-radius:15px 15px 0 0;position:relative;z-index:2;">

    <br>

    <h3>Rekap Presensi Bulan {{ $namabulan[$bulanini] }} {{ $tahunini }}</h3>

    <div id="rekappresensi" class="mt-2">
        <div class="flex flex-wrap -mx-2">

            {{-- Hadir --}}
            <div class="w-1/2 sm:w-1/4 px-2 mb-2">
                <div class="card text-center py-3 px-2 rounded-[10px] h-full relative overflow-hidden">
                    <div class="p-3">
                        <ion-icon name="accessibility-outline" class="text-green-500 text-[28px] mb-1"></ion-icon>
                        <br>
                        <span class="text-center text-xs font-bold block mt-1 leading-[1.2]">Hadir</span>
                    </div>
                    @if ($rekappresensi->jmlhadir > 0)
                        <span class="absolute bottom-0 left-0 bg-green-500/90 text-white text-base font-bold px-2.5 py-0.5 rounded-tr-lg">
                            {{ $rekappresensi->jmlhadir }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- WFH --}}
            <div class="w-1/2 sm:w-1/4 px-2 mb-2">
                <div class="card text-center py-3 px-2 rounded-[10px] h-full relative overflow-hidden">
                    <div class="p-3">
                        <ion-icon name="home-outline" class="text-blue-500 text-[28px] mb-1"></ion-icon>
                        <br>
                        <span class="text-center text-xs font-bold block mt-1 leading-[1.2]">WFH</span>
                    </div>
                    @if ($rekapwfh->jmlwfh > 0)
                        <span class="absolute bottom-0 left-0 bg-blue-500/90 text-white text-base font-bold px-2.5 py-0.5 rounded-tr-lg">
                            {{ $rekapwfh->jmlwfh }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Lembur --}}
            <div class="w-1/2 sm:w-1/4 px-2 mb-2">
                <div class="card text-center py-3 px-2 rounded-[10px] h-full relative overflow-hidden">
                    <div class="p-3">
                        <ion-icon name="hourglass-outline" class="text-yellow-500 text-[28px] mb-1"></ion-icon>
                        <br>
                        <span class="text-center text-xs font-bold block mt-1 leading-[1.2]">Lembur</span>
                    </div>
                    @if (($rekaplembur->jmllembur ?? 0) > 0)
                        <span class="absolute bottom-0 left-0 bg-yellow-500/90 text-white text-base font-bold px-2.5 py-0.5 rounded-tr-lg">
                            {{ $rekaplembur->jmllembur }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Izin / Sakit --}}
            <div class="w-1/2 sm:w-1/4 px-2 mb-2">
                <div class="card text-center py-3 px-2 rounded-[10px] h-full relative overflow-hidden">
                    <div class="p-3">
                        <ion-icon name="document-text-outline" class="text-red-500 text-[28px] mb-1"></ion-icon>
                        <br>
                        <span class="text-center text-xs font-bold block mt-1 leading-[1.2]">Izin / Sakit</span>
                    </div>
                    @if ($rekapizin->jmlizin > 0)
                        <span class="absolute bottom-0 left-0 bg-red-500/90 text-white text-base font-bold px-2.5 py-0.5 rounded-tr-lg">
                            {{ $rekapizin->jmlizin }}
                        </span>
                    @endif
                </div>
            </div>

        </div>
    </div>



    {{-- PRESENSI HARI INI --}}
    <div class="mt-6 sm:mt-8 mb-5">
        <div class="flex flex-wrap -mx-2">

            {{-- Presensi Masuk --}}
            <div class="w-1/2 px-2">
                <div class="card text-white bg-gradient-to-br from-green-500 to-green-500/80">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-[44px] h-[44px] sm:w-[50px] sm:h-[50px] flex-shrink-0 flex items-center justify-center">
                                @if ($presensihariini != null)
                                    @php
                                        $path = Storage::url('/uploads/absensi/' . $presensihariini->foto_in);
                                    @endphp
                                    <img src="{{ url($path) }}?v={{ time() }}" alt="" class="w-[44px] h-[44px] sm:w-[50px] sm:h-[50px] object-cover rounded-xl">
                                @else
                                    <ion-icon name="camera" class="text-[26px] sm:text-[30px]"></ion-icon>
                                @endif
                            </div>
                            <div class="leading-[1.3] min-w-0">
                                <h4 class="text-white font-semibold text-sm sm:text-base mb-1">Masuk</h4>
                                <span class="text-[11px] sm:text-[13px] block break-words">
                                    {{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Presensi' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Presensi Pulang --}}
            <div class="w-1/2 px-2">
                <div class="card text-white bg-gradient-to-br from-red-600 to-red-600/80">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-[44px] h-[44px] sm:w-[50px] sm:h-[50px] flex-shrink-0 flex items-center justify-center">
                                @if ($presensihariini != null && $presensihariini->jam_out != null)
                                    @php
                                        $path = Storage::url('/uploads/absensi/' . $presensihariini->foto_out);
                                    @endphp
                                    <img src="{{ url($path) }}?v={{ time() }}" alt="" class="w-[44px] h-[44px] sm:w-[50px] sm:h-[50px] object-cover rounded-xl">
                                @else
                                    <ion-icon name="camera" class="text-[26px] sm:text-[30px]"></ion-icon>
                                @endif
                            </div>
                            <div class="leading-[1.3] min-w-0">
                                <h4 class="text-white font-semibold text-sm sm:text-base mb-1">Pulang</h4>
                                <span class="text-[11px] sm:text-[13px] block break-words">
                                    {{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Presensi' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- HISTORI PRESENSI --}}
    <div class="mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Bulan Ini
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content mt-2 mb-24">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach ($historibulanini as $d)
                        @php
                            $path = Storage::url('uploads/absensi/' . $d->foto_in);
                        @endphp
                        <li>
                            <div class="item">
                                <img src="{{ url($path) }}?v={{ time() }}" alt="" class="w-[35px] h-[35px] rounded-[10px] object-cover mr-3 border-2 border-white shadow-sm foto-histori-dashboard flex-shrink-0">
                                <div class="in flex-wrap gap-1">
                                    <div class="w-full text-[13px]">
                                        {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}
                                    </div>
                                    <span class="inline-flex items-center justify-center rounded-full text-white text-[10px] sm:text-xs px-2 py-0.5 {{ $d->terlambat > 0 ? 'bg-red-500' : 'bg-green-500' }}">
                                        {{ $d->jam_in }}
                                    </span>
                                    <span class="inline-flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] sm:text-xs px-2 py-0.5">
                                        {{ $d->jam_out != null ? $d->jam_out : 'Belum Presensi' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</div>



{{-- LOGOUT --}}
<script>
    document.getElementById('btnlogout').addEventListener('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: 'Anda akan keluar dari aplikasi!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#9c6b43',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/proseslogout";
            }
        });
    });
</script>



{{-- PREVIEW FOTO --}}
<script>
    document.querySelectorAll('.foto-presensi, .foto-histori-dashboard').forEach(function (foto) {
        foto.addEventListener('click', function () {
            Swal.fire({
                html: `<img src="${this.src}" style="width:100%;height:100%;border-radius:12px;display:block;">`,
                showConfirmButton: false,
                showCloseButton: true,
                width: '390px',
                padding: '10px',
                background: 'transparent'
            });
        });
    });
</script>

@endsection
