<!-- User Form Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">
                    <i class="bi bi-person-plus"></i> Tambah Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="userForm" onsubmit="submitUserForm(event)">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">

                    <div class="form-group mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen Pembimbing</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="userPassword" name="password" required minlength="6">
                        <small class="form-text text-muted">Min 6 karakter</small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="userActive" name="is_active" checked>
                        <label class="form-check-label" for="userActive">
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Form Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-tag-plus"></i> Tambah Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="categoryForm" onsubmit="submitCategoryForm(event)">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categorySlug" name="slug" required>
                        <small class="form-text text-muted">Format: lowercase-dengan-dash</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="categoryDesc" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Ukuran File Max (MB)</label>
                        <input type="number" class="form-control" id="categoryMaxSize" name="max_file_size" value="10" min="1" max="100">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="categoryActive" name="is_active" checked>
                        <label class="form-check-label" for="categoryActive">
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUserModal(userId = null) {
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        const form = document.getElementById('userForm');
        
        if (userId) {
            document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
            // TODO: Load user data
        } else {
            document.getElementById('userModalTitle').textContent = 'Tambah Pengguna';
            form.reset();
        }
        modal.show();
    }

    function openCategoryModal() {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        document.getElementById('categoryForm').reset();
        modal.show();
    }

    function submitUserForm(e) {
        e.preventDefault();
        const name = document.getElementById('userName').value;
        const email = document.getElementById('userEmail').value;
        const role = document.getElementById('userRole').value;
        
        showToast(`Pengguna ${name} berhasil disimpan`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        
        if (typeof loadUsers === 'function') {
            loadUsers();
        }
    }

    function submitCategoryForm(e) {
        e.preventDefault();
        const name = document.getElementById('categoryName').value;
        
        showToast(`Kategori ${name} berhasil disimpan`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        
        if (typeof loadCategories === 'function') {
            loadCategories();
        }
    }
</script>
