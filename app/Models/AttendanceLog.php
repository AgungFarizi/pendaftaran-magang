<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'proposal_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'location',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Status labels
     */
    public static function statusLabels(): array
    {
        return [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Absen',
            'permission' => 'Izin',
            'sick' => 'Sakit',
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'present' => 'green',
            'late' => 'yellow',
            'absent' => 'red',
            'permission' => 'blue',
            'sick' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Proposal
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Verifier
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Verify attendance
     */
    public function verify(User $verifier): void
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);
    }

    /**
     * Scope for verified
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }
}
