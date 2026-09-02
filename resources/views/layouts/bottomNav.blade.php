<div class="appBottomMenu">

    <a href="/dashboard"
        class="item {{ request()->is('dashboard') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="home"></i>
            <strong>Home</strong>
        </div>
    </a>

    <a href="/presensi/histori"
        class="item {{ request()->is('presensi/histori') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="clock"></i>
            <strong>Histori</strong>
        </div>
    </a>

    <a href="/presensi/create"
        class="item {{ request()->is('presensi/create') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="camera"></i>
            <strong>Presensi</strong>
        </div>
    </a>

    <a href="/presensi/izin"
        class="item {{ request()->is('presensi/izin') || request()->is('presensi/buatizin') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="file-text"></i>
            <strong>Izin</strong>
        </div>
    </a>

    <a href="/presensi/lembur"
        class="item {{ request()->is('presensi/lembur') || request()->is('presensi/buatlembur') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="timer"></i>
            <strong>Lembur</strong>
        </div>
    </a>

    <a href="/presensi/wfh"
        class="item {{ request()->is('presensi/wfh') || request()->is('presensi/buatwfh') || request()->is('presensi/wfh/*/laporan') ? 'active disabled' : '' }}">
        <div class="col">
            <i data-lucide="save"></i>
            <strong>WFH</strong>
        </div>
    </a>

</div>
