<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Proposal;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ==================== MAHASISWA ====================

    public function index()
    {
        $user = Auth::user();
        
        // Check if user has approved proposal
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return view('mahasiswa.attendance.not-available');
        }

        $attendances = AttendanceLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $stats = [
            'total' => AttendanceLog::where('user_id', $user->id)->count(),
            'approved' => AttendanceLog::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending' => AttendanceLog::where('user_id', $user->id)->where('status', 'pending')->count(),
            'rejected' => AttendanceLog::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('mahasiswa.attendance.index', compact('attendances', 'stats', 'proposal'));
    }

    public function create()
    {
        $user = Auth::user();
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return redirect()->route('mahasiswa.attendance.index');
        }

        // Check if already checked in today
        $todayAttendance = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        return view('mahasiswa.attendance.create', compact('todayAttendance', 'proposal'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return redirect()->route('mahasiswa.attendance.index')
                ->with('error', 'Anda belum memiliki proposal yang disetujui.');
        }

        $request->validate([
            'type' => 'required|in:check_in,check_out',
        ]);

        $today = today();
        $attendance = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($request->type === 'check_in') {
            if ($attendance) {
                return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
            }

            AttendanceLog::create([
                'user_id' => $user->id,
                'proposal_id' => $proposal->id,
                'date' => $today,
                'check_in' => now(),
                'status' => 'pending',
            ]);

            return redirect()->route('mahasiswa.attendance.index')
                ->with('success', 'Check-in berhasil!');
        } else {
            if (!$attendance) {
                return back()->with('error', 'Anda belum melakukan check-in hari ini.');
            }

            if ($attendance->check_out) {
                return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
            }

            $attendance->update([
                'check_out' => now(),
            ]);

            return redirect()->route('mahasiswa.attendance.index')
                ->with('success', 'Check-out berhasil!');
        }
    }

    // ==================== PEMBIMBING ====================

    public function pembimbingIndex(Request $request)
    {
        $user = Auth::user();
        
        // Get students assigned to this pembimbing
        $studentIds = Proposal::where('pembimbing_id', $user->id)
            ->where('status', 'approved')
            ->pluck('user_id');

        // Also include proposal members
        $memberIds = Proposal::where('pembimbing_id', $user->id)
            ->where('status', 'approved')
            ->with('members')
            ->get()
            ->pluck('members')
            ->flatten()
            ->pluck('user_id')
            ->filter();

        $allStudentIds = $studentIds->merge($memberIds)->unique();

        $query = AttendanceLog::whereIn('user_id', $allStudentIds)
            ->with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);

        $stats = [
            'total' => AttendanceLog::whereIn('user_id', $allStudentIds)->count(),
            'pending' => AttendanceLog::whereIn('user_id', $allStudentIds)->where('status', 'pending')->count(),
            'approved' => AttendanceLog::whereIn('user_id', $allStudentIds)->where('status', 'approved')->count(),
        ];

        $students = User::whereIn('id', $allStudentIds)->get();

        return view('pembimbing.attendance.index', compact('attendances', 'stats', 'students'));
    }

    public function pembimbingVerify(Request $request, AttendanceLog $attendance)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $request->notes,
        ]);

        // Notify mahasiswa
        Notification::create([
            'user_id' => $attendance->user_id,
            'title' => $request->action === 'approve' ? 'Kehadiran Diverifikasi' : 'Kehadiran Ditolak',
            'message' => $request->action === 'approve'
                ? 'Kehadiran Anda tanggal ' . $attendance->date->format('d/m/Y') . ' telah diverifikasi.'
                : 'Kehadiran Anda tanggal ' . $attendance->date->format('d/m/Y') . ' ditolak. ' . $request->notes,
            'type' => 'attendance',
            'reference_id' => $attendance->id,
        ]);

        return back()->with('success', 'Status kehadiran berhasil diperbarui!');
    }

    public function pembimbingBulkVerify(Request $request)
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendance_logs,id',
            'action' => 'required|in:approve,reject',
        ]);

        $attendances = AttendanceLog::whereIn('id', $request->attendance_ids)->get();

        foreach ($attendances as $attendance) {
            $attendance->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            Notification::create([
                'user_id' => $attendance->user_id,
                'title' => $request->action === 'approve' ? 'Kehadiran Diverifikasi' : 'Kehadiran Ditolak',
                'message' => 'Kehadiran Anda tanggal ' . $attendance->date->format('d/m/Y') . ' telah ' . ($request->action === 'approve' ? 'diverifikasi' : 'ditolak') . '.',
                'type' => 'attendance',
                'reference_id' => $attendance->id,
            ]);
        }

        return back()->with('success', count($attendances) . ' kehadiran berhasil diperbarui!');
    }

    // ==================== OPERATOR ====================

    public function operatorIndex(Request $request)
    {
        $query = AttendanceLog::with(['user', 'proposal']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        $stats = [
            'today_total' => AttendanceLog::whereDate('date', today())->count(),
            'today_present' => AttendanceLog::whereDate('date', today())->whereNotNull('check_in')->count(),
            'pending_verification' => AttendanceLog::where('status', 'pending')->count(),
        ];

        return view('operator.attendance.index', compact('attendances', 'stats'));
    }

    // ==================== HELPER ====================

    private function getUserProposal($user)
    {
        return Proposal::where('user_id', $user->id)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();
    }
}
