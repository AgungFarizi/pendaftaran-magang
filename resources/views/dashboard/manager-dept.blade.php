<!-- Dashboard Manager Divisi -->
<div class="row">
    <!-- Proposal Menunggu Approval (Divisi) -->
    <div class="col-md-3 mb-4">
        @php
            $proposalPending = \App\Models\Proposal::where('status', 'disetujui_operator')
                ->where('divisi', auth()->user()->divisi)
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

    <!-- Total Proposal Diterima (Divisi) -->
    <div class="col-md-3 mb-4">
        @php
            $proposalDiterima = \App\Models\Proposal::where('status', 'diterima')
                ->where('divisi', auth()->user()->divisi)
                ->count();
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

    <!-- Total Pembimbing Divisi -->
    <div class="col-md-3 mb-4">
        @php
            $totalPembimbing = \App\Models\User::where('peran', 'Pembimbing Lapang')
                ->where('divisi', auth()->user()->divisi)
                ->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Pembimbing</h6>
                        <h3 class="mb-0">{{ $totalPembimbing }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Mahasiswa Divisi -->
    <div class="col-md-3 mb-4">
        @php
            $totalMahasiswa = \App\Models\Proposal::where('status', 'diterima')
                ->where('divisi', auth()->user()->divisi)
                ->join('users', 'proposals.user_id', 'users.id')
                ->distinct('users.id')
                ->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Mahasiswa Magang</h6>
                        <h3 class="mb-0">{{ $totalMahasiswa }}</h3>
                    </div>
                    <i class="fas fa-graduation-cap fa-2x text-primary opacity-25"></i>
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
                    <h5>Proposal Menunggu Persetujuan ({{ auth()->user()->divisi }})</h5>
                    <a href="{{ route('manager-dept.proposals.pending') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $pendingProposals = \App\Models\Proposal::where('status', 'disetujui_operator')
                        ->where('divisi', auth()->user()->divisi)
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
                                    Status: <strong>{{ ucfirst($proposal->status) }}</strong>
                                </small>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('manager-dept.proposals.show', $proposal) }}" class="btn btn-sm btn-outline-primary">
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
                <a href="{{ route('manager-dept.proposals.pending') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-check"></i> Persetujuan Proposal
                </a>
                <a href="{{ route('manager-dept.pembimbing.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-user-plus"></i> Tambah Pembimbing
                </a>
                <a href="{{ route('manager-dept.pembimbing.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-users"></i> Kelola Pembimbing
                </a>
                <a href="{{ route('manager-dept.mahasiswa.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> Data Mahasiswa
                </a>
            </div>
        </div>

        <!-- Info Divisi -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Info Divisi</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Divisi:</strong> {{ auth()->user()->divisi }}</p>
                <p class="mb-2"><strong>Manager:</strong> {{ auth()->user()->nama_lengkap }}</p>
                <p class="mb-0"><strong>Email:</strong> {{ auth()->user()->email }}</p>
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
