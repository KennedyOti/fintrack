<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_balance',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Explicit route model binding - scope to current user
    public function resolveRouteBinding($value, $field = null)
    {
        // If no user is authenticated, return null (will show 404)
        if (!auth()->check()) {
            return null;
        }
        
        return $this->where('id', $value)
            ->where('user_id', auth()->id())
            ->first();
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    // Helper methods
    public function progressPercentage()
    {
        if (!$this->target_amount || $this->target_amount == 0) {
            return 0;
        }
        return min(100, ($this->current_balance / $this->target_amount) * 100);
    }
}
