<!-- Dashboard Manager -->
<div class="row">
    <!-- Proposal Menunggu Approval -->
    <div class="col-md-3 mb-4">
        @php
            $proposalPending = \App\Models\Proposal::where('status', 'disetujui_operator')
                ->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Proposal Pending</h6>
                        <h3 class="mb-0">{{ $proposalPending }}</h3>
                    </div>
                    <i class="fas fa-file-check fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Proposal Diterima -->
    <div class="col-md-3 mb-4">
        @php
            $proposalDiterima = \App\Models\Proposal::where('status', 'diterima')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Diterima</h6>
                        <h3 class="mb-0">{{ $proposalDiterima }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Operator -->
    <div class="col-md-3 mb-4">
        @php
            $totalOperator = \App\Models\User::where('peran', 'Operator')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Operator</h6>
                        <h3 class="mb-0">{{ $totalOperator }}</h3>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-info opacity-25"></i>
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

<!-- Approval Section -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Proposal Menunggu Persetujuan</h5>
                    <a href="{{ route('manager.proposals.pending') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $pendingProposals = \App\Models\Proposal::where('status', 'disetujui_operator')
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($pendingProposals as $proposal)
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
                            <a href="{{ route('manager.proposals.show', $proposal) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check"></i> Setujui/Tolak
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada proposal yang menunggu persetujuan</p>
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
                <a href="{{ route('manager.proposals.pending') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-check"></i> Persetujuan Proposal
                </a>
                <a href="{{ route('manager.operators.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-user-plus"></i> Tambah Operator
                </a>
                <a href="{{ route('manager.operators.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-users"></i> Kelola Operator
                </a>
                <a href="{{ route('manager.mahasiswa.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> Data Mahasiswa
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <h5>Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                @php
                    $recentNotif = auth()->user()->notifications()
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($recentNotif as $notif)
                    <div class="alert alert-info mb-2 p-2">
                        <small>{{ Str::limit($notif->pesan, 80) }}</small>
                        <br>
                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
