<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Static Logger ───────────────────────────────────────────────────────

    /**
     * Log an activity.
     *
     * @param  string      $action       Dot-notation identifier e.g. 'admin.user.suspended'
     * @param  string      $description  Human-readable description
     * @param  mixed|null  $subject      The model being acted upon (optional)
     * @param  array       $properties   Extra structured data (old/new values, etc.)
     */
    public static function log(
        string $action,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): static {
        return static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'properties'   => $properties ?: null,
            'ip_address'   => request()->ip(),
            'user_agent'   => substr(request()->userAgent() ?? '', 0, 500),
        ]);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Map action dot-notation to a Font Awesome icon + colour.
     */
    public function getIconAttribute(): array
    {
        return match (true) {
            str_contains($this->action, 'login')       => ['fas fa-right-to-bracket',  '#22C55E'],
            str_contains($this->action, 'logout')      => ['fas fa-right-from-bracket', '#64748B'],
            str_contains($this->action, 'deleted')     => ['fas fa-trash',              '#F43F5E'],
            str_contains($this->action, 'restored')    => ['fas fa-rotate-left',        '#22D3EE'],
            str_contains($this->action, 'suspended')   => ['fas fa-ban',                '#F59E0B'],
            str_contains($this->action, 'activated')   => ['fas fa-circle-check',       '#22C55E'],
            str_contains($this->action, 'password')    => ['fas fa-key',                '#8B5CF6'],
            str_contains($this->action, 'settings')    => ['fas fa-gear',               '#0E7490'],
            str_contains($this->action, 'role')        => ['fas fa-user-shield',        '#0F3A66'],
            str_contains($this->action, 'created')     => ['fas fa-plus-circle',        '#059669'],
            str_contains($this->action, 'updated')     => ['fas fa-pen',                '#0E7490'],
            default                                    => ['fas fa-circle-dot',         '#94A3B8'],
        };
    }

    /**
     * Format action slug for display: 'admin.user.suspended' → 'User Suspended'
     */
    public function getActionLabelAttribute(): string
    {
        $parts = explode('.', $this->action);
        $last  = array_pop($parts);
        return ucwords(str_replace('_', ' ', $last));
    }
}
