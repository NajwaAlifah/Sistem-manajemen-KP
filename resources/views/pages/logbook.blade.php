@extends('layouts.app')
@section('title', 'Logbook Kegiatan - Manajemen KP')
@section('page-title', 'Logbook Kegiatan')
@section('page-subtitle', 'Catatan kegiatan harian dan validasi pembimbing lapangan')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php $me = auth()->user(); $st = \App\Models\Logbook::STATUS; $bd = ['menunggu' => 'submitted', 'disetujui' => 'approved', 'revisi' => 'revision']; @endphp
@if ($me->role === 'mahasiswa')
<div class="sk-panel">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-plus-circle-fill"></i> Catat Kegiatan</h3></div>
    <form method="POST" action="{{ route('logbook.store') }}" enctype="multipart/form-data" class="sk-panel-body">
        @csrf
        <div class="sk-form">
            <div><label>Tanggal</label><input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required></div>
            <div><label>Dokumentasi (opsional)</label><input type="file" class="form-control" name="file" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="full"><label>Uraian kegiatan</label><textarea class="form-control" name="kegiatan" rows="3" maxlength="2000" required>{{ old('kegiatan') }}</textarea></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan Kegiatan</button>
    </form>
</div>
@else
<form method="GET" class="sk-inline mb-3">
    <div><label>Mahasiswa</label><select class="form-control" name="mahasiswa"><option value="">Semua</option>
        @foreach ($students as $s)<option value="{{ $s->id }}" @selected(request('mahasiswa') == $s->id)>{{ $s->name }}</option>@endforeach</select></div>
    <div><label>Status</label><select class="form-control" name="status"><option value="">{{ $me->role === 'lapangan' ? 'Menunggu validasi' : 'Semua' }}</option>
        @foreach ($st as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach</select></div>
    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
</form>
@endif

<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-journal-text"></i> Riwayat Logbook <small class="text-muted">({{ $logs->total() }})</small></h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:760px">
        <thead><tr><th>Tanggal</th>@if ($me->role !== 'mahasiswa')<th>Mahasiswa</th>@endif<th>Kegiatan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse ($logs as $l)
            <tr><td class="doc-date">{{ $l->tanggal->format('d M Y') }}</td>
                @if ($me->role !== 'mahasiswa')<td><strong>{{ $l->owner->name }}</strong></td>@endif
                <td style="max-width:360px">{{ $l->kegiatan }}
                    @if ($l->file_path)<div><a href="{{ route('logbook.file', $l->id) }}"><i class="bi bi-paperclip"></i> {{ $l->file_name }}</a></div>@endif
                    @if ($l->catatan)<div class="sk-note mt-2"><strong>{{ $l->validator->name ?? 'Pembimbing' }}:</strong> {{ $l->catatan }}</div>@endif</td>
                <td><span class="badge badge-{{ $bd[$l->status] }}">{{ $st[$l->status] }}</span></td>
                <td>
                    @if ($me->role === 'mahasiswa')
                        @if (in_array($l->status, ['menunggu', 'revisi']))
                        <div class="doc-actions">
                            <button class="doc-action-btn" title="Ubah" onclick='openLog({{ $l->id }}, "{{ $l->tanggal->format('Y-m-d') }}", @json($l->kegiatan))'><i class="bi bi-pencil-square"></i></button>
                            @if ($l->status === 'menunggu')
                            <form method="POST" action="{{ route('logbook.destroy', $l->id) }}" onsubmit="return confirm('Hapus catatan ini?')">@csrf @method('DELETE')<button class="doc-action-btn delete" title="Hapus"><i class="bi bi-trash3"></i></button></form>
                            @endif
                        </div>@endif
                    @elseif ($me->role === 'lapangan' && $l->status !== 'disetujui')
                        <form method="POST" action="{{ route('logbook.validasi', $l->id) }}" class="sk-inline">@csrf
                            <input class="form-control" name="catatan" placeholder="Catatan (wajib bila perbaikan)" maxlength="1000">
                            <button class="btn btn-primary btn-sm" name="aksi" value="setuju"><i class="bi bi-check-lg"></i> Setujui</button>
                            <button class="btn btn-secondary btn-sm" name="aksi" value="revisi"><i class="bi bi-arrow-counterclockwise"></i> Perbaiki</button>
                        </form>
                    @endif
                </td></tr>
        @empty<tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-journal"></i></div><div class="empty-state-title">Belum ada catatan</div></div></td></tr>@endforelse</tbody>
    </table></div>
    <div class="pagination-container"><div class="pagination-info">{{ $logs->total() }} catatan</div>{{ $logs->links('pagination::bootstrap-5') }}</div>
</div>
@endsection

@section('modals')
@if (auth()->user()->role === 'mahasiswa')
<div class="modal fade" id="logModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" id="logForm" class="modal-content">@csrf @method('PUT')
    <div class="modal-header"><h5 class="modal-title">Ubah Catatan Kegiatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="form-group"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" id="lgTgl" max="{{ date('Y-m-d') }}" required></div>
        <div class="form-group"><label class="form-label">Uraian kegiatan</label><textarea class="form-control" name="kegiatan" id="lgKeg" rows="4" required></textarea></div>
        <small class="text-muted">Setelah disimpan, catatan akan divalidasi ulang oleh pembimbing lapangan.</small></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
</form></div></div>
@endif
@endsection
@section('extra-js')
<script>function openLog(id,t,k){document.getElementById('logForm').action='{{ url('logbook') }}/'+id;document.getElementById('lgTgl').value=t;document.getElementById('lgKeg').value=k;bootstrap.Modal.getOrCreateInstance(document.getElementById('logModal')).show();}</script>
@endsection
