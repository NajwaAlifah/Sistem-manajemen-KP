/* ============================================
   DASHBOARD ADMIN - JAVASCRIPT
   ============================================ */

const USE_MOCK_DATA = true;

// Mock Users
const mockUsers = [
    { id: 1, name: 'Budi Santoso', email: 'budi@test.com', role: 'mahasiswa', is_active: true, created_at: '2026-10-15' },
    { id: 2, name: 'Siti Nurhaliza', email: 'siti@test.com', role: 'mahasiswa', is_active: true, created_at: '2026-10-16' },
    { id: 3, name: 'Dr. Ahmad Hidayat', email: 'ahmad.dosen@test.com', role: 'dosen', is_active: true, created_at: '2026-10-01' },
    { id: 4, name: 'Admin User', email: 'admin@test.com', role: 'admin', is_active: true, created_at: '2026-10-01' }
];

// Mock Categories
const mockCategories = [
    { id: 1, name: 'Proposal KP', slug: 'proposal', description: 'Proposal untuk kerja praktik', is_active: true },
    { id: 2, name: 'Surat Pengantar', slug: 'surat_pengantar', description: 'Surat pengantar dari kampus', is_active: true },
    { id: 3, name: 'Laporan KP', slug: 'laporan', description: 'Laporan akhir kerja praktik', is_active: true },
    { id: 4, name: 'Lembar Bimbingan', slug: 'lembar_bimbingan', description: 'Lembar bimbingan mingguan', is_active: true }
];

// Mock Archive Documents
const mockArchive = [
    { id: 1, name: 'Proposal KP 2025', mahasiswa: 'Budi Santoso', archived_at: '2026-11-01' },
    { id: 2, name: 'Laporan Lengkap 2025', mahasiswa: 'Siti Nurhaliza', archived_at: '2026-11-02' }
];

const roleLabels = {
    'mahasiswa': 'Mahasiswa',
    'dosen': 'Dosen Pembimbing',
    'admin': 'Administrator'
};

async function loadAdminDashboard() {
    try {
        // Load stats
        await loadStats();
        
        // Load users
        await loadUsers();
        
        // Load categories
        await loadCategories();
        
        // Load archive
        await loadArchive();
    } catch (error) {
        console.error('Error loading admin dashboard:', error);
        showToast('Gagal memuat data', 'error');
    }
}

async function loadStats() {
    // Mock stats
    const totalUsers = mockUsers.length;
    const totalDocs = 15;
    const pendingDocs = 3;
    const approvedDocs = 10;

    document.getElementById('totalUsers').textContent = totalUsers;
    document.getElementById('totalDocs').textContent = totalDocs;
    document.getElementById('pendingDocs').textContent = pendingDocs;
    document.getElementById('approvedDocs').textContent = approvedDocs;
}

async function loadUsers() {
    const tbody = document.getElementById('usersBody');
    
    tbody.innerHTML = mockUsers.map(user => `
        <tr>
            <td>
                <strong>${escapeHtml(user.name)}</strong>
            </td>
            <td>
                <code>${escapeHtml(user.email)}</code>
            </td>
            <td>
                <span class="badge ${getRoleBadgeClass(user.role)}">
                    ${roleLabels[user.role]}
                </span>
            </td>
            <td>
                <span class="badge ${user.is_active ? 'bg-success' : 'bg-secondary'}">
                    ${user.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            </td>
            <td>
                <small class="text-muted">${user.created_at}</small>
            </td>
            <td>
                <button class="btn btn-xs btn-outline-primary" onclick="editUser(${user.id})">Edit</button>
                <button class="btn btn-xs btn-outline-danger" onclick="deleteUser(${user.id}, '${escapeHtml(user.name)}')">Hapus</button>
            </td>
        </tr>
    `).join('');
}

async function loadCategories() {
    const grid = document.getElementById('categoriesGrid');
    
    grid.innerHTML = mockCategories.map(cat => `
        <div class="col-md-6 mb-3">
            <div class="category-card">
                <div class="category-header">
                    <h5 class="mb-0">${escapeHtml(cat.name)}</h5>
                    <span class="badge ${cat.is_active ? 'bg-success' : 'bg-secondary'}">
                        ${cat.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                </div>
                <p class="text-muted small mb-2">${escapeHtml(cat.description)}</p>
                <small class="text-muted">Slug: <code>${cat.slug}</code></small>
                <div class="category-actions mt-2">
                    <button class="btn btn-xs btn-outline-primary" onclick="editCategory(${cat.id})">Edit</button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteCategory(${cat.id})">Hapus</button>
                </div>
            </div>
        </div>
    `).join('');
}

async function loadArchive() {
    const tbody = document.getElementById('archiveBody');
    
    tbody.innerHTML = mockArchive.map(doc => `
        <tr>
            <td>${escapeHtml(doc.name)}</td>
            <td>${escapeHtml(doc.mahasiswa)}</td>
            <td><small class="text-muted">${doc.archived_at}</small></td>
            <td>
                <button class="btn btn-xs btn-outline-primary" onclick="restoreDocument(${doc.id})">Kembalikan</button>
            </td>
        </tr>
    `).join('');
}

function getRoleBadgeClass(role) {
    const classes = {
        'mahasiswa': 'bg-info',
        'dosen': 'bg-primary',
        'admin': 'bg-danger'
    };
    return classes[role] || 'bg-secondary';
}

function editUser(userId) {
    const user = mockUsers.find(u => u.id === userId);
    if (user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userRole').value = user.role;
        document.getElementById('userActive').checked = user.is_active;
        
        document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
        new bootstrap.Modal(document.getElementById('userModal')).show();
    }
}

function deleteUser(userId, userName) {
    if (confirm(`Hapus pengguna "${userName}"?`)) {
        const idx = mockUsers.findIndex(u => u.id === userId);
        if (idx > -1) {
            mockUsers.splice(idx, 1);
            loadUsers();
            showToast(`${userName} berhasil dihapus`, 'success');
        }
    }
}

function editCategory(catId) {
    const cat = mockCategories.find(c => c.id === catId);
    if (cat) {
        document.getElementById('categoryName').value = cat.name;
        document.getElementById('categorySlug').value = cat.slug;
        document.getElementById('categoryDesc').value = cat.description;
        document.getElementById('categoryActive').checked = cat.is_active;
        
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }
}

function deleteCategory(catId) {
    if (confirm('Hapus kategori ini?')) {
        const idx = mockCategories.findIndex(c => c.id === catId);
        if (idx > -1) {
            mockCategories.splice(idx, 1);
            loadCategories();
            showToast('Kategori berhasil dihapus', 'success');
        }
    }
}

function restoreDocument(docId) {
    if (confirm('Kembalikan dokumen dari arsip?')) {
        const idx = mockArchive.findIndex(d => d.id === docId);
        if (idx > -1) {
            mockArchive.splice(idx, 1);
            loadArchive();
            showToast('Dokumen berhasil dikembalikan', 'success');
        }
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
    loadAdminDashboard();
});
