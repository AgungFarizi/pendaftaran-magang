<!-- Dashboard Mahasiswa -->
<div class="row">
    <!-- Status Proposal -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Status Proposal</h6>
                        @php
                            $latestProposal = auth()->user()->proposals()->latest()->first();
                        @endphp
                        @if($latestProposal)
                            <h3 class="mb-0">
                                @if($latestProposal->status === 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($latestProposal->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($latestProposal->status) }}</span>
                                @endif
                            </h3>
                        @else
                            <h3 class="mb-0"><span class="badge bg-secondary">Belum ada proposal</span></h3>
                        @endif
                    </div>
                    <i class="fas fa-file-pdf fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Notifikasi</h6>
                        @php
                            $unreadCount = auth()->user()->notifications()->where('dibaca', false)->count();
                        @endphp
                        <h3 class="mb-0">{{ $unreadCount }}</h3>
                        <small class="text-muted">Belum dibaca</small>
                    </div>
                    <i class="fas fa-bell fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Absensi -->
    <div class="col-md-3 mb-4">
        @php
            $totalAttendance = auth()->user()->attendances()->where('status_verifikasi', 'diverifikasi')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Kehadiran</h6>
                        <h3 class="mb-0">{{ $totalAttendance }}</h3>
                        <small class="text-muted">Hari hadir</small>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Periode Aktif -->
    <div class="col-md-3 mb-4">
        @php
            $activePeriod = \App\Models\PeriodeMagang::where('status', 'aktif')->first();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Periode Aktif</h6>
                        @if($activePeriod)
                            <h6 class="mb-0">{{ $activePeriod->nama }}</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($activePeriod->tanggal_mulai)->format('d M Y') }}</small>
                        @else
                            <h6 class="mb-0 text-secondary">Tidak ada</h6>
                        @endif
                    </div>
                    <i class="fas fa-calendar-alt fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Proposal -->
<div class="row">
    <div class="col-md-8">
        @if($latestProposal)
            <div class="card">
                <div class="card-header">
                    <h5>Proposal Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Judul Proposal</p>
                            <h6>{{ $latestProposal->judul }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Status</p>
                            @if($latestProposal->status === 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @elseif($latestProposal->status === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($latestProposal->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Divisi</p>
                            <h6>{{ $latestProposal->divisi }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Tanggal Submit</p>
                            <h6>{{ \Carbon\Carbon::parse($latestProposal->created_at)->format('d M Y H:i') }}</h6>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted mb-1">Deskripsi</p>
                        <p>{{ Str::limit($latestProposal->deskripsi, 200) }}</p>
                    </div>

                    <a href="{{ route('mahasiswa.proposal.show', $latestProposal) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Anda belum membuat proposal. 
                <a href="{{ route('mahasiswa.proposal.create') }}" class="alert-link">Buat proposal sekarang</a>
            </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-md-4">
        <!-- Info Akses Menu -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Info Akses Menu</h5>
            </div>
            <div class="card-body">
                @php
                    $hasApprovedProposal = auth()->user()->proposals()
                        ->where('status', 'diterima')
                        ->exists();
                @endphp

                @if($hasApprovedProposal)
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle"></i> Proposal Anda telah diterima!
                    </div>
                    <p class="mb-2">Menu yang dapat diakses:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Absensi</li>
                        <li><i class="fas fa-check text-success"></i> Log Harian</li>
                    </ul>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Proposal belum diterima
                    </div>
                    <p class="mb-2">Menu yang terkunci:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-lock text-danger"></i> Absensi</li>
                        <li><i class="fas fa-lock text-danger"></i> Log Harian</li>
                    </ul>
                    <small class="text-muted">Menu akan terbuka setelah proposal Anda diterima</small>
                @endif
            </div>
        </div>

        <!-- Periode Magang -->
        <div class="card">
            <div class="card-header">
                <h5>Periode Magang Terbuka</h5>
            </div>
            <div class="card-body">
                @php
                    $openPeriods = \App\Models\PeriodeMagang::where('status', 'aktif')->get();
                @endphp

                @forelse($openPeriods as $period)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">{{ $period->nama }}</h6>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($period->tanggal_selesai)->format('d M Y') }}
                        </small>
                        <div class="mt-2">
                            <span class="badge bg-success">Pendaftaran Aktif</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada periode magang yang aktif</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
