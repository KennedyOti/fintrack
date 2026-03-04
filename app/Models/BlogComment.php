<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $fillable = [
        'blog_post_id', 'user_id', 'parent_id',
        'guest_name', 'guest_email', 'content',
        'status', 'ip_address', 'user_agent',
    ];

    // ── Relationships ─────────────────────────────────────────────────────
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('status', 'approved');
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public function getAuthorNameAttribute(): string
    {
        if ($this->user) return $this->user->name;
        return $this->guest_name ?? 'Anonymous';
    }

    public function getAuthorAvatarAttribute(): string
    {
        $name = $this->author_name;
        $initials = strtoupper(substr($name, 0, 1));
        $colors = ['#22D3EE', '#22C55E', '#F59E0B', '#8B5CF6', '#F43F5E', '#0E7490'];
        $color  = $colors[crc32($name) % count($colors)];
        return '<div style="width:36px;height:36px;border-radius:50%;background:' . $color . ';display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">' . $initials . '</div>';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
