{{-- ================================================== --}}
{{-- Top Navigation Bar --}}
{{-- ================================================== --}}
<header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">

    <div class="container-xl">

        {{-- ================================================== --}}
        {{-- User Menu --}}
        {{-- ================================================== --}}
        <div class="navbar-nav flex-row order-md-last">

            <div class="nav-item dropdown">

                <a href="#"
                    class="nav-link d-flex lh-1 text-reset p-0"
                    data-bs-toggle="dropdown"
                    aria-label="Open user menu">

                    <span class="avatar avatar-sm"
                        style="background-image: url('{{ asset('assets/img/admin_icon.png') }}')">
                    </span>

                    <div class="d-none d-xl-block ps-2">

                        <div>
                            {{ Auth::guard('user')->user()->name }}
                        </div>

                        <div class="mt-1 small text-muted">
                            Administrator
                        </div>

                    </div>

                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                    <a href="/proseslogoutadmin"
                        class="dropdown-item"
                        id="logout-admin">

                        Logout

                    </a>

                </div>

            </div>

        </div>

        {{-- ================================================== --}}
        {{-- Navbar Search --}}
        {{-- ================================================== --}}
        <div class="collapse navbar-collapse" id="navbar-menu">

            <div>

                <form action="./"
                    method="get"
                    autocomplete="off"
                    novalidate>

                    <div class="input-icon">

                        <span class="input-icon-addon">
                            {{-- Icon Search --}}
                        </span>

                        {{-- <input type="text" class="form-control" placeholder="Search..." aria-label="Search"> --}}

                    </div>

                </form>

            </div>

        </div>

    </div>

</header>