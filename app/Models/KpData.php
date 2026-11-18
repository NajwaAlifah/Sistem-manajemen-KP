<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KpData extends Model
{
    protected $table = 'kp_data';
    protected $fillable = ['mahasiswa_id', 'perusahaan', 'company_id', 'judul', 'tanggal_mulai', 'tanggal_selesai', 'status', 'catatan_admin', 'dikonfirmasi_at', 'nilai_published'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'dikonfirmasi_at' => 'datetime', 'nilai_published' => 'boolean'];
    public function mahasiswa() { return $this->belongsTo(User::class, 'mahasiswa_id'); }
    public function company() { return $this->belongsTo(Company::class); }
}
