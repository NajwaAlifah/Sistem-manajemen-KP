<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'max_file_size', 'is_active', 'deadline', 'wajib'];
    protected $casts = ['is_active' => 'boolean', 'wajib' => 'boolean', 'deadline' => 'date'];
    public function documents() { return $this->hasMany(Document::class); }
}
