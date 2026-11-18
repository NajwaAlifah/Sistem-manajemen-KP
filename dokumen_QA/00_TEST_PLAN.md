# Rencana Pengujian QA - Sistem Manajemen KP
**Project:** Manajemen KP SI  
**Versi:** 1.0  
**Tanggal:** 2026-11-18  
**Tipe Pengujian:** Black Box Testing (Functional Testing)

---

## 1. Ruang Lingkup Pengujian

### Modul yang Diuji
| No | Modul | Controller | Route Prefix | Peran Terkait |
|----|-------|------------|--------------|---------------|
| 1 | Autentikasi | AuthController | /login, /logout | Semua |
| 2 | Pengajuan KP | KpFlowController | /kp/pengajuan | Mahasiswa |
| 3 | Progress KP | KpFlowController | /kp/progress | Mahasiswa |
| 4 | Monitoring KP | KpFlowController | /monitoring | Dosen, Lapangan, Admin |
| 5 | Konfirmasi Pelaksanaan | KpFlowController | /monitoring/{id}/konfirmasi | Lapangan |
| 6 | Logbook | LogbookController | /logbook | Mahasiswa, Dosen, Lapangan |
| 7 | Validasi Logbook | LogbookController | /logbook/{id}/validasi | Lapangan |
| 8 | Bimbingan | GuidanceController | /bimbingan | Mahasiswa, Dosen |
| 9 | Nilai | GradeController | /nilai | Mahasiswa, Dosen, Lapangan, Admin |
| 10 | Dokumen | DocumentController | /documents, /api/documents | Mahasiswa, Dosen, Admin |
| 11 | Admin Panel | AdminController, CompanyController, InfoController | /admin/* | Admin |
| 12 | Ekspor Data | KpFlowController | /admin/export/{type} | Admin |
| 13 | Notifikasi & Pengumuman | InfoController | /notifications, /pengumuman | Semua |
| 14 | Pengaturan Profil | AuthController | /settings/profile, /settings/password | Semua |

---

## 2. Strategi Pengujian

### 2.1 Tipe Pengujian
- **Functional Testing**: Verifikasi setiap fitur sesuai requirement
- **Boundary Value Analysis**: Input batas (tanggal, ukuran file, panjang string)
- **Equivalence Partitioning**: Valid/Invalid input classes
- **Role-Based Access Control (RBAC)**: Verifikasi akses per peran
- **Negative Testing**: Error handling, validasi, unauthorized access
- **Integration Testing**: Alur end-to-end (pengajuan → konfirmasi → logbook → nilai → dokumen)

### 2.2 Data Uji
- **Valid Data**: Data yang memenuhi semua validasi
- **Invalid Data**: Data yang gagal validasi (format, ukuran, batas)
- **Edge Cases**: Nilai batas (0, 100, max file size, max length)
- **Empty/Null**: Field wajib dikosongkan

### 2.3 Lingkungan Pengujian
- **OS**: Windows 11 (Laragon)
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB
- **Web Server**: Nginx/Apache via Laragon
- **Browser**: Chrome/Edge (latest)

---

## 3. Kriteria Kelulusan (Exit Criteria)
- [ ] 100% test case critical path PASS
- [ ] 0 Critical/High severity bugs open
- [ ] Semua role-based access control terverifikasi
- [ ] Alur end-to-end mahasiswa → dosen → lapangan → admin berjalan
- [ ] Export CSV berfungsi untuk 3 tipe (KP, Dokumen, Nilai)
- [ ] Notifikasi terkirim ke peran yang tepat
- [ ] Audit log mencatat semua aksi penting

---

## 4. Matriks Risiko
| Risiko | Probabilitas | Dampak | Mitigasi |
|--------|--------------|--------|----------|
| Role bypass (mahasiswa akses admin) | Rendah | Tinggi | Test RBAC ketat per endpoint |
| File upload bypass (ekstensi/ukuran) | Sedang | Tinggi | Test validasi MIME & size |
| Race condition submit dokumen | Rendah | Sedang | Test concurrent submit |
| Nilai published tapi bisa diubah | Rendah | Tinggi | Test guard `nilai_published` |
| Logbook validasi double-click | Sedang | Sedang | Test idempotency |

---

## 5. Jadwal & Sumber Daya
| Fase | Aktivitas | Estimasi |
|------|-----------|----------|
| Persiapan | Setup env, seed data, review requirement | 1 jam |
| Eksekusi | Run test case per modul | 4-6 jam |
| Re-test | Verifikasi perbaikan bug | 1-2 jam |
| Laporan | Compile hasil, rekomendasi | 1 jam |

---

## 6. Deliverable
- `01_TEST_CASES_AUTH.md` - Test case Autentikasi
- `02_TEST_CASES_KP_FLOW.md` - Test case Pengajuan/Progress/Monitoring KP
- `03_TEST_CASES_LOGBOOK.md` - Test case Logbook & Validasi
- `04_TEST_CASES_BIMBINGAN.md` - Test case Bimbingan
- `05_TEST_CASES_NILAI.md` - Test case Nilai & Bobot
- `06_TEST_CASES_DOKUMEN.md` - Test case Dokumen & Review
- `07_TEST_CASES_ADMIN.md` - Test case Admin Panel
- `08_TEST_CASES_EXPORT.md` - Test case Export
- `09_TEST_CASES_NOTIF.md` - Test case Notifikasi & Pengumuman
- `10_TEST_EXECUTION_LOG.md` - Log eksekusi & hasil
- `11_BUG_REPORT.md` - Daftar bug ditemukan
- `12_TRACEABILITY_MATRIX.md` - Matriks traceability requirement-test