<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    protected $fillable = ['name', 'slug', 'meta_title', 'meta_description'];

    // ── Relationships ─────────────────────────────────────────────────────
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag', 'blog_tag_id', 'blog_post_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public static function findOrCreateByName(string $name): self
    {
        $slug = Str::slug($name);
        return static::firstOrCreate(['slug' => $slug], ['name' => $name, 'slug' => $slug]);
    }

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
