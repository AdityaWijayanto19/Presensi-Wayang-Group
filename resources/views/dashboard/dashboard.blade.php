@extends('layouts.presensi')

@php

    use Illuminate\Support\Facades\Storage;

    $user = Auth::guard('karyawan')->user();
    $nama = explode(' ', $user->nama_lengkap);
    $namaPendek = implode(' ', array_slice($nama, 0, 2));
    $pathFoto = Storage::url('uploads/karyawan/' . $user->foto);

@endphp


@section('content')

{{-- HEADER USER — SVG Pattern (exact code as provided) --}}
<div class="section overflow-hidden" id="user-section" style="height:220px;padding:20px;position:relative;margin-top:-60px;padding-top:80px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="absolute inset-0 w-full h-full" aria-hidden="true"><defs><pattern id="a" width="35.584" height="30.585" patternTransform="rotate(10)" patternUnits="userSpaceOnUse"><rect width="100%" height="100%" fill="#795234"/><path fill="#65452f" d="M36.908 9.243c-5.014 0-7.266 3.575-7.266 7.117 0 3.376 2.45 5.726 5.959 5.726 1.307 0 2.45-.463 3.244-1.307.744-.811 1.125-1.903 1.042-3.095-.066-.811-.546-1.655-1.274-2.185-.596-.447-1.639-.894-3.162-.546a.87.87 0 0 0-.662 1.06c.1.48.58.777 1.06.661.695-.149 1.274-.066 1.705.249.364.265.546.645.562.893.05.679-.165 1.308-.579 1.755-.446.48-1.125.744-1.936.744-2.55 0-4.188-1.538-4.188-3.938 0-2.466 1.44-5.347 5.495-5.347 2.897 0 6.008 1.888 6.388 6.058.166 1.804.067 5.147-2.598 7.034a1 1 0 0 0-.142.122c-1.311.783-2.87 1.301-4.972 1.301-4.088 0-6.123-1.952-8.275-4.021-2.317-2.218-4.7-4.518-9.517-4.518-4.094 0-6.439 1.676-8.479 3.545.227-1.102.289-2.307.17-3.596-.496-5.263-4.567-7.662-8.159-7.662-5.015 0-7.265 3.574-7.265 7.116 0 3.377 2.45 5.727 5.958 5.727 1.307 0 2.449-.463 3.243-1.308.745-.81 1.126-1.903 1.043-3.095-.066-.81-.546-1.654-1.274-2.184-.596-.447-1.639-.894-3.161-.546a.87.87 0 0 0-.662 1.06.866.866 0 0 0 1.059.66c.695-.148 1.275-.065 1.705.25.364.264.546.645.563.893.05.679-.166 1.307-.58 1.754-.447.48-1.125.745-1.936.745-2.549 0-4.188-1.539-4.188-3.939 0-2.466 1.44-5.345 5.495-5.345 2.897 0 6.008 1.87 6.389 6.057.163 1.781.064 5.06-2.504 6.96-1.36.864-2.978 1.447-5.209 1.447-4.088 0-6.124-1.952-8.275-4.021-2.317-2.218-4.7-4.518-9.516-4.518v1.787c4.088 0 6.123 1.953 8.275 4.022 2.317 2.218 4.7 4.518 9.516 4.518 4.8 0 7.2-2.3 9.517-4.518 2.151-2.069 4.187-4.022 8.275-4.022s6.124 1.953 8.275 4.022c2.318 2.218 4.701 4.518 9.517 4.518 4.8 0 7.2-2.3 9.516-4.518 2.152-2.069 4.188-4.022 8.276-4.022s6.123 1.953 8.275 4.022c2.317 2.218 4.7 4.518 9.517 4.518v-1.788c-4.088 0-6.124-1.952-8.275-4.021-2.318-2.218-4.701-4.518-9.517-4.518-4.103 0-6.45 1.683-8.492 3.556.237-1.118.304-2.343.184-3.656-.497-5.263-4.568-7.663-8.16-7.663"/><path fill="#65452f" d="M23.42 41.086a.9.9 0 0 1-.729-.38.883.883 0 0 1 .215-1.242c2.665-1.887 2.764-5.23 2.599-7.034-.38-4.187-3.492-6.058-6.389-6.058-4.055 0-5.495 2.88-5.495 5.346 0 2.4 1.639 3.94 4.188 3.94.81 0 1.49-.265 1.936-.745.414-.447.63-1.076.58-1.755-.017-.248-.2-.629-.547-.893-.43-.315-1.026-.398-1.704-.249a.87.87 0 0 1-1.06-.662.87.87 0 0 1 .662-1.059c1.523-.348 2.566.1 3.161.546.729.53 1.209 1.374 1.275 2.185.083 1.191-.298 2.284-1.043 3.095-.794.844-1.936 1.307-3.244 1.307-3.508 0-5.958-2.35-5.958-5.726 0-3.542 2.25-7.117 7.266-7.117 3.591 0 7.663 2.4 8.16 7.663.347 3.79-.828 6.868-3.344 8.656a.82.82 0 0 1-.53.182zm0-30.585a.9.9 0 0 1-.729-.38.883.883 0 0 1 .215-1.242c2.665-1.887 2.764-5.23 2.599-7.034-.381-4.187-3.493-6.058-6.389-6.058-4.055 0-5.495 2.88-5.495 5.346 0 2.4 1.639 3.94 4.188 3.94.81 0 1.49-.266 1.936-.746.414-.446.629-1.075.58-1.754-.017-.248-.2-.629-.547-.894-.43-.314-1.026-.397-1.705-.248A.87.87 0 0 1 17.014.77a.87.87 0 0 1 .662-1.06c1.523-.347 2.566.1 3.161.547.729.53 1.209 1.374 1.275 2.185.083 1.191-.298 2.284-1.043 3.095-.794.844-1.936 1.307-3.244 1.307-3.508 0-5.958-2.35-5.958-5.726 0-3.542 2.25-7.117 7.266-7.117 3.591 0 7.663 2.4 8.16 7.663.347 3.79-.828 6.868-3.344 8.656a.82.82 0 0 1-.53.182zm29.956 1.572c-4.8 0-7.2-2.3-9.517-4.518-2.151-2.069-4.187-4.022-8.275-4.022S29.46 5.486 27.31 7.555c-2.317 2.218-4.7 4.518-9.517 4.518-4.8 0-7.2-2.3-9.516-4.518C6.124 5.486 4.088 3.533 0 3.533s-6.124 1.953-8.275 4.022c-2.317 2.218-4.7 4.518-9.517 4.518-4.8 0-7.2-2.3-9.516-4.518-2.152-2.069-4.188-4.022-8.276-4.022V1.746c4.8 0 7.2 2.3 9.517 4.518 2.152 2.069 4.187 4.022 8.275 4.022s6.124-1.953 8.276-4.022C-7.2 4.046-4.816 1.746 0 1.746c4.8 0 7.2 2.3 9.517 4.518 2.151 2.069 4.187 4.022 8.275 4.022s6.124-1.953 8.275-4.022c2.318-2.218 4.7-4.518 9.517-4.518 4.8 0 7.2 2.3 9.517 4.518 2.151 2.069 4.187 4.022 8.275 4.022s6.124-1.953 8.275-4.022c2.317-2.218 4.7-4.518 9.517-4.518v1.787c-4.088 0-6.124 1.953-8.275 4.022-2.317 2.234-4.717 4.518-9.517 4.518"/></pattern></defs><rect width="800%" height="800%" fill="url(#a)" transform="translate(0 -.17)"/></svg>

    <a href="#" class="absolute text-white text-[30px] no-underline right-[15px] hover:text-[#bdb4b4] z-10" id="btnlogout">
        <ion-icon name="exit"></ion-icon>
    </a>

    <div class="mt-5 flex relative z-10">
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
