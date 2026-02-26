<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_registration',
        'end_registration',
        'start_internship',
        'end_internship',
        'departments',
        'quota',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_registration' => 'datetime',
        'end_registration' => 'datetime',
        'start_internship' => 'datetime',
        'end_internship' => 'datetime',
        'departments' => 'array',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    // Cek apakah pendaftaran masih dibuka
    public function isRegistrationOpen(): bool
    {
        return $this->is_active &&
            now()->between($this->start_registration, $this->end_registration);
    }

    // Cek apakah magang sedang berlangsung
    public function isInternshipOngoing(): bool
    {
        return now()->between($this->start_internship, $this->end_internship);
    }

    // Sisa kuota
    public function getRemainingQuotaAttribute(): int
    {
        $approvedCount = $this->proposals()
            ->where('status', 'approved')
            ->withCount('members')
            ->get()
            ->sum('members_count');

        return max(0, $this->quota - $approvedCount);
    }

    // Jumlah proposal approved
    public function getApprovedCountAttribute(): int
    {
        return $this->proposals()
            ->where('status', 'approved')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // Period aktif (is_active = true)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Period dengan pendaftaran masih dibuka
    public function scopeRegistrationOpen($query)
    {
        return $query->where('is_active', true)
            ->where('start_registration', '<=', now())
            ->where('end_registration', '>=', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper
    |--------------------------------------------------------------------------
    */

    // Ambil period aktif terbaru
    public static function getCurrentActivePeriod()
    {
        return self::active()
            ->orderBy('start_registration', 'desc')
            ->first();
    }
}