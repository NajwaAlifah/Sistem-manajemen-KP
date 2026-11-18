@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - Manajemen KP')

@section('page-title', 'Dashboard Mahasiswa')
@section('page-subtitle', 'Kelola dokumen kerja praktik Anda')

@section('header-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-upload"></i> Upload Dokumen
    </button>
@endsection

@section('hero')
    @include('components.hero')
    @include('components.widgets')
    <div class="sk-alert" id="skInfo">
        <div>
            <div>Petunjuk singkat :</div>
            <strong>Unggah dokumen KP Anda, ajukan ke dosen pembimbing, lalu pantau status dan catatan revisinya di sini.</strong><br>
            <button class="sk-link" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="bi bi-cloud-upload"></i> Upload Dokumen</button>
        </div>
        <button class="x" onclick="document.getElementById('skInfo').remove()" aria-label="Tutup">&times;</button>
    </div>
@endsection

@section('content')
    <!-- Status Cards -->
    <div class="status-cards-grid">
        <div class="status-card draft" onclick="filterByStatus('draft')">
            <div class="status-card-icon">
                <i class="bi bi-file-text"></i>
            </div>
            <div class="status-card-label">Draft</div>
            <div class="status-card-count" id="count-draft">0</div>
        </div>

        <div class="status-card submitted" onclick="filterByStatus('submitted')">
            <div class="status-card-icon">
                <i class="bi bi-file-check"></i>
            </div>
            <div class="status-card-label">Diajukan</div>
            <div class="status-card-count" id="count-submitted">0</div>
        </div>

        <div class="status-card revision" onclick="filterByStatus('revision')">
            <div class="status-card-icon">
                <i class="bi bi-file-earmark-exclamation"></i>
            </div>
            <div class="status-card-label">Revisi</div>
            <div class="status-card-count" id="count-revision">0</div>
        </div>

        <div class="status-card approved" onclick="filterByStatus('approved')">
            <div class="status-card-icon">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <div class="status-card-label">Disetujui</div>
            <div class="status-card-count" id="count-approved">0</div>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="documents-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-files"></i> Dokumen Saya
            </h3>
            <div class="section-actions">
                <select class="form-select form-select-sm" id="statusFilter" onchange="filterDocuments()">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Diajukan</option>
                    <option value="revision">Revisi</option>
                    <option value="approved">Disetujui</option>
                    <option value="archived">Arsip</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive-wrapper">
            <table class="documents-table" id="documentsTable">
                <thead>
                    <tr>
                        <th style="width: 30%;">Nama Dokumen</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 28%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="documentsBody">
                    <!-- Filled by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-state-icon">
                <i class="bi bi-file-earmark-diff"></i>
            </div>
            <div class="empty-state-title">Tidak Ada Dokumen</div>
            <div class="empty-state-text">Anda belum mengunggah dokumen. Mulai dengan klik tombol "Upload Dokumen"</div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload"></i> Upload Dokumen Pertama
            </button>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
            <div class="pagination-info">
                <span id="paginationText">Menampilkan 0 dari 0</span>
            </div>
            <ul class="pagination" id="paginationList">
                <!-- Filled by JavaScript -->
            </ul>
        </div>
    </div>
@endsection

@section('modals')
    @include('modals.upload-document')
@endsection

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-mahasiswa.css') }}">
@endsection

@section('extra-js')
    <script src="{{ asset('js/dashboard-mahasiswa.js') }}"></script>
@endsection
