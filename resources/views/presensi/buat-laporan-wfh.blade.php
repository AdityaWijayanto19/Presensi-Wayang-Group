@extends('layouts.presensi')

@section('header')
    <div class="appHeader bg-coklat text-light">
        <div class="left">
            <a href="/presensi/wfh" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Input Laporan WFH</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    @php
        $karyawan = Auth::guard('karyawan')->user();
        $weekdayMap = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        $today = now();
        $weekday = $weekdayMap[$today->format('l')] ?? $today->format('l');
        $presensiToday = \Illuminate\Support\Facades\DB::table('presensi')
            ->where('nik', $karyawan->nik)
            ->where('tgl_presensi', $today->format('Y-m-d'))
            ->first();
        $liveLocation = $presensiToday->lokasi_in ?? '-';
    @endphp

    <div class="flex mt-[70px]">
        <div class="w-full px-3">
            {{-- Info WFH --}}
            <div class="bg-white rounded-2xl border border-[#f0ece8] p-4 mb-3">
                <div class="text-[11px] font-semibold tracking-wide text-[#a8a29e] uppercase">WFH {{ date('d M Y', strtotime($wfh->tgl_wfh)) }}</div>
                <div class="text-[13px] font-bold text-[#1c1917]">Status: Disetujui</div>
                <p class="text-[12px] text-[#78716c] mt-1">Silakan input laporan pekerjaan WFH. Laporan akan melalui proses persetujuan atasan dan administrator.</p>
            </div>

            @if(Session::get('success'))
                <div class="bg-[#ecfdf5] border border-[#a7f3d0] text-[#065f46] text-[13px] rounded-xl py-2.5 px-3.5 mb-3">{{ Session::get('success') }}</div>
            @endif
            @if(Session::get('error'))
                <div class="bg-[#fef2f2] border border-[#fecaca] text-[#991b1b] text-[13px] rounded-xl py-2.5 px-3.5 mb-3">{{ Session::get('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-[#fef2f2] border border-[#fecaca] text-[#991b1b] text-[13px] rounded-xl py-2.5 px-3.5 mb-3">
                    <ul class="mb-0 list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="/presensi/wfh/{{ $wfh->id }}/laporan" enctype="multipart/form-data" id="form_laporan">
                @csrf

                {{-- Hari & Tanggal (Auto) --}}
                <div class="bg-white rounded-2xl border border-[#f0ece8] p-4 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#f0f9ff] border border-[#bae6fd] flex items-center justify-center text-sky-600">
                            <ion-icon name="calendar-outline" style="font-size:18px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[11px] font-semibold tracking-wide text-[#a8a29e] uppercase">Hari & Tanggal</div>
                            <div class="text-[14px] font-bold text-[#1c1917]">{{ $weekday }}, {{ $today->format('d M Y') }}</div>
                            <div class="text-[11px] text-[#a8a29e]">Otomatis dari waktu saat ini</div>
                        </div>
                    </div>
                </div>

                {{-- Telah Dilaksanakan Oleh (Auto) --}}
                <div class="bg-white rounded-2xl border border-[#f0ece8] p-4 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#fdf8f4] border border-[#f0ece8] flex items-center justify-center text-coklat">
                            <ion-icon name="person-outline" style="font-size:18px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[11px] font-semibold tracking-wide text-[#a8a29e] uppercase">Telah Dilaksanakan Oleh</div>
                            <div class="text-[14px] font-bold text-[#1c1917]">{{ $karyawan->nama_lengkap }}</div>
                            <div class="text-[12px] text-[#78716c]">{{ $karyawan->jabatan }}</div>
                        </div>
                    </div>
                </div>

                {{-- Live Location (Auto from absen masuk) --}}
                <div class="bg-white rounded-2xl border border-[#f0ece8] p-4 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#f0fdf4] border border-[#bbf7d0] flex items-center justify-center text-green-600">
                            <ion-icon name="location-outline" style="font-size:18px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[11px] font-semibold tracking-wide text-[#a8a29e] uppercase">Live Location</div>
                            <div class="text-[13px] font-bold text-[#1c1917]">{{ $liveLocation }}</div>
                            <div class="text-[11px] text-[#a8a29e]">Diambil dari lokasi absen masuk</div>
                        </div>
                    </div>
                </div>

                {{-- Detail Hasil Pekerjaan (Deskripsi) --}}
                <div class="form-group mb-3">
                    <label class="text-[12px] font-semibold text-[#44403c] mb-1 block">Deskripsi Hasil Pekerjaan <span class="text-red-500">*</span></label>
                    <textarea name="laporan_deskripsi" rows="5" class="form-control" placeholder="Jelaskan detail hasil pekerjaan WFH hari ini..." required>{{ old('laporan_deskripsi', $wfh->laporan_deskripsi) }}</textarea>
                    <small class="text-[11px] text-[#a8a29e]">Minimal 10 karakter. Jelaskan pekerjaan yang telah diselesaikan.</small>
                </div>

                {{-- Upload Gambar Hasil Pekerjaan (Min 2, Max 5) --}}
                <div class="form-group mb-3">
                    <label class="text-[12px] font-semibold text-[#44403c] mb-1 block">Foto Hasil Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="file" name="laporan_images[]" id="laporan_images" class="form-control" accept="image/*" multiple required>
                    <small class="text-[11px] text-[#a8a29e]">Minimal 2 foto, maksimal 5 foto. Format: JPG, JPEG, PNG. Maks 4MB per foto.</small>
                    <div id="image-preview" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                {{-- Approved By (Info) --}}
                <div class="bg-[#f5f3ff] border border-[#ddd6fe] rounded-xl p-3 mb-3">
                    <div class="flex items-center gap-2 mb-2">
                        <ion-icon name="shield-checkmark-outline" class="text-violet-600 text-[16px]"></ion-icon>
                        <span class="text-[12px] font-semibold text-violet-800">Proses Persetujuan</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-violet-100 border border-violet-300 flex items-center justify-center text-[10px] font-bold text-violet-700">1</span>
                            <span class="text-[11px] text-violet-800">Atasan Persetujuan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-violet-100 border border-violet-300 flex items-center justify-center text-[10px] font-bold text-violet-700">2</span>
                            <span class="text-[11px] text-violet-800">Administrator (HR) Persetujuan</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-violet-600 mt-2">Laporan harus disetujui atasan dan administrator sebelum dianggap sah secara administrasi.</p>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        <ion-icon name="checkmark-circle-outline" style="margin-right:6px;"></ion-icon> Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
<script>
$(function(){
    // Image preview & validation
    const fileInput = document.getElementById('laporan_images');
    const previewContainer = document.getElementById('image-preview');

    fileInput.addEventListener('change', function(){
        previewContainer.innerHTML = '';
        const files = Array.from(this.files);

        if(files.length < 2){
            Swal.fire({icon:'warning', text:'Minimal upload 2 foto hasil pekerjaan', confirmButtonColor:'#7a5234'});
            this.value = '';
            return;
        }
        if(files.length > 5){
            Swal.fire({icon:'warning', text:'Maksimal 5 foto hasil pekerjaan', confirmButtonColor:'#7a5234'});
            this.value = '';
            return;
        }

        files.forEach(file => {
            if(file.size > 4*1024*1024){
                Swal.fire({icon:'warning', text:'Ukuran foto maksimal 4MB: ' + file.name, confirmButtonColor:'#7a5234'});
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e){
                const div = document.createElement('div');
                div.className = 'relative w-16 h-16 rounded-lg overflow-hidden border border-[#f0ece8]';
                div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Submit validation
    $('#form_laporan').submit(function(e){
        e.preventDefault();
        const desk = $('textarea[name="laporan_deskripsi"]').val().trim();
        const files = fileInput.files;
        if(!desk || desk.length < 10){
            Swal.fire({icon:'warning', text:'Deskripsi minimal 10 karakter', confirmButtonColor:'#7a5234'});
            return;
        }
        if(files.length < 2){
            Swal.fire({icon:'warning', text:'Minimal upload 2 foto hasil pekerjaan', confirmButtonColor:'#7a5234'});
            return;
        }
        if(files.length > 5){
            Swal.fire({icon:'warning', text:'Maksimal 5 foto hasil pekerjaan', confirmButtonColor:'#7a5234'});
            return;
        }
        for(let f of files){
            if(f.size > 4*1024*1024){
                Swal.fire({icon:'warning', text:'Ukuran foto maksimal 4MB: ' + f.name, confirmButtonColor:'#7a5234'});
                return;
            }
        }
        Swal.fire({
            title: "Kirim Laporan?",
            text: "Laporan akan dikirim untuk persetujuan atasan dan administrator.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#7a5234",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Kirim",
            cancelButtonText: "Batal"
        }).then((r)=>{ if(r.isConfirmed) $('#form_laporan')[0].submit(); });
    });
});
</script>
@endpush
