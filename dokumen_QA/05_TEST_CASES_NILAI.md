# Test Case - Nilai & Bobot
**Modul:** GradeController  
**Routes:** GET /nilai, POST /nilai, POST /nilai/bobot, POST /nilai/{id}/publikasi

---

## TC-NIL-001: Index Nilai - Mahasiswa Melihat Nilai Sendiri
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (punya grade dosen=85, lapangan=90, seminar=80) | - | - |
| 2. Buka `/nilai` | - | Tampil nilai per komponen, nilai akhir = (85*40 + 90*40 + 80*20)/100 = 86, huruf = A |
| 3. Cek bobot default | - | Dosen 40%, Lapangan 40%, Seminar 20% |

---

## TC-NIL-002: Index Nilai - Mahasiswa Belum Punya Nilai
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa (belum ada grade) | - | - |
| 2. Buka `/nilai` | - | Semua komponen kosong, nilai akhir = -, huruf = - |

---

## TC-NIL-003: Index Nilai - Dosen Melihat Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (3 mahasiswa binaan) | - | - |
| 2. Buka `/nilai` | - | List 3 mahasiswa dengan nilai masing-masing, form input nilai dosen & seminar |

---

## TC-NIL-004: Index Nilai - Lapangan Melihat Binaannya
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan (2 mahasiswa binaan) | - | - |
| 2. Buka `/nilai` | - | List 2 mahasiswa, form input hanya komponen lapangan |

---

## TC-NIL_005: Index Nilai - Admin Melihat Semua
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Buka `/nilai` | - | Semua mahasiswa, readonly (tidak ada form input), bisa publikasi |

---

## TC-NIL-006: Simpan Nilai - Dosen Isi Komponen Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen (mahasiswa binaan belum punya nilai dosen) | - | - |
| 2. POST `/nilai` | `user_id`: mhs_id, `komponen`: "dosen", `nilai`: 85, `catatan`: "Baik" | Success: "Nilai [nama] disimpan.", grade created dengan graded_by = dosen |
| 3. Cek audit log | - | `action: nilai_simpan`, detail: "[nama] dosen=85" |

---

## TC-NIL-007: Simpan Nilai - Dosen Isi Komponen Seminar
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST `/nilai` | `komponen`: "seminar", `nilai`: 80 | Success, grade komponen seminar created |

---

## TC-NIL-008: Simpan Nilai - Lapangan Isi Komponen Lapangan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. POST `/nilai` | `komponen`: "lapangan", `nilai`: 90 | Success, grade komponen lapangan created |

---

## TC-NIL-009: Simpan Nilai - Lapangan Coba Isi Dosen (Forbidden)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login lapangan | - | - |
| 2. POST `/nilai` | `komponen`: "dosen", `nilai`: 85 | Error 403: "Anda tidak berwenang mengisi komponen nilai ini." |

---

## TC-NIL-010: Simpan Nilai - Dosen Coba Isi Lapangan (Forbidden)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen | - | - |
| 2. POST `/nilai` | `komponen`: "lapangan", `nilai`: 90 | Error 403 |

---

## TC-NIL-011: Simpan Nilai - Validasi Nilai
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Nilai < 0 | `nilai`: -5 | Error: nilai minimal 0 |
| 2. Nilai > 100 | `nilai`: 101 | Error: nilai maksimal 100 |
| 3. Nilai desimal | `nilai`: 85.5 | Success (numeric) |
| 4. Nilai kosong | `nilai`: "" | Error: nilai wajib diisi |
| 5. Komponen invalid | `komponen`: "invalid" | Error: komponen harus dosen/lapangan/seminar |

---

## TC-NIL-012: Simpan Nilai - Mahasiswa Bukan Binaan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen A | - | - |
| 2. POST nilai mahasiswa binaan dosen B | `user_id`: mhs_binaan_B | 404 Not Found (scope binaan) |

---

## TC-NIL-013: Simpan Nilai - Update Nilai Existing
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa sudah punya nilai dosen = 80 | - | - |
| 2. Dosen update ke 85 | `komponen`: "dosen", `nilai`: 85 | Success, grade updated (updateOrCreate), graded_by = dosen terbaru |

---

## TC-NIL-014: Simpan Nilai - Mahasiswa Sudah Nilai Published
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa kp_data.nilai_published = true | - | - |
| 2. Dosen coba simpan nilai | `komponen`: "dosen", `nilai`: 90 | Error 422: "Nilai sudah dipublikasikan; hubungi admin prodi untuk perubahan." |

---

