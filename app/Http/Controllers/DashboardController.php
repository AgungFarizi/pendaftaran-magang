<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proposal;
use App\Models\AttendanceLog;
use App\Models\DailyLog;
use App\Models\InternshipPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        switch ($user->role) {
            case 'manager':
                $data = $this->getManagerDashboard();
                break;

            case 'manager_dept':
                $data = $this->getManagerDeptDashboard();
                break;

            case 'operator':
                $data = $this->getOperatorDashboard();
                break;

            case 'pembimbing':
                $data = $this->getPembimbingDashboard();
                break;

            case 'mahasiswa':
                $data = $this->getMahasiswaDashboard();
                break;
        }

        return view('dashboard.index', compact('user', 'data'));
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGER DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function getManagerDashboard()
    {
        return [
            'pending_approvals' => Proposal::where('status', 'reviewed')
                ->whereNull('manager_approval')
                ->count(),

            'approved_proposals' => Proposal::where('manager_approval', 'approved')->count(),

            'rejected_proposals' => Proposal::where('manager_approval', 'rejected')->count(),

            'total_operators' => User::where('role', 'operator')->count(),

            'recent_proposals' => Proposal::where('status', 'reviewed')
                ->whereNull('manager_approval')
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGER DEPARTMENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function getManagerDeptDashboard()
    {
        $user = Auth::user();

        return [
            'pending_approvals' => Proposal::where('status', 'reviewed')
                ->where('department', $user->department)
                ->whereNull('manager_dept_approval')
                ->count(),

            'approved_proposals' => Proposal::where('department', $user->department)
                ->where('manager_dept_approval', 'approved')
                ->count(),

            'total_pembimbing' => User::where('role', 'pembimbing')
                ->where('department', $user->department)
                ->count(),

            'active_interns' => User::where('role', 'mahasiswa')
                ->whereHas('proposals', function ($q) use ($user) {
                    $q->where('status', 'approved')
                        ->where('department', $user->department);
                })
                ->count(),

            'recent_proposals' => Proposal::where('status', 'reviewed')
                ->where('department', $user->department)
                ->whereNull('manager_dept_approval')
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OPERATOR DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function getOperatorDashboard()
    {
        return [
            'pending_proposals' => Proposal::where('status', 'pending')->count(),

            'reviewed_proposals' => Proposal::where('status', 'reviewed')->count(),

            'approved_proposals' => Proposal::where('status', 'approved')->count(),

            'rejected_proposals' => Proposal::where('status', 'rejected')->count(),

            'pending_reply_letters' => Proposal::where('status', 'approved')
                ->whereNull('reply_letter_sent_at')
                ->count(),

            'recent_proposals' => Proposal::with('user')
                ->latest()
                ->take(10)
                ->get(),

            // ✅ FIXED
            'active_periods' => InternshipPeriod::registrationOpen()->get(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function getPembimbingDashboard()
    {
        $user = Auth::user();

        $assignedStudents = User::whereHas('proposals', function ($q) use ($user) {
            $q->where('status', 'approved')
                ->where('pembimbing_id', $user->id);
        })->get();

        return [
            'total_students' => $assignedStudents->count(),

            'pending_attendance' => AttendanceLog::whereIn('user_id', $assignedStudents->pluck('id'))
                ->where('status', 'pending')
                ->count(),

            'pending_daily_logs' => DailyLog::whereIn('user_id', $assignedStudents->pluck('id'))
                ->where('status', 'pending')
                ->count(),

            'students' => $assignedStudents,

            'recent_attendance' => AttendanceLog::whereIn('user_id', $assignedStudents->pluck('id'))
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),

            'recent_daily_logs' => DailyLog::whereIn('user_id', $assignedStudents->pluck('id'))
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function getMahasiswaDashboard()
    {
        $user = Auth::user();

        $proposal = Proposal::where('user_id', $user->id)
            ->orWhereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        $isApproved = $proposal && $proposal->status === 'approved';

        $data = [
            'proposal' => $proposal,
            'is_approved' => $isApproved,

            // ✅ FIXED TOTAL (tidak pakai status & end_date lagi)
            'active_periods' => InternshipPeriod::registrationOpen()->get(),
        ];

        if ($isApproved) {
            $data['attendance_count'] = AttendanceLog::where('user_id', $user->id)->count();

            $data['approved_attendance'] = AttendanceLog::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();

            $data['daily_log_count'] = DailyLog::where('user_id', $user->id)->count();

            $data['recent_attendance'] = AttendanceLog::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $data['recent_daily_logs'] = DailyLog::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return $data;
    }
}