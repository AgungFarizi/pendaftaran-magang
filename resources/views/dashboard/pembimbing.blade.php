<!-- Dashboard Pembimbing Lapang -->
<div class="row">
    <!-- Mahasiswa Bimbingan -->
    <div class="col-md-3 mb-4">
        @php
            $mahasiswaBimbingan = auth()->user()->mahasiswaBimbingan()->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Mahasiswa Bimbingan</h6>
                        <h3 class="mb-0">{{ $mahasiswaBimbingan }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi Menunggu Verifikasi -->
    <div class="col-md-3 mb-4">
        @php
            $absensiPending = \App\Models\Kehadiran::whereHas('mahasiswa', function($q) {
                $q->where('pembimbing_lapang_id', auth()->id());
            })->where('status_verifikasi', 'menunggu')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Absensi Pending</h6>
                        <h3 class="mb-0">{{ $absensiPending }}</h3>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Harian Menunggu Verifikasi -->
    <div class="col-md-3 mb-4">
        @php
            $logPending = \App\Models\LogHarian::whereHas('mahasiswa', function($q) {
                $q->where('pembimbing_lapang_id', auth()->id());
            })->where('status_verifikasi', 'menunggu')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Log Harian Pending</h6>
                        <h3 class="mb-0">{{ $logPending }}</h3>
                    </div>
                    <i class="fas fa-book fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Verifikasi -->
    <div class="col-md-3 mb-4">
        @php
            $totalVerifikasi = \App\Models\Kehadiran::whereHas('mahasiswa', function($q) {
                $q->where('pembimbing_lapang_id', auth()->id());
            })->where('status_verifikasi', 'diverifikasi')->count() +
            \App\Models\LogHarian::whereHas('mahasiswa', function($q) {
                $q->where('pembimbing_lapang_id', auth()->id());
            })->where('status_verifikasi', 'diverifikasi')->count();
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total Verifikasi</h6>
                        <h3 class="mb-0">{{ $totalVerifikasi }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tasks & Recent Items -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Absensi Menunggu Verifikasi</h5>
            </div>
            <div class="card-body">
                @php
                    $recentAbsensi = \App\Models\Kehadiran::whereHas('mahasiswa', function($q) {
                        $q->where('pembimbing_lapang_id', auth()->id());
                    })->where('status_verifikasi', 'menunggu')
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($recentAbsensi as $absensi)
                    <div class="item-row mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $absensi->mahasiswa->nama_lengkap }}</h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }} | 
                                    {{ $absensi->jam_masuk }} - {{ $absensi->jam_keluar }}
                                </small>
                            </div>
                            <span class="badge bg-warning">Menunggu</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('pembimbing.attendance.verify', $absensi) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check"></i> Verifikasi
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada absensi yang menunggu verifikasi</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Menu Utama</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('pembimbing.attendance.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-check"></i> Verifikasi Absensi
                </a>
                <a href="{{ route('pembimbing.daily-log.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-book"></i> Verifikasi Log Harian
                </a>
                <a href="{{ route('pembimbing.mahasiswa.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users"></i> Data Mahasiswa Bimbingan
                </a>
            </div>
        </div>

        <!-- Statistik -->
        <div class="card">
            <div class="card-header">
                <h5>Statistik Bimbingan</h5>
            </div>
            <div class="card-body">
                @php
                    $totalAbsensi = \App\Models\Kehadiran::whereHas('mahasiswa', function($q) {
                        $q->where('pembimbing_lapang_id', auth()->id());
                    })->count();
                    
                    $totalLog = \App\Models\LogHarian::whereHas('mahasiswa', function($q) {
                        $q->where('pembimbing_lapang_id', auth()->id());
                    })->count();
                @endphp

                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Absensi</span>
                        <strong>{{ $totalAbsensi }}</strong>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="d-flex justify-content-between">
                        <span>Total Log Harian</span>
                        <strong>{{ $totalLog }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