## TC-NIL-015: Publikasi Nilai - Admin Publish
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Mahasiswa punya kp_data, nilai_published = false | - | - |
| 3. POST `/nilai/{id}/publikasi` | `id`: mahasiswa_id | Success, nilai_published = true |
| 3. Cek notifikasi mahasiswa | - | Notif: "Nilai KP dipublikasikan" |
| 4. Cek audit log | - | `action: nilai_publikasi`, detail: "[nama] ya" |

---

## TC-NIL-016: Publikasi Nilai - Admin Tarik Publikasi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Mahasiswa nilai_published = true | - | - |
| 3. POST `/nilai/{id}/publikasi` | `id`: mahasiswa_id | Success, nilai_published = false |
| 3. Cek audit log | - | `action: nilai_publikasi`, detail: "[nama] tarik" |

---

## TC-NIL-017: Publikasi Nilai - Mahasiswa Belum Punya KP Data
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. Mahasiswa role=mahasiswa tapi belum punya kp_data | - | - |
| 3. POST publikasi | `id`: mahasiswa_id | Error 422: "Mahasiswa belum memiliki data KP." |

---

## TC-NIL-018: Publikasi Nilai - Non-Admin Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen/mahasiswa/lapangan | - | - |
| 2. POST `/nilai/{id}/publikasi` | - | 403 Forbidden (middleware admin) |

---

## TC-NIL-019: Atur Bobot - Admin Valid (Total 100)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST `/nilai/bobot` | `bobot_dosen`: 30, `bobot_lapangan`: 50, `bobot_seminar`: 20 | Success: "Formula nilai akhir disimpan.", setting tersimpan |
| 3. Cek perhitungan nilai akhir | - | Menggunakan bobot baru (30/50/20) |
| 4. Cek audit log | - | `action: bobot_nilai` |

---

## TC-NIL-020: Atur Bobot - Total Tidak 100
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login admin | - | - |
| 2. POST bobot | `bobot_dosen`: 40, `bobot_lapangan`: 40, `bobot_seminar`: 30 | Error 422: "Total bobot harus 100%." |

---

## TC-NIL-021: Atur Bobot - Bobot Negatif / > 100
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Bobot negatif | `bobot_dosen`: -10 | Error: minimal 0 |
| 2. Bobot > 100 | `bobot_dosen`: 150 | Error: maksimal 100 |

---

## TC-NIL-022: Atur Bobot - Non-Admin Akses
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dosen/mahasiswa/lapangan | - | - |
| 2. POST `/nilai/bobot` | - | 403 Forbidden (middleware admin) |

---

## TC-NIL-023: Perhitungan Nilai Akhir - Hanya Sebagian Komponen Ada
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Mahasiswa hanya punya nilai dosen=80, lapangan=90 (seminar belum) | - | - |
| 2. Bobot default 40/40/20 | - | - |
| 3. Cek nilai akhir | - | (80*40 + 90*40) / (40+40) = 85 (hanya bobot komponen yang ada) |

---

## TC-NIL-024: Perhitungan Nilai Akhir - Bobot Custom
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Bobot: dosen 30, lapangan 50, seminar 20 | - | - |
| 2. Nilai: dosen=80, lapangan=90, seminar=70 | - | - |
| 3. Nilai akhir | - | (80*30 + 90*50 + 70*20) / 100 = 83 |

---

## TC-NIL-025: Konversi Huruf
| Nilai Akhir | Expected Huruf |
|-------------|----------------|
| 85 | A |
| 82 | A- |
| 77 | B+ |
| 72 | B |
| 67 | B- |
| 62 | C+ |
| 57 | C |
| 50 | D |
| 30 | E |
| null | - |

---

## Traceability Matrix - Nilai
| Requirement | Test Case |
|-------------|-----------|
| REQ-NIL-01: Input nilai per komponen per role | TC-NIL-006 s.d 010 |
| REQ-NIL-02: Validasi nilai 0-100 | TC-NIL-011 |
| REQ-NIL-03: RBAC komponen (dosen: dosen/seminar, lapangan: lapangan) | TC-NIL-009, 010 |
| REQ-NIL-04: Guard nilai_published | TC-NIL-014 |
| REQ-NIL-05: Publikasi nilai admin | TC-NIL-015 s.d 018 |
| REQ-NIL-06: Bobot nilai configurable admin | TC-NIL-019 s.d 022 |
| REQ-NIL-07: Validasi total bobot 100% | TC-NIL-020 |
| REQ-NIL-08: Perhitungan weighted average | TC-NIL-023, 024 |
| REQ-NIL-09: Konversi huruf | TC-NIL-025 |
| REQ-NIL-10: Audit log | TC-NIL-006, 015, 019 |