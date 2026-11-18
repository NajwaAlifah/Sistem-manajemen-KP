# Test Case - Logbook & Validasi
**Modul:** LogbookController  
**Routes:** GET /logbook, POST /logbook, PUT /logbook/{id}, DELETE /logbook/{id}, POST /logbook/{id}/validasi, GET /logbook/{id}/file

---

## TC-LB-001: Logbook Index - Mahasiswa Melihat Logbook Sendiri
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (punya 5 logbook) | - | - |
| 2. Buka `/logbook` | - | List 5 logbook sendiri, paginasi 15 |
| 3. Cek query | - | WHERE user_id = mahasiswa_id |

---

## TC-LB-002: Logbook Index - Dosen Melihat Logbook Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (3 mahasiswa binaan, total 10 logbook) | - | - |
| 2. Buka `/logbook` | - | List 10 logbook dari 3 mahasiswa binaan |
| 3. Filter mahasiswa | `mahasiswa`: id_mhs_1 | Hanya logbook mahasiswa terpilih |

---

## TC-LB-003: Logbook Index - Lapangan Melihat Logbook Menunggu Validasi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan (2 mahasiswa binaan, 3 logbook status menunggu) | - | - |
| 2. Buka `/logbook` | - | Default filter status=menunggu, tampil 3 logbook |
| 3. Hapus filter status | `status`: "" | Tampil semua logbook binaan |

---

## TC-LB-004: Logbook Index - Admin Melihat Semua
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/logbook` | - | Semua logbook semua mahasiswa |
| 3. Filter status & mahasiswa | `status`: "disetujui", `mahasiswa`: id | Filter bekerja |

---

## TC-LB-005: Tambah Logbook - Berhasil
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. POST `/logbook` | `tanggal`: "2026-01-20", `kegiatan`: "Mempelajari arsitektur sistem", `file`: (PDF 2MB) | Success: "Kegiatan berhasil dicatat.", status = `menunggu` |
| 3. Cek DB | - | Record logbook created, file_path terisi |
| 4. Cek notifikasi lapangan | - | Notif ke pembimbing lapangan: "Logbook baru menunggu validasi" |
| 5. Cek audit log | - | `action: logbook_tambah` |

---

## TC-LB-006: Tambah Logbook - Validasi Tanggal
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Tanggal masa depan | `tanggal`: "2026-12-31" (besok) | Error: "Tanggal tidak boleh di masa depan" (before_or_equal:today) |
| 2. Tanggal kosong | `tanggal`: "" | Error: "Tanggal wajib diisi" |
| 3. Kegiatan kosong | `kegiatan`: "" | Error: "Kegiatan wajib diisi" |
| 4. Kegiatan > 2000 char | `kegiatan`: "a..." (2001 char) | Error: max 2000 karakter |

---

## TC-LB-007: Tambah Logbook - Validasi File
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. File ekstensi tidak diizinkan | `file`: `test.exe` | Error: "Dokumentasi harus JPG, PNG, atau PDF (maks. 5 MB)" |
| 2. File > 5 MB | `file`: PDF 6MB | Error: max 5 MB |
| 3. File valid JPG/PNG/PDF ≤ 5MB | `file`: `foto.jpg` 2MB | Success, file tersimpan di storage `logbook/{user_id}/` |

---

## TC-LB-008: Tambah Logbook - Tanpa File
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Submit tanpa file | `file`: (kosong) | Success, file_path = null, file_name = null |

---

## TC-LB-009: Update Logbook - Pemilik, Status Menunggu
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (punya logbook status `menunggu`) | - | - |
| 2. PUT `/logbook/{id}` | `tanggal`: "2026-01-21", `kegiatan`: "Update kegiatan" | Success: "Logbook diperbarui dan menunggu validasi ulang.", status reset ke `menunggu` |
| 3. Cek notifikasi lapangan | - | Notif: "Logbook diperbarui" |

---

## TC-LB-010: Update Logbook - Pemilik, Status Revisi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook status `revisi`) | - | - |
| 2. PUT `/logbook/{id}` | Data valid | Success, status reset ke `menunggu` |

---

## TC-LB-011: Update Logbook - Status Sudah Disetujui
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook status `disetujui`) | - | - |
| 2. PUT `/logbook/{id}` | Data valid | Error 422: "Logbook yang sudah disetujui tidak dapat diubah." |

---

## TC-LB-012: Update Logbook - Bukan Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A | - | - |
| 2. PUT logbook milik mahasiswa B | `id`: logbook_B | 404 Not Found (query WHERE user_id = auth) |

---

## TC-LB_013: Update Logbook - Validasi Tanggal Masa Depan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook status `menunggu`) | - | - |
| 2. PUT dengan tanggal besok | `tanggal`: "2026-12-31" | Error validasi |

---

## TC-LB-014: Hapus Logbook - Pemilik, Status Menunggu
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook status `menunggu`, ada file) | - | - |
| 2. DELETE `/logbook/{id}` | - | Success: "Catatan dihapus.", file dihapus dari storage, record deleted |

---

## TC-LB-015: Hapus Logbook - Status Bukan Menunggu
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook status `disetujui`) | - | - |
| 2. DELETE `/logbook/{id}` | - | 404 Not Found (query WHERE status = 'menunggu') |

---

## TC-LB-016: Hapus Logbook - Bukan Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A | - | - |
| 2. DELETE logbook mahasiswa B | - | 404 Not Found |

---

## TC-LB-017: Validasi Logbook - Lapangan Setuju
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan (mahasiswa binaan punya logbook `menunggu`) | - | - |
| 2. POST `/logbook/{id}/validasi` | `aksi`: "setuju", `catatan`: "" (optional) | Success: "Logbook disetujui.", status = `disetujui`, validated_by = lapangan, validated_at = now() |
| 3. Cek notifikasi mahasiswa | - | Notif: "Logbook disetujui" |
| 4. Cek audit log | - | `action: logbook_validasi`, detail: "{id} setuju" |

---

## TC-LB-018: Validasi Logbook - Lapangan Revisi (Dengan Catatan)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. POST validasi revisi | `aksi`: "revisi", `catatan`: "Perlu tambah bukti foto" | Success: "Logbook dikembalikan untuk diperbaiki.", status = `revisi` |
| 3. Cek notifikasi mahasiswa | - | Notif: "Logbook perlu diperbaiki" + catatan |

---

## TC-LB-019: Validasi Logbook - Lapangan Revisi Tanpa Catatan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. POST validasi revisi | `aksi`: "revisi", `catatan`: "" | Error 422: "Catatan wajib diisi untuk permintaan perbaikan." |

---

## TC-LB-020: Validasi Logbook - Bukan Lapangan Binaan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan A | - | - |
| 2. POST validasi logbook mahasiswa binaan lapangan B | - | 404 Not Found (scope binaan) |

---

## TC-LB-021: Validasi Logbook - Non-Lapangan Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/dosen/admin | - | - |
| 2. POST `/logbook/{id}/validasi` | - | 403 Forbidden (middleware admin:lapangan) |

---

## TC-LB-022: Download File Logbook - Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (logbook punya file) | - | - |
| 2. GET `/logbook/{id}/file` | - | File download, nama file original |

---

## TC-LB-023: Download File Logbook - Lapangan Binaan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan (logbook mahasiswa binaan) | - | - |
| 2. GET `/logbook/{id}/file` | - | File download |

---

## TC-LB-024: Download File Logbook - Admin
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/logbook/{id}/file` | - | File download |

