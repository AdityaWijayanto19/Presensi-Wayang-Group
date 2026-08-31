@forelse ($datawfh as $d)
    @php
        $status = $d->status ?? 'pending_atasan';
        $badge = match ($status) {
            'pending_atasan' => 'bg-warning',
            'pending_admin' => 'bg-info',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'unpaid' => 'bg-secondary',
            default => 'bg-secondary',
        };
        $label = match ($status) {
            'pending_atasan' => 'Menunggu Atasan',
            'pending_admin' => 'Menunggu Admin',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'unpaid' => 'Unpaid',
            default => $status,
        };
        $pdfUrl = !empty($d->pdf_form_path) ? Storage::url($d->pdf_form_path) : null;
    @endphp
    <tr>
        <td>{{ ($datawfh->currentPage() - 1) * $datawfh->perPage() + $loop->iteration }}</td>
        <td>{{ date('d-m-Y', strtotime($d->tgl_wfh)) }}<br><small
                class="text-muted">{{ $d->live_location ?? '-' }}</small></td>
        <td><small class="text-muted">{{ $d->nik }}</small><br>{{ $d->nama_lengkap }}</td>
        <td><span
                class="badge bg-{{ match($d->jabatan) { 'Direktur' => 'danger', 'GM' => 'warning', 'Manager' => 'info', 'SPV' => 'primary', 'Staff' => 'success', 'Intern' => 'info', default => 'secondary' } }}">{{ $d->jabatan ?? '-' }}</span><br>{{ $d->posisi }}
        </td>
        <td>{{ $d->perusahaan }}<br><small class="text-muted">{{ $d->unit }}</small></td>
        <td>{{ $d->atasan_nama ?? '—' }}<br><small
                class="text-muted">{{ $d->atasan_jabatan ?? ($d->atasan_nik ? $d->atasan_nik : 'Langsung Admin') }}</small>
        </td>
        <td>
            <span class="badge {{ $badge }}">{{ $label }}</span>
            @if ($status == 'rejected' && !empty($d->rejected_reason))
                <br><small class="text-danger">{{ Str::limit($d->rejected_reason, 30) }}</small>
            @endif
            @if (!empty($d->keterangan))
                <br><small class="text-info"><b>Ket:</b> {{ Str::limit($d->keterangan, 40) }}</small>
            @endif
            <br><small class="text-muted">{{ Str::limit($d->deskripsi_pekerjaan, 40) }}</small>
        </td>
        <td>
            @if ($pdfUrl)
                <button type="button" class="btn btn-sm btn-primary js-preview-admin" data-url="{{ $pdfUrl }}"
                    data-filename="{{ basename($pdfUrl) }}"
                    data-label="Form WFH — {{ $d->nama_lengkap }} {{ date('d-m-Y', strtotime($d->tgl_wfh)) }}">Preview</button>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if (!empty($d->laporan_file))
                <button type="button" class="btn btn-sm btn-success js-preview-admin"
                    data-url="{{ Storage::url($d->laporan_file) }}" data-filename="{{ basename($d->laporan_file) }}"
                    data-label="Laporan — {{ $d->nama_lengkap }}">Preview</button>
            @elseif(!empty($d->laporan_deskripsi))
                <small class="text-muted">{{ Str::limit($d->laporan_deskripsi, 30) }}</small>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-start;">
                @if ($status === 'pending_admin')
                    <div style="display:flex; gap:4px;">
                        <form action="/presensi/datawfh/{{ $d->id }}/approve" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">✓ Setujui</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-warning btn-reject-admin"
                            data-id="{{ $d->id }}">Tolak</button>
                    </div>
                @elseif($status === 'approved')
                    <span class="badge bg-success">Disetujui</span>
                @elseif($status === 'rejected')
                    <span class="badge bg-danger">Ditolak</span>
                @elseif($status === 'unpaid')
                    <span class="badge bg-secondary">Unpaid</span>
                @endif
                @if (!empty($d->laporan_status))
                    @php
                        $lStatus = $d->laporan_status;
                        $lBadge = match ($lStatus) {
                            'pending_atasan' => 'bg-yellow-lt text-yellow',
                            'pending_admin' => 'bg-blue-lt text-blue',
                            'approved' => 'bg-green-lt text-green',
                            'rejected' => 'bg-red-lt text-red',
                            default => 'bg-gray-lt text-gray',
                        };
                        $lLabel = match ($lStatus) {
                            'pending_atasan' => 'Laporan: Menunggu Atasan',
                            'pending_admin' => 'Laporan: Menunggu Admin',
                            'approved' => 'Laporan: Disetujui',
                            'rejected' => 'Laporan: Ditolak',
                            default => 'Laporan: ' . $lStatus,
                        };
                    @endphp
                    <div>
                        <span class="badge {{ $lBadge }}">{{ $lLabel }}</span>
                    </div>
                    @if ($lStatus == 'pending_admin')
                        <div style="display:flex; gap:4px;">
                            <form action="/presensi/datawfh/{{ $d->id }}/approve-laporan-admin" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">✓ Setujui Laporan</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-warning btn-reject-laporan-admin"
                                data-id="{{ $d->id }}">Tolak Laporan</button>
                        </div>
                    @endif
                    @if ($lStatus == 'rejected' && !empty($d->laporan_rejected_reason))
                        <small class="text-danger">{{ Str::limit($d->laporan_rejected_reason, 40) }}</small>
                    @endif
                @endif
                <form action="/presensi/datawfh/{{ $d->id }}/delete" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger delete-confirm">Hapus</button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center text-muted">Data WFH tidak ditemukan</td>
    </tr>
@endforelse
