<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'name',
        'domain',
        'duration',
        'expiration_date',
        'is_active',
        'last_checked_at',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Get remaining days until expiration
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->expiration_date) {
            return 0;
        }
        
        $now = Carbon::now();
        $expiration = Carbon::parse($this->expiration_date);
        
        return $expiration->diffInDays($now, false);
    }

    /**
     * Check if license is expired
     */
    public function isExpired()
    {
        return $this->expiration_date < Carbon::now();
    }

    /**
     * Scope for active licenses only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
