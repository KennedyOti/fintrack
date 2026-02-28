<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color',
        'monthly_budget',
    ];

    protected $casts = [
        'monthly_budget' => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Sum of expenses in the given year/month (defaults to current month).
     */
    public function spentThisMonth(?int $year = null, ?int $month = null): float
    {
        $year  = $year  ?? now()->year;
        $month = $month ?? now()->month;

        return (float) $this->expenses()
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->sum('amount');
    }

    /**
     * Budget utilisation as a percentage (0–∞). Returns null when no budget set.
     */
    public function budgetPercent(?int $year = null, ?int $month = null): ?float
    {
        if (!$this->monthly_budget || $this->monthly_budget <= 0) {
            return null;
        }

        return round(($this->spentThisMonth($year, $month) / $this->monthly_budget) * 100, 1);
    }

    /**
     * Status string: 'over' | 'warning' | 'ok' | null (no budget).
     */
    public function budgetStatus(?int $year = null, ?int $month = null): ?string
    {
        $pct = $this->budgetPercent($year, $month);
        if ($pct === null) return null;
        if ($pct >= 100) return 'over';
        if ($pct >= 80)  return 'warning';
        return 'ok';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
