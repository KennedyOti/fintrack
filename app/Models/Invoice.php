<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'project_id',
        'quote_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'notes',
        'share_token',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function debtsReceivable(): HasMany
    {
        return $this->hasMany(DebtsReceivable::class);
    }

    // Helper methods
    public static function generateInvoiceNumber()
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return 'INV-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function outstandingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getPaidAmountAttribute()
    {
        // Return the stored paid_amount from the invoices table
        // This ensures invoices without payments (paid_amount = 0) are not considered as income
        return $this->attributes['paid_amount'] ?? 0;
    }

    public function hasIncomeRecord()
    {
        return Income::where('invoice_id', $this->id)->exists();
    }

    public function getIncomeRecord()
    {
        return Income::where('invoice_id', $this->id)->first();
    }

    public function isOverdue()
    {
        return $this->due_date->isPast() && $this->status !== 'paid';
    }

    public function getShareUrl(): ?string
    {
        if (!$this->share_token) {
            return null;
        }
        return url('/view/invoice/' . $this->share_token);
    }
}
