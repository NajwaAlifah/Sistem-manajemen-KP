@extends('layouts.app')
@section('title', 'Bimbingan - Manajemen KP')
@section('page-title', 'Bimbingan')
@section('page-subtitle', 'Jadwal konsultasi, catatan, dan riwayat bimbingan')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php $me = auth()->user(); $st = \App\Models\Guidance::STATUS; $bd = ['diajukan' => 'submitted', 'disetujui' => 'approved', 'jadwal_diubah' => 'revision', 'selesai' => 'reviewed']; @endphp
@if ($me->role === 'mahasiswa')
<div class="sk-panel">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-calendar-plus-fill"></i> Ajukan Jadwal Bimbingan</h3>
        <span class="text-muted small">Dosen: {{ $me->dosen->name ?? 'belum ditetapkan' }}</span></div>
    @if ($me->dosen_id)
    <form method="POST" action="{{ route('bimbingan.store') }}" class="sk-panel-body">@csrf
        <div class="sk-form">
            <div><label>Waktu usulan</label><input type="datetime-local" class="form-control" name="tanggal_usul" value="{{ old('tanggal_usul') }}" required></div>
            <div><label>Metode</label><select class="form-control" name="metode"><option value="daring">Daring</option><option value="luring">Luring (tatap muka)</option></select></div>
            <div class="full"><label>Topik / pokok bahasan</label><input class="form-control" name="topik" value="{{ old('topik') }}" maxlength="200" required></div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-send"></i> Ajukan</button>
    </form>
    @else<div class="sk-panel-body"><div class="sk-note">Dosen pembimbing Anda belum ditetapkan oleh admin prodi, sehingga jadwal belum dapat diajukan.</div></div>@endif
</div>
@endif

<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-clock-history"></i> Riwayat Bimbingan</h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:820px">
        <thead><tr><th>Jadwal</th>@if ($me->role !== 'mahasiswa')<th>Mahasiswa</th>@endif<th>Topik</th><th>Status</th><th>Catatan / Lampiran</th>@if ($me->role === 'dosen')<th>Tanggapan</th>@endif</tr></thead>
        <tbody>@forelse ($items as $g)
            <tr><td class="doc-date">{{ ($g->tanggal_final ?? $g->tanggal_usul)->format('d M Y, H:i') }}<div class="text-muted small">{{ ucfirst($g->metode) }}@if ($g->status === 'jadwal_diubah') · usulan: {{ $g->tanggal_usul->format('d M, H:i') }}@endif</div></td>
                @if ($me->role !== 'mahasiswa')<td><strong>{{ $g->owner->name }}</strong></td>@endif
                <td>{{ $g->topik }}</td><td><span class="badge badge-{{ $bd[$g->status] }}">{{ $st[$g->status] }}</span></td>
                <td style="max-width:300px">@if ($g->catatan_dosen)<div class="sk-note">{{ $g->catatan_dosen }}</div>@endif
                    @if ($g->lampiran_path)<div class="mt-1"><a href="{{ route('bimbingan.download', $g->id) }}"><i class="bi bi-paperclip"></i> {{ $g->lampiran_name }}</a></div>@endif
                    @if ($me->role === 'mahasiswa' && $g->status !== 'diajukan')
                        <form method="POST" action="{{ route('bimbingan.lampiran', $g->id) }}" enctype="multipart/form-data" class="sk-inline mt-2">@csrf
                            <input type="file" class="form-control form-control-sm" name="file" required><button class="btn btn-secondary btn-sm"><i class="bi bi-upload"></i> Unggah tindak lanjut</button></form>
                    @endif</td>
                @if ($me->role === 'dosen')
                <td>@if ($g->status !== 'selesai')
                    <form method="POST" action="{{ route('bimbingan.respon', $g->id) }}">@csrf
                        <select class="form-control form-control-sm mb-1" name="aksi" onchange="this.nextElementSibling.style.display=this.value==='ubah'?'':'none'">
                            <option value="setuju">Setujui jadwal</option><option value="ubah">Usul jadwal lain</option><option value="selesai">Tandai selesai + catatan</option></select>
                        <input type="datetime-local" class="form-control form-control-sm mb-1" name="tanggal_final" style="display:none">
                        <textarea class="form-control form-control-sm mb-1" name="catatan_dosen" rows="2" placeholder="Catatan / arahan"></textarea>
                        <button class="btn btn-primary btn-sm">Simpan</button></form>@else<span class="text-muted small">Selesai</span>@endif</td>
                @endif</tr>
        @empty<tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-chat-dots"></i></div><div class="empty-state-title">Belum ada riwayat bimbingan</div></div></td></tr>@endforelse</tbody>
    </table></div>
    <div class="pagination-container"><div class="pagination-info">{{ $items->total() }} sesi</div>{{ $items->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
