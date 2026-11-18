/* ============================================
   DASHBOARD MAHASISWA - JAVASCRIPT
   ============================================ */

// Configuration
const DOCS_PER_PAGE = 10;
let currentPage = 1;
let currentFilter = '';
let allDocuments = [];
let filteredDocuments = [];

// Use mock data if backend not ready
const USE_MOCK_DATA = false;

// Mock data
const mockDocuments = [
    {
        id: 1,
        name: "Proposal KP - PT Telkom Indonesia",
        category: "proposal",
        status: "draft",
        version: "1.0",
        file_size: 2048576,
        created_at: "2026-11-07T08:30:00Z",
        updated_at: "2026-11-07T08:30:00Z",
        download_url: "#"
    },
    {
        id: 2,
        name: "Surat Pengantar KP",
        category: "surat_pengantar",
        status: "submitted",
        version: "1.0",
        file_size: 512000,
        created_at: "2026-11-06T10:15:00Z",
        updated_at: "2026-11-06T14:20:00Z",
        download_url: "#"
    },
    {
        id: 3,
        name: "Laporan KP - Bab 1-3",
        category: "laporan",
        status: "revision",
        version: "1.1",
        file_size: 3145728,
        created_at: "2026-11-05T09:00:00Z",
        updated_at: "2026-11-06T16:45:00Z",
        download_url: "#"
    },
    {
        id: 4,
        name: "Lembar Bimbingan November",
        category: "lembar_bimbingan",
        status: "approved",
        version: "2.0",
        file_size: 1024000,
        created_at: "2026-10-28T11:30:00Z",
        updated_at: "2026-11-04T13:00:00Z",
        download_url: "#"
    },
    {
        id: 5,
        name: "Laporan Lengkap KP",
        category: "laporan",
        status: "approved",
        version: "3.0",
        file_size: 4096000,
        created_at: "2026-10-15T08:00:00Z",
        updated_at: "2026-11-03T15:30:00Z",
        download_url: "#"
    }
];

// Category mapping
const categoryNames = {
    'proposal': 'Proposal KP',
    'surat_pengantar': 'Surat Pengantar',
    'surat_balasan': 'Surat Balasan',
    'laporan': 'Laporan KP',
    'lembar_bimbingan': 'Lembar Bimbingan',
    'dokumen_revisi': 'Dokumen Revisi',
    'lembar_penilaian': 'Lembar Penilaian',
    'dokumen_pendukung': 'Dokumen Pendukung'
};

// Status labels
const statusLabels = {
    'draft': 'Draft',
    'submitted': 'Diajukan',
    'reviewed': 'Diperiksa',
    'revision': 'Revisi',
    'approved': 'Disetujui',
    'archived': 'Arsip'
};

/**
 * Load documents from API or mock data
 */
async function loadDocuments() {
    try {
        if (USE_MOCK_DATA) {
            allDocuments = mockDocuments;
        } else {
            const response = await apiGet('/documents?limit=100');
            if (response.success) {
                allDocuments = response.data || [];
            }
        }
        
        updateStatusCards();
        filterDocuments();
        
    } catch (error) {
        console.error('Error loading documents:', error);
        showToast('Gagal memuat dokumen', 'error');
        updateStatusCards();
    }
}

/**
 * Update status card counts
 */
function updateStatusCards() {
    const counts = {
        draft: 0,
        submitted: 0,
        revision: 0,
        approved: 0
    };

    allDocuments.forEach(doc => {
        if (counts.hasOwnProperty(doc.status)) {
            counts[doc.status]++;
        }
    });

    Object.keys(counts).forEach(status => {
        document.getElementById(`count-${status}`).textContent = counts[status];
    });
}

/**
 * Filter documents by status
 */
function filterDocuments() {
    const statusFilter = document.getElementById('statusFilter').value;
    currentFilter = statusFilter;
    currentPage = 1;

    if (statusFilter) {
        filteredDocuments = allDocuments.filter(doc => doc.status === statusFilter);
    } else {
        filteredDocuments = [...allDocuments];
    }

    renderTable();
}

/**
 * Filter by status card click
 */
