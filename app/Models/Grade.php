<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    public const KOMPONEN = ['dosen' => 'Dosen Pembimbing', 'lapangan' => 'Pembimbing Lapangan', 'seminar' => 'Seminar / Presentasi'];
    protected $fillable = ['user_id', 'komponen', 'nilai', 'catatan', 'graded_by'];
    public function owner() { return $this->belongsTo(User::class, 'user_id'); }
}
