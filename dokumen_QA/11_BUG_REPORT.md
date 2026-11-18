# Bug Report - Manajemen KP SI
**Project:** Manajemen KP SI  
**Versi:** 1.0  
**Tanggal:** 2026-11-18

---

## Klasifikasi Severity
| Level | Definisi | SLA Fix |
|-------|----------|---------|
| **Critical** | System crash, data loss, security breach, core flow broken | < 24 jam |
| **High** | Major feature tidak berfungsi, RBAC bypass, perhitungan salah | < 3 hari |
| **Medium** | Minor feature bug, UI issue, validasi kurang ketat | < 1 minggu |
| **Low** | Cosmetic, typo, enhancement, dokumentasi | Next sprint |

---

## Daftar Bug

### BUG-001
| Field | Value |
|-------|-------|
| **ID** | BUG-001 |
| **Judul** | [Template] Contoh format bug report |
| **Modul** | Auth / Login |
| **Severity** | Critical |
| **Status** | Open |
| **TC Terkait** | TC-AUTH-001 |
| **Steps to Reproduce** | 1. Buka `/login`<br>2. Isi NPM valid, password valid<br>3. Klik Login |
| **Expected** | Redirect ke dashboard sesuai role |
| **Actual** | [Deskripsi bug aktual] |
| **Evidence** | Screenshot / Video / Log error |
| **Environment** | Chrome 120, Windows 11, Laragon PHP 8.2 |
| **Ditemukan Oleh** | QA Tester |
| **Tanggal** | 2026-11-18 |
| **Root Cause** | [Analisis penyebab] |
| **Fix** | [Deskripsi perbaikan] |
| **Verified By** | - |
| **Tanggal Verified** | - |

---

### BUG-002
| Field | Value |
|-------|-------|
| **ID** | BUG-002 |
| **Judul** | [Template] RBAC bypass - mahasiswa akses admin |
| **Modul** | Admin Panel |
| **Severity** | Critical |
| **Status** | Open |
| **TC Terkait** | TC-ADM-025 |
| **Steps to Reproduce** | 1. Login mahasiswa<br>2. Akses `/admin/users` langsung via URL |
| **Expected** | 403 Forbidden / Redirect |
| **Actual** | [Bisa akses halaman admin] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-003
| Field | Value |
|-------|-------|
| **ID** | BUG-003 |
| **Judul** | [Template] Perhitungan nilai akhir salah saat bobot custom |
| **Modul** | Nilai |
| **Severity** | High |
| **Status** | Open |
| **TC Terkait** | TC-NIL-024 |
| **Steps to Reproduce** | 1. Admin set bobot 30/50/20<br>2. Input nilai dosen=80, lapangan=90, seminar=70<br>3. Cek nilai akhir |
| **Expected** | (80*30 + 90*50 + 70*20) / 100 = 83 |
| **Actual** | [Hasil perhitungan tidak sesuai] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-004
| Field | Value |
|-------|-------|
| **ID** | BUG-004 |
| **Judul** | [Template] File upload tidak tervalidasi MIME type server-side |
| **Modul** | Dokumen / Logbook / Bimbingan |
| **Severity** | High |
| **Status** | Open |
| **TC Terkait** | TC-DOC-003, TC-LB-007, TC-BIM-017 |
| **Steps to Reproduce** | 1. Siapkan file .exe rename jadi .pdf<br>2. Upload via form dokumen<br>3. Cek apakah diterima |
| **Expected** | Ditolak (validasi MIME type server-side) |
| **Actual** | [Diterima / error lain] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-005
| Field | Value |
|-------|-------|
| **ID** | BUG-005 |
| **Judul** | [Template] Notifikasi tidak terkirim saat [aksi] |
| **Modul** | Notifikasi |
| **Severity** | Medium |
| **Status** | Open |
| **TC Terkait** | TC-NOTIF-008 |
| **Steps to Reproduce** | 1. Lakukan [aksi trigger]<br>2. Cek tabel notifs / UI notifikasi penerima |
| **Expected** | Notifikasi created untuk penerima yang benar |
| **Actual** | [Notifikasi tidak ada / salah penerima] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-006
| Field | Value |
|-------|-------|
| **ID** | BUG-006 |
| **Judul** | [Template] Export CSV encoding masalah di Excel |
| **Modul** | Export |
| **Severity** | Medium |
| **Status** | Open |
| **TC Terkait** | TC-EXP-001 |
| **Steps to Reproduce** | 1. Export KP CSV<br>2. Buka di Microsoft Excel |
| **Expected** | Karakter UTF-8 tampil benar (BOM terbaca) |
| **Actual** | [Karakter garbled / tidak ada BOM] |
| **Evidence** | |
| **Environment** | Excel 365 / 2019 |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-007
| Field | Value |
|-------|-------|
| **ID** | BUG-007 |
| **Judul** | [Template] Validasi tanggal logbook before_or_equal:today tidak jalan |
| **Modul** | Logbook |
| **Severity** | Medium |
| **Status** | Open |
| **TC Terkait** | TC-LB-006, TC-LB-013 |
| **Steps to Reproduce** | 1. Isi tanggal besok/kemarin<br>2. Submit logbook |
| **Expected** | Error validasi tanggal masa depan |
| **Actual** | [Terseved / error lain] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

### BUG-008
| Field | Value |
|-------|-------|
| **ID** | BUG-008 |
| **Judul** | [Template] Dokumen status reviewed tidak otomatis saat dosen buka |
| **Modul** | Dokumen |
| **Severity** | Medium |
| **Status** | Open |
| **TC Terkait** | TC-DOC-010 |
| **Steps to Reproduce** | 1. Mahasiswa submit dokumen (status submitted)<br>2. Dosen GET `/api/documents/{id}`<br>3. Cek status dokumen |
| **Expected** | Status berubah ke `reviewed` |
| **Actual** | [Tetap submitted] |
| **Evidence** | |
| **Environment** | |
| **Ditemukan Oleh** | |
| **Tanggal** | |
| **Root Cause** | |
| **Fix** | |
| **Verified By** | |
| **Tanggal Verified** | |

---

## Ringkasan Bug per Modul & Severity

| Modul | Critical | High | Medium | Low | Total |
|-------|----------|------|--------|-----|-------|
| Auth | 0 | 0 | 0 | 0 | 0 |
| KP Flow | 0 | 0 | 0 | 0 | 0 |
| Logbook | 0 | 0 | 0 | 0 | 0 |
| Bimbingan | 0 | 0 | 0 | 0 | 0 |
| Nilai | 0 | 0 | 0 | 0 | 0 |
| Dokumen | 0 | 0 | 0 | 0 | 0 |
| Admin | 0 | 0 | 0 | 0 | 0 |
| Export | 0 | 0 | 0 | 0 | 0 |
| Notifikasi | 0 | 0 | 0 | 0 | 0 |
| **TOTAL** | **0** | **0** | **0** | **0** | **0** |

---

## Bug Trend (Untuk Update Berkala)
| Minggu | Open | In Progress | Fixed | Verified | Closed |
|--------|------|-------------|-------|----------|--------|
| 1 (18-24 Nov) | 0 | 0 | 0 | 0 | 0 |
| 2 | | | | | |

---

## Catatan
- Gunakan template di atas untuk setiap bug baru (copy row BUG-XXX)
- Assign bug ke developer via GitHub Issues / Jira / Trello
- Retest wajib setelah fix (update kolom Verified By & Tanggal Verified)
- Critical/High bug: escalate ke PM/Tech Lead segera