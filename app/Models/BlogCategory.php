<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon',
        'meta_title', 'meta_description', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id')
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public static function generateSlug(string $name, ?int $exceptId = null): string
    {
        $slug  = Str::slug($name);
        $count = 0;
        while (true) {
            $candidate = $count === 0 ? $slug : $slug . '-' . $count;
            $query     = static::where('slug', $candidate);
            if ($exceptId) $query->where('id', '!=', $exceptId);
            if (!$query->exists()) return $candidate;
            $count++;
        }
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
