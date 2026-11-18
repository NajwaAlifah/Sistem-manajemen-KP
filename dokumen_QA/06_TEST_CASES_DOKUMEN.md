# Test Case - Dokumen & Review
**Modul:** DocumentController (API + Web)  
**Routes API:** GET/POST /api/documents, GET /api/documents/{id}, GET /api/documents/{id}/versions, PUT /api/documents/{id}, POST /api/documents/{id}/replace, DELETE /api/documents/{id}, POST /api/documents/{id}/submit, POST /api/documents/{id}/feedback, PUT /api/documents/{id}/approve, GET /api/documents/{id}/download
**Routes Web:** GET /documents/{id}, GET /documents/{id}/history, GET /documents/{id}/download, GET /archive

---

## TC-DOC-001: Upload Dokumen - Mahasiswa Upload Draft
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. POST `/api/documents` | `category`: slug kategori aktif, `description`: "Laporan KP", `file`: PDF 2MB | 201 Created, status = `draft`, version = 1, file tersimpan di private storage |
| 3. Cek DB documents | - | Record created, user_id = mahasiswa, category_id = kategori |
| 4. Cek document_versions | - | Version 1 created dengan path, mime, size |
| 5. Cek audit log | - | `action: dokumen_unggah` |

---

## TC-DOC-002: Upload Dokumen - Validasi Kategori
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Kategori tidak exist | `category`: "invalid-slug" | Error 422: "Kategori tidak valid" |
| 2. Kategori tidak aktif | `category`: slug kategori is_active=false | Error 422: "Kategori tidak valid" (where is_active true) |
| 3. Kategori valid & aktif | - | Success |

---

## TC-DOC-003: Upload Dokumen - Validasi File
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Ekstensi tidak diizinkan | `file`: `test.exe` | Error: "Format file harus: PDF, DOC, DOCX" |
| 2. File > max size kategori | `file`: PDF 15MB (kategori max 10MB) | Error: "File terlalu besar (max 10MB)" |
| 3. File kosong | `file`: (tidak dikirim) | Error: file wajib diisi |
| 4. File valid PDF/DOC/DOCX ≤ max size | - | Success |

---

## TC-DOC-004: Upload Dokumen - Non-Mahasiswa
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen/admin/lapangan | - | - |
| 2. POST `/api/documents` | Data valid | Error 403: "Hanya mahasiswa yang dapat mengunggah dokumen." |

---

## TC-DOC-005: List Dokumen - Mahasiswa Melihat Sendiri
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (5 dokumen) | - | - |
| 2. GET `/api/documents` | - | 200 OK, data 5 dokumen milik mahasiswa |
| 3. Cek response | - | success=true, data array, pagination info |

---

## TC-DOC-006: List Dokumen - Dosen Melihat Binaan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (3 mahasiswa binaan, total 8 dokumen) | - | - |
| 2. GET `/api/documents` | - | 8 dokumen mahasiswa binaan |
| 3. Cek visibleTo scope | - | Hanya dokumen mahasiswa binaan |

---

