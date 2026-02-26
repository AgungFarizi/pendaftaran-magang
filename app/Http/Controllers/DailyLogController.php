<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\Proposal;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyLogController extends Controller
{
    // ==================== MAHASISWA ====================

    public function index()
    {
        $user = Auth::user();
        
        // Check if user has approved proposal
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return view('mahasiswa.daily-logs.not-available');
        }

        $dailyLogs = DailyLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $stats = [
            'total' => DailyLog::where('user_id', $user->id)->count(),
            'approved' => DailyLog::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending' => DailyLog::where('user_id', $user->id)->where('status', 'pending')->count(),
            'rejected' => DailyLog::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('mahasiswa.daily-logs.index', compact('dailyLogs', 'stats', 'proposal'));
    }

    public function create()
    {
        $user = Auth::user();
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return redirect()->route('mahasiswa.daily-logs.index');
        }

        // Check if already submitted today
        $todayLog = DailyLog::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        return view('mahasiswa.daily-logs.create', compact('todayLog', 'proposal'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $proposal = $this->getUserProposal($user);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return redirect()->route('mahasiswa.daily-logs.index')
                ->with('error', 'Anda belum memiliki proposal yang disetujui.');
        }

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'activities' => 'required|string|min:50',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Check if already exists for this date
        $existingLog = DailyLog::where('user_id', $user->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingLog) {
            return back()->with('error', 'Anda sudah mengisi log untuk tanggal tersebut.')
                ->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('daily-logs', 'public');
        }

        DailyLog::create([
            'user_id' => $user->id,
            'proposal_id' => $proposal->id,
            'date' => $request->date,
            'activities' => $request->activities,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.daily-logs.index')
            ->with('success', 'Log harian berhasil disimpan!');
    }

    public function edit(DailyLog $dailyLog)
    {
        $user = Auth::user();
        
        if ($dailyLog->user_id !== $user->id) {
            abort(403);
        }

        if ($dailyLog->status !== 'pending') {
            return redirect()->route('mahasiswa.daily-logs.index')
                ->with('error', 'Log yang sudah diverifikasi tidak dapat diedit.');
        }

        return view('mahasiswa.daily-logs.edit', compact('dailyLog'));
    }

    public function update(Request $request, DailyLog $dailyLog)
    {
        $user = Auth::user();
        
        if ($dailyLog->user_id !== $user->id) {
            abort(403);
        }

        if ($dailyLog->status !== 'pending') {
            return redirect()->route('mahasiswa.daily-logs.index')
                ->with('error', 'Log yang sudah diverifikasi tidak dapat diedit.');
        }

        $request->validate([
            'activities' => 'required|string|min:50',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $updateData = [
            'activities' => $request->activities,
        ];

        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($dailyLog->attachment) {
                Storage::disk('public')->delete($dailyLog->attachment);
            }
            $updateData['attachment'] = $request->file('attachment')->store('daily-logs', 'public');
        }

        $dailyLog->update($updateData);

        return redirect()->route('mahasiswa.daily-logs.index')
            ->with('success', 'Log harian berhasil diperbarui!');
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

        $query = DailyLog::whereIn('user_id', $allStudentIds)
            ->with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->student_id) {
            $query->where('user_id', $request->student_id);
        }

        $dailyLogs = $query->orderBy('date', 'desc')->paginate(15);

        $stats = [
            'total' => DailyLog::whereIn('user_id', $allStudentIds)->count(),
            'pending' => DailyLog::whereIn('user_id', $allStudentIds)->where('status', 'pending')->count(),
            'approved' => DailyLog::whereIn('user_id', $allStudentIds)->where('status', 'approved')->count(),
        ];

        $students = User::whereIn('id', $allStudentIds)->get();

        return view('pembimbing.daily-logs.index', compact('dailyLogs', 'stats', 'students'));
    }

    public function pembimbingShow(DailyLog $dailyLog)
    {
        $dailyLog->load('user');
        return view('pembimbing.daily-logs.show', compact('dailyLog'));
    }

    public function pembimbingVerify(Request $request, DailyLog $dailyLog)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'nullable|string|max:500',
        ]);

        $dailyLog->update([
            'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'feedback' => $request->feedback,
        ]);

        // Notify mahasiswa
        Notification::create([
            'user_id' => $dailyLog->user_id,
            'title' => $request->action === 'approve' ? 'Log Harian Diverifikasi' : 'Log Harian Ditolak',
            'message' => $request->action === 'approve'
                ? 'Log harian Anda tanggal ' . $dailyLog->date->format('d/m/Y') . ' telah diverifikasi.'
                : 'Log harian Anda tanggal ' . $dailyLog->date->format('d/m/Y') . ' ditolak. ' . $request->feedback,
            'type' => 'daily_log',
            'reference_id' => $dailyLog->id,
        ]);

        return back()->with('success', 'Status log harian berhasil diperbarui!');
    }

    public function pembimbingBulkVerify(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array',
            'log_ids.*' => 'exists:daily_logs,id',
            'action' => 'required|in:approve,reject',
        ]);

        $logs = DailyLog::whereIn('id', $request->log_ids)->get();

        foreach ($logs as $log) {
            $log->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            Notification::create([
                'user_id' => $log->user_id,
                'title' => $request->action === 'approve' ? 'Log Harian Diverifikasi' : 'Log Harian Ditolak',
                'message' => 'Log harian Anda tanggal ' . $log->date->format('d/m/Y') . ' telah ' . ($request->action === 'approve' ? 'diverifikasi' : 'ditolak') . '.',
                'type' => 'daily_log',
                'reference_id' => $log->id,
            ]);
        }

        return back()->with('success', count($logs) . ' log harian berhasil diperbarui!');
    }

    // ==================== OPERATOR ====================

    public function operatorIndex(Request $request)
    {
        $query = DailyLog::with(['user', 'proposal']);

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

        $dailyLogs = $query->orderBy('date', 'desc')->paginate(20);

        return view('operator.daily-logs.index', compact('dailyLogs'));
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
