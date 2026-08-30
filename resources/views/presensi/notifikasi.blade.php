@extends('layouts.presensi')

@section('header')
    <div class="appHeader bg-coklat text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Notifikasi</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="flex mt-[60px] pb-6">
        <div class="w-full px-3">

            <!-- Card Wrapper Container -->
            <div class="bg-white rounded-2xl border border-[#f0ece8] shadow-sm divide-y divide-[#f5f0eb] overflow-hidden">
                @forelse($notifications ?? [] as $n)
                    <div
                        class="p-3.5 hover:bg-[#fdf8f4] transition-colors duration-150 flex items-start justify-between gap-3 {{ is_null($n->read_at) ? 'bg-amber-50/50' : '' }}">
                        <div class="flex-1">
                            <!-- Pesan Notifikasi -->
                            <div class="text-[13px] font-medium text-[#1c1917] leading-snug">
                                {{ $n->data['message'] ?? 'Notifikasi' }}
                            </div>
                            <!-- Waktu -->
                            <div class="text-[11px] text-[#a8a29e] mt-1.5 flex items-center gap-1">
                                <ion-icon name="time-outline" class="text-[12px]"></ion-icon>
                                <span>{{ $n->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Indikator Belum Dibaca -->
                        @if (is_null($n->read_at))
                            <span class="w-2 h-2 rounded-full bg-amber-500 mt-1 flex-shrink-0"></span>
                        @endif
                    </div>
                @empty
                    <!-- Tampilan Jika Kosong -->
                    <div class="p-8 text-center">
                        <ion-icon name="notifications-off-outline" class="text-4xl text-[#a8a29e] mb-2"></ion-icon>
                        <div class="text-[13px] font-medium text-[#1c1917]">Belum ada notifikasi</div>
                        <p class="text-[11px] text-[#a8a29e] mt-1">Semua pemberitahuan terbaru akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var unreadDots = document.querySelectorAll('.bg-amber-500');
    if (unreadDots.length > 0) {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(function() {
            document.querySelectorAll('.bg-amber-500').forEach(function(el) { el.remove(); });
            document.querySelectorAll('.bg-amber-50\\/50').forEach(function(el) { el.classList.remove('bg-amber-50/50'); });
        });
    }
});
</script>
@endpush
