@extends('layouts.app')

@section('title', 'Dashboard Dosen - Manajemen KP')

@section('page-title', 'Dashboard Dosen')
@section('page-subtitle', 'Periksa dan beri feedback dokumen mahasiswa')

@section('hero')
    @include('components.hero')
    @include('components.widgets')
@endsection

@section('content')
    <!-- Status Cards -->
    <div class="status-cards-grid">
        <div class="status-card submitted" onclick="filterByStatus('submitted')">
            <div class="status-card-icon">
                <i class="bi bi-file-check"></i>
            </div>
            <div class="status-card-label">Menunggu Pemeriksaan</div>
            <div class="status-card-count" id="count-submitted">0</div>
        </div>

        <div class="status-card revision" onclick="filterByStatus('revision')">
            <div class="status-card-icon">
                <i class="bi bi-file-earmark-exclamation"></i>
            </div>
            <div class="status-card-label">Revisi Dikirim</div>
            <div class="status-card-count" id="count-revision">0</div>
        </div>

        <div class="status-card approved" onclick="filterByStatus('approved')">
            <div class="status-card-icon">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <div class="status-card-label">Telah Disetujui</div>
            <div class="status-card-count" id="count-approved">0</div>
        </div>

        <div class="status-card archived" onclick="filterByStatus('archived')">
            <div class="status-card-icon">
                <i class="bi bi-archive"></i>
            </div>
            <div class="status-card-label">Diarsipkan</div>
            <div class="status-card-count" id="count-archived">0</div>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="documents-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-files"></i> Dokumen untuk Pemeriksaan
            </h3>
            <div class="section-actions">
                <select class="form-select form-select-sm" id="statusFilter" onchange="filterDocuments()">
                    <option value="">Semua Status</option>
                    <option value="submitted">Menunggu Pemeriksaan</option>
                    <option value="revision">Revisi Dikirim</option>
                    <option value="approved">Telah Disetujui</option>
                    <option value="archived">Diarsipkan</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive-wrapper">
            <table class="documents-table" id="documentsTable">
                <thead>
                    <tr>
                        <th style="width: 25%;">Nama Mahasiswa</th>
                        <th style="width: 25%;">Dokumen</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 15%;">Tanggal Submit</th>
                        <th style="width: 23%;">Aksi</th>
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
                <i class="bi bi-inbox"></i>
            </div>
            <div class="empty-state-title">Tidak Ada Dokumen</div>
            <div class="empty-state-text">Belum ada dokumen yang perlu Anda periksa</div>
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
    @include('modals.feedback-form')
@endsection

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-dosen.css') }}">
@endsection

@section('extra-js')
    <script src="{{ asset('js/dashboard-dosen.js') }}"></script>
@endsection
