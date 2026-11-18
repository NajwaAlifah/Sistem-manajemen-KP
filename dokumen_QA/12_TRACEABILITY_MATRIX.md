# Matriks Traceability Requirement - Test Case
**Project:** Manajemen KP SI  
**Versi:** 1.0  
**Tanggal:** 2026-11-18

---

## Daftar Requirement (Dari Kode & Dokumentasi)

| Req ID | Deskripsi Requirement | Sumber | Modul |
|--------|----------------------|--------|-------|
| REQ-AUTH-01 | Login menggunakan NPM (mahasiswa) atau NIP (dosen/admin/lapangan) | AuthController | Auth |
| REQ-AUTH-02 | Rate limiting 5 percobaan per menit per IP+NPM | AuthController | Auth |
| REQ-AUTH-03 | Validasi field wajib login | AuthController | Auth |
| REQ-AUTH-04 | Logout dengan session destroy & audit log | AuthController | Auth |
| REQ-AUTH-05 | Update profil (nama, phone) | AuthController | Auth |
| REQ-AUTH-06 | Ganti password dengan validasi current password | AuthController | Auth |
| REQ-AUTH-07 | Remember me cookie | AuthController | Auth |
| REQ-AUTH-08 | Role-based redirect setelah login | Routes + AuthController | Auth |
| REQ-AUTH-09 | Redirect ke intended URL setelah login | AuthController | Auth |
| REQ-AUTH-10 | Session regeneration after login | AuthController | Auth |
| REQ-KP-01 | Mahasiswa pengajuan KP (perusahaan, judul, tanggal) | KpFlowController | KP Flow |
| REQ-KP-02 | Validasi status KP (tidak bisa ubah jika sudah disetujui) | KpFlowController | KP Flow |
| REQ-KP-03 | Progress tracking 7 step (pengajuan → selesai) | KpFlowController | KP Flow |
| REQ-KP-04 | Monitoring KP per role (dosen/lapangan/admin) | KpFlowController | KP Flow |
| REQ-KP-05 | Konfirmasi pelaksanaan oleh pembimbing lapangan | KpFlowController | KP Flow |
| REQ-KP-06 | Export CSV 3 tipe (KP, Dokumen, Nilai) | KpFlowController | Export |
| REQ-KP-07 | RBAC export hanya admin | Routes middleware | Export |
| REQ-LB-01 | CRUD logbook mahasiswa (tambah, edit, hapus) | LogbookController | Logbook |
| REQ-LB-02 | Validasi file upload (JPG/PNG/PDF, max 5MB) | LogbookController | Logbook |
| REQ-LB-03 | Status flow: menunggu → disetujui/revisi | LogbookController | Logbook |
| REQ-LB-04 | Validasi logbook hanya pembimbing lapangan | LogbookController + middleware | Logbook |
| REQ-LB-05 | Notifikasi validasi ke mahasiswa | Kp::notify | Notifikasi |
| REQ-LB-06 | Download file dengan access control | LogbookController | Logbook |
| REQ-LB-07 | Audit log setiap aksi logbook | Kp::audit | Logbook |
| REQ-BIM-01 | Mahasiswa ajukan jadwal bimbingan (tanggal, metode, topik) | GuidanceController | Bimbingan |
| REQ-BIM-02 | Validasi dosen_id wajib sebelum ajukan | GuidanceController | Bimbingan |
| REQ-BIM-03 | Dosen respon: setuju / ubah jadwal / selesai | GuidanceController | Bimbingan |
| REQ-BIM-04 | Catatan wajib untuk respon selesai & revisi | GuidanceController | Bimbingan |
| REQ-BIM-05 | RBAC respon hanya dosen pembimbing | GuidanceController + middleware | Bimbingan |
| REQ-BIM-06 | Mahasiswa upload lampiran tindak lanjut | GuidanceController | Bimbingan |
| REQ-BIM-07 | Validasi file lampiran (PDF/DOC/DOCX/JPG/PNG, max 10MB) | GuidanceController | Bimbingan |
| REQ-BIM-08 | Replace lampiran lama otomatis | GuidanceController | Bimbingan |
| REQ-BIM-09 | Download lampiran RBAC (mahasiswa, dosen, admin) | GuidanceController | Bimbingan |
| REQ-BIM-10 | Notifikasi setiap aksi bimbingan | Kp::notify | Notifikasi |
| REQ-BIM-11 | Audit log bimbingan | Kp::audit | Bimbingan |
| REQ-NIL-01 | Input nilai per komponen per role (dosen: dosen/seminar, lapangan: lapangan) | GradeController | Nilai |
| REQ-NIL-02 | Validasi nilai 0-100 numeric | GradeController | Nilai |
| REQ-NIL-03 | RBAC komponen nilai | GradeController | Nilai |
| REQ-NIL-04 | Guard nilai_published (tidak bisa ubah setelah publish) | GradeController | Nilai |
| REQ-NIL-05 | Publikasi nilai oleh admin | GradeController | Nilai |
| REQ-NIL-06 | Bobot nilai configurable admin (total 100%) | GradeController | Nilai |
| REQ-NIL-07 | Validasi total bobot = 100% | GradeController | Nilai |
| REQ-NIL-08 | Perhitungan weighted average (hanya komponen yg ada) | Kp::finalGrade | Nilai |
| REQ-NIL-09 | Konversi nilai angka ke huruf | Kp::huruf | Nilai |
| REQ-NIL-10 | Audit log nilai | Kp::audit | Nilai |
| REQ-DOC-01 | Upload dokumen mahasiswa (kategori, file, deskripsi) | DocumentController | Dokumen |
| REQ-DOC-02 | Validasi kategori aktif & file (PDF/DOC/DOCX, max size per kategori) | DocumentController | Dokumen |
| REQ-DOC-03 | List dokumen dengan filter, search, pagination, sorting | DocumentController | Dokumen |
| REQ-DOC-04 | Detail dokumen + auto status reviewed saat dosen buka | DocumentController | Dokumen |
| REQ-DOC-05 | Update metadata & file (hanya draft/revision) | DocumentController | Dokumen |
| REQ-DOC-06 | Versioning otomatis saat ganti file | DocumentController | Dokumen |
| REQ-DOC-07 | Hapus dokumen (pemilik draft only, admin all) | DocumentController | Dokumen |
| REQ-DOC-08 | Submit dokumen ke dosen (draft/revision → submitted) | DocumentController | Dokumen |
| REQ-REVIEW-01 | Review dosen: revision / approved / rejected | DocumentController | Dokumen |
| REQ-REVIEW-02 | Catatan wajib untuk revision & rejected | DocumentController | Dokumen |
| REQ-REVIEW-03 | RBAC review hanya dosen pembimbing | DocumentController | Dokumen |
| REQ-REVIEW-04 | Guard status sudah diputuskan (approved/archived) | DocumentController | Dokumen |
| REQ-DOC-09 | Download dokumen dengan access control | DocumentController | Dokumen |
| REQ-ARSIP-01 | Arsip view (approved + archived) | DocumentController + PageController | Dokumen |
| REQ-ADM-01 | Dashboard admin dengan statistik | AdminController + PageController | Admin |
| REQ-ADM-02 | CRUD User (mahasiswa, dosen, lapangan, admin) | AdminController | Admin |
| REQ-ADM-03 | CRUD Kategori Dokumen (wajib, max size, deadline) | AdminController | Admin |
| REQ-ADM-04 | CRUD KP Data (status, catatan admin) | AdminController | Admin |
| REQ-ADM-05 | CRUD Perusahaan | CompanyController | Admin |
| REQ-ADM-06 | Settings (info prodi, bobot nilai) | InfoController | Admin |
| REQ-ADM-07 | Audit Log (user, action, detail, IP, waktu) | InfoController | Admin |
| REQ-ADM-08 | Pengumuman (target role) | InfoController | Notifikasi |
| REQ-ADM-09 | RBAC Admin Only | Middleware admin | Admin |
| REQ-ADM-10 | Categories API untuk dropdown mahasiswa | AdminController | Admin |
| REQ-EXP-01 | Export KP CSV dengan format standar | KpFlowController | Export |
| REQ-EXP-02 | Export Dokumen CSV | KpFlowController | Export |
| REQ-EXP-03 | Export Nilai CSV dengan perhitungan akhir | KpFlowController | Export |
| REQ-EXP-04 | Format CSV: UTF-8 BOM, delimiter semicolon | KpFlowController | Export |
| REQ-EXP-05 | Validasi type export (kp/dokumen/nilai) | KpFlowController | Export |
| REQ-EXP-06 | RBAC Export Admin Only | Middleware admin | Export |
| REQ-EXP-07 | Handle data kosong (header only) | KpFlowController | Export |
| REQ-EXP-08 | Escape karakter khusus di CSV | KpFlowController | Export |
| REQ-NOTIF-01 | List notifikasi user (read/unread) | InfoController | Notifikasi |
| REQ-NOTIF-02 | Mark all read | InfoController | Notifikasi |
| REQ-NOTIF-03 | Go to notifikasi (navigasi + mark read) | InfoController | Notifikasi |
| REQ-NOTIF-04 | Pengumuman per role target | InfoController | Notifikasi |
| REQ-NOTIF-05 | Notifikasi otomatis pada aksi sistem | Kp::notify (various controllers) | Notifikasi |
| REQ-NOTIF-06 | Format data notifikasi (title, body, url, read_at) | Notif model | Notifikasi |

