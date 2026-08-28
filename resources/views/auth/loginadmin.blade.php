<!doctype html>
<html lang="en">

<head>

    <!-- ================== META ================== -->

    <meta charset="utf-8" />

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover" />

    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge" />

    <!-- ================== TITLE ================== -->

    <title>Dashboard Administrator WAG - Presensi Digital</title>

    <!-- ================== TABLER CSS ================== -->

    <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402') }}" rel="stylesheet" />

    <!-- ================== CUSTOM CSS ================== -->

    <style>

        @import url('https://rsms.me/inter/inter.css');

        :root {

            --tblr-font-sans-serif:
                'Inter Var',
                -apple-system,
                BlinkMacSystemFont,
                San Francisco,
                Segoe UI,
                Roboto,
                Helvetica Neue,
                sans-serif;

        }

        body {

            font-feature-settings:
                "cv03",
                "cv04",
                "cv11";

        }

        .btn-primary {

            background: #91623d !important;

        }

        .btn-primary:hover {

            background: #7a5234 !important;

        }

    </style>

</head>

<body class="d-flex flex-column">

    <!-- ================== TABLER THEME ================== -->

    <script src="{{ asset('tabler/dist/js/demo-theme.min.js?1674944402') }}"></script>

    <!-- ================== LOGIN ================== -->

    <div class="page page-center">

        <div class="container container-normal py-4">

            <div class="row align-items-center g-4">

                <div class="col-lg">

                    <div class="container-tight">

                        <!-- ================== LOGO ================== -->

                        <div class="text-center mb-2">

                            <a href="/panel" class="navbar-brand navbar-brand-autodark">

                                <img
                                    src="{{ asset('assets/img/login/logo_aplikasi_admin.png') }}"
                                    height="240"
                                    alt="Logo Administrator">

                            </a>

                        </div>

                        <!-- ================== CARD LOGIN ================== -->

                        <div class="card card-md">

                            <div class="card-body">

                                <h1 class="h1 text-center mb-4">
                                    Login dengan Akun Administrator
                                </h1>

                                <!-- ================== ALERT ================== -->

                                @if (Session::get('danger'))

                                    <div class="alert alert-danger">

                                        <p>{{ Session::get('danger') }}</p>

                                    </div>

                                @endif

                                <!-- ================== FORM LOGIN ================== -->

                                <form
                                    action="/prosesloginadmin"
                                    method="POST"
                                    autocomplete="off"
                                    novalidate>

                                    @csrf

                                    <!-- ================== EMAIL ================== -->

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Email

                                        </label>

                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            placeholder="Masukkan Email"
                                            autocomplete="off">

                                    </div>

                                    <!-- ================== PASSWORD ================== -->

                                    <div class="mb-2">

                                        <label class="form-label">

                                            Password

                                        </label>

                                        <div class="input-group input-group-flat">

                                            <input
                                                type="password"
                                                name="password"
                                                id="password"
                                                class="form-control"
                                                placeholder="Masukkan Password"
                                                autocomplete="off">

                                            <span class="input-group-text">

                                                <a
                                                    href="#"
                                                    id="togglePassword"
                                                    class="link-secondary"
                                                    title="Show password"
                                                    data-bs-toggle="tooltip">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="2"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round">

                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                                                        <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />

                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />

                                                    </svg>

                                                </a>

                                            </span>

                                        </div>

                                    </div>

                                    <!-- ================== BUTTON LOGIN ================== -->

                                    <div class="form-footer">

                                        <button
                                            type="submit"
                                            class="btn btn-primary w-100">

                                            Masuk

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ================== TABLER JS ================== -->

    <script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402') }}" defer></script>

    <script src="{{ asset('tabler/dist/js/demo.min.js?1674944402') }}" defer></script>

    <!-- ================== TOGGLE PASSWORD ================== -->

    <script>

        document
            .getElementById('togglePassword')
            .addEventListener('click', function (e) {

                e.preventDefault();

                const password = document.getElementById('password');

                if (password.type === 'password') {

                    password.type = 'text';

                } else {

                    password.type = 'password';

                }

            });

    </script>

</body>

</html>