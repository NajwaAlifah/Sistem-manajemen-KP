<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['document_id', 'user_id', 'version', 'type', 'message', 'status_after'];
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
}
