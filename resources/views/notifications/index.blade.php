@extends('layouts.app')

@section('title', 'Notifikasi - Tellinter')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-bell"></i> Notifikasi</h1>
            <p>Kelola notifikasi dan informasi penting Anda</p>
        </div>
        @if($unreadNotifications->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @if($notifications->count() > 0)
            <div class="card">
                <div class="card-body p-0">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ !$notification->dibaca ? 'unread' : '' }} p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if($notification->tipe === 'proposal')
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            <strong class="text-dark">Notifikasi Proposal</strong>
                                        @elseif($notification->tipe === 'attendance')
                                            <i class="fas fa-calendar-check text-success"></i>
                                            <strong class="text-dark">Notifikasi Absensi</strong>
                                        @elseif($notification->tipe === 'daily_log')
                                            <i class="fas fa-book text-info"></i>
                                            <strong class="text-dark">Notifikasi Log Harian</strong>
                                        @else
                                            <i class="fas fa-info-circle text-warning"></i>
                                            <strong class="text-dark">Informasi Sistem</strong>
                                        @endif
                                    </div>

                                    <p class="text-dark mb-1">{{ $notification->pesan }}</p>

                                    <small class="text-muted">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <div class="ms-3">
                                    @if(!$notification->dibaca)
                                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="badge bg-primary" style="border:none; cursor:pointer;">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>Tidak Ada Notifikasi</h5>
                <p>Anda tidak memiliki notifikasi baru. Semua notifikasi akan muncul di sini.</p>
            </div>
        @endif
    </div>

    <!-- Filter & Stats Sidebar -->
    <div class="col-md-4">
        <!-- Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Statistik Notifikasi</h5>
            </div>
            <div class="card-body">
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Notifikasi</span>
                        <strong>{{ $allNotificationsCount }}</strong>
                    </div>
                </div>
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Belum Dibaca</span>
                        <strong class="text-warning">{{ $unreadNotifications->count() }}</strong>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="d-flex justify-content-between">
                        <span>Sudah Dibaca</span>
                        <strong class="text-success">{{ $allNotificationsCount - $unreadNotifications->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter by Type -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Filter Notifikasi</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list"></i> Semua
                </a>
                <a href="{{ route('notifications.index', ['type' => 'proposal']) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-file-pdf"></i> Proposal
                </a>
                <a href="{{ route('notifications.index', ['type' => 'attendance']) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-calendar-check"></i> Absensi
                </a>
                <a href="{{ route('notifications.index', ['type' => 'daily_log']) }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-book"></i> Log Harian
                </a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card">
            <div class="card-header">
                <h5>Informasi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <small><i class="fas fa-info-circle"></i> Notifikasi akan dikirim otomatis ketika ada update status proposal, absensi, atau log harian Anda.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-item {
        transition: all 0.3s ease;
        background-color: #f9fafb;
    }

    .notification-item.unread {
        background-color: #eff6ff;
        border-left: 3px solid #2563eb;
    }

    .notification-item:hover {
        background-color: #f3f4f6;
    }

    .notification-item.unread:hover {
        background-color: #dbeafe;
    }

    .stat-item {
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
</style>
@endsection
