<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'business_name',
        'tax_number',
        'business_address',
        'logo_path',
        'currency_code',
        'timezone',
        'role',
        'status',
        'last_login_at',
        'google_id',
        'google_avatar',
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'two_factor_enabled',
        'dark_mode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'dark_mode' => 'boolean',
        ];
    }

    // Relationships
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function savingsAccounts(): HasMany
    {
        return $this->hasMany(SavingsAccount::class);
    }

    public function debtsReceivable(): HasMany
    {
        return $this->hasMany(DebtsReceivable::class);
    }

    public function debtsPayable(): HasMany
    {
        return $this->hasMany(DebtsPayable::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationSettings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Role / Status Helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // Helper methods
    public function totalIncome()
    {
        return $this->incomes()->sum('amount');
    }

    public function totalExpenses()
    {
        return $this->expenses()->sum('amount');
    }

    public function totalSavings()
    {
        return $this->savingsAccounts()->sum('current_balance');
    }

    public function totalReceivables()
    {
        return $this->debtsReceivable()
            ->whereIn('status', ['pending', 'partial'])
            ->sum(\DB::raw('original_amount - paid_amount'));
    }

    public function totalPayables()
    {
        return $this->debtsPayable()
            ->whereIn('status', ['pending', 'partial'])
            ->sum(\DB::raw('original_amount - paid_amount'));
    }

    public function netPosition()
    {
        return $this->totalIncome()
            - $this->totalExpenses()
            + $this->totalSavings()
            + $this->totalReceivables()
            - $this->totalPayables();
    }
}
