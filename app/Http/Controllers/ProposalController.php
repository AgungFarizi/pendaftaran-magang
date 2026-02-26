<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalMember;
use App\Models\User;
use App\Models\Notification;
use App\Models\InternshipPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    // ==================== MAHASISWA ====================
    
    public function index()
    {
        $user = Auth::user();
        $proposal = Proposal::where('user_id', $user->id)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['members.user', 'period'])
            ->first();

        $activePeriods = InternshipPeriod::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('mahasiswa.proposals.index', compact('proposal', 'activePeriods'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has proposal
        $existingProposal = Proposal::where('user_id', $user->id)->first();
        if ($existingProposal) {
            return redirect()->route('mahasiswa.proposals.index')
                ->with('error', 'Anda sudah memiliki proposal yang diajukan.');
        }

        $activePeriods = InternshipPeriod::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        if ($activePeriods->isEmpty()) {
            return redirect()->route('mahasiswa.proposals.index')
                ->with('error', 'Tidak ada periode magang yang sedang dibuka.');
        }

        $departments = [
            'IT' => 'Information Technology',
            'HR' => 'Human Resources',
            'Finance' => 'Finance & Accounting',
            'Marketing' => 'Marketing',
            'Operations' => 'Operations',
            'RnD' => 'Research & Development',
        ];

        return view('mahasiswa.proposals.create', compact('activePeriods', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'period_id' => 'required|exists:internship_periods,id',
            'department' => 'required|string|max:100',
            'description' => 'required|string',
            'proposal_file' => 'required|file|mimes:pdf|max:10240',
            'members' => 'nullable|array',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.email' => 'required_with:members|email|max:255',
            'members.*.student_id' => 'required_with:members|string|max:50',
            'members.*.university' => 'required_with:members|string|max:255',
            'members.*.major' => 'required_with:members|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Upload file
            $filePath = $request->file('proposal_file')->store('proposals', 'public');

            // Create proposal
            $proposal = Proposal::create([
                'user_id' => Auth::id(),
                'period_id' => $request->period_id,
                'title' => $request->title,
                'department' => $request->department,
                'description' => $request->description,
                'proposal_file' => $filePath,
                'status' => 'pending',
            ]);

            // Add members if any
            if ($request->has('members')) {
                foreach ($request->members as $memberData) {
                    // Check if member user exists
                    $memberUser = User::where('email', $memberData['email'])->first();
                    
                    ProposalMember::create([
                        'proposal_id' => $proposal->id,
                        'user_id' => $memberUser ? $memberUser->id : null,
                        'name' => $memberData['name'],
                        'email' => $memberData['email'],
                        'student_id' => $memberData['student_id'],
                        'university' => $memberData['university'],
                        'major' => $memberData['major'],
                    ]);
                }
            }

            // Notify operators
            $operators = User::where('role', 'operator')->get();
            foreach ($operators as $operator) {
                Notification::create([
                    'user_id' => $operator->id,
                    'title' => 'Proposal Baru',
                    'message' => 'Ada proposal baru dari ' . Auth::user()->name . ' yang perlu direview.',
                    'type' => 'proposal',
                    'reference_id' => $proposal->id,
                ]);
            }

            DB::commit();
            return redirect()->route('mahasiswa.proposals.index')
                ->with('success', 'Proposal berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Proposal $proposal)
    {
        $user = Auth::user();
        
        // Check access
        if ($user->role === 'mahasiswa') {
            if ($proposal->user_id !== $user->id && 
                !$proposal->members()->where('user_id', $user->id)->exists()) {
                abort(403);
            }
        }

        $proposal->load(['user', 'members.user', 'period', 'pembimbing']);
        return view('proposals.show', compact('proposal'));
    }

    public function uploadDocuments(Request $request, Proposal $proposal)
    {
        $request->validate([
            'documents' => 'required|file|mimes:pdf|max:10240',
        ]);

        $filePath = $request->file('documents')->store('documents', 'public');
        
        $proposal->update([
            'documents_file' => $filePath,
            'documents_status' => 'pending',
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah!');
    }

    // ==================== OPERATOR ====================

    public function operatorIndex(Request $request)
    {
        $query = Proposal::with(['user', 'period']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $proposals = $query->latest()->paginate(10);
        
        return view('operator.proposals.index', compact('proposals'));
    }

    public function operatorReview(Proposal $proposal)
    {
        $proposal->load(['user', 'members.user', 'period']);
        return view('operator.proposals.review', compact('proposal'));
    }

    public function operatorSubmitReview(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action' => 'required|in:forward,reject',
            'operator_notes' => 'nullable|string|max:1000',
        ]);

        if ($request->action === 'forward') {
            $proposal->update([
                'status' => 'reviewed',
                'operator_notes' => $request->operator_notes,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

            // Notify managers
            $managers = User::whereIn('role', ['manager', 'manager_dept'])
                ->where(function($q) use ($proposal) {
                    $q->where('role', 'manager')
                      ->orWhere(function($q2) use ($proposal) {
                          $q2->where('role', 'manager_dept')
                            ->where('department', $proposal->department);
                      });
                })
                ->get();

            foreach ($managers as $manager) {
                Notification::create([
                    'user_id' => $manager->id,
                    'title' => 'Proposal Perlu Approval',
                    'message' => 'Proposal dari ' . $proposal->user->name . ' telah direview operator dan memerlukan approval Anda.',
                    'type' => 'approval',
                    'reference_id' => $proposal->id,
                ]);
            }

            return redirect()->route('operator.proposals.index')
                ->with('success', 'Proposal berhasil diteruskan ke Manager!');
        } else {
            $proposal->update([
                'status' => 'rejected',
                'operator_notes' => $request->operator_notes,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

            // Notify mahasiswa
            Notification::create([
                'user_id' => $proposal->user_id,
                'title' => 'Proposal Ditolak',
                'message' => 'Maaf, proposal Anda ditolak. Alasan: ' . $request->operator_notes,
                'type' => 'proposal',
                'reference_id' => $proposal->id,
            ]);

            return redirect()->route('operator.proposals.index')
                ->with('success', 'Proposal ditolak.');
        }
    }

    public function verifyDocuments(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $proposal->update([
            'documents_status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'documents_notes' => $request->notes,
        ]);

        Notification::create([
            'user_id' => $proposal->user_id,
            'title' => $request->action === 'approve' ? 'Dokumen Diverifikasi' : 'Dokumen Ditolak',
            'message' => $request->action === 'approve' 
                ? 'Dokumen Anda telah diverifikasi dan dinyatakan lengkap.'
                : 'Dokumen Anda ditolak. ' . $request->notes,
            'type' => 'document',
            'reference_id' => $proposal->id,
        ]);

        return back()->with('success', 'Status dokumen berhasil diperbarui!');
    }

    // ==================== MANAGER ====================

    public function managerIndex(Request $request)
    {
        $user = Auth::user();
        $query = Proposal::where('status', 'reviewed')
            ->with(['user', 'period']);

        if ($user->role === 'manager_dept') {
            $query->where('department', $user->department);
        }

        if ($request->status === 'pending') {
            if ($user->role === 'manager') {
                $query->whereNull('manager_approval');
            } else {
                $query->whereNull('manager_dept_approval');
            }
        }

        $proposals = $query->latest()->paginate(10);
        
        return view('manager.proposals.index', compact('proposals'));
    }

    public function managerApproval(Proposal $proposal)
    {
        $proposal->load(['user', 'members.user', 'period']);
        return view('manager.proposals.approval', compact('proposal'));
    }

    public function managerSubmitApproval(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $updateData = [];

        if ($user->role === 'manager') {
            $updateData['manager_approval'] = $request->action === 'approve' ? 'approved' : 'rejected';
            $updateData['manager_notes'] = $request->notes;
            $updateData['manager_approved_at'] = now();
            $updateData['manager_approved_by'] = $user->id;
        } else {
            $updateData['manager_dept_approval'] = $request->action === 'approve' ? 'approved' : 'rejected';
            $updateData['manager_dept_notes'] = $request->notes;
            $updateData['manager_dept_approved_at'] = now();
            $updateData['manager_dept_approved_by'] = $user->id;
        }

        $proposal->update($updateData);

        // Check if both managers approved
        $proposal->refresh();
        if ($proposal->manager_approval === 'approved' && $proposal->manager_dept_approval === 'approved') {
            $proposal->update(['status' => 'approved']);

            // Notify operator to send reply letter
            $operators = User::where('role', 'operator')->get();
            foreach ($operators as $operator) {
                Notification::create([
                    'user_id' => $operator->id,
                    'title' => 'Proposal Diterima - Kirim Surat Balasan',
                    'message' => 'Proposal dari ' . $proposal->user->name . ' telah disetujui. Silakan kirim surat balasan.',
                    'type' => 'reply_letter',
                    'reference_id' => $proposal->id,
                ]);
            }
        } elseif ($proposal->manager_approval === 'rejected' || $proposal->manager_dept_approval === 'rejected') {
            $proposal->update(['status' => 'rejected']);

            // Notify mahasiswa
            Notification::create([
                'user_id' => $proposal->user_id,
                'title' => 'Proposal Ditolak',
                'message' => 'Maaf, proposal Anda ditolak oleh ' . ($user->role === 'manager' ? 'Manager' : 'Manager Departemen') . '.',
                'type' => 'proposal',
                'reference_id' => $proposal->id,
            ]);
        }

        return redirect()->route('manager.proposals.index')
            ->with('success', 'Keputusan berhasil disimpan!');
    }

    // ==================== REPLY LETTER ====================

    public function replyLetterIndex()
    {
        $proposals = Proposal::where('status', 'approved')
            ->with(['user', 'period'])
            ->latest()
            ->paginate(10);

        return view('operator.reply-letters.index', compact('proposals'));
    }

    public function sendReplyLetter(Request $request, Proposal $proposal)
    {
        $request->validate([
            'reply_letter' => 'required|file|mimes:pdf|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pembimbing_id' => 'required|exists:users,id',
        ]);

        $filePath = $request->file('reply_letter')->store('reply-letters', 'public');

        $proposal->update([
            'reply_letter_file' => $filePath,
            'reply_letter_sent_at' => now(),
            'internship_start_date' => $request->start_date,
            'internship_end_date' => $request->end_date,
            'pembimbing_id' => $request->pembimbing_id,
        ]);

        // Notify mahasiswa
        Notification::create([
            'user_id' => $proposal->user_id,
            'title' => 'Selamat! Anda Diterima Magang',
            'message' => 'Proposal magang Anda telah disetujui. Silakan unduh surat balasan dan persiapkan dokumen yang diperlukan.',
            'type' => 'acceptance',
            'reference_id' => $proposal->id,
        ]);

        // Notify all members
        foreach ($proposal->members as $member) {
            if ($member->user_id) {
                Notification::create([
                    'user_id' => $member->user_id,
                    'title' => 'Selamat! Tim Anda Diterima Magang',
                    'message' => 'Proposal magang tim Anda telah disetujui. Silakan hubungi ketua tim untuk informasi lebih lanjut.',
                    'type' => 'acceptance',
                    'reference_id' => $proposal->id,
                ]);
            }
        }

        return redirect()->route('operator.reply-letters.index')
            ->with('success', 'Surat balasan berhasil dikirim!');
    }
}