---

## Matriks Traceability (Requirement → Test Case)

| Req ID | Test Case IDs | Coverage |
|--------|---------------|----------|
| REQ-AUTH-01 | TC-AUTH-001, 002, 003, 004 | ✓ Full |
| REQ-AUTH-02 | TC-AUTH-008 | ✓ Full |
| REQ-AUTH-03 | TC-AUTH-009 | ✓ Full |
| REQ-AUTH-04 | TC-AUTH-010 | ✓ Full |
| REQ-AUTH-05 | TC-AUTH-011, 012 | ✓ Full |
| REQ-AUTH-06 | TC-AUTH-013, 014 | ✓ Full |
| REQ-AUTH-07 | TC-AUTH-016 | ✓ Full |
| REQ-AUTH-08 | TC-AUTH-001-004, 019 | ✓ Full |
| REQ-AUTH-09 | TC-AUTH-015 | ✓ Full |
| REQ-AUTH-10 | TC-AUTH-020 | ✓ Full |
| REQ-KP-01 | TC-KP-001, 002, 004, 005 | ✓ Full |
| REQ-KP-02 | TC-KP-002, 003 | ✓ Full |
| REQ-KP-03 | TC-KP-006, 007, 008, 009, 010, 011, 025 | ✓ Full |
| REQ-KP-04 | TC-KP-012, 013, 014, 015 | ✓ Full |
| REQ-KP-05 | TC-KP-016, 017, 018, 019 | ✓ Full |
| REQ-KP-06 | TC-KP-020, 021, 022, TC-EXP-001, 002, 003 | ✓ Full |
| REQ-KP-07 | TC-KP-024, TC-EXP-005 | ✓ Full |
| REQ-LB-01 | TC-LB-005, 009, 010, 011, 012, 013, 014, 015, 016 | ✓ Full |
| REQ-LB-02 | TC-LB-007 | ✓ Full |
| REQ-LB-03 | TC-LB-005, 009, 010, 017, 018, 019 | ✓ Full |
| REQ-LB-04 | TC-LB-020, 021 | ✓ Full |
| REQ-LB-05 | TC-LB-017, 018 | ✓ Full |
| REQ-LB-06 | TC-LB-022, 023, 024, 025, 026 | ✓ Full |
| REQ-LB-07 | TC-LB-005, 017, 018 | ✓ Full |
| REQ-BIM-01 | TC-BIM-004, 006, 007, 008 | ✓ Full |
| REQ-BIM-02 | TC-BIM-005 | ✓ Full |
| REQ-BIM-03 | TC-BIM-009, 010, 011, 012, 013 | ✓ Full |
| REQ-BIM-04 | TC-BIM-011, 013 | ✓ Full |
| REQ-BIM-05 | TC-BIM-014, 015 | ✓ Full |
| REQ-BIM-06 | TC-BIM-016, 017, 018, 019 | ✓ Full |
| REQ-BIM-07 | TC-BIM-017 | ✓ Full |
| REQ-BIM-08 | TC-BIM-018 | ✓ Full |
| REQ-BIM-09 | TC-BIM-020, 021, 022, 023, 024 | ✓ Full |
| REQ-BIM-10 | TC-BIM-004, 009, 010, 012, 016 | ✓ Full |
| REQ-BIM-11 | TC-BIM-004, 009, 010, 012, 016 | ✓ Full |
| REQ-NIL-01 | TC-NIL-006, 007, 008, 009, 010 | ✓ Full |
| REQ-NIL-02 | TC-NIL-011 | ✓ Full |
| REQ-NIL-03 | TC-NIL-009, 010 | ✓ Full |
| REQ-NIL-04 | TC-NIL-014 | ✓ Full |
| REQ-NIL-05 | TC-NIL-015, 016, 017, 018 | ✓ Full |
| REQ-NIL-06 | TC-NIL-019, 022, TC-ADM-020, 021 | ✓ Full |
| REQ-NIL-07 | TC-NIL-020, TC-ADM-021 | ✓ Full |
| REQ-NIL-08 | TC-NIL-023, 024 | ✓ Full |
| REQ-NIL-09 | TC-NIL-025 | ✓ Full |
| REQ-NIL-10 | TC-NIL-006, 015, 019 | ✓ Full |
| REQ-DOC-01 | TC-DOC-001, 002, 003, 004 | ✓ Full |
| REQ-DOC-02 | TC-DOC-002, 003 | ✓ Full |
| REQ-DOC-03 | TC-DOC-005, 006, 007, 008 | ✓ Full |
| REQ-DOC-04 | TC-DOC-009, 010, 011 | ✓ Full |
| REQ-DOC-05 | TC-DOC-012, 013, 014 | ✓ Full |
| REQ-DOC-06 | TC-DOC-012, 029 | ✓ Full |
| REQ-DOC-07 | TC-DOC-015, 016, 017 | ✓ Full |
| REQ-DOC-08 | TC-DOC-018, 019 | ✓ Full |
| REQ-REVIEW-01 | TC-DOC-020, 022, 023, 024 | ✓ Full |
| REQ-REVIEW-02 | TC-DOC-021 | ✓ Full |
| REQ-REVIEW-03 | TC-DOC-025 | ✓ Full |
| REQ-REVIEW-04 | TC-DOC-026 | ✓ Full |
| REQ-DOC-09 | TC-DOC-027, 028 | ✓ Full |
| REQ-ARSIP-01 | TC-DOC-030 | ✓ Full |
| REQ-ADM-01 | TC-ADM-001 | ✓ Full |
| REQ-ADM-02 | TC-ADM-002, 003, 004, 005, 006, 007 | ✓ Full |
| REQ-ADM-03 | TC-ADM-008, 009, 010, 011, 012 | ✓ Full |
| REQ-ADM-04 | TC-ADM-013, 014, 015, 016 | ✓ Full |
| REQ-ADM-05 | TC-ADM-017, 018 | ✓ Full |
| REQ-ADM-06 | TC-ADM-019, 020, 021 | ✓ Full |
| REQ-ADM-07 | TC-ADM-022 | ✓ Full |
| REQ-ADM-08 | TC-ADM-023, 024, TC-NOTIF-006, 007 | ✓ Full |
| REQ-ADM-09 | TC-ADM-025, TC-EXP-005 | ✓ Full |
| REQ-ADM-10 | TC-ADM-026 | ✓ Full |
| REQ-EXP-01 | TC-EXP-001 | ✓ Full |
| REQ-EXP-02 | TC-EXP-002 | ✓ Full |
| REQ-EXP-03 | TC-EXP-003 | ✓ Full |
| REQ-EXP-04 | TC-EXP-001, 002, 003 | ✓ Full |
| REQ-EXP-05 | TC-EXP-004 | ✓ Full |
| REQ-EXP-06 | TC-EXP-005 | ✓ Full |
| REQ-EXP-07 | TC-EXP-006 | ✓ Full |
| REQ-EXP-08 | TC-EXP-007 | ✓ Full |
| REQ-NOTIF-01 | TC-NOTIF-001, 002 | ✓ Full |
| REQ-NOTIF-02 | TC-NOTIF-003 | ✓ Full |
| REQ-NOTIF-03 | TC-NOTIF-004, 005 | ✓ Full |
| REQ-NOTIF-04 | TC-NOTIF-006, 007 | ✓ Full |
| REQ-NOTIF-05 | TC-NOTIF-008 | ✓ Full |
| REQ-NOTIF-06 | TC-NOTIF-009 | ✓ Full |

---

## Coverage Summary

| Kategori | Requirements | Test Cases | Coverage |
|----------|--------------|------------|----------|
| Auth | 10 | 20 | 100% |
| KP Flow | 7 | 25 | 100% |
| Logbook | 7 | 28 | 100% |
| Bimbingan | 11 | 25 | 100% |
| Nilai | 10 | 25 | 100% |
| Dokumen | 14 | 31 | 100% |
| Admin | 10 | 26 | 100% |
| Export | 8 | 8 | 100% |
| Notifikasi | 6 | 10 | 100% |
| **TOTAL** | **83** | **198** | **100%** |

---

## Gap Analysis (Jika Ada)
*Belum ada gap - semua requirement memiliki test case minimal 1*

---

## Catatan
- Matriks ini dibuat berdasarkan analisis kode (Controller, Routes, Model, Support Class)
- Requirement diekstrak dari: validasi, middleware, business logic di controller, comment kode (REQ-XXX)
- Test case di file `01-09_TEST_CASES_*.md` merujuk ke Req ID di kolom "Traceability Matrix"
- Update matriks ini saat: requirement baru, test case baru/dihapus, refactoring besar