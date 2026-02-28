<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'income_id',
        'category_id',
        'vendor_name',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'expense_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function receiptIsImage(): bool
    {
        if (!$this->receipt_path) return false;
        $ext = strtolower(pathinfo($this->receipt_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function receiptIsPdf(): bool
    {
        if (!$this->receipt_path) return false;
        return strtolower(pathinfo($this->receipt_path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
