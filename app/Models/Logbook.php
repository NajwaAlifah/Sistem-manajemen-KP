<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    public const STATUS = ['menunggu' => 'Menunggu Validasi', 'disetujui' => 'Disetujui', 'revisi' => 'Perlu Perbaikan'];
    protected $fillable = ['user_id', 'tanggal', 'kegiatan', 'file_path', 'file_name', 'status', 'catatan', 'validated_by', 'validated_at'];
    protected $casts = ['tanggal' => 'date', 'validated_at' => 'datetime'];
    public function owner() { return $this->belongsTo(User::class, 'user_id'); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }
}
