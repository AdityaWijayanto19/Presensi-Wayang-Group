<!DOCTYPE html>
<html lang="en">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta name="theme-color" content="#7A5234">
    <meta name="description" content="WAG Presensi Digital">

    <title>WAG - Presensi Digital</title>

    <link rel="icon"
        type="image/png"
        href="{{ asset('assets/img/login/logo_aplikasi.png') }}"
        sizes="32x32">

    <link rel="apple-touch-icon"
        href="/icons/icon_192.png">

    <link rel="manifest"
        href="/manifest.json">

    <link rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin="">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            },
            theme: {
                extend: {
                    colors: {
                        coklat: '#7a5234',
                        'coklat-dark': '#5e3e27',
                    }
                }
            }
        }
    </script>

    <style>
        @import url("https://fonts.googleapis.com/css?family=Inter:400,500,700&display=swap");

        /* CSS RESET */
        *, *::before, *::after { box-sizing: border-box; }
        body, h1, h2, h3, h4, h5, h6, p, ul, ol, figure, blockquote, dl, dd {
            margin: 0;
        }
        ul, ol { padding: 0; list-style: none; }
        img, video, canvas { display: block; max-width: 100%; }
        img { height: auto; }
        a { color: inherit; text-decoration: none; }
        input, button, textarea, select { font: inherit; }
        table { border-collapse: collapse; border-spacing: 0; }

        /* BASE */
        body {
            font-family: "Inter", sans-serif;
            font-size: 15px;
            line-height: 1.55rem;
            letter-spacing: -0.015rem;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overscroll-behavior-y: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        ::-webkit-scrollbar { width: 0; }
        button { outline: 0 !important; }
        button:hover, button:active, button:focus { outline: 0 !important; }
        ion-icon { --ionicon-stroke-width: 32px; }

        /* TYPOGRAPHY */
        :is(h1, h2, h3, h4, h5, h6) {
            color: #141515;
            margin: 0 0 10px 0;
            letter-spacing: -0.02em;
            line-height: 1.3em;
        }

        h1 { font-size: 34px; font-weight: 700; }
        h2 { font-size: 24px; font-weight: 700; }
        h3 { font-size: 17px; font-weight: 700; }
        h4 { font-size: 15px; font-weight: 500; }
        h5 { font-size: 13px; font-weight: 500; }
        h6 { font-size: 11px; font-weight: 500; }
        strong, b { font-weight: 500; }

        /* APP HEADER */
        .appHeader {
            min-height: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: env(safe-area-inset-top);
            left: 0;
            right: 0;
            z-index: 999;
            background: #fff;
            color: #141515;
            box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.08);
        }
        .appHeader .left,
        .appHeader .right {
            height: 56px;
            display: flex;
            align-items: center;
            position: absolute;
        }
        .appHeader .left ion-icon,
        .appHeader .right ion-icon {
            font-size: 26px;
            --ionicon-stroke-width: 36px;
        }
        .appHeader .left .headerButton,
        .appHeader .right .headerButton {
            min-width: 36px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: #141515;
            position: relative;
        }
        .appHeader .left { left: 8px; top: 0; }
        .appHeader .right { right: 8px; top: 0; }
        .appHeader .pageTitle {
            font-size: 17px;
            font-weight: 500;
            padding: 0 10px;
        }
        .appHeader.text-light { color: #fff; }
        .appHeader.text-light .headerButton,
        .appHeader.text-light .pageTitle { color: #fff; }
        .appHeader[class*="bg-"] { border: 0; }

        /* SECTION */
        .section { padding: 0 16px; }

        /* TEXT MUTED */
        .text-muted {
            font-size: 13px;
            color: #4f5050 !important;
        }

        /* APP BOTTOM MENU */
        .appBottomMenu {
            min-height: 56px;
            position: fixed;
            z-index: 99999;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top: 1px solid #e1e1e1;
            padding-left: 4px;
            padding-right: 4px;
            padding-bottom: env(safe-area-inset-bottom);
        }
        .appBottomMenu .disabled { pointer-events: none; }
        .appBottomMenu .item {
            font-size: 9px;
            letter-spacing: 0;
            text-align: center;
            width: 100%;
            height: 56px;
            line-height: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-decoration: none !important;
        }
        .appBottomMenu .item .col {
            width: 100%;
            padding: 0 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .appBottomMenu .item ion-icon {
            display: block;
            margin-top: 1px;
            margin-bottom: 3px;
            font-size: 26px;
            line-height: 1em;
            color: #141515;
            transition: 0.1s all;
        }
        .appBottomMenu .item strong {
            margin-top: 4px;
            display: block;
            color: #141515;
            font-weight: 400;
            transition: 0.1s all;
        }
        .appBottomMenu .item:active {
            opacity: 0.8;
            text-decoration: none !important;
        }
        .appBottomMenu .item.active ion-icon,
        .appBottomMenu .item.active strong {
            color: #7a5234 !important;
            font-weight: 500;
        }
        .appBottomMenu .item:hover ion-icon,
        .appBottomMenu .item:hover strong {
            color: #bdb4b4;
        }

        /* APP CAPSULE */
        #appCapsule {
            margin-bottom: env(safe-area-inset-bottom);
            margin-top: env(safe-area-inset-top);
            padding-bottom: 70px;
        }

        /* LISTVIEW */
        .listview {
            display: block;
            padding: 0;
            margin: 0;
            color: #141515;
            background: #fff;
            border-top: 1px solid #e1e1e1;
            border-bottom: 1px solid #e1e1e1;
            line-height: 1.3em;
        }
        .listview > li {
            padding: 8px 16px;
            display: block;
            align-items: center;
            justify-content: space-between;
            position: relative;
            min-height: 50px;
        }
        .listview > li::after {
            content: "";
            display: block;
            position: absolute;
            left: 0; right: 0; bottom: 0;
            height: 1px;
            background: #e1e1e1;
        }
        .listview > li:last-child::after { display: none; }
        .image-listview > li { padding: 0; }
        .image-listview > li::after { left: 68px; }
        .image-listview > li .item {
            padding: 10px 16px;
            width: 100%;
            min-height: 50px;
            display: flex;
            align-items: center;
        }
        .image-listview > li .item .in {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .image-listview > li a.item {
            color: #141515 !important;
            padding-right: 36px;
        }
        .image-listview > li a.item:active {
            background: rgba(225, 225, 225, 0.3);
        }

        /* CARD */
        .card {
            background: #ffffff;
            border-radius: 6px;
            border: 0;
            box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.08);
        }
        .card .card-body {
            padding: 24px 16px;
            line-height: 1.4em;
        }

        /* BUTTON */
        .btn {
            height: 40px;
            padding: 3px 18px;
            font-size: 13px;
            line-height: 1.2em;
            font-weight: 500;
            box-shadow: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s all;
            text-decoration: none !important;
            border-radius: 6px;
            border-width: 2px;
            cursor: pointer;
        }
        .btn ion-icon {
            font-size: 22px;
            margin-right: 10px;
            margin-top: -2px;
            font-weight: 700;
        }
        .btn-lg {
            height: 48px;
            padding: 3px 24px;
            font-size: 18px;
        }
        .btn-lg ion-icon { font-size: 26px; }
        .btn-primary {
            background: #91623d !important;
            border-color: #91623d !important;
            color: #ffffff !important;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active {
            background: #7a5234 !important;
            border-color: #7a5234 !important;
        }
        .btn-danger {
            background: #ec4433 !important;
            border-color: #ec4433 !important;
            color: #ffffff !important;
        }
        .btn-danger:hover,
        .btn-danger:focus,
        .btn-danger:active,
        .btn-danger.active {
            background: #ea2f1c !important;
            border-color: #ea2f1c !important;
        }

        /* ALERT */
        .alert {
            margin: 0;
            padding: 6px 16px;
            border: 0;
            font-size: 13px;
            border-radius: 6px;
        }
        .alert-success {
            background: #34c759;
            color: #fff;
            border: 1px solid #34c759;
        }
        .alert-danger {
            background: #ec4433;
            color: #fff;
            border: 1px solid #ec4433;
        }
        .alert-outline-warning {
            background: transparent;
            color: #fe9500;
            border: 1px solid #fe9500;
        }
        .alert-outline-danger {
            background: transparent;
            color: #ec4433;
            border: 1px solid #ec4433;
        }

        /* FORM */
        .form-group { width: 100%; }
        textarea,
        .form-control {
            background-clip: padding-box;
            background-image: linear-gradient(transparent, transparent);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 15px;
            font-weight: 400;
            line-height: 1.5;
            color: #141515;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out;
        }
        .form-group.boxed {
            margin: 0;
            padding: 8px 0;
        }
        .form-group.boxed .form-control {
            background: #fff;
            box-shadow: none;
            height: 42px;
            border-radius: 6px;
            padding: 0 40px 0 16px;
            border: 1px solid #e1e1e1;
        }
        .form-group .clear-input {
            display: none;
            align-items: center;
            justify-content: center;
            color: #4f5050;
            height: 38px;
            font-size: 22px;
            position: absolute;
            right: -10px;
            bottom: 0;
            width: 32px;
            opacity: 0.5;
        }
        .form-group .input-wrapper { position: relative; }
        .form-group .input-wrapper.not-empty .clear-input { display: flex; }
        .form-group.boxed .clear-input {
            right: 0;
            height: 42px;
            width: 40px;
        }

        /* FAB BUTTON */
        .fab-button .fab {
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 100%;
            color: #fff !important;
        }
        .fab-button .fab > ion-icon {
            font-size: 26px;
            transition: 0.2s all;
            --ionicon-stroke-width: 42px;
        }
        .fab-button.bottom-right {
            position: fixed;
            bottom: 16px;
            right: 16px;
            z-index: 100;
            margin-bottom: env(safe-area-inset-bottom);
        }
        .fab-button.bottom-right span {
            position: absolute;
            bottom: 65px;
            left: 45%;
            transform: translateX(-50%);
            text-align: center;
            color: black;
            font-size: 12px;
            white-space: nowrap;
        }

        /* NAV TABS */
        .nav-tabs.style1 {
            border: 0;
            background: rgba(225, 225, 225, 0.4);
            border-radius: 6px;
            display: flex;
        }
        .nav-tabs.style1 .nav-item {
            flex: 1;
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        .nav-tabs.style1 .nav-item .nav-link {
            color: #4f5050;
            font-weight: 500;
            font-size: 13px;
            border: 0 !important;
            line-height: 1.2em;
            width: 100%;
            border-radius: 6px;
            padding: 6px 10px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px !important;
        }
        .nav-tabs.style1 .nav-item .nav-link.active {
            color: #141515;
            box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.08);
        }

        /* WEBCAM */
        .webcam-capture {
            max-width: 380px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
            line-height: 0;
        }
        @media (min-width: 768px) {
            .webcam-capture { max-width: 100%; border-radius: 15px; }
        }
        .webcam-capture > div:empty {
            display: none;
        }
        .webcam-capture video {
            margin: 0;
            padding: 0;
            display: block;
        }
        .webcam-capture video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            display: block;
            transform: scaleX(-1);
        }

        /* MAP */
        #map {
            height: 280px;
            border-radius: 15px;
            overflow: hidden;
        }
        @media (min-width: 768px) {
            #map { height: 400px; }
        }

        /* BADGE */
        .badge {
            font-size: 12px;
            line-height: 1em;
            border-radius: 100px;
            letter-spacing: 0;
            height: 22px;
            min-width: 22px;
            padding: 0 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 400;
        }

        /* SWEETALERT OVERRIDES */
        .swal2-close:focus { box-shadow: none !important; }
        .swal2-confirm {
            background-color: #7a5234 !important;
            border-color: #7a5234 !important;
            color: white !important;
        }
        .swal2-confirm:hover {
            background-color: #5e3e27 !important;
        }

        /* FLATPICKR OVERRIDES */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.startRange,
        .flatpickr-day.selected.endRange {
            background: #7a5234;
            border-color: #7a5234;
        }

        /* RESPONSIVE */
        @media (max-width: 370px) {
            .presencecontent { gap: 6px; }
            .iconpresence img { width: 38px; height: 38px; object-fit: cover; }
            .presencetitle { font-size: 14px; }
            .presencedetail span { font-size: 11px; }
        }
    </style>

</head>

<body style="background-color: #e9ecef;">

    {{-- Header --}}
    @yield('header')

    {{-- App Content --}}
    <div id="appCapsule">

        @yield('content')

    </div>

    {{-- Bottom Navigation --}}
    @include('layouts.bottomNav')

    {{-- Script --}}
    @include('layouts.script')

    {{-- Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        console.log('Service Worker Registered');
                    })
                    .catch(function (error) {
                        console.log('Service Worker Failed', error);
                    });
            });
        }
    </script>

</body>

</html>
