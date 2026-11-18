# Test Case - Export Data (Admin)
**Modul:** KpFlowController::export()  
**Route:** GET /admin/export/{type} (type: kp, dokumen, nilai)

---

## TC-EXP-001: Export KP - CSV Format
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/kp` | - | Download file `rekap-kp-YYYYMMDD.csv` |
| 3. Buka di Excel/Notepad | - | Encoding UTF-8 dengan BOM (﻿ di awal) |
| 4. Cek delimiter | - | Semicolon (;) bukan koma |
| 5. Cek Header | - | NPM;Nama;Perusahaan;Judul;Mulai;Selesai;Status KP;Dosen Pembimbing;Pembimbing Lapangan |
| 6. Cek Data Row | - | Setiap mahasiswa dengan kp_data 1 baris |
| 7. Cek Status KP Label | - | Menggunakan Kp::KP_STATUS (label Indonesia) |
| 8. Cek Tanggal Format | - | Y-m-d (2026-01-15) |
| 9. Cek Relasi | - | Dosen & Lapangan nama tampil (bisa kosong "-" jika null) |
| 10. Cek Audit Log | - | `action: export`, detail: "kp" |

---

## TC-EXP-002: Export Dokumen - CSV Format
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/dokumen` | - | Download `rekap-dokumen-YYYYMMDD.csv` |
| 3. Cek Header | - | Dokumen;Mahasiswa;NPM;Kategori;Status;Versi;Diunggah;Diperbarui |
| 4. Cek Data | - | Semua dokumen (Document::with owner, category) |
| 5. Cek Status Label | - | Document::STATUS label (Draft, Diajukan, Diperiksa, Disetujui, Revisi, Diarsipkan) |
| 6. Cek Versi Format | - | `v{current_version}.0` (contoh: v3.0) |
| 7. Cek Tanggal | - | created_at & updated_at format Y-m-d |

---

## TC-EXP-003: Export Nilai - CSV Format
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/nilai` | - | Download `rekap-nilai-YYYYMMDD.csv` |
| 3. Cek Header | - | NPM;Nama;Nilai Dosen;Nilai Lapangan;Nilai Seminar;Nilai Akhir;Huruf;Dipublikasikan |
| 4. Cek Data | - | Semua user role=mahasiswa (urut nama) |
| 5. Cek Perhitungan Nilai Akhir | - | Sesuai bobot setting saat export (Kp::finalGrade) |
| 6. Cek Huruf | - | Kp::huruf(nilai_akhir) |
| 7. Cek Dipublikasikan | - | "Ya" / "Belum" berdasarkan kp_data.nilai_published |
| 8. Mahasiswa tanpa nilai | - | Kolom nilai kosong, nilai akhir = "", huruf = "-" |

---

## TC-EXP-004: Export - Type Invalid
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. GET `/admin/export/invalid` | - | 404 Not Found (abort_unless in_array) |

---

## TC-EXP-005: Export - Non-Admin Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/dosen/lapangan | - | - |
| 2. GET `/admin/export/kp` | - | 403 Forbidden (middleware admin) |

---

## TC-EXP-006: Export - Data Kosong
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Database kosong (tidak ada mahasiswa/kp_data) | - | - |
| 2. GET `/admin/export/kp` | - | CSV hanya header, tidak error |

---

## TC-EXP-007: Export - Karakter Khusus
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Data mengandung koma, titik koma, newline, quote | - | - |
| 2. Export CSV | - | fputcsv handle escaping dengan benar (quote field yg mengandung delimiter) |

---

## TC-EXP-008: Export - Filename Timestamp
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Export hari ini | - | Filename mengandung YYYYMMDD hari ini |
| 2. Export besok | - | Filename besok |

---

## Traceability Matrix - Export
| Requirement | Test Case |
|-------------|-----------|
| REQ-EXP-01: Export KP CSV | TC-EXP-001 |
| REQ-EXP-02: Export Dokumen CSV | TC-EXP-002 |
| REQ-EXP-03: Export Nilai CSV | TC-EXP-003 |
| REQ-EXP-04: Format CSV (UTF-8 BOM, semicolon) | TC-EXP-001 s.d 003 |
| REQ-EXP-05: Validasi type export | TC-EXP-004 |
| REQ-EXP-06: RBAC Admin Only | TC-EXP-005 |
| REQ-EXP-07: Handle data kosong | TC-EXP-006 |
| REQ-EXP-08: Escape karakter khusus | TC-EXP-007 |