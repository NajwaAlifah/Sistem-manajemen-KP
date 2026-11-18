# Test Case - Bimbingan (Guidance)
**Modul:** GuidanceController  
**Routes:** GET /bimbingan, POST /bimbingan, POST /bimbingan/{id}/respon, POST /bimbingan/{id}/lampiran, GET /bimbingan/{id}/lampiran

---

## TC-BIM-001: Index Bimbingan - Mahasiswa Melihat Pengajuannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (punya 3 bimbingan) | - | - |
| 2. Buka `/bimbingan` | - | List 3 bimbingan sendiri, urut tanggal_usul desc |
| 3. Cek status badge | - | Tampil status: diajukan/disetujui/jadwal_diubah/selesai |

---

## TC-BIM-002: Index Bimbingan - Dosen Melihat Bimbingan Untuk Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (3 mahasiswa binaan, total 5 bimbingan) | - | - |
| 2. Buka `/bimbingan` | - | List 5 bimbingan mahasiswa binaan |
| 3. Cek aksi tersedia | - | Tombol: Setuju, Ubah, Selesai per baris |

---

## TC-BIM-003: Index Bimbingan - Lapangan/Admin
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. Buka `/bimbingan` | - | 403 Forbidden (middleware: mahasiswa,dosen,admin) |
| 3. Login admin | - | List semua bimbingan |

---

## TC-BIM-004: Ajukan Bimbingan - Berhasil
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (sudah punya dosen_id) | - | - |
| 2. POST `/bimbingan` | `tanggal_usul`: "2026-01-25 10:00" (masa depan), `metode`: "daring", `topik`: "Review Bab 1" | Success: "Jadwal bimbingan diajukan ke dosen pembimbing.", status = `diajukan` |
| 3. Cek DB | - | Record guidance created, dosen_id = mahasiswa.dosen_id |
| 4. Cek notifikasi dosen | - | Notif: "Pengajuan jadwal bimbingan" + topik |
| 5. Cek audit log | - | `action: bimbingan_ajukan` |

---

## TC-BIM-005: Ajukan Bimbingan - Mahasiswa Belum Punya Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (dosen_id = null) | - | - |
| 2. POST `/bimbingan` | Data valid | Error 422: "Dosen pembimbing Anda belum ditetapkan oleh prodi." |

---

## TC-BIM-006: Ajukan Bimbingan - Validasi Tanggal
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Tanggal masa lalu | `tanggal_usul`: "2026-01-01" (kemarin) | Error: "Jadwal harus di masa depan." (after:now) |
| 2. Tanggal sekarang | `tanggal_usul`: now() | Error: after:now strictly > now |
| 3. Tanggal valid besok | `tanggal_usul`: tomorrow 10:00 | Success |

---

## TC-BIM-007: Ajukan Bimbingan - Validasi Metode
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Metode invalid | `metode`: "online" | Error: metode harus "daring" atau "luring" |
| 2. Metode valid | `metode`: "daring" / "luring" | Success |

---

## TC-BIM-008: Ajukan Bimbingan - Validasi Topik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Topik kosong | `topik`: "" | Error: topik wajib diisi |
| 2. Topik > 200 char | `topik`: "a..." (201 char) | Error: max 200 karakter |

---

## TC-BIM-009: Respon Bimbingan - Dosen Setuju
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (ada bimbingan status `diajukan` dari mahasiswa binaan) | - | - |
| 2. POST `/bimbingan/{id}/respon` | `aksi`: "setuju", `catatan_dosen`: "Baik, lanjutkan" | Success, status = `disetujui`, tanggal_final = tanggal_usul |
| 3. Cek notifikasi mahasiswa | - | Notif: "Bimbingan: Disetujui" |
| 4. Cek audit log | - | `action: bimbingan_respon`, detail: "{id} setuju" |

---

## TC-BIM-010: Respon Bimbingan - Dosen Ubah Jadwal
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (bimbingan status `diajukan`) | - | - |
| 2. POST respon ubah | `aksi`: "ubah", `tanggal_final`: "2026-01-26 14:00", `catatan_dosen`: "Bentrok jadwal" | Success, status = `jadwal_diubah`, tanggal_final updated |
| 3. Cek notifikasi mahasiswa | - | Notif: "Bimbingan: Jadwal Diubah" |

---

## TC-BIM-011: Respon Bimbingan - Dosen Ubah Jadwal Tanpa Tanggal
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST respon ubah | `aksi`: "ubah", `tanggal_final`: "" | Error 422: "Isi jadwal usulan baru." |

---

## TC-BIM-012: Respon Bimbingan - Dosen Selesai (Dengan Catatan)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (bimbingan status `disetujui` atau `jadwal_diubah`) | - | - |
| 2. POST respon selesai | `aksi`: "selesai", `catatan_dosen`: "Bab 1 sudah direview, lanjut Bab 2" | Success, status = `selesai`, catatan_dosen tersimpan |
| 3. Cek notifikasi mahasiswa | - | Notif: "Bimbingan: Selesai" |

