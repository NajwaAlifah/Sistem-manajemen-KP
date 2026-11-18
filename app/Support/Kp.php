<?php
namespace App\Support;

use App\Models\{AuditLog, Category, Document, Grade, Notif, Setting, User};

class Kp
{
    public const ROLES = ['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen Pembimbing', 'admin' => 'Admin Prodi', 'lapangan' => 'Pembimbing Lapangan'];
    public const KP_STATUS = ['belum_mengajukan' => 'Belum Mengajukan', 'menunggu_persetujuan' => 'Menunggu Persetujuan', 'berlangsung' => 'Sedang Berlangsung',
        'menunggu_penyelesaian' => 'Menunggu Penyelesaian', 'selesai' => 'Selesai', 'ditolak' => 'Pengajuan Ditolak'];
    public const KP_BADGE = ['belum_mengajukan' => 'draft', 'menunggu_persetujuan' => 'submitted', 'berlangsung' => 'reviewed', 'menunggu_penyelesaian' => 'revision', 'selesai' => 'approved', 'ditolak' => 'revision'];

    public static function notify($users, string $title, ?string $body = null, ?string $url = null): void
    {
        foreach (collect($users instanceof \Illuminate\Support\Collection ? $users : (array) $users)->filter()->unique() as $id) {
            Notif::create(['user_id' => $id, 'title' => $title, 'body' => $body, 'url' => $url]);
        }
    }

    public static function admins() { return User::where('role', 'admin')->where('is_active', true)->pluck('id'); }

    public static function audit(string $action, ?string $detail = null): void
    {
        AuditLog::create(['user_id' => auth()->id(), 'action' => $action, 'detail' => $detail, 'ip' => request()->ip()]);
    }

    public static function setting(string $key, $default = null)
    {
        return Setting::find($key)?->value ?? $default;
    }

    public static function setSetting(string $key, $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    /** Kelengkapan dokumen wajib seorang mahasiswa (REQ: Kelengkapan Dokumen, Deadline Tracker). */
    public static function completeness(User $m): array
    {
        $docs = Document::where('user_id', $m->id)->get()->groupBy('category_id');
        $rank = ['approved' => 5, 'archived' => 5, 'reviewed' => 4, 'submitted' => 3, 'revision' => 2, 'draft' => 1];
        $items = [];
        foreach (Category::where('is_active', true)->where('wajib', true)->orderBy('id')->get() as $c) {
            $best = ($docs[$c->id] ?? collect())->sortByDesc(fn ($d) => $rank[$d->status])->first();
            $items[] = ['category' => $c, 'status' => $best?->status, 'doc_id' => $best?->id,
                'late' => $c->deadline && $c->deadline->isPast() && !in_array($best?->status, ['approved', 'archived'])];
        }
        $done = collect($items)->whereIn('status', ['approved', 'archived'])->count();
        return ['items' => $items, 'done' => $done, 'total' => count($items), 'percent' => count($items) ? (int) round($done / count($items) * 100) : 0];
    }

    /** Nilai akhir berbobot (formula dapat diubah admin prodi). */
    public static function finalGrade(User $m): ?float
    {
        $g = Grade::where('user_id', $m->id)->pluck('nilai', 'komponen');
        $w = ['dosen' => (float) self::setting('bobot_dosen', 40), 'lapangan' => (float) self::setting('bobot_lapangan', 40), 'seminar' => (float) self::setting('bobot_seminar', 20)];
        $sum = 0; $tot = 0;
        foreach ($w as $k => $bobot) { if (isset($g[$k])) { $sum += $g[$k] * $bobot; $tot += $bobot; } }
        return $tot > 0 ? round($sum / $tot, 2) : null;
    }

    public static function huruf(?float $n): string
    {
        return $n === null ? '-' : ($n >= 85 ? 'A' : ($n >= 80 ? 'A-' : ($n >= 75 ? 'B+' : ($n >= 70 ? 'B' : ($n >= 65 ? 'B-' : ($n >= 60 ? 'C+' : ($n >= 55 ? 'C' : ($n >= 40 ? 'D' : 'E'))))))));
    }
}
