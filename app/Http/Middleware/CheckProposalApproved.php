<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Proposal;

class CheckProposalApproved
{
    /**
     * Handle an incoming request.
     * Check if mahasiswa has approved proposal before accessing attendance/daily log
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->role !== 'mahasiswa') {
            return $next($request);
        }

        $proposal = Proposal::where('user_id', $user->id)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$proposal || $proposal->status !== 'approved') {
            return redirect()->route('mahasiswa.proposals.index')
                ->with('error', 'Anda harus memiliki proposal yang disetujui untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
