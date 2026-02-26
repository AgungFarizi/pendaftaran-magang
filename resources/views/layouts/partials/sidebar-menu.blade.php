@php
    $user = auth()->user();
    $role = $user->role;
    
    // Check if mahasiswa has approved proposal
    $hasApprovedProposal = false;
    if ($role === 'mahasiswa') {
        $hasApprovedProposal = \App\Models\Proposal::where('user_id', $user->id)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', 'approved')
            ->exists();
    }
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

@if($role === 'mahasiswa')
    <!-- Mahasiswa Menu -->
    <a href="{{ route('mahasiswa.proposals.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('mahasiswa.proposals.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Proposal Saya
    </a>

    @if($hasApprovedProposal)
        <a href="{{ route('mahasiswa.attendance.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('mahasiswa.attendance.*') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Absensi
        </a>

        <a href="{{ route('mahasiswa.daily-logs.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('mahasiswa.daily-logs.*') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Log Harian
        </a>
    @endif

@elseif($role === 'operator')
    <!-- Operator Menu -->
    <a href="{{ route('operator.proposals.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('operator.proposals.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Review Proposal
    </a>

    <a href="{{ route('operator.reply-letters.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('operator.reply-letters.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Surat Balasan
    </a>

    <a href="{{ route('operator.periods.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('operator.periods.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Periode Magang
    </a>

    <a href="{{ route('operator.mahasiswa.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('operator.mahasiswa.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Data Mahasiswa
    </a>

    <a href="{{ route('operator.attendance.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('operator.attendance.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Rekap Kehadiran
    </a>

@elseif($role === 'manager')
    <!-- Manager Menu -->
    <a href="{{ route('manager.proposals.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('manager.proposals.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Approval Proposal
    </a>

    <a href="{{ route('manager.operators.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('manager.operators.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Kelola Operator
    </a>

    <a href="{{ route('manager.pembimbing.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('manager.pembimbing.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Kelola Pembimbing
    </a>

@elseif($role === 'manager_dept')
    <!-- Manager Dept Menu -->
    <a href="{{ route('manager.proposals.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('manager.proposals.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Approval Proposal
    </a>

    <a href="{{ route('manager.pembimbing.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('manager.pembimbing.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Kelola Pembimbing
    </a>

@elseif($role === 'pembimbing')
    <!-- Pembimbing Menu -->
    <a href="{{ route('pembimbing.attendance.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('pembimbing.attendance.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Verifikasi Kehadiran
    </a>

    <a href="{{ route('pembimbing.daily-logs.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('pembimbing.daily-logs.*') ? 'bg-blue-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Verifikasi Log Harian
    </a>
@endif

<!-- Notifications -->
<a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 rounded-lg mb-1 {{ request()->routeIs('notifications.*') ? 'bg-blue-700' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
    </svg>
    Notifikasi
    @php
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
    @endphp
    @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $unreadCount }}</span>
    @endif
</a>
