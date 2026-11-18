# Test Case - Admin Panel
**Modul:** AdminController, CompanyController, InfoController  
**Routes:** GET /admin/* (middleware admin), API /api/* (middleware admin)

---

## TC-ADM-001: Dashboard Admin - Statistik
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/dashboard/admin` | - | Tampil statistik: total mahasiswa, dosen, lapangan, perusahaan, KP aktif, dokumen pending, dll |
| 3. Cek API `/api/stats` | - | JSON response dengan stats yang sama |

---

## TC-ADM-002: Manajemen User - List User
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/users` | - | Tabel user dengan kolom: Nama, Email, Role, NPM/NIP, Status, Aksi |
| 3. Cek API `/api/users` | - | JSON paginated list users |

---

## TC-ADM-003: Manajemen User - Tambah User
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/api/users` | `name`: "Mahasiswa Baru", `email`: "mhs@test.com", `role`: "mahasiswa", `nim`: "2026001", `password`: "password123", `is_active`: true | 201 Created, user created, password hashed |
| 3. Cek login user baru | - | Bisa login dengan NPM & password |

---

## TC-ADM-004: Manajemen User - Validasi Tambah User
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Email duplikat | `email`: existing@test.com | Error: email unique |
| 2. NPM duplikat (role mahasiswa) | `nim`: existing_npm | Error: nim unique |
| 3. NIP duplikat (role dosen/lapangan/admin) | `nip`: existing_nip | Error: nip unique |
| 4. Role invalid | `role`: "invalid" | Error: role harus mahasiswa/dosen/admin/lapangan |
| 5. Password < 6 char | `password`: "123" | Error: password minimal 6 |

---

## TC-ADM-005: Manajemen User - Edit User
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. PUT `/api/users/{id}` | `name`: "Nama Update", `phone`: "08123456789", `is_active`: false | Success, user updated |
| 3. Cek user dinonaktifkan | - | is_active = false, tidak bisa login |

---

## TC-ADM-006: Manajemen User - Hapus User
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. DELETE `/api/users/{id}` | `id`: user_id | Success, user deleted (cascade: kp_data, logbook, dll?) |
| 3. Cek tidak bisa login | - | User not found |

---

## TC-ADM-007: Manajemen User - Admin Tidak Bisa Hapus Diri Sendiri
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin (id=1) | - | - |
| 2. DELETE `/api/users/1` | - | Error / Prevented (best practice) |

---

## TC-ADM-008: Manajemen Kategori - List Kategori
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/categories` | - | List kategori: Nama, Slug, Wajib, Max Size, Deadline, Status Aktif |
| 3. Cek API `/api/categories` | - | JSON list (tanpa pagination, untuk dropdown) |

---

## TC-ADM-009: Manajemen Kategori - Tambah Kategori
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/api/categories` | `name`: "Laporan Akhir", `slug`: "laporan-akhir", `wajib`: true, `max_file_size`: 10, `deadline`: "2026-06-30", `is_active`: true | Success, kategori created |
| 3. Cek slug unique | - | Slug harus unik |

---

## TC-ADM-010: Manajemen Kategori - Validasi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Slug duplikat | `slug`: existing | Error: slug unique |
| 2. Max file size > 0 | `max_file_size`: 0 | Error: minimal 1 MB |
| 3. Deadline format | `deadline`: "invalid" | Error: date format |

---

## TC-ADM-011: Manajemen Kategori - Edit Kategori
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. PUT `/api/categories/{id}` | `wajib`: false, `max_file_size`: 20 | Success, kategori updated |

---

## TC-ADM_012: Manajemen Kategori - Hapus Kategori
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. DELETE `/api/categories/{id}` | Kategori tidak dipakai dokumen | Success |
| 3. Kategori dipakai dokumen | - | Error / Prevented (FK constraint) |

---

## TC-ADM-013: Manajemen KP Data - List KP
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/kp` | - | Tabel KP: Mahasiswa, Perusahaan, Judul, Tanggal, Status, Dosen, Lapangan, Aksi |
| 3. Filter status | - | Filter by KP status bekerja |

---

## TC-ADM-014: Manajemen KP Data - Tambah KP (Admin)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/api/kp` | `mahasiswa_id`, `perusahaan`, `judul`, `tanggal_mulai`, `tanggal_selesai`, `status`: "menunggu_persetujuan" | Success, KP data created untuk mahasiswa |

---

## TC-ADM-015: Manajemen KP Data - Update Status KP
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. PUT `/api/kp/{id}` | `status`: "berlangsung", `catatan_admin`: "Disetujui" | Success, status updated, catatan_admin tersimpan |
| 3. Cek notifikasi mahasiswa | - | Notif status KP berubah |

---

## TC-ADM-016: Manajemen KP Data - Hapus KP
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. DELETE `/api/kp/{id}` | - | Success, KP data deleted |

---

## TC-ADM-017: Manajemen Perusahaan - List Perusahaan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/companies` | - | Tabel perusahaan: Nama, Alamat, Telepon, Email, Contact Person, Aktif |

---

## TC-ADM-018: Manajemen Perusahaan - CRUD
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. POST `/api/companies` | `name`: "PT Baru", `address`, `phone`, `email`, `contact_person`, `is_active`: true | Success |
| 2. PUT `/api/companies/{id}` | Update data | Success |
| 3. DELETE `/api/companies/{id}` | Tidak dipakai KP data | Success |
| 4. Dipakai KP data | - | Error / Prevented |

---

## TC-ADM-019: Pengaturan - Buka Halaman Settings
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/settings` | - | Form: Nama Prodi, Email Prodi, Bobot Nilai, dll |

---

## TC-ADM-020: Pengaturan - Simpan Settings
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/admin/settings` | `prodi_name`: "Sistem Informasi", `prodi_email`: "si@univ.ac.id", `bobot_dosen`: 30, `bobot_lapangan`: 50, `bobot_seminar`: 20 | Success, settings tersimpan di tabel settings |
| 3. Cek bobot nilai | - | Perhitungan nilai akhir pakai bobot baru |

---

## TC-ADM-021: Pengaturan - Validasi Bobot Total 100
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. POST settings | bobot total != 100 | Error: "Total bobot harus 100%" |

---

## TC-ADM-022: Audit Log - List Audit
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/admin/audit` | - | Tabel audit: Waktu, User, Aksi, Detail, IP |
| 3. Filter & search | - | Filter by user, action, date range bekerja |

---

## TC-ADM-023: Pengumuman - Admin Buat Pengumuman
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/api/announcements` | `title`: "Pengumuman Baru", `body`: "Isi pengumuman", `target_roles`: ["mahasiswa", "dosen"] | Success, announcement created |
| 3. Cek notifikasi target | - | Notif dibuat untuk user dengan role target |

---

## TC-ADM-024: Pengumuman - Edit & Hapus
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. PUT `/api/announcements/{id}` | Update title/body | Success |
| 2. DELETE `/api/announcements/{id}` | - | Success |

---

## TC-ADM-025: Non-Admin Akses Admin Panel
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa/dosen/lapangan | - | - |
| 2. Akses `/admin/*` atau `/api/*` (admin middleware) | - | 403 Forbidden |

---

## TC-ADM-026: API Categories - Public Access (Untuk Dropdown)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. GET `/api/categories` | - | 200 OK, list kategori aktif (untuk dropdown upload dokumen) |

---

## Traceability Matrix - Admin
| Requirement | Test Case |
|-------------|-----------|
| REQ-ADM-01: Dashboard stats | TC-ADM-001 |
| REQ-ADM-02: CRUD User | TC-ADM-002 s.d 007 |
| REQ-ADM-03: CRUD Kategori Dokumen | TC-ADM-008 s.d 012 |
| REQ-ADM-04: CRUD KP Data | TC-ADM-013 s.d 016 |
| REQ-ADM-05: CRUD Perusahaan | TC-ADM-017, 018 |
| REQ-ADM-06: Settings (bobot, info prodi) | TC-ADM-019 s.d 021 |
| REQ-ADM-07: Audit Log | TC-ADM-022 |
| REQ-ADM-08: Pengumuman | TC-ADM-023, 024 |
| REQ-ADM-09: RBAC Admin Only | TC-ADM-025 |
| REQ-ADM-10: Categories API untuk mahasiswa | TC-ADM-026 |