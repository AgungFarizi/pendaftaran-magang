<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        // Redirect based on notification type
        return $this->redirectToReference($notification);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    private function redirectToReference(Notification $notification)
    {
        $user = Auth::user();

        switch ($notification->type) {
            case 'proposal':
            case 'approval':
            case 'reply_letter':
            case 'acceptance':
                if ($user->role === 'mahasiswa') {
                    return redirect()->route('mahasiswa.proposals.index');
                } elseif ($user->role === 'operator') {
                    return redirect()->route('operator.proposals.index');
                } elseif (in_array($user->role, ['manager', 'manager_dept'])) {
                    return redirect()->route('manager.proposals.index');
                }
                break;

            case 'attendance':
                if ($user->role === 'mahasiswa') {
                    return redirect()->route('mahasiswa.attendance.index');
                } elseif ($user->role === 'pembimbing') {
                    return redirect()->route('pembimbing.attendance.index');
                }
                break;

            case 'daily_log':
                if ($user->role === 'mahasiswa') {
                    return redirect()->route('mahasiswa.daily-logs.index');
                } elseif ($user->role === 'pembimbing') {
                    return redirect()->route('pembimbing.daily-logs.index');
                }
                break;

            case 'document':
                if ($user->role === 'mahasiswa') {
                    return redirect()->route('mahasiswa.proposals.index');
                }
                break;
        }

        return redirect()->route('notifications.index');
    }
}
