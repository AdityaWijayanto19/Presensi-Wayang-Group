<!DOCTYPE html>
<html lang="en">

<head>

    {{-- ================================================== --}}
    {{-- Meta --}}
    {{-- ================================================== --}}
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>WAG Presensi Digital - Administrator</title>

    {{-- ================================================== --}}
    {{-- Icon --}}
    {{-- ================================================== --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/img/login/logo_aplikasi.png') }}" sizes="32x32">

    {{-- ================================================== --}}
    {{-- Stylesheet --}}
    {{-- ================================================== --}}
    <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402') }}" rel="stylesheet">
    <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1674944402') }}" rel="stylesheet">
    <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1674944402') }}" rel="stylesheet">
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402') }}" rel="stylesheet">
    <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    {{-- ================================================== --}}
    {{-- Custom Style --}}
    {{-- ================================================== --}}
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont,
                San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }

        .flatpickr-calendar {
            background: #ffffff !important;
            border: 1px solid #e6e8e9 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            font-family: inherit !important;
            padding: 8px !important;
            width: 315px !important;
        }

        /* Arrow indicator atas */
        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after {
            border-bottom-color: #ffffff !important;
        }

        /* Header Bulan & Tahun */
        .flatpickr-current-month {
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            padding-top: 4px !important;
        }

        /* Nama Hari (Sen, Sel, Rab...) */
        span.flatpickr-weekday {
            color: #626976 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
        }

        /* Angka Tanggal */
        .flatpickr-day {
            border-radius: 6px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }

        /* Hover Tanggal */
        .flatpickr-day:hover,
        .flatpickr-day:focus {
            background: #f1f5f9 !important;
            border-color: transparent !important;
        }

        /* Tanggal yang Dipilih (Selected) */
        .flatpickr-day.today.selected,
        .flatpickr-day.today.selected:hover,
        .flatpickr-day.today.selected:focus {
            background: #206bc4 !important;
            border-color: #206bc4 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        /* Hari Ini (Today) */
        .flatpickr-day.today {
            border-color: #206bc4 !important;
            color: #206bc4 !important;
        }

        .flatpickr-day.today:hover {
            background: #206bc4 !important;
            color: #ffffff !important;
        }

        /* Fix Input Form Alignment di Tabler */
        .flatpickr-input[readonly] {
            background-color: #ffffff !important;
        }
    </style>

</head>

<body>

    <script src="{{ asset('tabler/dist/js/demo-theme.min.js?1674944402') }}" defer></script>

    <div class="page">

        {{-- ================================================== --}}
        {{-- Sidebar --}}
        {{-- ================================================== --}}
        @include('layouts.admin.sidebar')

        {{-- ================================================== --}}
        {{-- Header --}}
        {{-- ================================================== --}}
        @include('layouts.admin.header')

        <div class="page-wrapper">

            {{-- ================================================== --}}
            {{-- Content --}}
            {{-- ================================================== --}}
            @yield('content')

            {{-- ================================================== --}}
            {{-- Footer --}}
            {{-- ================================================== --}}
            @include('layouts.admin.footer')

        </div>

    </div>

    {{-- ================================================== --}}
    {{-- Javascript Libraries --}}
    {{-- ================================================== --}}
    <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1674944402') }}" defer></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1674944402') }}" defer></script>
    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1674944402') }}" defer></script>
    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1674944402') }}" defer></script>

    {{-- ================================================== --}}
    {{-- Tabler Core --}}
    {{-- ================================================== --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


    <!-- Flatpickr JS & Indonesian Locale -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402') }}" defer></script>
    <script src="{{ asset('tabler/dist/js/demo.min.js?1674944402') }}" defer></script>

    {{-- ================================================== --}}
    {{-- Plugin --}}
    {{-- ================================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

    {{-- ================================================== --}}
    {{-- Logout Confirmation --}}
    {{-- ================================================== --}}
    <script>
        $(document).ready(function() {

            $("#logout-admin").click(function(e) {

                e.preventDefault();

                let url = $(this).attr('href');

                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: 'Anda akan keluar dari sistem.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    backdrop: false
                }).then((result) => {

                    if (result.isConfirmed) {
                        window.location.href = url;
                    }

                });

            });

        });
    </script>

    {{-- ================================================== --}}
    {{-- Session Alert --}}
    {{-- ================================================== --}}
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ Session::get('success') }}',
                backdrop: false
            });
        </script>
    @endif

    @if (Session::get('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: '{{ Session::get('warning') }}',
                backdrop: false
            });
        </script>
    @endif

    @if (Session::get('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ Session::get('error') }}',
                backdrop: false
            });
        </script>
    @endif

    {{-- ================================================== --}}
    {{-- Global Badge Polling (Sidebar WFH) --}}
    {{-- ================================================== --}}
    <script>
    (function(){
        function pollAdminBadge(){
            fetch('/api/realtime/admin', { credentials: 'same-origin' })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    var badgeEl = document.getElementById('adminWfhBadge');
                    var total = (data.pending_wfh || 0) + (data.pending_laporan || 0);
                    if(badgeEl){
                        if(total > 0){
                            badgeEl.textContent = total;
                            badgeEl.style.display = 'inline-flex';
                        } else {
                            badgeEl.style.display = 'none';
                        }
                    }
                }).catch(function(){});
        }
        pollAdminBadge();
        setInterval(pollAdminBadge, 5000);
    })();
    </script>

    {{-- ================================================== --}}
    {{-- Custom Script --}}
    {{-- ================================================== --}}
    @stack('myscript')

</body>

</html>
