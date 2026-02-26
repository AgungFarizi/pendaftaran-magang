<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'internship_period_id',
        'title',
        'description',
        'department',
        'start_date',
        'end_date',
        'proposal_file',
        'supporting_documents',
        'status',
        'operator_notes',
        'manager_approval',
        'manager_approved_at',
        'manager_dept_approval',
        'manager_dept_approved_at',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'manager_approval' => 'boolean',
        'manager_dept_approval' => 'boolean',
        'manager_approved_at' => 'datetime',
        'manager_dept_approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * Status labels
     */
    public static function statusLabels(): array
    {
        return [
            'pending' => 'Menunggu Review',
            'reviewed' => 'Sedang Direview',
            'forwarded' => 'Diteruskan ke Manager',
            'approved' => 'Diterima',
            'rejected' => 'Ditolak',
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
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'forwarded' => 'indigo',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * User who submitted the proposal
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - submitter
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Internship period
     */
    public function internshipPeriod()
    {
        return $this->belongsTo(InternshipPeriod::class);
    }

    /**
     * Proposal members
     */
    public function members()
    {
        return $this->hasMany(ProposalMember::class);
    }

    /**
     * Get leader member
     */
    public function leader()
    {
        return $this->members()->where('is_leader', true)->first();
    }

    /**
     * Reviewer (operator)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Acceptance letter
     */
    public function acceptanceLetter()
    {
        return $this->hasOne(AcceptanceLetter::class);
    }

    /**
     * Attendance logs
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Daily activities
     */
    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class);
    }

    /**
     * Check if proposal is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if proposal is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if proposal is forwarded
     */
    public function isForwarded(): bool
    {
        return $this->status === 'forwarded';
    }

    /**
     * Check if proposal is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if both managers have approved
     */
    public function isBothApproved(): bool
    {
        return $this->manager_approval && $this->manager_dept_approval;
    }

    /**
     * Forward proposal to managers
     */
    public function forward(User $operator, ?string $notes = null): void
    {
        $this->update([
            'status' => 'forwarded',
            'reviewed_by' => $operator->id,
            'reviewed_at' => now(),
            'operator_notes' => $notes,
        ]);
    }

    /**
     * Reject proposal
     */
    public function reject(User $user, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'operator_notes' => $notes,
        ]);
    }

    /**
     * Approve by manager
     */
    public function approveByManager(): void
    {
        $this->update([
            'manager_approval' => true,
            'manager_approved_at' => now(),
        ]);

        $this->checkAndSetApproved();
    }

    /**
     * Approve by manager department
     */
    public function approveByManagerDept(): void
    {
        $this->update([
            'manager_dept_approval' => true,
            'manager_dept_approved_at' => now(),
        ]);

        $this->checkAndSetApproved();
    }

    /**
     * Check if both approved and set status
     */
    private function checkAndSetApproved(): void
    {
        if ($this->isBothApproved()) {
            $this->update(['status' => 'approved']);
        }
    }

    /**
     * Scope for pending proposals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for forwarded proposals
     */
    public function scopeForwarded($query)
    {
        return $query->where('status', 'forwarded');
    }

    /**
     * Scope for approved proposals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for specific department
     */
    public function scopeForDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }
}
