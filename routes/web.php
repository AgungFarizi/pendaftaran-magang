<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InternshipPeriodController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/info-magang', [InternshipPeriodController::class, 'publicIndex'])->name('public.periods');

// ==================== AUTH ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // View proposal detail (all roles)
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');

    // ==================== MAHASISWA ROUTES ====================
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Proposals
        Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
        Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
        Route::post('/proposals/{proposal}/documents', [ProposalController::class, 'uploadDocuments'])->name('proposals.documents');

        // Attendance & Daily Logs (require approved proposal)
        Route::middleware(['proposal.approved'])->group(function () {
            Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
            Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

            Route::get('/daily-logs', [DailyLogController::class, 'index'])->name('daily-logs.index');
            Route::get('/daily-logs/create', [DailyLogController::class, 'create'])->name('daily-logs.create');
            Route::post('/daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store');
            Route::get('/daily-logs/{dailyLog}/edit', [DailyLogController::class, 'edit'])->name('daily-logs.edit');
            Route::put('/daily-logs/{dailyLog}', [DailyLogController::class, 'update'])->name('daily-logs.update');
        });
    });

    // ==================== OPERATOR ROUTES ====================
    Route::middleware(['role:operator'])->prefix('operator')->name('operator.')->group(function () {
        // Proposals
        Route::get('/proposals', [ProposalController::class, 'operatorIndex'])->name('proposals.index');
        Route::get('/proposals/{proposal}/review', [ProposalController::class, 'operatorReview'])->name('proposals.review');
        Route::post('/proposals/{proposal}/review', [ProposalController::class, 'operatorSubmitReview'])->name('proposals.submit-review');
        Route::post('/proposals/{proposal}/verify-documents', [ProposalController::class, 'verifyDocuments'])->name('proposals.verify-documents');

        // Reply Letters
        Route::get('/reply-letters', [ProposalController::class, 'replyLetterIndex'])->name('reply-letters.index');
        Route::post('/reply-letters/{proposal}', [ProposalController::class, 'sendReplyLetter'])->name('reply-letters.send');

        // Internship Periods
        Route::resource('periods', InternshipPeriodController::class);
        Route::post('/periods/{period}/toggle-status', [InternshipPeriodController::class, 'toggleStatus'])->name('periods.toggle-status');

        // Mahasiswa List
        Route::get('/mahasiswa', [UserManagementController::class, 'mahasiswaIndex'])->name('mahasiswa.index');
        Route::get('/mahasiswa/{user}', [UserManagementController::class, 'mahasiswaShow'])->name('mahasiswa.show');

        // Attendance & Daily Logs (view only)
        Route::get('/attendance', [AttendanceController::class, 'operatorIndex'])->name('attendance.index');
        Route::get('/daily-logs', [DailyLogController::class, 'operatorIndex'])->name('daily-logs.index');
    });

    // ==================== MANAGER ROUTES ====================
    Route::middleware(['role:manager,manager_dept'])->prefix('manager')->name('manager.')->group(function () {
        // Proposal Approvals
        Route::get('/proposals', [ProposalController::class, 'managerIndex'])->name('proposals.index');
        Route::get('/proposals/{proposal}/approval', [ProposalController::class, 'managerApproval'])->name('proposals.approval');
        Route::post('/proposals/{proposal}/approval', [ProposalController::class, 'managerSubmitApproval'])->name('proposals.submit-approval');

        // Pembimbing Management (Manager Dept)
        Route::get('/pembimbing', [UserManagementController::class, 'pembimbingIndex'])->name('pembimbing.index');
        Route::get('/pembimbing/create', [UserManagementController::class, 'pembimbingCreate'])->name('pembimbing.create');
        Route::post('/pembimbing', [UserManagementController::class, 'pembimbingStore'])->name('pembimbing.store');
        Route::get('/pembimbing/{user}/edit', [UserManagementController::class, 'pembimbingEdit'])->name('pembimbing.edit');
        Route::put('/pembimbing/{user}', [UserManagementController::class, 'pembimbingUpdate'])->name('pembimbing.update');
        Route::delete('/pembimbing/{user}', [UserManagementController::class, 'pembimbingDestroy'])->name('pembimbing.destroy');
    });

    // Operator Management (Manager only)
    Route::middleware(['role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/operators', [UserManagementController::class, 'operatorIndex'])->name('operators.index');
        Route::get('/operators/create', [UserManagementController::class, 'operatorCreate'])->name('operators.create');
        Route::post('/operators', [UserManagementController::class, 'operatorStore'])->name('operators.store');
        Route::get('/operators/{user}/edit', [UserManagementController::class, 'operatorEdit'])->name('operators.edit');
        Route::put('/operators/{user}', [UserManagementController::class, 'operatorUpdate'])->name('operators.update');
        Route::delete('/operators/{user}', [UserManagementController::class, 'operatorDestroy'])->name('operators.destroy');
    });

    // ==================== PEMBIMBING ROUTES ====================
    Route::middleware(['role:pembimbing'])->prefix('pembimbing')->name('pembimbing.')->group(function () {
        // Attendance Verification
        Route::get('/attendance', [AttendanceController::class, 'pembimbingIndex'])->name('attendance.index');
        Route::post('/attendance/{attendance}/verify', [AttendanceController::class, 'pembimbingVerify'])->name('attendance.verify');
        Route::post('/attendance/bulk-verify', [AttendanceController::class, 'pembimbingBulkVerify'])->name('attendance.bulk-verify');

        // Daily Log Verification
        Route::get('/daily-logs', [DailyLogController::class, 'pembimbingIndex'])->name('daily-logs.index');
        Route::get('/daily-logs/{dailyLog}', [DailyLogController::class, 'pembimbingShow'])->name('daily-logs.show');
        Route::post('/daily-logs/{dailyLog}/verify', [DailyLogController::class, 'pembimbingVerify'])->name('daily-logs.verify');
        Route::post('/daily-logs/bulk-verify', [DailyLogController::class, 'pembimbingBulkVerify'])->name('daily-logs.bulk-verify');
           Route::post('/daily-logs/bulk-verify1', [DailyLogController::class, 'pembimbingBulkVerify'])->name('daily-logs.bulk-verify');

        });
});
