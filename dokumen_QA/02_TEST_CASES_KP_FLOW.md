# Test Case - Pengajuan KP, Progress, Monitoring, Konfirmasi, Export
**Modul:** KpFlowController  
**Routes:** 
- Mahasiswa: GET/POST /kp/pengajuan, GET /kp/progress
- Dosen/Lapangan/Admin: GET /monitoring, POST /monitoring/{id}/konfirmasi
- Admin: GET /admin/export/{type}

---

## TC-KP-001: Pengajuan KP - Mahasiswa Belum Pernah Mengajukan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (belum punya kp_data) | - | - |
| 2. Buka `/kp/pengajuan` | - | Form tampil, dropdown perusahaan terisi |
| 3. Isi form valid | `perusahaan`: "PT Teknologi Maju", `judul`: "Sistem Informasi Akademik", `tanggal_mulai`: "2026-01-15", `tanggal_selesai`: "2026-07-15", `company_id`: (pilih dari dropdown) | Success: "Pengajuan KP berhasil dikirim...", status KP = `menunggu_persetujuan` |
| 4. Cek DB kp_data | - | Record created dengan status `menunggu_persetujuan` |
| 5. Cek notifikasi admin | - | Notif ke semua admin: "Pengajuan KP baru" |
| 6. Cek audit log | - | `action: pengajuan_kp` |

---

## TC-KP-002: Pengajuan KP - Mahasiswa Sudah Memiliki KP Status Ditolak
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (kp_data.status = `ditolak`) | - | - |
| 2. Buka `/kp/pengajuan` | - | Form tampil, bisa mengajukan ulang |
| 3. Submit form valid | Data valid baru | Success, status = `menunggu_persetujuan`, catatan_admin = null |

---

## TC-KP-003: Pengajuan KP - Mahasiswa Sudah Status Berlangsung
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (kp_data.status = `berlangsung`) | - | - |
| 2. Buka `/kp/pengajuan` | - | Error 422: "Pengajuan sudah disetujui dan tidak dapat diubah." |

---

## TC-KP-004: Pengajuan KP - Validasi Field Wajib
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Submit kosong | Semua field kosong | Error validasi: perusahaan, judul, tanggal_mulai, tanggal_selesai wajib |
| 2. Tanggal mulai > tanggal selesai | `tanggal_mulai`: "2026-07-15", `tanggal_selesai`: "2026-01-15" | Error: "Tanggal selesai harus setelah atau sama dengan tanggal mulai" |
| 3. Perusahaan > 150 char | `perusahaan`: "a..." (151 char) | Error: max 150 karakter |
| 4. Judul > 200 char | `judul`: "a..." (201 char) | Error: max 200 karakter |
| 5. company_id tidak exist | `company_id`: 99999 | Error: company_id harus exist di tabel companies |

---

## TC-KP-005: Pengajuan KP - Otomatis Isi Perusahaan dari Company
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Pilih company_id dari dropdown | `company_id`: 1 (PT ABC) | Field `perusahaan` otomatis terisi "PT ABC" setelah submit |

---

## TC-KP-006: Progress KP - Mahasiswa Belum Mengajukan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (belum punya kp_data) | - | - |
| 2. Buka `/kp/progress` | - | Tampil 7 step: semua step 1 (Pengajuan KP) = false, status "Belum mengajukan" |

---

## TC-KP-007: Progress KP - Mahasiswa Status Menunggu Persetujuan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (status `menunggu_persetujuan`) | - | - |
| 2. Buka `/kp/progress` | - | Step 1: true, status "Menunggu Persetujuan", step 2-7: false |

---

## TC-KP-008: Progress KP - Mahasiswa Status Berlangsung (Belum Konfirmasi Lapangan)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (status `berlangsung`, dikonfirmasi_at = null) | - | - |
| 2. Buka `/kp/progress` | - | Step 1: true, Step 2: true tapi status "Menunggu persetujuan prodi" |

---

## TC-KP-009: Progress KP - Logbook Progress
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa punya 5 logbook, 3 divalidasi | - | - |
| 2. Buka `/kp/progress` | - | Step 3: true (validated > 0), detail "5 catatan, 3 tervalidasi" |

---

## TC-KP-010: Progress KP - Kelengkapan Dokumen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa punya 3 kategori wajib, 2 approved, 1 submitted | - | - |
| 2. Buka `/kp/progress` | - | Step 4: false, detail "2 dari 3 dokumen wajib disetujui" |

---

## TC-KP-011: Progress KP - Semua Step Complete
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa: KP approved, dikonfirmasi, logbook >0 validated, semua dokumen wajib approved, nilai published, status selesai | - | - |
| 2. Buka `/kp/progress` | - | Semua 7 step = true, status final "Selesai" |

---

## TC-KP-012: Monitoring - Dosen Melihat Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (punya 3 mahasiswa binaan) | - | - |
| 2. Buka `/monitoring` | - | List 3 mahasiswa dengan info: KP status, completeness %, logbook validated count |
| 3. Cek query | - | Hanya mahasiswa dengan dosen_id = user login |

