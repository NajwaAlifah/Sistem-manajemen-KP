# Log Eksekusi Pengujian QA
**Project:** Manajemen KP SI  
**Periode:** 2026-11-18  
**Tester:** QA Team  
**Versi Build:** v1.0 (commit: latest)

---

## Ringkasan Eksekusi

| Modul | Total TC | Passed | Failed | Blocked | Not Run | Pass Rate |
|-------|----------|--------|--------|---------|---------|-----------|
| 01 Auth | 20 | - | - | - | 20 | - |
| 02 KP Flow | 25 | - | - | - | 25 | - |
| 03 Logbook | 28 | - | - | - | 28 | - |
| 04 Bimbingan | 25 | - | - | - | 25 | - |
| 05 Nilai | 25 | - | - | - | 25 | - |
| 06 Dokumen | 31 | - | - | - | 31 | - |
| 07 Admin | 26 | - | - | - | 26 | - |
| 08 Export | 8 | - | - | - | 8 | - |
| 09 Notifikasi | 10 | - | - | - | 10 | - |
| **TOTAL** | **198** | **0** | **0** | **0** | **198** | **0%** |

---

## Detail Eksekusi Per Test Case

### 01 Auth
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-AUTH-001 | Login Berhasil - Mahasiswa | Not Run | | | | |
| TC-AUTH-002 | Login Berhasil - Dosen | Not Run | | | | |
| TC-AUTH-003 | Login Berhasil - Lapangan | Not Run | | | | |
| TC-AUTH-004 | Login Berhasil - Admin | Not Run | | | | |
| TC-AUTH-005 | Login Gagal - NPM Tidak Terdaftar | Not Run | | | | |
| TC-AUTH-006 | Login Gagal - Password Salah | Not Run | | | | |
| TC-AUTH-007 | Login Gagal - Akun Tidak Aktif | Not Run | | | | |
| TC-AUTH-008 | Rate Limiting 5x | Not Run | | | | |
| TC-AUTH-009 | Field Kosong | Not Run | | | | |
| TC-AUTH-010 | Logout Berhasil | Not Run | | | | |
| TC-AUTH-011 | Update Profil Berhasil | Not Run | | | | |
| TC-AUTH-012 | Update Profil Validasi | Not Run | | | | |
| TC-AUTH-013 | Update Password Berhasil | Not Run | | | | |
| TC-AUTH-014 | Update Password Validasi | Not Run | | | | |
| TC-AUTH-015 | Redirect Intended URL | Not Run | | | | |
| TC-AUTH-016 | Remember Me | Not Run | | | | |
| TC-AUTH-017 | Guest Middleware | Not Run | | | | |
| TC-AUTH-018 | Auth Middleware | Not Run | | | | |
| TC-AUTH-019 | Role Middleware | Not Run | | | | |
| TC-AUTH-020 | Session Regeneration | Not Run | | | | |

### 02 KP Flow
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-KP-001 | Pengajuan KP Baru | Not Run | | | | |
| TC-KP-002 | Pengajuan Ulang Setelah Ditolak | Not Run | | | | |
| TC-KP-003 | Pengajuan Saat Sudah Berlangsung | Not Run | | | | |
| TC-KP-004 | Validasi Field Pengajuan | Not Run | | | | |
| TC-KP-005 | Auto Isi Perusahaan dari Company | Not Run | | | | |
| TC-KP-006 | Progress Belum Mengajukan | Not Run | | | | |
| TC-KP-007 | Progress Menunggu Persetujuan | Not Run | | | | |
| TC-KP-008 | Progress Berlangsung Belum Konfirmasi | Not Run | | | | |
| TC-KP-009 | Progress Logbook | Not Run | | | | |
| TC-KP-010 | Progress Kelengkapan Dokumen | Not Run | | | | |
| TC-KP-011 | Progress Semua Complete | Not Run | | | | |
| TC-KP-012 | Monitoring Dosen | Not Run | | | | |
| TC-KP-013 | Monitoring Lapangan | Not Run | | | | |
| TC-KP-014 | Monitoring Admin | Not Run | | | | |
| TC-KP-015 | Monitoring Search & Filter | Not Run | | | | |
| TC-KP-016 | Konfirmasi Lapangan Berhasil | Not Run | | | | |
| TC-KP-017 | Konfirmasi Bukan Binaan | Not Run | | | | |
| TC-KP-018 | Konfirmasi Belum Disetujui Prodi | Not Run | | | | |
| TC-KP-019 | Konfirmasi Sudah Dikonfirmasi | Not Run | | | | |
| TC-KP-020 | Export KP CSV | Not Run | | | | |
| TC-KP-021 | Export Dokumen CSV | Not Run | | | | |
| TC-KP-022 | Export Nilai CSV | Not Run | | | | |
| TC-KP-023 | Export Type Invalid | Not Run | | | | |
| TC-KP-024 | Export Non-Admin | Not Run | | | | |
| TC-KP-025 | Progress Tanpa Dosen | Not Run | | | | |

