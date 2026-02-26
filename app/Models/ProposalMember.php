<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'user_id',
        'name',
        'nim',
        'email',
        'university',
        'phone',
        'is_leader',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    /**
     * Proposal
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * User (if registered)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if member is leader
     */
    public function isLeader(): bool
    {
        return $this->is_leader;
    }

    /**
     * Scope for leaders
     */
    public function scopeLeaders($query)
    {
        return $query->where('is_leader', true);
    }
}