## TC-DOC-007: List Dokumen - Admin Melihat Semua
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/api/documents` | - | Semua dokumen semua mahasiswa |

---

## TC-DOC-008: List Dokumen - Filter & Search
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Filter status | `status`: "approved" | Hanya dokumen status approved |
| 2. Filter archive | `status`: "archive" | Status approved ATAU archived |
| 3. Filter kategori | `category`: "laporan" | Hanya kategori slug=laporan |
| 4. Search nama | `search`: "Laporan" | Nama dokumen ATAU nama mahasiswa mengandung "Laporan" |
| 5. Filter tanggal | `date_from`: "2026-01-01", `date_to`: "2026-01-31" | created_at dalam range |
| 6. Sorting | `sort_by`: "name", `order`: "asc" | Urut nama A-Z |
| 7. Pagination limit | `limit`: 5 | Per page = 5 (min 5, max 100) |

---

## TC-DOC-009: Detail Dokumen - Mahasiswa Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (dokumen sendiri) | - | - |
| 2. GET `/api/documents/{id}` | - | 200 OK, document detail + versions + reviews |

---

## TC-DOC-010: Detail Dokumen - Dosen Review (Auto Status Change)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (dokumen mahasiswa binaan status `submitted`) | - | - |
| 2. GET `/api/documents/{id}` | - | Status otomatis berubah ke `reviewed`, response include updated document |

---

## TC-DOC_011: Detail Dokumen - Unauthorized
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A (dokumen mahasiswa B, dosen bukan pembimbing) | - | - |
| 2. GET `/api/documents/{id}` | - | 403: "Anda tidak berwenang mengakses dokumen ini." |

---

## TC-DOC-012: Update Dokumen - Pemilik, Status Draft/Revisi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (dokumen status `draft`) | - | - |
| 2. PUT `/api/documents/{id}` | `description`: "Deskripsi baru" | Success, metadata updated |
| 3. PUT dengan file baru | `file`: PDF baru | current_version++ (2), version 2 created, status_at_upload = draft |
| 4. Cek audit log | - | `action: dokumen_versi_baru` |

---

## TC-DOC-013: Update Dokumen - Status Submitted/Reviewed/Approved (Tidak Bisa)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Dokumen status `submitted` | - | - |
| 2. PUT `/api/documents/{id}` | Data valid | Error 422: "Dokumen hanya dapat diubah saat berstatus Draft atau Revisi." |

---

## TC-DOC-014: Update Dokumen - Bukan Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A | - | - |
| 2. PUT dokumen mahasiswa B | - | 403: "Bukan dokumen Anda." |

---

## TC-DOC-015: Hapus Dokumen - Pemilik, Status Draft
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (dokumen status `draft`, punya 2 version) | - | - |
| 2. DELETE `/api/documents/{id}` | - | Success, semua version files dihapus dari storage, record deleted |
| 3. Cek audit log | - | `action: dokumen_hapus` |

---

## TC-DOC-016: Hapus Dokumen - Status Bukan Draft
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Dokumen status `revision` | - | - |
| 2. DELETE oleh pemilik | - | 403: "Dokumen ini tidak dapat dihapus." |

---

## TC-DOC-017: Hapus Dokumen - Admin Bisa Hapus Semua
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. DELETE dokumen status `approved` milik mahasiswa | - | Success (admin bypass ownership & status check) |

---

## TC-DOC-018: Submit Dokumen - Mahasiswa Draft/Revisi → Submitted
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (dokumen status `draft` atau `revision`) | - | - |
| 2. POST `/api/documents/{id}/submit` | - | Success: "Dokumen berhasil diajukan untuk pemeriksaan", status = `submitted`, submitted_at = now() |
| 3. Cek notifikasi dosen | - | Notif: "Dokumen menunggu pemeriksaan" |
| 4. Cek audit log | - | `action: dokumen_ajukan` |

---

## TC-DOC-019: Submit Dokumen - Status Bukan Draft/Revisi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Dokumen status `submitted` | - | - |
| 2. POST submit | - | Error 422: "Dokumen hanya dapat diubah saat berstatus Draft atau Revisi." (ownedEditable check) |

---

## TC-DOC-020: Feedback/Review - Dosen Revisi (Dengan Catatan)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (dokumen binaan status `submitted`/`reviewed`) | - | - |
| 2. POST `/api/documents/{id}/feedback` | `type`: "revision", `message`: "Perbaiki bab 2" | Success, status = `revision`, review created type=revision |
| 3. Cek notifikasi mahasiswa | - | Notif: "Dokumen perlu revisi" + pesan |
| 4. Cek audit log | - | `action: dokumen_review`, detail: "[nama] revision" |

---

## TC-DOC-021: Feedback/Review - Dosen Revisi Tanpa Catatan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST feedback | `type`: "revision", `message`: "" | Error 422: "Catatan wajib diisi untuk permintaan revisi." |

---

## TC-DOC-022: Feedback/Review - Dosen Approve
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (dokumen status `submitted`/`reviewed`) | - | - |
| 2. POST `/api/documents/{id}/feedback` | `type`: "approved", `message`: "Baik" (optional) | Success, status = `approved`, approved_at = now(), approved_by = dosen |
| 3. Cek notifikasi mahasiswa | - | Notif: "Dokumen disetujui" |
| 4. Cek review record | - | type=approved, status_after=approved |

---

## TC-DOC-023: Feedback/Review - Dosen Reject
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST feedback | `type`: "rejected", `message`: "Tidak memenuhi syarat" | Success, status = `revision` (reject = revision), review type=rejected |

---

## TC-DOC-024: Feedback/Review - Shortcut Approve Endpoint
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. PUT `/api/documents/{id}/approve` | - | Sama dengan feedback type=approved |

---

## TC-DOC-025: Feedback/Review - Non-Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/admin/lapangan | - | - |
| 2. POST feedback | - | 403: "Hanya Dosen Pembimbing yang dapat memeriksa dokumen." |

---

## TC-DOC-026: Feedback/Review - Dokumen Status Sudah Diputuskan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Dokumen status `approved` | - | - |
| 2. Dosen POST feedback | - | Error 422: "Dokumen belum diajukan atau sudah diputuskan." |

---

## TC-DOC-027: Download Dokumen - Akses Berdasarkan Role
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa pemilik download v1 | `v`: 1 | File download (inline jika PDF & inline=true) |
| 2. Dosen pembimbing download latest | (tanpa v) | File download versi terbaru |
| 3. Admin download | - | File download |
| 4. Unauthorized | - | 403/404 |

---

## TC-DOC-028: Download Dokumen - File Tidak Ada
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Version record ada tapi file dihapus manual dari storage | - | - |
| 2. GET download | - | 404: "Berkas tidak ditemukan." |

---

## TC-DOC-029: Versi Dokumen - List Versi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Dokumen punya 3 versi | - | - |
| 2. GET `/api/documents/{id}/versions` | - | List 3 versi dengan metadata (version, size, mime, status_at_upload, uploaded_by) |

---

## TC-DOC-030: Arsip Dokumen - Web View
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/dosen/admin | - | - |
| 2. GET `/archive` | - | Halaman arsip tampil (dokumen approved + archived) |

---

## TC-DOC-031: Flow Lengkap Dokumen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa upload draft | - | Status `draft` v1 |
| 2. Mahasiswa update file (v2) | - | Status `draft` v2 |
| 3. Mahasiswa submit | - | Status `submitted`, notif ke dosen |
| 4. Dosen buka detail (auto reviewed) | - | Status `reviewed` |
| 5. Dosen minta revisi | - | Status `revision`, notif ke mahasiswa |
| 6. Mahasiswa update file (v3) | - | Status `revision` v3 |
| 7. Mahasiswa submit lagi | - | Status `submitted` |
| 8. Dosen approve | - | Status `approved`, notif ke mahasiswa |
| 9. Mahasiswa download approved | - | File v3 download |

---

## Traceability Matrix - Dokumen
| Requirement | Test Case |
|-------------|-----------|
| REQ-DOC-01: Upload dokumen mahasiswa | TC-DOC-001 s.d 004 |
| REQ-DOC-02: Validasi kategori & file | TC-DOC-002, 003 |
| REQ-DOC-03: List dokumen dengan filter/search/pagination | TC-DOC-005 s.d 008 |
| REQ-DOC-04: Detail dokumen & auto reviewed | TC-DOC-009 s.d 011 |
| REQ-DOC-05: Update metadata & file (draft/revision only) | TC-DOC-012 s.d 014 |
| REQ-DOC-06: Versioning otomatis | TC-DOC-012, 029 |
| REQ-DOC-07: Hapus dokumen (draft only, admin all) | TC-DOC-015 s.d 017 |
| REQ-DOC-08: Submit ke dosen | TC-DOC-018, 019 |
| REQ-REVIEW-01: Review dosen (revision/approved/rejected) | TC-DOC-020 s.d 023 |
| REQ-REVIEW-02: Validasi catatan wajib revisi | TC-DOC-021 |
| REQ-REVIEW-03: RBAC review hanya dosen pembimbing | TC-DOC-025 |
| REQ-REVIEW-04: Guard status sudah diputuskan | TC-DOC-026 |
| REQ-DOC-09: Download dengan akses control | TC-DOC-027, 028 |
| REQ-ARSIP-01: Arsip view | TC-DOC-030 |