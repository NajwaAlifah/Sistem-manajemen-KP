/* ============================================
   DASHBOARD DOSEN - JAVASCRIPT
   ============================================ */

const DOCS_PER_PAGE = 10;
let currentPage = 1;
let currentFilter = '';
let allDocuments = [];
let filteredDocuments = [];

const USE_MOCK_DATA = false;

// Mock data - documents submitted by students
const mockDocuments = [
    {
        id: 1,
        name: "Proposal KP - PT Telkom",
        mahasiswa_name: "Budi Santoso",
        category: "proposal",
        status: "submitted",
        submitted_at: "2026-11-07T08:30:00Z",
        version: "1.0"
    },
    {
        id: 2,
        name: "Laporan KP - Bab 1-3",
        mahasiswa_name: "Siti Nurhaliza",
        category: "laporan",
        status: "submitted",
        submitted_at: "2026-11-06T14:20:00Z",
        version: "1.1"
    },
    {
        id: 3,
        name: "Proposal KP Revisi",
        mahasiswa_name: "Budi Santoso",
        category: "proposal",
        status: "revision",
        submitted_at: "2026-11-05T10:15:00Z",
        version: "2.0"
    },
    {
        id: 4,
        name: "Laporan Lengkap",
        mahasiswa_name: "Ahmad Hidayat",
        category: "laporan",
        status: "approved",
        submitted_at: "2026-11-04T09:00:00Z",
        version: "3.0"
    }
];

const statusLabels = {
    'submitted': 'Menunggu',
    'revision': 'Revisi',
    'approved': 'Disetujui',
    'archived': 'Arsip'
};

const categoryNames = {
    'proposal': 'Proposal KP',
    'surat_pengantar': 'Surat Pengantar',
    'laporan': 'Laporan KP',
    'lembar_bimbingan': 'Lembar Bimbingan'
};

async function loadDocuments() {
    try {
        if (USE_MOCK_DATA) {
            allDocuments = mockDocuments;
        } else {
            const response = await apiGet('/documents?role=dosen&limit=100');
            if (response.success) {
                allDocuments = response.data || [];
            }
        }
        
        updateStatusCards();
        filterDocuments();
    } catch (error) {
        console.error('Error loading documents:', error);
        showToast('Gagal memuat dokumen', 'error');
    }
}

function updateStatusCards() {
    const counts = {
        submitted: 0,
        revision: 0,
        approved: 0,
        archived: 0
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

function filterByStatus(status) {
    document.getElementById('statusFilter').value = status;
    filterDocuments();
}

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

    tbody.innerHTML = pageDocuments.map(doc => `
        <tr>
            <td>
                <strong>${escapeHtml(doc.mahasiswa_name)}</strong><br>
                <small class="text-muted">${escapeHtml(doc.mahasiswa_name)}</small>
            </td>
            <td>
                <a href="#" onclick="event.preventDefault(); viewDocument(${doc.id})" class="doc-name">
                    ${escapeHtml(doc.name)}
                </a><br>
                <small class="text-muted">${categoryNames[doc.category] || doc.category}</small>
            </td>
            <td>
                <span class="badge ${getBadgeClass(doc.status)}">
                    ${statusLabels[doc.status] || doc.status}
                </span>
            </td>
            <td class="text-muted">
                ${formatDate(doc.submitted_at)}
            </td>
            <td>
                <div class="doc-actions">
                    <button class="doc-action-btn" title="Lihat" onclick="viewDocument(${doc.id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="doc-action-btn" title="Download" onclick="downloadDocument(${doc.id})">
                        <i class="bi bi-download"></i>
                    </button>
                    ${doc.status === 'submitted' || doc.status === 'revision' ? `
                        <button class="doc-action-btn" title="Berikan Feedback" onclick="openFeedbackModal(${doc.id}, '${escapeHtml(doc.name)}', '${escapeHtml(doc.mahasiswa_name)}')">
                            <i class="bi bi-chat-dots"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');

    updatePagination();
}

function getBadgeClass(status) {
    const classes = {
        'submitted': 'badge-submitted',
        'revision': 'badge-revision',
        'approved': 'badge-approved',
        'archived': 'badge-archived'
    };
    return classes[status] || 'badge-secondary';
}

function updatePagination() {
    const totalPages = Math.ceil(filteredDocuments.length / DOCS_PER_PAGE);
    const startItem = filteredDocuments.length === 0 ? 0 : (currentPage - 1) * DOCS_PER_PAGE + 1;
    const endItem = Math.min(currentPage * DOCS_PER_PAGE, filteredDocuments.length);

    document.getElementById('paginationText').textContent = 
        `Menampilkan ${startItem}-${endItem} dari ${filteredDocuments.length}`;

    const paginationList = document.getElementById('paginationList');
    paginationList.innerHTML = '';

    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="event.preventDefault(); goToPage(${currentPage - 1})">« Sebelumnya</a>`;
    paginationList.appendChild(prevLi);

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

    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="event.preventDefault(); goToPage(${currentPage + 1})">Selanjutnya »</a>`;
    paginationList.appendChild(nextLi);
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredDocuments.length / DOCS_PER_PAGE);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
        document.getElementById('documentsTable').scrollIntoView({ behavior: 'smooth' });
    }
}

function viewDocument(docId) {
    window.location.href = `/documents/${docId}`;
}

function downloadDocument(docId) {
    const doc = allDocuments.find(d => d.id === docId);
    if (doc && doc.download_url) {
        window.open(doc.download_url, '_blank');
    }
}

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

document.addEventListener('DOMContentLoaded', () => {
    loadDocuments();
});
