<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['user_id', 'title', 'body', 'url', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];
}
