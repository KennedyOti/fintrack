<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'tax_number',
        'notes',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function debtsReceivable(): HasMany
    {
        return $this->hasMany(DebtsReceivable::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Helper methods
    public function totalInvoiced()
    {
        // Only sum invoices that are paid or partially paid
        return $this->invoices()->whereIn('status', ['paid', 'partial'])->sum('total_amount');
    }

    public function totalPaid()
    {
        // Sum of actual paid amounts from invoices that are paid or partially paid
        return $this->invoices()->whereIn('status', ['paid', 'partial'])->sum('paid_amount');
    }

    public function outstandingBalance()
    {
        return $this->totalInvoiced() - $this->totalPaid();
    }

    public function totalProjects()
    {
        return $this->projects()->count();
    }
}
