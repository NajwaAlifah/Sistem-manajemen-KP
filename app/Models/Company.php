<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'bidang', 'phone'];
    public function pembimbing() { return $this->hasMany(User::class, 'company_id'); }
    public function kp() { return $this->hasMany(KpData::class); }
}
