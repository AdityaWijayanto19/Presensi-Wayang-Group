<div class="appBottomMenu">

    <a href="/dashboard"
        class="item {{ request()->is('dashboard') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="home"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>

    <a href="/presensi/histori"
        class="item {{ request()->is('presensi/histori') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="time"></ion-icon>
            <strong>Histori</strong>
        </div>
    </a>

    <a href="/presensi/create"
        class="item {{ request()->is('presensi/create') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="camera"></ion-icon>
            <strong>Presensi</strong>
        </div>
    </a>

    <a href="/presensi/izin"
        class="item {{ request()->is('presensi/izin') || request()->is('presensi/buatizin') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="document-text"></ion-icon>
            <strong>Izin</strong>
        </div>
    </a>

    <a href="/presensi/lembur"
        class="item {{ request()->is('presensi/lembur') || request()->is('presensi/buatlembur') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="timer"></ion-icon>
            <strong>Lembur</strong>
        </div>
    </a>

    <a href="/presensi/wfh"
        class="item {{ request()->is('presensi/wfh') || request()->is('presensi/buatwfh') ? 'active disabled' : '' }}">
        <div class="col">
            <ion-icon name="save"></ion-icon>
            <strong>WFH</strong>
        </div>
    </a>

</div>