---

## TC-BIM-013: Respon Bimbingan - Dosen Selesai Tanpa Catatan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST respon selesai | `aksi`: "selesai", `catatan_dosen`: "" | Error 422: "Catatan hasil bimbingan wajib diisi." |

---

## TC-BIM-014: Respon Bimbingan - Dosen Bukan Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen A | - | - |
| 2. POST respon bimbingan mahasiswa binaan dosen B | - | 404 Not Found (WHERE dosen_id = auth) |

---

## TC-BIM-015: Respon Bimbingan - Non-Dosen Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/lapangan | - | - |
| 2. POST `/bimbingan/{id}/respon` | - | 403 Forbidden (middleware admin:dosen) |

---

## TC-BIM-016: Upload Lampiran - Mahasiswa Upload (Bimbingan Selesai)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (bimbingan status `selesai`) | - | - |
| 2. POST `/bimbingan/{id}/lampiran` | `file`: PDF 3MB | Success: "Lampiran diunggah.", lampiran_path & lampiran_name tersimpan |
| 3. Cek notifikasi dosen | - | Notif: "Lampiran tindak lanjut bimbingan" |
| 4. Cek file storage | - | Tersimpan di `bimbingan/{user_id}/` |

---

## TC-BIM-017: Upload Lampiran - Validasi File
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Ekstensi tidak diizinkan | `file`: `test.exe` | Error: "Lampiran harus PDF, DOC, DOCX, JPG, atau PNG (maks. 10 MB)" |
| 2. File > 10 MB | `file`: PDF 15MB | Error: max 10 MB |
| 3. File valid PDF/DOC/DOCX/JPG/PNG ≤ 10MB | - | Success |

---

## TC-BIM-018: Upload Lampiran - Replace Lampiran Lama
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa sudah punya lampiran lama | - | - |
| 2. Upload lampiran baru | `file`: baru.pdf | Lampiran lama dihapus dari storage, yang baru tersimpan |

---

## TC-BIM_019: Upload Lampiran - Bukan Pemilik Bimbingan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A | - | - |
| 2. POST lampiran bimbingan mahasiswa B | - | 404 Not Found (WHERE user_id = auth) |

---

## TC-BIM-020: Download Lampiran - Mahasiswa Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (bimbingan punya lampiran) | - | - |
| 2. GET `/bimbingan/{id}/lampiran` | - | File download |

---

## TC-BIM-021: Download Lampiran - Dosen Pembimbing
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (bimbingan milik dosen ini) | - | - |
| 2. GET `/bimbingan/{id}/lampiran` | - | File download |

---

## TC-BIM-022: Download Lampiran - Admin
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/bimbingan/{id}/lampiran` | - | File download |

---

## TC-BIM-023: Download Lampiran - Unauthorized
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa A (bimbingan mahasiswa B) | - | - |
| 2. GET `/bimbingan/{id}/lampiran` | - | 403 Forbidden |

---

## TC-BIM-024: Download Lampiran - File Tidak Ada
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login pemilik (lampiran_path null atau file dihapus manual) | - | - |
| 2. GET `/bimbingan/{id}/lampiran` | - | 404 Not Found |

---

## TC-BIM-025: Flow Lengkap Bimbingan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa ajukan bimbingan | - | Status `diajukan` |
| 2. Dosen setuju | - | Status `disetujui`, tanggal_final = tanggal_usul |
| 3. Dosen ubah jadwal | - | Status `jadwal_diubah`, tanggal_final baru |
| 4. Dosen selesai + catatan | - | Status `selesai` |
| 5. Mahasiswa upload lampiran | - | Lampiran tersimpan, notif ke dosen |
| 6. Dosen download lampiran | - | File download |

---

## Traceability Matrix - Bimbingan
| Requirement | Test Case |
|-------------|-----------|
| REQ-BIM-01: Mahasiswa ajukan jadwal | TC-BIM-004 s.d 008 |
| REQ-BIM-02: Validasi dosen_id wajib | TC-BIM-005 |
| REQ-BIM-03: Dosen respon (setuju/ubah/selesai) | TC-BIM-009 s.d 013 |
| REQ-BIM-04: Validasi catatan wajib untuk selesai | TC-BIM-013 |
| REQ-BIM-05: RBAC respon hanya dosen | TC-BIM-014, 015 |
| REQ-BIM-06: Upload lampiran mahasiswa | TC-BIM-016 s.d 019 |
| REQ-BIM-07: Validasi file lampiran | TC-BIM-017 |
| REQ-BIM-08: Replace lampiran lama | TC-BIM-018 |
| REQ-BIM-09: Download lampiran RBAC | TC-BIM-020 s.d 023 |
| REQ-BIM-10: Notifikasi setiap aksi | TC-BIM-004, 009, 010, 012, 016 |
| REQ-BIM-11: Audit log | TC-BIM-004, 009, 010, 012, 016 |