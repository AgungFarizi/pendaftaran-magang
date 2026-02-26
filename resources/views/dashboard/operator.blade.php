<!-- Dashboard Operator -->
<div class="row">
    <!-- Proposal Menunggu Review -->
    <div class="col-md-3 mb-4">
        @php
            $pendingReview = \App\Models\Proposal::where('status', 'pending')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Proposal Pending</h6>
                        <h3 class="mb-0">{{ $pendingReview }}</h3>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Layak -->
    <div class="col-md-3 mb-4">
        @php
            $approvedOperator = \App\Models\Proposal::where('status', 'disetujui_operator')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Proposal Teruskan</h6>
                        <h3 class="mb-0">{{ $approvedOperator }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Surat Balasan -->
    <div class="col-md-3 mb-4">
        @php
            $lettersSent = \App\Models\SuratBalasan::count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Surat Balasan</h6>
                        <h3 class="mb-0">{{ $lettersSent }}</h3>
                    </div>
                    <i class="fas fa-envelope fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Mahasiswa -->
    <div class="col-md-3 mb-4">
        @php
            $totalMahasiswa = \App\Models\User::where('peran', 'Mahasiswa')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Mahasiswa</h6>
                        <h3 class="mb-0">{{ $totalMahasiswa }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Proposals -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Proposal Terbaru (Menunggu Review)</h5>
                    <a href="{{ route('operator.proposals.review') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $recentProposals = \App\Models\Proposal::where('status', 'pending')
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($recentProposals as $proposal)
                    <div class="proposal-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $proposal->judul }}</h6>
                                <small class="text-muted">
                                    Dari: <strong>{{ $proposal->submittedBy->nama_lengkap }}</strong> | 
                                    Divisi: <strong>{{ $proposal->divisi }}</strong>
                                </small>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('operator.proposals.show', $proposal) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada proposal yang menunggu review</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Aksi Cepat</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('operator.proposals.review') }}" class="btn btn-outline-primary">
                    <i class="fas fa-clipboard-list"></i> Review Proposal
                </a>
                <a href="{{ route('operator.response-letters.create') }}" class="btn btn-outline-info">
                    <i class="fas fa-envelope"></i> Buat Surat Balasan
                </a>
                <a href="{{ route('operator.periods.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-calendar-plus"></i> Buat Periode Magang
                </a>
                <a href="{{ route('operator.mahasiswa.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users"></i> Lihat Data Mahasiswa
                </a>
            </div>
        </div>

        <!-- Status Notifikasi -->
        <div class="card">
            <div class="card-header">
                <h5>Notifikasi</h5>
            </div>
            <div class="card-body">
                @php
                    $unreadNotif = auth()->user()->notifications()
                        ->where('dibaca', false)
                        ->latest()
                        ->limit(3)
                        ->get();
                @endphp

                @forelse($unreadNotif as $notif)
                    <div class="alert alert-info mb-2 p-2">
                        <small>{{ Str::limit($notif->pesan, 100) }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada notifikasi baru</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
