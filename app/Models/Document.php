<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public const STATUS = ['draft' => 'Draft', 'submitted' => 'Diajukan', 'reviewed' => 'Diperiksa', 'revision' => 'Revisi', 'approved' => 'Disetujui', 'archived' => 'Arsip'];
    protected $fillable = ['user_id', 'category_id', 'name', 'description', 'status', 'current_version', 'submitted_at', 'approved_at', 'approved_by'];
    protected $casts = ['submitted_at' => 'datetime', 'approved_at' => 'datetime'];

    public function owner()    { return $this->belongsTo(User::class, 'user_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function versions() { return $this->hasMany(DocumentVersion::class)->orderBy('version'); }
    public function reviews()  { return $this->hasMany(Review::class)->latest(); }

    /** Pembatasan akses berdasarkan peran (BR-02, BR-08, REQ-ARSIP-03). */
    public function scopeVisibleTo(Builder $q, User $u): Builder
    {
        return match ($u->role) {
            'admin' => $q,
            'dosen' => $q->whereHas('owner', fn ($o) => $o->where('dosen_id', $u->id)),
            'lapangan' => $q->whereRaw('1 = 0'),
            default => $q->where('user_id', $u->id),
        };
    }

    public function canBeSeenBy(User $u): bool
    {
        return static::whereKey($this->id)->visibleTo($u)->exists();
    }

    public static function versionLabel(int $v): string { return $v . '.0'; }

    public function toApi(bool $detail = false): array
    {
        $v = $this->versions->firstWhere('version', $this->current_version) ?? $this->versions->last();
        $last = $this->reviews->first();
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category->slug,
            'category_name' => $this->category->name,
            'status' => $this->status,
            'version' => static::versionLabel($this->current_version),
            'file_size' => $v?->size,
            'file_type' => $v?->mime,
            'download_url' => route('documents.download', $this->id),
            'created_by' => ['id' => $this->owner->id, 'name' => $this->owner->name, 'email' => $this->owner->email],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'last_feedback' => $last ? [
                'id' => $last->id, 'message' => $last->message,
                'given_by' => $last->author->email, 'created_at' => $last->created_at->toIso8601String(),
            ] : null,
        ];
        if ($detail) {
            $data['current_version'] = $data['version'];
            $data['submitted_at'] = $this->submitted_at?->toIso8601String();
            $data['approved_at'] = $this->approved_at?->toIso8601String();
            $data['versions'] = $this->versions->map(fn ($x) => [
                'version' => static::versionLabel($x->version),
                'uploaded_at' => $x->created_at->toIso8601String(),
                'download_url' => route('documents.download', [$this->id, 'v' => $x->version]),
                'status' => $x->status_at_upload,
            ])->values();
            $data['feedback'] = $this->reviews->map(fn ($r) => [
                'id' => $r->id, 'version' => static::versionLabel($r->version), 'message' => $r->message, 'type' => $r->type,
                'given_by' => ['id' => $r->author->id, 'name' => $r->author->name, 'email' => $r->author->email],
                'created_at' => $r->created_at->toIso8601String(),
            ])->values();
        }
        return $data;
    }
}