---

## TC-LB-025: Download File Logbook - Unauthorized
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A (logbook mahasiswa B) | - | - |
| 2. GET `/logbook/{id}/file` | - | 404 Not Found (abort_unless $ok) |

---

## TC-LB-026: Download File Logbook - File Tidak Ada
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login pemilik (logbook file_path null atau file dihapus manual) | - | - |
| 2. GET `/logbook/{id}/file` | - | 404: "Berkas tidak ditemukan" |

---

## TC-LB-027: Logbook - Paginasi & Query String
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin (50 logbook) | - | - |
| 2. Buka `/logbook?page=2&status=disetujui` | - | Halaman 2, filter status terjaga di pagination links |

---

## TC-LB-028: Logbook - Urutan Terbaru
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (3 logbook tanggal berbeda) | - | - |
| 2. Buka `/logbook` | - | Urutan: tanggal desc, id desc (terbaru di atas) |

---

## Traceability Matrix - Logbook
| Requirement | Test Case |
|-------------|-----------|
| REQ-LB-01: CRUD logbook mahasiswa | TC-LB-005 s.d 015 |
| REQ-LB-02: Validasi file upload | TC-LB-007 |
| REQ-LB-03: Status flow (menunggu → disetujui/revisi) | TC-LB-017 s.d 019 |
| REQ-LB-04: RBAC validasi (hanya lapangan) | TC-LB-020, 021 |
| REQ-LB-05: Notifikasi validasi | TC-LB-017, 018 |
| REQ-LB-06: Download file dengan akses control | TC-LB-022 s.d 026 |
| REQ-LB-07: Audit log | TC-LB-005, 017, 018 |