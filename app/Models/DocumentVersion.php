<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = ['document_id', 'version', 'path', 'original_name', 'mime', 'size', 'status_at_upload', 'uploaded_by'];
    public function document() { return $this->belongsTo(Document::class); }
}
