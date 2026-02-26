<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'nim',
        'university',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is manager departement
     */
    public function isManagerDept(): bool
    {
        return $this->role === 'manager_dept';
    }

    /**
     * Check if user is operator
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Check if user is pembimbing
     */
    public function isPembimbing(): bool
    {
        return $this->role === 'pembimbing';
    }

    /**
     * Check if user is mahasiswa
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        return in_array($this->role, $roles);
    }

    /**
     * Get proposals submitted by user
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Get proposal memberships
     */
    public function proposalMemberships()
    {
        return $this->hasMany(ProposalMember::class);
    }

    /**
     * Get attendance logs
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Get daily activities
     */
    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class);
    }

    /**
     * Get system notifications
     */
    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class);
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->systemNotifications()->where('is_read', false)->count();
    }

    /**
     * Check if mahasiswa has approved proposal
     */
    public function hasApprovedProposal(): bool
    {
        if (!$this->isMahasiswa()) {
            return true;
        }

        // Check from proposals (as submitter)
        $hasApprovedAsSubmitter = $this->proposals()->where('status', 'approved')->exists();
        
        // Check from proposal members
        $hasApprovedAsMember = $this->proposalMemberships()
            ->whereHas('proposal', function ($query) {
                $query->where('status', 'approved');
            })->exists();

        return $hasApprovedAsSubmitter || $hasApprovedAsMember;
    }

    /**
     * Get the approved proposal for mahasiswa
     */
    public function getApprovedProposal()
    {
        // First check as submitter
        $proposal = $this->proposals()->where('status', 'approved')->first();
        
        if ($proposal) {
            return $proposal;
        }

        // Then check as member
        $membership = $this->proposalMemberships()
            ->whereHas('proposal', function ($query) {
                $query->where('status', 'approved');
            })->first();

        return $membership ? $membership->proposal : null;
    }
}
