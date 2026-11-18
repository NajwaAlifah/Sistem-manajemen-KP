<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Guidance extends Model
{
    public const STATUS = ['diajukan' => 'Menunggu Konfirmasi', 'disetujui' => 'Disetujui', 'jadwal_diubah' => 'Jadwal Diubah', 'selesai' => 'Selesai'];
    protected $fillable = ['user_id', 'dosen_id', 'tanggal_usul', 'tanggal_final', 'metode', 'topik', 'status', 'catatan_dosen', 'lampiran_path', 'lampiran_name'];
    protected $casts = ['tanggal_usul' => 'datetime', 'tanggal_final' => 'datetime'];
    public function owner() { return $this->belongsTo(User::class, 'user_id'); }
    public function dosen() { return $this->belongsTo(User::class, 'dosen_id'); }
}