### 03 Logbook
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-LB-001 | Index Mahasiswa | Not Run | | | | |
| TC-LB-002 | Index Dosen | Not Run | | | | |
| TC-LB-003 | Index Lapangan (Filter Menunggu) | Not Run | | | | |
| TC-LB-004 | Index Admin | Not Run | | | | |
| TC-LB-005 | Tambah Logbook Berhasil | Not Run | | | | |
| TC-LB-006 | Validasi Tanggal | Not Run | | | | |
| TC-LB-007 | Validasi File | Not Run | | | | |
| TC-LB-008 | Tambah Tanpa File | Not Run | | | | |
| TC-LB-009 | Update Status Menunggu | Not Run | | | | |
| TC-LB-010 | Update Status Revisi | Not Run | | | | |
| TC-LB-011 | Update Status Disetujui (Forbidden) | Not Run | | | | |
| TC-LB-012 | Update Bukan Pemilik | Not Run | | | | |
| TC-LB-013 | Update Tanggal Masa Depan | Not Run | | | | |
| TC-LB-014 | Hapus Status Menunggu | Not Run | | | | |
| TC-LB-015 | Hapus Status Bukan Menunggu | Not Run | | | | |
| TC-LB-016 | Hapus Bukan Pemilik | Not Run | | | | |
| TC-LB-017 | Validasi Setuju | Not Run | | | | |
| TC-LB-018 | Validasi Revisi Dengan Catatan | Not Run | | | | |
| TC-LB-019 | Validasi Revisi Tanpa Catatan | Not Run | | | | |
| TC-LB-020 | Validasi Bukan Binaan | Not Run | | | | |
| TC-LB-021 | Validasi Non-Lapangan | Not Run | | | | |
| TC-LB-022 | Download File Pemilik | Not Run | | | | |
| TC-LB-023 | Download File Lapangan | Not Run | | | | |
| TC-LB-024 | Download File Admin | Not Run | | | | |
| TC-LB-025 | Download Unauthorized | Not Run | | | | |
| TC-LB-026 | Download File Tidak Ada | Not Run | | | | |
| TC-LB-027 | Paginasi & Query String | Not Run | | | | |
| TC-LB-028 | Urutan Terbaru | Not Run | | | | |

### 04 Bimbingan
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-BIM-001 | Index Mahasiswa | Not Run | | | | |
| TC-BIM-002 | Index Dosen | Not Run | | | | |
| TC-BIM-003 | Index Lapangan/Admin | Not Run | | | | |
| TC-BIM-004 | Ajukan Bimbingan Berhasil | Not Run | | | | |
| TC-BIM-005 | Ajukan Tanpa Dosen | Not Run | | | | |
| TC-BIM-006 | Validasi Tanggal Masa Depan | Not Run | | | | |
| TC-BIM-007 | Validasi Metode | Not Run | | | | |
| TC-BIM-008 | Validasi Topik | Not Run | | | | |
| TC-BIM-009 | Respon Dosen Setuju | Not Run | | | | |
| TC-BIM-010 | Respon Dosen Ubah Jadwal | Not Run | | | | |
| TC-BIM-011 | Respon Ubah Tanpa Tanggal | Not Run | | | | |
| TC-BIM-012 | Respon Selesai Dengan Catatan | Not Run | | | | |
| TC-BIM-013 | Respon Selesai Tanpa Catatan | Not Run | | | | |
| TC-BIM-014 | Respon Bukan Pemilik | Not Run | | | | |
| TC-BIM-015 | Respon Non-Dosen | Not Run | | | | |
| TC-BIM-016 | Upload Lampiran Berhasil | Not Run | | | | |
| TC-BIM-017 | Validasi File Lampiran | Not Run | | | | |
| TC-BIM-018 | Replace Lampiran Lama | Not Run | | | | |
| TC-BIM-019 | Upload Bukan Pemilik | Not Run | | | | |
| TC-BIM-020 | Download Lampiran Mahasiswa | Not Run | | | | |
| TC-BIM-021 | Download Lampiran Dosen | Not Run | | | | |
| TC-BIM-022 | Download Lampiran Admin | Not Run | | | | |
| TC-BIM-023 | Download Unauthorized | Not Run | | | | |
| TC-BIM-024 | Download File Tidak Ada | Not Run | | | | |
| TC-BIM-025 | Flow Lengkap Bimbingan | Not Run | | | | |

