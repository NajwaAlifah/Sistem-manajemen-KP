@extends('layouts.app')
@section('title', $doc->name . ' - Manajemen KP')
@section('page-title', \Illuminate\Support\Str::limit($doc->name, 48))
@section('page-subtitle', $doc->category->name . ' · ' . $doc->owner->name)
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection

@section('header-actions')
    <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-secondary"><i class="bi bi-download"></i> Unduh</a>
@endsection

@section('content')
@php
    $st = \App\Models\Document::STATUS; $me = auth()->user();
    $isOwner = $me->id === $doc->user_id;
    $canEdit = $isOwner && in_array($doc->status, ['draft', 'revision']);
    $canReview = $me->role === 'dosen' && in_array($doc->status, ['submitted', 'reviewed']);
    $docUrl = '/documents/' . $doc->id;
@endphp

<div class="documents-section">
    <div class="flow">
        @foreach (['draft', 'submitted', 'reviewed', 'revision', 'approved'] as $i => $s)
            @if ($i) <i class="bi bi-chevron-right"></i> @endif
            <span class="badge badge-{{ $s }}" @if ($s === $doc->status) style="outline:2px solid var(--primary);outline-offset:2px" @endif>{{ $st[$s] }}</span>
        @endforeach
    </div>
    <div class="info-grid">
        <div class="info-item"><div class="k">Status</div><div class="v"><span class="badge badge-{{ $doc->status }}">{{ $st[$doc->status] }}</span></div></div>
        <div class="info-item"><div class="k">Versi Terbaru</div><div class="v">v{{ $doc->current_version }}.0</div></div>
        <div class="info-item"><div class="k">Mahasiswa</div><div class="v">{{ $doc->owner->name }} @if ($doc->owner->nim)<span class="text-muted">· {{ $doc->owner->nim }}</span>@endif</div></div>
        <div class="info-item"><div class="k">Dosen Pembimbing</div><div class="v">{{ $doc->owner->dosen->name ?? '-' }}</div></div>
        <div class="info-item"><div class="k">Diunggah</div><div class="v">{{ $doc->created_at->format('d M Y, H:i') }}</div></div>
        <div class="info-item"><div class="k">Diajukan / Disetujui</div><div class="v">{{ $doc->submitted_at?->format('d M Y') ?? '-' }} / {{ $doc->approved_at?->format('d M Y') ?? '-' }}</div></div>
        @if ($doc->description)<div class="info-item" style="grid-column:1/-1"><div class="k">Keterangan</div><div class="v" style="font-weight:400">{{ $doc->description }}</div></div>@endif
    </div>

    @if ($canEdit)
    <div class="review-box" style="background:#fff">
        <div class="action-bar">
            <button class="btn btn-primary" onclick="kpDo(this, () => kp('POST', '{{ $docUrl }}/submit'))"><i class="bi bi-send"></i> {{ $doc->status === 'revision' ? 'Ajukan Kembali' : 'Ajukan untuk Diperiksa' }}</button>
            <label class="btn btn-secondary" style="margin:0"><i class="bi bi-cloud-upload"></i> Unggah Versi Baru
                <input type="file" hidden accept=".pdf,.doc,.docx" onchange="if(this.files[0]){const f=new FormData();f.append('file',this.files[0]);kpDo(null,()=>kp('POST','{{ $docUrl }}/replace',f))}"></label>
            @if ($doc->status === 'draft')
            <button class="btn btn-danger" onclick="if(confirm('Hapus dokumen ini beserta seluruh versinya?'))kpDo(this,async()=>{await kp('DELETE','{{ $docUrl }}');location='{{ route('dashboard') }}'})"><i class="bi bi-trash"></i> Hapus</button>
            @endif
        </div>
    </div>
    @endif

    @if ($canReview)
    <div class="review-box">
        <h4 class="mb-2"><i class="bi bi-chat-square-text"></i> Pemeriksaan Dokumen</h4>
        <div class="form-group"><label class="form-label">Keputusan</label>
            <select class="form-control" id="rvType">
                <option value="revision">Minta Revisi</option><option value="approved">Setujui</option>
            </select></div>
        <div class="form-group"><label class="form-label">Catatan <small class="text-muted">(wajib untuk revisi)</small></label>
            <textarea class="form-control" id="rvMsg" rows="4" maxlength="2000" placeholder="Tulis catatan untuk mahasiswa..."></textarea></div>
        <button class="btn btn-primary" onclick="kpDo(this, () => kp('POST', '{{ $docUrl }}/feedback', {type: rvType.value, message: rvMsg.value}))"><i class="bi bi-check2-circle"></i> Kirim Keputusan</button>
    </div>
    @endif
