<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeDoc extends Model
{
    protected $fillable = [
        'type',
        'token',
        'document_data',
        'expires_at',
    ];

    protected $casts = [
        'document_data' => 'array',
        'expires_at'    => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