### 05 Nilai
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-NIL-001 | Index Mahasiswa Dengan Nilai | Not Run | | | | |
| TC-NIL-002 | Index Mahasiswa Tanpa Nilai | Not Run | | | | |
| TC-NIL-003 | Index Dosen | Not Run | | | | |
| TC-NIL-004 | Index Lapangan | Not Run | | | | |
| TC-NIL-005 | Index Admin | Not Run | | | | |
| TC-NIL-006 | Simpan Nilai Dosen | Not Run | | | | |
| TC-NIL-007 | Simpan Nilai Seminar | Not Run | | | | |
| TC-NIL-008 | Simpan Nilai Lapangan | Not Run | | | | |
| TC-NIL-009 | Lapangan Isi Dosen (Forbidden) | Not Run | | | | |
| TC-NIL-010 | Dosen Isi Lapangan (Forbidden) | Not Run | | | | |
| TC-NIL-011 | Validasi Nilai 0-100 | Not Run | | | | |
| TC-NIL-012 | Simpan Bukan Binaan | Not Run | | | | |
| TC-NIL-013 | Update Nilai Existing | Not Run | | | | |
| TC-NIL-014 | Simpan Saat Published | Not Run | | | | |
| TC-NIL-015 | Publikasi Nilai Admin | Not Run | | | | |
| TC-NIL-016 | Tarik Publikasi Admin | Not Run | | | | |
| TC-NIL-017 | Publikasi Tanpa KP Data | Not Run | | | | |
| TC-NIL-018 | Publikasi Non-Admin | Not Run | | | | |
| TC-NIL-019 | Atur Bobot Valid | Not Run | | | | |
| TC-NIL-020 | Atur Bobot Total != 100 | Not Run | | | | |
| TC-NIL-021 | Atur Bobot Negatif/>100 | Not Run | | | | |
| TC-NIL-022 | Atur Bobot Non-Admin | Not Run | | | | |
| TC-NIL-023 | Perhitungan Sebagian Komponen | Not Run | | | | |
| TC-NIL-024 | Perhitungan Bobot Custom | Not Run | | | | |
| TC-NIL-025 | Konversi Huruf | Not Run | | | | |

### 06 Dokumen
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-DOC-001 | Upload Draft Berhasil | Not Run | | | | |
| TC-DOC-002 | Validasi Kategori | Not Run | | | | |
| TC-DOC-003 | Validasi File | Not Run | | | | |
| TC-DOC-004 | Upload Non-Mahasiswa | Not Run | | | | |
| TC-DOC-005 | List Mahasiswa | Not Run | | | | |
| TC-DOC-006 | List Dosen | Not Run | | | | |
| TC-DOC-007 | List Admin | Not Run | | | | |
| TC-DOC-008 | Filter & Search | Not Run | | | | |
| TC-DOC-009 | Detail Pemilik | Not Run | | | | |
| TC-DOC-010 | Detail Dosen Auto Reviewed | Not Run | | | | |
| TC-DOC-011 | Detail Unauthorized | Not Run | | | | |
| TC-DOC-012 | Update Draft/Revisi | Not Run | | | | |
| TC-DOC-013 | Update Status Submitted (Forbidden) | Not Run | | | | |
| TC-DOC-014 | Update Bukan Pemilik | Not Run | | | | |
| TC-DOC-015 | Hapus Draft Pemilik | Not Run | | | | |
| TC-DOC-016 | Hapus Status Bukan Draft | Not Run | | | | |
| TC-DOC-017 | Hapus Admin Bypass | Not Run | | | | |
| TC-DOC-018 | Submit Draft/Revisi | Not Run | | | | |
| TC-DOC-019 | Submit Status Bukan Draft/Revisi | Not Run | | | | |
| TC-DOC-020 | Feedback Revisi Dengan Catatan | Not Run | | | | |
| TC-DOC-021 | Feedback Revisi Tanpa Catatan | Not Run | | | | |
| TC-DOC-022 | Feedback Approve | Not Run | | | | |
| TC-DOC-023 | Feedback Reject | Not Run | | | | |
| TC-DOC-024 | Shortcut Approve Endpoint | Not Run | | | | |
| TC-DOC-025 | Feedback Non-Dosen | Not Run | | | | |
| TC-DOC-026 | Feedback Status Diputuskan | Not Run | | | | |
| TC-DOC-027 | Download Akses Control | Not Run | | | | |
| TC-DOC-028 | Download File Tidak Ada | Not Run | | | | |
| TC-DOC-029 | List Versi | Not Run | | | | |
| TC-DOC-030 | Arsip View | Not Run | | | | |
| TC-DOC-031 | Flow Lengkap Dokumen | Not Run | | | | |

