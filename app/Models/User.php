<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'nim', 'nip', 'phone', 'profile_photo', 'is_active', 'dosen_id', 'company_id', 'lapangan_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];

    public function dosen()          { return $this->belongsTo(self::class, 'dosen_id'); }
    public function lapangan()       { return $this->belongsTo(self::class, 'lapangan_id'); }
    public function company()        { return $this->belongsTo(Company::class); }
    public function kp()             { return $this->hasOne(KpData::class, 'mahasiswa_id'); }
    public function bimbingan()      { return $this->hasMany(self::class, 'dosen_id'); }
    public function binaanLapangan() { return $this->hasMany(self::class, 'lapangan_id'); }
    public function documents()      { return $this->hasMany(Document::class); }
    public function logbooks()       { return $this->hasMany(Logbook::class); }
    public function grades()         { return $this->hasMany(Grade::class); }

    public function getRoleLabelAttribute(): string { return \App\Support\Kp::ROLES[$this->role] ?? ucfirst($this->role); }
    /** Mahasiswa yang berada di bawah user ini (sesuai perannya). */
    public function scopeBinaan($q, self $u)
    {
        $q->where('role', 'mahasiswa');
        return match ($u->role) { 'dosen' => $q->where('dosen_id', $u->id), 'lapangan' => $q->where('lapangan_id', $u->id), 'admin' => $q, default => $q->whereRaw('1 = 0') };
    }
}
