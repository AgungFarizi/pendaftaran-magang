<!-- Sidebar Menu -->
<nav class="navbar-nav flex-column">
    <!-- Dashboard -->
    <div class="nav-label">Menu Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Dashboard
    </a>

    <!-- Menu berdasarkan Role -->
    @if(auth()->user()->peran === 'Mahasiswa')
        <!-- Menu Mahasiswa -->
        <a href="{{ route('mahasiswa.proposal.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.proposal.*') ? 'active' : '' }}">
            <i class="fas fa-file-pdf"></i> Proposal Saya
        </a>

        @php
            $hasApprovedProposal = auth()->user()->proposals()
                ->where('status', 'diterima')
                ->exists();
        @endphp

        @if($hasApprovedProposal)
            <a href="{{ route('mahasiswa.attendance.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Absensi
            </a>
            <a href="{{ route('mahasiswa.daily-log.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.daily-log.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Log Harian
            </a>
        @endif

        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
            @php
                $unreadCount = auth()->user()->notifications()->where('dibaca', false)->count();
            @endphp
            @if($unreadCount > 0)
                <span class="badge bg-danger float-end">{{ $unreadCount }}</span>
            @endif
        </a>

    @elseif(auth()->user()->peran === 'Operator')
        <!-- Menu Operator -->
        <div class="nav-label">Pendaftaran Magang</div>
        <a href="{{ route('operator.periods.index') }}" class="nav-link {{ request()->routeIs('operator.periods.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Periode Magang
        </a>

        <div class="nav-label">Proposal & Verifikasi</div>
        <a href="{{ route('operator.proposals.review') }}" class="nav-link {{ request()->routeIs('operator.proposals.review') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Review Proposal
        </a>
        <a href="{{ route('operator.proposals.submitted') }}" class="nav-link {{ request()->routeIs('operator.proposals.submitted') ? 'active' : '' }}">
            <i class="fas fa-check-circle"></i> Proposal Teruskan
        </a>

        <div class="nav-label">Surat Balasan</div>
        <a href="{{ route('operator.response-letters.index') }}" class="nav-link {{ request()->routeIs('operator.response-letters.*') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i> Surat Balasan
        </a>

        <div class="nav-label">Data</div>
        <a href="{{ route('operator.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('operator.mahasiswa.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Mahasiswa
        </a>

        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
        </a>

    @elseif(auth()->user()->peran === 'Pembimbing Lapang')
        <!-- Menu Pembimbing Lapang -->
        <div class="nav-label">Verifikasi</div>
        <a href="{{ route('pembimbing.attendance.index') }}" class="nav-link {{ request()->routeIs('pembimbing.attendance.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Verifikasi Absensi
        </a>
        <a href="{{ route('pembimbing.daily-log.index') }}" class="nav-link {{ request()->routeIs('pembimbing.daily-log.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i> Verifikasi Log Harian
        </a>

        <div class="nav-label">Data</div>
        <a href="{{ route('pembimbing.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('pembimbing.mahasiswa.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Mahasiswa
        </a>

        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
        </a>

    @elseif(auth()->user()->peran === 'Manager')
        <!-- Menu Manager -->
        <div class="nav-label">Persetujuan</div>
        <a href="{{ route('manager.proposals.pending') }}" class="nav-link {{ request()->routeIs('manager.proposals.*') ? 'active' : '' }}">
            <i class="fas fa-file-check"></i> Persetujuan Proposal
        </a>

        <div class="nav-label">Manajemen User</div>
        <a href="{{ route('manager.operators.index') }}" class="nav-link {{ request()->routeIs('manager.operators.*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Kelola Operator
        </a>

        <div class="nav-label">Data</div>
        <a href="{{ route('manager.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('manager.mahasiswa.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Mahasiswa
        </a>

        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
        </a>

    @elseif(auth()->user()->peran === 'Manager Divisi')
        <!-- Menu Manager Divisi -->
        <div class="nav-label">Persetujuan</div>
        <a href="{{ route('manager-dept.proposals.pending') }}" class="nav-link {{ request()->routeIs('manager-dept.proposals.*') ? 'active' : '' }}">
            <i class="fas fa-file-check"></i> Persetujuan Proposal
        </a>

        <div class="nav-label">Manajemen User</div>
        <a href="{{ route('manager-dept.pembimbing.index') }}" class="nav-link {{ request()->routeIs('manager-dept.pembimbing.*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Kelola Pembimbing
        </a>

        <div class="nav-label">Data</div>
        <a href="{{ route('manager-dept.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('manager-dept.mahasiswa.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Mahasiswa
        </a>

        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
        </a>
    @endif

    <hr class="my-3">

    <div class="nav-label">Pengaturan</div>
    <a href="{{ route('profile') }}" class="nav-link">
        <i class="fas fa-user-cog"></i> Profil Saya
    </a>
</nav>