function filterByStatus(status) {
    document.getElementById('statusFilter').value = status;
    filterDocuments();
    document.getElementById('statusFilter').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Render documents table
 */
function renderTable() {
    const tbody = document.getElementById('documentsBody');
    const emptyState = document.getElementById('emptyState');
    const startIdx = (currentPage - 1) * DOCS_PER_PAGE;
    const endIdx = startIdx + DOCS_PER_PAGE;
    const pageDocuments = filteredDocuments.slice(startIdx, endIdx);

    if (pageDocuments.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        updatePagination();
        return;
    }

    emptyState.style.display = 'none';

    // Safe: Using template literals with trusted data from app state
    tbody.innerHTML = pageDocuments.map(doc => `
        <tr class="clickable" onclick="viewDocument(${doc.id})">
            <td>
                <div class="doc-name" title="${escapeHtml(doc.name)}">
                    ${escapeHtml(doc.name)}
                </div>
                <small class="text-muted">v${doc.version}</small>
            </td>
            <td>
                <span class="doc-category">${escapeHtml(categoryNames[doc.category] || doc.category)}</span>
            </td>
            <td>
                <span class="badge ${getBadgeClass(doc.status)}">
                    ${escapeHtml(statusLabels[doc.status] || doc.status)}
                </span>
            </td>
            <td class="doc-date">
                ${formatDate(doc.updated_at)}
            </td>
            <td>
                <div class="doc-actions">
                    <button class="doc-action-btn" title="Lihat" onclick="event.stopPropagation(); viewDocument(${doc.id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="doc-action-btn" title="Download" onclick="event.stopPropagation(); downloadDocument(${doc.id}, '${escapeHtml(doc.name)}')">
                        <i class="bi bi-download"></i>
                    </button>
                    ${doc.status === 'draft' ? `
                        <button class="doc-action-btn" title="Edit" onclick="event.stopPropagation(); editDocument(${doc.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="doc-action-btn" title="Hapus" onclick="event.stopPropagation(); deleteDocument(${doc.id}, '${escapeHtml(doc.name)}')">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="doc-action-btn" title="Kirim" onclick="event.stopPropagation(); submitDocument(${doc.id})">
                            <i class="bi bi-arrow-up-circle"></i>
                        </button>
                    ` : ''}
                    ${doc.status === 'revision' ? `
                        <button class="doc-action-btn" title="Lihat Revisi" onclick="event.stopPropagation(); viewFeedback(${doc.id})">
                            <i class="bi bi-chat-dots"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');

    updatePagination();
}

/**
 * Escape HTML special characters
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Get badge CSS class for status
 */
function getBadgeClass(status) {
    const classes = {
        'draft': 'badge-draft',
        'submitted': 'badge-submitted',
        'reviewed': 'badge-reviewed',
        'revision': 'badge-revision',
        'approved': 'badge-approved',
        'archived': 'badge-archived'
    };
    return classes[status] || 'badge-secondary';
}

/**
 * Update pagination
 */
function updatePagination() {
    const totalPages = Math.ceil(filteredDocuments.length / DOCS_PER_PAGE);
    const startItem = filteredDocuments.length === 0 ? 0 : (currentPage - 1) * DOCS_PER_PAGE + 1;
    const endItem = Math.min(currentPage * DOCS_PER_PAGE, filteredDocuments.length);

    // Update info text
    document.getElementById('paginationText').textContent = 
        `Menampilkan ${startItem}-${endItem} dari ${filteredDocuments.length}`;

    // Generate pagination
    const paginationList = document.getElementById('paginationList');
    paginationList.innerHTML = '';

    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="event.preventDefault(); goToPage(${currentPage - 1})">« Sebelumnya</a>`;
    paginationList.appendChild(prevLi);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="event.preventDefault(); goToPage(${i})">${i}</a>`;
            paginationList.appendChild(li);
        } else if (i === 2 && currentPage > 3) {
            const li = document.createElement('li');
            li.innerHTML = '<span class="page-link">...</span>';
            paginationList.appendChild(li);
        }
    }

    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="event.preventDefault(); goToPage(${currentPage + 1})">Selanjutnya »</a>`;
    paginationList.appendChild(nextLi);
}

/**
 * Go to specific page
 */
function goToPage(page) {
    const totalPages = Math.ceil(filteredDocuments.length / DOCS_PER_PAGE);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
        document.getElementById('documentsTable').scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * View document detail
 */
function viewDocument(docId) {
    // TODO: Redirect to detail page
    window.location.href = `/documents/${docId}`;
}

/**
 * Download document
 */
function downloadDocument(docId, docName) {
    const doc = allDocuments.find(d => d.id === docId);
    if (doc && doc.download_url && doc.download_url !== '#') {
        window.open(doc.download_url, '_blank');
    }
    showToast(`Mengunduh ${docName}...`, 'info');
}

/**
 * Edit document (upload new version)
 */
function editDocument(docId) {
    // Unggah versi baru dilakukan di halaman detail dokumen
    window.location.href = `/documents/${docId}`;
}

/**
 * Delete document
 */
async function deleteDocument(docId, docName) {
    if (!confirm(`Apakah Anda yakin ingin menghapus "${docName}"?`)) return;

    const doc = allDocuments.find(d => d.id === docId);
    if (doc && doc.status === 'draft') {
        try { await apiDelete(`/documents/${docId}`); } catch (e) { showToast(e.message || 'Gagal menghapus dokumen', 'error'); return; }
        allDocuments = allDocuments.filter(d => d.id !== docId);
        updateStatusCards();
        filterDocuments();
        showToast(`${docName} berhasil dihapus`, 'success');
    } else {
        showToast('Hanya dokumen Draft yang dapat dihapus', 'warning');
    }
}

/**
 * Submit document for review
 */
async function submitDocument(docId) {
    const doc = allDocuments.find(d => d.id === docId);
    if (!doc) return;

    if (!['draft', 'revision'].includes(doc.status)) {
        showToast('Hanya dokumen Draft atau Revisi yang dapat diajukan', 'warning');
        return;
    }

    if (!confirm(`Ajukan "${doc.name}" untuk pemeriksaan?`)) return;

    try {
        await apiPost(`/documents/${docId}/submit`);
        doc.status = 'submitted';
        doc.updated_at = new Date().toISOString();
        updateStatusCards();
        filterDocuments();
        showToast(`${doc.name} berhasil diajukan`, 'success');
    } catch (error) {
        showToast(error.message || 'Gagal mengajukan dokumen', 'error');
    }
}

/**
 * View feedback/revision
 */
function viewFeedback(docId) {
    const doc = allDocuments.find(d => d.id === docId);
    if (doc) {
        console.log('View feedback:', doc);
        // TODO: Open feedback modal
        showToast(`Membuka revisi untuk ${doc.name}...`, 'info');
    }
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    loadDocuments();

    // Refresh button (optional)
    const refreshBtn = document.querySelector('[data-refresh]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadDocuments);
    }
});