---

## TC-KP-013: Monitoring - Lapangan Melihat Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan (punya 2 mahasiswa binaan) | - | - |
| 2. Buka `/monitoring` | - | List 2 mahasiswa, filter by lapangan_id |

---

## TC-KP-014: Monitoring - Admin Melihat Semua
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/monitoring` | - | List SEMUA mahasiswa (role=mahasiswa) |

---

## TC-KP-015: Monitoring - Search & Filter
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Search nama | `q`: "Budi" | Filter mahasiswa nama mengandung "Budi" |
| 3. Search NPM | `q`: "2021001" | Filter mahasiswa NPM mengandung "2021001" |
| 4. Filter status | `status`: "berlangsung" | Hanya mahasiswa dengan KP status berlangsung |

---

## TC-KP-016: Konfirmasi Pelaksanaan - Lapangan Konfirmasi Mahasiswa Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. Mahasiswa binaan status `berlangsung`, dikonfirmasi_at = null | - | - |
| 3. POST `/monitoring/{id}/konfirmasi` | `id`: mahasiswa_id | Success, `dikonfirmasi_at` = now() |
| 3. Cek notifikasi | - | Notif ke mahasiswa & dosen: "Pelaksanaan KP dikonfirmasi" |
| 4. Cek audit log | - | `action: konfirmasi_kp` |

---

## TC-KP_017: Konfirmasi Pelaksanaan - Lapangan Konfirmasi Bukan Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan A | - | - |
| 2. POST konfirmasi mahasiswa binaan lapangan B | `id`: mahasiswa_binaan_B | 403/422 Error (tidak ditemukan di scope binaan) |

---

## TC-KP-018: Konfirmasi Pelaksanaan - Mahasiswa Belum Disetujui Prodi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. Mahasiswa status `menunggu_persetujuan` | - | - |
| 3. POST konfirmasi | `id`: mahasiswa_id | Error 422: "KP mahasiswa belum disetujui prodi." |

---

## TC-KP-019: Konfirmasi Pelaksanaan - Sudah Dikonfirmasi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. Mahasiswa sudah punya `dikonfirmasi_at` | - | - |
| 3. POST konfirmasi lagi | `id`: mahasiswa_id | Update `dikonfirmasi_at` ke now() (idempotent) |

---

## TC-KP-020: Export KP - Admin Export CSV KP
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/kp` | - | Download file `rekap-kp-YYYYMMDD.csv` |
| 3. Buka CSV | - | Header: NPM,Nama,Perusahaan,Judul,Mulai,Selesai,Status KP,Dosen Pembimbing,Pembimbing Lapangan |
| 4. Cek data | - | Semua mahasiswa dengan kp_data tercantum |
| 5. Cek encoding | - | UTF-8 dengan BOM (bisa dibuka Excel) |
| 6. Cek delimiter | - | Semicolon (;) |
| 7. Cek audit log | - | `action: export`, detail: "kp" |

---

## TC-KP-021: Export Dokumen - Admin Export CSV Dokumen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/dokumen` | - | Download `rekap-dokumen-YYYYMMDD.csv` |
| 3. Cek header | - | Dokumen,Mahasiswa,NPM,Kategori,Status,Versi,Diunggah,Diperbarui |

---

## TC-KP-022: Export Nilai - Admin Export CSV Nilai
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/nilai` | - | Download `rekap-nilai-YYYYMMDD.csv` |
| 3. Cek header | - | NPM,Nama,Nilai Dosen,Nilai Lapangan,Nilai Seminar,Nilai Akhir,Huruf,Dipublikasikan |
| 4. Cek perhitungan nilai akhir | - | Sesuai bobot setting (default 40/40/20) |

---

## TC-KP-023: Export - Type Invalid
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/invalid` | - | 404 Not Found |

---

## TC-KP-024: Export - Non-Admin Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. GET `/admin/export/kp` | - | 403 Forbidden (middleware admin) |

---

## TC-KP-025: Progress - Mahasiswa Tidak Punya Dosen Pembimbing
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa dosen_id = null | - | - |
| 2. Buka `/kp/progress` | - | Step 2 (Pelaksanaan) info dosen = "-" |

---

## Traceability Matrix - KP Flow
| Requirement | Test Case |
|-------------|-----------|
| REQ-KP-01: Pengajuan KP mahasiswa | TC-KP-001 s.d 005 |
| REQ-KP-02: Validasi status KP | TC-KP-002, 003 |
| REQ-KP-03: Progress tracking 7 step | TC-KP-006 s.d 011 |
| REQ-KP-04: Monitoring per role | TC-KP-012 s.d 015 |
| REQ-KP-05: Konfirmasi lapangan | TC-KP-016 s.d 019 |
| REQ-KP-06: Export CSV 3 tipe | TC-KP-020 s.d 023 |
| REQ-KP-07: RBAC export | TC-KP-024 |