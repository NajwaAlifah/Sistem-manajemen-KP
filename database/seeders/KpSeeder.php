<?php
namespace Database\Seeders;

use App\Models\{Category, User};
use Illuminate\Database\Seeder;

class KpSeeder extends Seeder
{
    public function run(): void
    {
        // 8 kategori dokumen (Project Charter, butir Tujuan)
        foreach ([
            'proposal' => 'Proposal KP', 'surat_pengantar' => 'Surat Pengantar', 'surat_balasan' => 'Surat Balasan Perusahaan',
            'laporan' => 'Laporan KP', 'lembar_bimbingan' => 'Lembar Bimbingan', 'dokumen_revisi' => 'Dokumen Revisi',
            'lembar_penilaian' => 'Lembar Penilaian', 'dokumen_pendukung' => 'Dokumen Pendukung',
        ] as $slug => $name) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name, 'max_file_size' => 10]);
        }

        // Akun demo (data anonim, Risiko R4 / UU PDP). Password: password123
        $dosen = User::updateOrCreate(['email' => 'dosen@test.com'], ['name' => 'Dosen Pembimbing', 'role' => 'dosen', 'nip' => '0000000001', 'password' => 'password123']);
        User::updateOrCreate(['email' => 'admin@test.com'], ['name' => 'Admin Prodi SI', 'role' => 'admin', 'nip' => '0000000000', 'password' => 'password123']);
        $co = \App\Models\Company::firstOrCreate(['name' => 'PT Contoh Teknologi'], ['address' => 'Jl. Contoh No. 1, Jakarta', 'bidang' => 'Teknologi Informasi', 'phone' => '021-0000000']);
        $lap = User::updateOrCreate(['email' => 'lapangan@test.com'], ['name' => 'Pembimbing Lapangan', 'role' => 'lapangan', 'nip' => '0000000002', 'company_id' => $co->id, 'password' => 'password123']);
        $mhs = User::updateOrCreate(['email' => 'mahasiswa@test.com'], ['name' => 'Mahasiswa Demo', 'role' => 'mahasiswa', 'nim' => '20240000001', 'dosen_id' => $dosen->id, 'lapangan_id' => $lap->id, 'password' => 'password123']);
        \App\Models\KpData::firstOrCreate(['mahasiswa_id' => $mhs->id], ['perusahaan' => 'PT Contoh Teknologi', 'company_id' => $co->id, 'status' => 'berlangsung', 'judul' => 'Sistem Informasi Inventori', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-08-31']);
        \App\Models\Announcement::firstOrCreate(['title' => 'Selamat datang di Sistem Manajemen KP'], ['body' => 'Unggah dokumen KP, isi logbook harian, dan ajukan jadwal bimbingan melalui menu yang tersedia. Pantau tenggat dokumen di menu Progress KP.']);
        foreach (['bobot_dosen' => 40, 'bobot_lapangan' => 40, 'bobot_seminar' => 20] as $k => $v) \App\Support\Kp::setSetting($k, $v);
        \App\Support\Kp::setSetting('allowed_ext', 'pdf,doc,docx');
    }
}
