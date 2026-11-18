<!-- Upload Document Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cloud-upload"></i> Upload Dokumen Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Category Select -->
                    <div class="form-group">
                        <label class="form-label" for="category">Kategori <span class="text-danger">*</span></label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="proposal">Proposal KP</option>
                            <option value="surat_pengantar">Surat Pengantar</option>
                            <option value="surat_balasan">Surat Balasan Perusahaan</option>
                            <option value="laporan">Laporan KP</option>
                            <option value="lembar_bimbingan">Lembar Bimbingan</option>
                            <option value="dokumen_revisi">Dokumen Revisi</option>
                            <option value="lembar_penilaian">Lembar Penilaian</option>
                            <option value="dokumen_pendukung">Dokumen Pendukung</option>
                        </select>
                        <small class="form-text text-muted">Pilih kategori dokumen yang akan diunggah</small>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Tambahkan catatan atau deskripsi singkat..."></textarea>
                        <small class="form-text text-muted">Maksimal 500 karakter</small>
                    </div>

                    <!-- File Upload Area -->
                    <div class="form-group">
                        <label class="form-label" for="file">File Dokumen <span class="text-danger">*</span></label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>
                            <div class="file-upload-text">
                                <p class="file-upload-main">Drag & drop file di sini atau klik untuk browse</p>
                                <p class="file-upload-sub">Format: PDF, DOC, DOCX | Max: 10 MB</p>
                            </div>
                            <input type="file" id="file" name="file" accept=".pdf,.doc,.docx" required style="display: none;">
                        </div>
                        <small class="form-text text-muted" id="fileInfo"></small>
                    </div>

                    <!-- File Info -->
                    <div id="fileInfoBox" style="display: none;">
                        <div class="alert alert-info">
                            <strong>File terpilih:</strong><br>
                            Nama: <span id="fileName">-</span><br>
                            Ukuran: <span id="fileSize">-</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div id="uploadProgress" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="form-text text-muted" id="progressText">0%</small>
                    </div>

                    <!-- Error/Success Message -->
                    <div id="uploadMessage"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-cloud-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .file-upload-area {
        border: 2px dashed var(--primary);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        background: rgba(102, 126, 234, 0.05);
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        background: rgba(102, 126, 234, 0.1);
        border-color: var(--primary-dark);
    }

    .file-upload-area.active {
        background: rgba(102, 126, 234, 0.15);
        border-color: var(--primary-dark);
    }

    .file-upload-icon {
        font-size: 48px;
        color: var(--primary);
        margin-bottom: 12px;
    }

    .file-upload-main {
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .file-upload-sub {
        font-size: 13px;
        color: var(--gray);
        margin: 4px 0 0 0;
    }

    .progress {
        height: 24px;
        border-radius: 4px;
        background: #e9ecef;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const uploadForm = document.getElementById('uploadForm');
        const fileInput = document.getElementById('file');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInfoBox = document.getElementById('fileInfoBox');
        const submitBtn = document.getElementById('submitBtn');

        // Drag & drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('active');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('active');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('active');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        // Click to browse
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });

        // Display file info
        function displayFileInfo(file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['application/pdf', 'application/msword', 
                                 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            // Validation
            if (!allowedTypes.includes(file.type) && !/\.(pdf|docx?)$/i.test(file.name)) {
                showMessage('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX', 'error');
                fileInput.value = '';
                fileInfoBox.style.display = 'none';
                return;
            }

            if (file.size > maxSize) {
                showMessage('File terlalu besar. Maksimal 10 MB', 'error');
                fileInput.value = '';
                fileInfoBox.style.display = 'none';
                return;
            }

            // Show info
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatBytes(file.size);
            fileInfoBox.style.display = 'block';
            clearMessage();
        }

        // Form submit
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const category = document.getElementById('category').value;
            const description = document.getElementById('description').value;
            const file = fileInput.files[0];

            if (!category || !file) {
                showMessage('Kategori dan file harus dipilih', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengunggah...';
            document.getElementById('uploadProgress').style.display = 'block';

            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('category', category);
                formData.append('description', description);

                const response = await apiUploadFile('/documents', file, {
                    category,
                    description
                });

                showMessage('Dokumen berhasil diunggah!', 'success');
                
                // Reset form
                setTimeout(() => {
                    uploadForm.reset();
                    fileInfoBox.style.display = 'none';
                    document.getElementById('uploadProgress').style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
                    
                    // Reload documents
                    loadDocuments();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                    modal.hide();
                }, 1500);

            } catch (error) {
                showMessage(error.message || 'Gagal mengunggah dokumen', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
            }
        });

        function showMessage(message, type) {
            const messageBox = document.getElementById('uploadMessage');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'}`;
            alertDiv.textContent = message; // Safe: textContent for user message
            messageBox.innerHTML = ''; // Clear previous
            messageBox.appendChild(alertDiv);
        }

        function clearMessage() {
            document.getElementById('uploadMessage').innerHTML = '';
        }
    });
</script>