### 07 Admin
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-ADM-001 | Dashboard Stats | Not Run | | | | |
| TC-ADM-002 | List User | Not Run | | | | |
| TC-ADM-003 | Tambah User | Not Run | | | | |
| TC-ADM-004 | Validasi Tambah User | Not Run | | | | |
| TC-ADM-005 | Edit User | Not Run | | | | |
| TC-ADM-006 | Hapus User | Not Run | | | | |
| TC-ADM-007 | Hapus Diri Sendiri | Not Run | | | | |
| TC-ADM-008 | List Kategori | Not Run | | | | |
| TC-ADM-009 | Tambah Kategori | Not Run | | | | |
| TC-ADM-010 | Validasi Kategori | Not Run | | | | |
| TC-ADM-011 | Edit Kategori | Not Run | | | | |
| TC-ADM-012 | Hapus Kategori | Not Run | | | | |
| TC-ADM-013 | List KP Data | Not Run | | | | |
| TC-ADM-014 | Tambah KP Admin | Not Run | | | | |
| TC-ADM-015 | Update Status KP | Not Run | | | | |
| TC-ADM-016 | Hapus KP | Not Run | | | | |
| TC-ADM-017 | List Perusahaan | Not Run | | | | |
| TC-ADM-018 | CRUD Perusahaan | Not Run | | | | |
| TC-ADM-019 | Buka Settings | Not Run | | | | |
| TC-ADM-020 | Simpan Settings | Not Run | | | | |
| TC-ADM-021 | Validasi Bobot Settings | Not Run | | | | |
| TC-ADM-022 | Audit Log | Not Run | | | | |
| TC-ADM-023 | Buat Pengumuman | Not Run | | | | |
| TC-ADM-024 | Edit Hapus Pengumuman | Not Run | | | | |
| TC-ADM-025 | Non-Admin Akses Admin | Not Run | | | | |
| TC-ADM-026 | Categories API Public | Not Run | | | | |

### 08 Export
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-EXP-001 | Export KP CSV | Not Run | | | | |
| TC-EXP-002 | Export Dokumen CSV | Not Run | | | | |
| TC-EXP-003 | Export Nilai CSV | Not Run | | | | |
| TC-EXP-004 | Export Type Invalid | Not Run | | | | |
| TC-EXP-005 | Export Non-Admin | Not Run | | | | |
| TC-EXP-006 | Export Data Kosong | Not Run | | | | |
| TC-EXP-007 | Export Karakter Khusus | Not Run | | | | |
| TC-EXP-008 | Export Filename Timestamp | Not Run | | | | |

### 09 Notifikasi
| TC ID | Nama Test Case | Status | Actual Result | Bug Ref | Tester | Tanggal |
|-------|----------------|--------|---------------|---------|--------|---------|
| TC-NOTIF-001 | List Notifikasi User | Not Run | | | | |
| TC-NOTIF-002 | Urutan Terbaru | Not Run | | | | |
| TC-NOTIF-003 | Mark All Read | Not Run | | | | |
| TC-NOTIF-004 | Go To Notifikasi | Not Run | | | | |
| TC-NOTIF-005 | Go To Bukan Pemilik | Not Run | | | | |
| TC-NOTIF-006 | List Pengumuman | Not Run | | | | |
| TC-NOTIF-007 | Pengumuman Target Role | Not Run | | | | |
| TC-NOTIF-008 | Notifikasi Otomatis Trigger | Not Run | | | | |
| TC-NOTIF-009 | Format Data Notifikasi | Not Run | | | | |
| TC-NOTIF-010 | Real-time Polling | Not Run | | | | |

---

## Catatan Eksekusi
- **Setup:** Jalankan `php artisan migrate:fresh --seed` untuk data bersih
- **Account Test:** Buat user per role (mahasiswa, dosen, lapangan, admin) via seeder atau manual
- **Data Prasyarat:** Setiap TC butuh data setup khusus (lihat kolom "Data Prasyarat" di file TC)
- **Browser:** Test di Chrome/Edge latest, responsive check mobile
- **API Testing:** Gunakan Postman/Thunder Client untuk endpoint API

---

## Re-test Log
| Bug ID | TC ID | Tanggal Re-test | Status | Catatan |
|--------|-------|-----------------|--------|---------|
| | | | | |