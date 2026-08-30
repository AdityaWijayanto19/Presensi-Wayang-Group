<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan WFH - {{ $nama_lengkap }}</title>
<style>
    @page { margin: 20px 25px; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1c1917; line-height: 1.5; }
    .kop { text-align: center; border-bottom: 3px double #7a5234; padding-bottom: 12px; margin-bottom: 18px; }
    .kop h2 { margin:0; font-size: 16px; color: #7a5234; letter-spacing: 0.5px; }
    .kop p { margin:2px 0 0; font-size: 9px; color: #78716c; }
    .title { text-align: center; margin: 16px 0 16px; }
    .title h3 { margin:0; font-size: 14px; text-transform: uppercase; letter-spacing: 0.8px; color: #1c1917; }
    .title p { margin:4px 0 0; font-size: 10px; color: #57534e; }
    table.info { width: 100%; border-collapse: collapse; margin-top: 12px; }
    table.info td { padding: 6px 8px; vertical-align: top; }
    table.info td.label { width: 160px; font-weight: 700; background: #fdf8f4; border: 1px solid #f0ece8; color: #44403c; }
    table.info td.value { border: 1px solid #f0ece8; }
    .deskripsi { margin-top: 16px; border: 1px solid #f0ece8; border-radius: 6px; padding: 12px; background: #fff; min-height: 80px; }
    .deskripsi .label { font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #7a5234; margin-bottom: 6px; }
    .deskripsi .content { white-space: pre-wrap; font-size: 11px; }
    .footer { margin-top: 24px; display: table; width: 100%; }
    .footer .left, .footer .right { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
    .stempel { width: 110px; height: auto; margin: 8px auto; display: block; }
    .ttd-line { margin-top: 40px; border-top: 1px solid #1c1917; width: 160px; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 10px; }
    .meta { margin-top: 18px; font-size: 8px; color: #a8a29e; text-align: center; border-top: 1px solid #f0ece8; padding-top: 8px; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; border: 1px solid; }
    .badge-approved { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
</style>
</head>
<body>
    <div class="kop">
        <h2>WAG - PRESENSI DIGITAL</h2>
        <p>Work From Home - Laporan Hasil Pekerjaan</p>
    </div>

    <div class="title">
        <h3>Laporan Hasil Pekerjaan Work From Home</h3>
        <p>No: LAPORAN-{{ date('Ymd', strtotime($tgl_wfh)) }}-{{ $nik }} &nbsp;|&nbsp; Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table class="info">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td class="value">{{ $hariTanggal }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="value">{{ $nik }}</td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="value">{{ $nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="value">{{ $jabatan }}</td>
        </tr>
        <tr>
            <td class="label">Posisi</td>
            <td class="value">{{ $posisi }}</td>
        </tr>
        <tr>
            <td class="label">Unit / Perusahaan</td>
            <td class="value">{{ $unit }} — {{ $perusahaan ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal WFH</td>
            <td class="value">{{ date('d F Y', strtotime($tgl_wfh)) }} ({{ \Carbon\Carbon::parse($tgl_wfh)->locale('id')->isoFormat('dddd') }})</td>
        </tr>
        <tr>
            <td class="label">Live Location</td>
            <td class="value">{{ $live_location ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan WFH</td>
            <td class="value">{{ $keterangan ?? '-' }}</td>
        </tr>
    </table>

    <div class="deskripsi">
        <div class="label">Deskripsi Hasil Pekerjaan</div>
        <div class="content">{{ $laporan_deskripsi }}</div>
    </div>

    @if(!empty($laporan_images) && count($laporan_images) > 0)
    <div style="margin-top:12px;">
        <div style="font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:0.5px;color:#7a5234;margin-bottom:6px;">Foto Hasil Pekerjaan</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($laporan_images as $img)
                @php $imgPath = storage_path('app/public/' . $img); @endphp
                @if(file_exists($imgPath))
                    <img src="file://{{ $imgPath }}" style="width:120px;height:90px;object-fit:cover;border:1px solid #f0ece8;border-radius:4px;">
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="left">
            <p style="font-size:10px;color:#78716c;">Disetujui Oleh,</p>
            <div class="ttd-line">
                @if(!empty($stempelPath) && file_exists(public_path($stempelPath)))
                    <img src="file://{{ public_path($stempelPath) }}" class="stempel">
                @endif
                <p style="font-size:9px;color:#57534e;">Administrator (HR)</p>
            </div>
        </div>
        <div class="right">
            <p style="font-size:10px;color:#78716c;">Dilaksanakan Oleh,</p>
            <div class="ttd-line">
                <p style="font-weight:700;">{{ $nama_lengkap }}</p>
                <p style="font-size:9px;color:#57534e;">{{ $jabatan }}</p>
            </div>
        </div>
    </div>

    <div class="meta">
        Dokumen ini digenerate otomatis oleh Sistem Presensi Digital — {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
