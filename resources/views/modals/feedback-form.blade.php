<!-- Feedback Form Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-chat-dots"></i> Berikan Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="feedbackForm" onsubmit="submitFeedback(event)">
                @csrf
                <div class="modal-body">
                    <!-- Document Info -->
                    <div class="alert alert-info">
                        <strong id="feedbackDocName">Nama Dokumen</strong><br>
                        <small id="feedbackMahasiswaName">Mahasiswa</small>
                    </div>

                    <input type="hidden" id="feedbackDocId" name="document_id">

                    <!-- Feedback Type -->
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Feedback <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="feedback_type" id="type-revision" value="revision" required>
                            <label class="btn btn-outline-warning" for="type-revision">
                                <i class="bi bi-pencil"></i> Revisi
                            </label>

                            <input type="radio" class="btn-check" name="feedback_type" id="type-approved" value="approved">
                            <label class="btn btn-outline-success" for="type-approved">
                                <i class="bi bi-check-circle"></i> Setujui
                            </label>

                            <input type="radio" class="btn-check" name="feedback_type" id="type-rejected" value="rejected">
                            <label class="btn btn-outline-danger" for="type-rejected">
                                <i class="bi bi-x-circle"></i> Tolak
                            </label>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="form-label">Pesan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedbackMessage" name="message" rows="6" 
                                  placeholder="Tuliskan feedback atau komentar Anda..." required></textarea>
                        <small class="form-text text-muted">Maksimal 1000 karakter</small>
                    </div>

                    <!-- Preview -->
                    <div id="feedbackPreview" class="alert alert-light" style="display: none; margin-top: 16px;">
                        <strong>Preview:</strong>
                        <div id="previewContent" style="margin-top: 8px;"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitFeedbackBtn">
                        <i class="bi bi-send"></i> Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-group {
        display: flex;
        gap: 8px;
    }

    .btn-group .btn {
        flex: 1;
        border-radius: 6px;
        font-size: 13px;
    }

    .form-control {
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 14px;
        resize: vertical;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .alert-light {
        background: #f9f9f9;
        border: 1px solid var(--border);
        border-radius: 6px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const feedbackForm = document.getElementById('feedbackForm');
        const messageInput = document.getElementById('feedbackMessage');
        const typeInputs = document.querySelectorAll('input[name="feedback_type"]');
        const preview = document.getElementById('feedbackPreview');
        const previewContent = document.getElementById('previewContent');

        // Update preview on input
        messageInput.addEventListener('input', updatePreview);
        typeInputs.forEach(input => {
            input.addEventListener('change', updatePreview);
        });

        function updatePreview() {
            const type = document.querySelector('input[name="feedback_type"]:checked')?.value;
            const message = messageInput.value;

            if (message) {
                const typeLabel = {
                    'revision': '⚠️ Revisi',
                    'approved': '✅ Disetujui',
                    'rejected': '❌ Ditolak'
                }[type] || 'Feedback';

                previewContent.innerHTML = `
                    <strong>${typeLabel}</strong><br>
                    <p style="margin: 8px 0; line-height: 1.6;">${escapeHtml(message)}</p>
                `;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        feedbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const docId = document.getElementById('feedbackDocId').value;
            const type = document.querySelector('input[name="feedback_type"]:checked').value;
            const message = messageInput.value;

            if (!docId || !type || !message) {
                showToast('Semua field harus diisi', 'warning');
                return;
            }

            const submitBtn = document.getElementById('submitFeedbackBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

            try {
                // TODO: Call API POST /documents/{id}/feedback
                await new Promise(resolve => setTimeout(resolve, 1000)); // Mock delay
                
                showToast('Feedback berhasil dikirim', 'success');
                feedbackForm.reset();
                preview.style.display = 'none';
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                modal.hide();
                
                // Reload documents
                if (typeof loadDocuments === 'function') {
                    loadDocuments();
                }
            } catch (error) {
                showToast('Gagal mengirim feedback', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Kirim Feedback';
            }
        });
    });

    function openFeedbackModal(docId, docName, mahasiswaName) {
        document.getElementById('feedbackDocId').value = docId;
        document.getElementById('feedbackDocName').textContent = docName;
        document.getElementById('feedbackMahasiswaName').textContent = mahasiswaName;
        document.getElementById('feedbackForm').reset();
        document.getElementById('feedbackPreview').style.display = 'none';
        
        const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        modal.show();
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
</script>
