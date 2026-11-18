# Test Case - Autentikasi (Auth)
**Modul:** Login, Logout, Profil, Password  
**Controller:** AuthController  
**Routes:** GET/POST /login, POST /logout, GET/POST /settings/profile, GET/POST /settings/password

---

## TC-AUTH-001: Login Berhasil - Mahasiswa
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NPM mahasiswa aktif | `login`: "2021001", `password`: "password123" | Redirect ke `/dashboard/mahasiswa` |
| 3. Cek session | - | User authenticated, role = mahasiswa |
| 4. Cek audit log | - | Terdapat record `action: login` |

**Data Prasyarat:** User mahasiswa exists, is_active=true, password hashed

---

## TC-AUTH-002: Login Berhasil - Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NIP dosen aktif | `login`: "19800101", `password`: "password123" | Redirect ke `/dashboard/dosen` |
| 3. Cek role | - | role = dosen |

---

## TC-AUTH-003: Login Berhasil - Pembimbing Lapangan
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NIP lapangan aktif | `login`: "19750515", `password`: "password123" | Redirect ke `/dashboard/lapangan` |
| 3. Cek role | - | role = lapangan |

---

## TC-AUTH-004: Login Berhasil - Admin
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NIP admin aktif | `login`: "19700101", `password`: "password123" | Redirect ke `/dashboard/admin` |
| 3. Cek role | - | role = admin |

---

## TC-AUTH-005: Login Gagal - NPM/NIP Tidak Terdaftar
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NPM tidak exist | `login`: "9999999", `password`: "any" | Error: "NPM / NIP atau password salah..." |
| 3. Cek rate limiter | - | Attempt tercatat |

---

## TC-AUTH-006: Login Gagal - Password Salah
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NPM valid, password salah | `login`: "2021001", `password`: "wrong" | Error: "NPM / NIP atau password salah..." |

---

## TC-AUTH-007: Login Gagal - Akun Tidak Aktif
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Buka `/login` | - | Halaman login tampil |
| 2. Isi NPM user is_active=false | `login`: "2021002", `password`: "password123" | Error: "...akun tidak aktif" |

---

## TC-AUTH-008: Rate Limiting - 5x Percobaan Gagal
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Lakukan login gagal 5x berturut | `login`: "2021001", `password`: "wrong" | Ke-6: Error "Terlalu banyak percobaan. Coba lagi dalam X detik." |
| 2. Tunggu 60 detik | - | Bisa login lagi |

---

## TC-AUTH-009: Login - Field Kosong
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Submit tanpa isi field | `login`: "", `password`: "" | Validasi error: "NPM / NIP wajib diisi", "Password wajib diisi" |

---

## TC-AUTH-010: Logout Berhasil
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login sebagai mahasiswa | - | Authenticated |
| 2. POST `/logout` | - | Redirect ke `/login`, session destroyed |
| 3. Akses `/dashboard` | - | Redirect ke `/login` |
| 4. Cek audit log | - | Record `action: logout` |

---

## TC-AUTH-011: Update Profil Berhasil
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. Buka `/settings/profile` | - | Form tampil dengan data current |
| 3. Update nama & phone | `name`: "Nama Baru", `phone`: "08123456789" | Success message, data tersimpan di DB |

---

## TC-AUTH-012: Update Profil - Validasi Nama
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. Submit nama kosong | `name`: "", `phone`: "08123456789" | Error: "Nama wajib diisi" |
| 3. Submit nama > 100 char | `name`: "a..." (101 char) | Error: "Nama maksimal 100 karakter" |

---

## TC-AUTH-013: Update Password Berhasil
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. Buka `/settings/password` | - | Form tampil |
| 3. Ganti password | `current_password`: "password123", `password`: "newpass123", `password_confirmation`: "newpass123" | Success, password di-hash, login pakai password baru berhasil |

---

## TC-AUTH-014: Update Password - Validasi
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Password lama salah | `current_password`: "wrong" | Error: "Password saat ini tidak cocok" |
| 2. Password baru < 6 char | `password`: "123" | Error: "Password minimal 6 karakter" |
| 3. Konfirmasi tidak cocok | `password`: "newpass123", `password_confirmation`: "different" | Error: "Konfirmasi password tidak cocok" |

---

## TC-AUTH-015: Redirect After Login (Intended URL)
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Akses `/kp/pengajuan` tanpa login | - | Redirect ke `/login` |
| 2. Login valid | - | Redirect ke `/kp/pengajuan` (intended) |

---

## TC-AUTH-016: Remember Me
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login dengan checkbox "remember" | `remember`: true | Cookie remember_token terset, session persist setelah close browser |

---

## TC-AUTH-017: Guest Middleware - Akses Login Saat Sudah Login
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. Akses `/login` | - | Redirect ke `/dashboard/mahasiswa` |

---

## TC-AUTH-018: Auth Middleware - Akses Dashboard Tanpa Login
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Clear session/cookies | - | - |
| 2. Akses `/dashboard/mahasiswa` | - | Redirect ke `/login` |

---

## TC-AUTH-019: Role Middleware - Mahasiswa Akses Dashboard Dosen
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Login mahasiswa | - | - |
| 2. Akses `/dashboard/dosen` | - | 403 Forbidden / Redirect |

---

## TC-AUTH-020: Session Regeneration After Login
| Langkah | Input | Expected Result |
|---------|-------|-----------------|
| 1. Catat session ID sebelum login | - | - |
| 2. Login | - | Session ID berubah (regenerated) |

---

## Traceability Matrix - Auth
| Requirement | Test Case |
|-------------|-----------|
| REQ-AUTH-01: Login NPM/NIP | TC-AUTH-001 s.d 004 |
| REQ-AUTH-02: Rate limiting 5x/menit | TC-AUTH-008 |
| REQ-AUTH-03: Validasi field wajib | TC-AUTH-009 |
| REQ-AUTH-04: Logout & audit | TC-AUTH-010 |
| REQ-AUTH-05: Update profil | TC-AUTH-011, 012 |
| REQ-AUTH-06: Ganti password | TC-AUTH-013, 014 |
| REQ-AUTH-07: Remember me | TC-AUTH-016 |
| REQ-AUTH-08: Role-based redirect | TC-AUTH-001 s.d 004, 019 |
| REQ-AUTH-09: Intended URL redirect | TC-AUTH-015 |
| REQ-AUTH-10: Session security | TC-AUTH-020 |