<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'og_image', 'status', 'published_at', 'scheduled_at',
        'views', 'reading_time', 'featured', 'allow_comments',
        'meta_title', 'meta_description', 'canonical_url', 'focus_keyword', 'schema_markup',
    ];

    protected $casts = [
        'published_at'  => 'datetime',
        'scheduled_at'  => 'datetime',
        'featured'      => 'boolean',
        'allow_comments'=> 'boolean',
        'schema_markup' => 'array',
        'views'         => 'integer',
        'reading_time'  => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // ── Relationships ─────────────────────────────────────────────────────
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_post_id');
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_post_id')
            ->where('status', 'approved')
            ->whereNull('parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class, 'blog_post_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public static function generateSlug(string $title, ?int $exceptId = null): string
    {
        $slug  = Str::slug($title);
        $count = 0;
        while (true) {
            $candidate = $count === 0 ? $slug : $slug . '-' . $count;
            $query     = static::withTrashed()->where('slug', $candidate);
            if ($exceptId) $query->where('id', '!=', $exceptId);
            if (!$query->exists()) return $candidate;
            $count++;
        }
    }

    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200));
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isLikedBy(?User $user, string $ip): bool
    {
        if ($user) {
            return $this->likes()->where('user_id', $user->id)->exists();
        }
        return $this->likes()->whereNull('user_id')->where('ip_address', $ip)->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'published' => '<span class="badge bg-success">Published</span>',
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'scheduled' => '<span class="badge bg-warning text-dark">Scheduled</span>',
            default     => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getFormattedPublishedAtAttribute(): string
    {
        if (!$this->published_at) return '—';
        return $this->published_at->format('M j, Y');
    }
}