</div>

@php $cv = $doc->versions->firstWhere('version', $doc->current_version); @endphp
<div class="documents-section mt-4">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-eye-fill"></i> Pratinjau Dokumen</h3></div>
    @if ($cv && $cv->mime === 'application/pdf')
        <iframe src="{{ route('documents.download', [$doc->id, 'inline' => 1]) }}" title="Pratinjau" style="width:100%;height:560px;border:0;display:block"></iframe>
    @else
        <div class="sk-panel-body"><div class="sk-note"><i class="bi bi-info-circle"></i> Pratinjau hanya tersedia untuk PDF. Format DOC/DOCX silakan <a href="{{ route('documents.download', $doc->id) }}">diunduh</a>.</div></div>
    @endif
</div>

<div class="documents-section mt-4">
    <div class="kp-tabs nav">
        <a class="nav-link {{ $tab !== 'feedback' ? 'active' : '' }}" data-bs-toggle="tab" href="#tVersions">Riwayat Versi ({{ $doc->versions->count() }})</a>
        <a class="nav-link {{ $tab === 'feedback' ? 'active' : '' }}" data-bs-toggle="tab" href="#tFeedback">Catatan & Status ({{ $doc->reviews->count() }})</a>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade {{ $tab !== 'feedback' ? 'show active' : '' }}" id="tVersions">
            <div class="table-responsive-wrapper"><table class="documents-table">
                <thead><tr><th>Versi</th><th>Berkas</th><th>Ukuran</th><th>Status Saat Unggah</th><th>Tanggal</th><th></th></tr></thead>
                <tbody>
                @foreach ($doc->versions->sortByDesc('version') as $v)
                    <tr><td><strong>v{{ $v->version }}.0</strong>@if ($v->version === $doc->current_version) <span class="badge badge-approved">Terbaru</span>@endif</td>
                        <td>{{ $v->original_name }}</td><td class="text-muted">{{ number_format($v->size / 1048576, 2) }} MB</td>
                        <td><span class="badge badge-{{ $v->status_at_upload }}">{{ $st[$v->status_at_upload] ?? $v->status_at_upload }}</span></td>
                        <td class="doc-date">{{ $v->created_at->format('d M Y, H:i') }}</td>
                        <td><a class="doc-action-btn" href="{{ route('documents.download', [$doc->id, 'v' => $v->version]) }}"><i class="bi bi-download"></i></a></td></tr>
                @endforeach
                </tbody></table></div>
        </div>
        <div class="tab-pane fade {{ $tab === 'feedback' ? 'show active' : '' }}" id="tFeedback">
            @forelse ($doc->reviews as $rv)
                @if ($loop->first)<ul class="timeline">@endif
                <li><strong>{{ $rv->author->name }}</strong> <span class="badge badge-{{ $rv->status_after }}">{{ $st[$rv->status_after] }}</span>
                    <div class="meta">Versi v{{ $rv->version }}.0 · {{ $rv->created_at->format('d M Y, H:i') }}</div>
                    @if ($rv->message)<p style="margin-top:6px">{{ $rv->message }}</p>@endif</li>
                @if ($loop->last)</ul>@endif
            @empty
                <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-chat-dots"></i></div>
                    <div class="empty-state-title">Belum ada catatan</div>
                    <div class="empty-state-text">Catatan dari dosen pembimbing akan muncul di sini.</div></div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('extra-js')<script src="{{ asset('js/kp-pages.js') }}"></script>@endsection
