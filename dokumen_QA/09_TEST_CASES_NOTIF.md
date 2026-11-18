# Test Case - Notifikasi & Pengumuman
**Modul:** InfoController  
**Routes:** GET /notifications, POST /notifications/read-all, GET /notifications/{id}/go, GET /pengumuman

---

## TC-NOTIF-001: List Notifikasi - User Melihat Notifikasi Sendiri
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (punya 5 notifikasi: 3 unread, 2 read) | - | - |
| 2. Buka `/notifications` | - | List 5 notifikasi, unread berwarna/highlight, read normal |
| 3. Cek pagination | - | 15 per page |

---

## TC-NOTIF-002: List Notifikasi - Urutan Terbaru
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login user (notifikasi tanggal berbeda) | - | - |
| 2. Buka `/notifications` | - | Urut created_at desc (terbaru di atas) |

---

## TC-NOTIF-003: Tandai Semua Dibaca
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login user (3 unread) | - | - |
| 2. POST `/notifications/read-all` | - | Success, semua notifikasi user jadi read_at = now() |
| 3. Cek list | - | Semua tampil sebagai read |

---

## TC-NOTIF-004: Go To Notifikasi - Navigasi & Tandai Baca
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login user (notifikasi unread dengan url: `/logbook`) | - | - |
| 2. GET `/notifications/{id}/go` | - | Redirect ke url notifikasi, notifikasi tersebut jadi read |
| 3. Notifikasi tanpa url | - | Redirect ke dashboard, tetap tandai baca |

---

## TC-NOTIF-005: Go To Notifikasi - Bukan Pemilik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login user A | - | - |
| 2. GET `/notifications/{id_user_B}` | - | 404/403 (notifikasi tidak ditemukan untuk user ini) |

---

## TC-NOTIF-006: Pengumuman - List Pengumuman
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (ada 3 pengumuman target mahasiswa, 1 target dosen) | - | - |
| 2. Buka `/pengumuman` | - | Tampil 3 pengumuman (target mahasiswa), urut terbaru |
| 3. Login dosen | - | Tampil pengumuman target dosen + target all |

---

## TC-NOTIF-007: Pengumuman - Target Role
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Admin buat pengumuman target ["mahasiswa"] | - | - |
| 2. Login mahasiswa | - | Pengumuman tampil |
| 3. Login dosen | - | Pengumuman TIDAK tampil |
| 4. Admin buat target ["mahasiswa", "dosen"] | - | Kedua role lihat |
| 5. Admin buat target [] (kosong = all) | - | Semua role lihat |

---

## TC-NOTIF-008: Notifikasi Terkirim Saat Aksi Terjadi
| Trigger Aksi | Penerima | Cek Notifikasi |
|--------------|----------|----------------|
| Mahasiswa submit pengajuan KP | Semua Admin | "Pengajuan KP baru" |
| Admin update status KP | Mahasiswa | "Status KP berubah" |
| Lapangan konfirmasi pelaksanaan | Mahasiswa + Dosen | "Pelaksanaan KP dikonfirmasi" |
| Mahasiswa tambah logbook | Lapangan | "Logbook baru menunggu validasi" |
| Mahasiswa update logbook | Lapangan | "Logbook diperbarui" |
| Lapangan validasi setuju/revisi | Mahasiswa | "Logbook disetujui/perlu diperbaiki" |
| Mahasiswa ajukan bimbingan | Dosen | "Pengajuan jadwal bimbingan" |
| Dosen respon bimbingan | Mahasiswa | "Bimbingan: [status]" |
| Mahasiswa upload lampiran bimbingan | Dosen | "Lampiran tindak lanjut bimbingan" |
| Mahasiswa upload dokumen | - | (audit only) |
| Mahasiswa submit dokumen | Dosen | "Dokumen menunggu pemeriksaan" |
| Dosen review revisi/approve | Mahasiswa | "Dokumen perlu revisi/disetujui" |
| Dosen/Lapangan input nilai | - | (audit only) |
| Admin publish nilai | Mahasiswa | "Nilai KP dipublikasikan" |
| Admin buat pengumuman | Target role | Notifikasi pengumuman |

---

## TC-NOTIF-009: Notifikasi - Format Data
| Field | Expected |
|-------|----------|
| title | String, judul notifikasi |
| body | String, detail singkat |
| url | String/NULL, link navigasi (route) |
| read_at | NULL (unread) / timestamp (read) |
| created_at | Timestamp pembuatan |

---

## TC-NOTIF-010: Real-time / Polling (Jika Ada)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka halaman notifikasi | - | - |
| 2. Trigger notifikasi baru (dari user lain) | - | Notifikasi muncul tanpa refresh (jika pakai polling/websocket) |
| 3. Jika tidak ada real-time | - | Refresh manual menampilkan notifikasi baru |

---

## Traceability Matrix - Notifikasi & Pengumuman
| Requirement | Test Case |
|-------------|-----------|
| REQ-NOTIF-01: List notifikasi user | TC-NOTIF-001, 002 |
| REQ-NOTIF-02: Mark all read | TC-NOTIF-003 |
| REQ-NOTIF-03: Go to notifikasi (navigasi + mark read) | TC-NOTIF-004, 005 |
| REQ-NOTIF-04: Pengumuman per role target | TC-NOTIF-006, 007 |
| REQ-NOTIF-05: Notifikasi otomatis pada aksi sistem | TC-NOTIF-008 |
| REQ-NOTIF-06: Format data notifikasi | TC-NOTIF-009 |