@extends('layouts.app')

@section('title', 'Detail Proposal - Tellinter')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-file-pdf"></i> Detail Proposal</h1>
            <p>{{ $proposal->judul }}</p>
        </div>
        <div>
            @if($proposal->status === 'diterima')
                <span class="badge bg-success fs-6">Diterima</span>
            @elseif($proposal->status === 'ditolak')
                <span class="badge bg-danger fs-6">Ditolak</span>
            @elseif($proposal->status === 'disetujui_operator')
                <span class="badge bg-info fs-6">Proses Manager</span>
            @else
                <span class="badge bg-warning fs-6">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Proposal</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Judul</p>
                        <h6>{{ $proposal->judul }}</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Divisi</p>
                        <h6>{{ $proposal->divisi }}</h6>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Durasi Magang</p>
                        <h6>{{ $proposal->durasi_minggu }} Minggu</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Tanggal Submit</p>
                        <h6>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}</h6>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <p class="text-muted mb-2">Deskripsi</p>
                    <p>{{ $proposal->deskripsi }}</p>
                </div>

                <hr>

                <div class="mb-3">
                    <p class="text-muted mb-2">Dokumen Proposal</p>
                    <a href="{{ Storage::url($proposal->dokumen_proposal) }}" target="_blank" class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf"></i> Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Anggota -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Anggota Magang</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ auth()->user()->nama_lengkap }}</strong></td>
                            <td>{{ auth()->user()->nim }}</td>
                            <td><span class="badge bg-primary">Ketua</span></td>
                        </tr>
                        @foreach($proposal->members as $member)
                            <tr>
                                <td>{{ $member->nama }}</td>
                                <td>{{ $member->nim }}</td>
                                <td><span class="badge bg-secondary">Anggota</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status & Feedback -->
        @if($proposal->status !== 'pending')
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Feedback & Catatan</h5>
                </div>
                <div class="card-body">
                    @if($proposal->catatan_operator)
                        <div class="feedback-item mb-3">
                            <h6><i class="fas fa-user-tie"></i> Catatan Operator</h6>
                            <p>{{ $proposal->catatan_operator }}</p>
                        </div>
                    @endif

                    @if($proposal->catatan_manager)
                        <div class="feedback-item mb-3">
                            <h6><i class="fas fa-crown"></i> Catatan Manager</h6>
                            <p>{{ $proposal->catatan_manager }}</p>
                        </div>
                    @endif

                    @if($proposal->catatan_manager_divisi)
                        <div class="feedback-item">
                            <h6><i class="fas fa-crown"></i> Catatan Manager Divisi</h6>
                            <p>{{ $proposal->catatan_manager_divisi }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-md-4">
        <!-- Status Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Status Proposal</h5>
            </div>
            <div class="card-body">
                <div class="status-timeline">
                    <div class="status-item {{ in_array($proposal->status, ['pending', 'disetujui_operator', 'ditolak_operator', 'disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'completed' : '' }}">
                        <div class="status-dot active"></div>
                        <div class="status-text">
                            <strong>Dibuat</strong>
                            <small>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y') }}</small>
                        </div>
                    </div>

                    <div class="status-item {{ in_array($proposal->status, ['disetujui_operator', 'ditolak_operator', 'disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'completed' : '' }}">
                        <div class="status-dot {{ in_array($proposal->status, ['disetujui_operator', 'ditolak_operator', 'disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'active' : '' }}"></div>
                        <div class="status-text">
                            <strong>Review Operator</strong>
                            <small>{{ $proposal->status !== 'pending' ? 'Selesai' : 'Menunggu' }}</small>
                        </div>
                    </div>

                    <div class="status-item {{ in_array($proposal->status, ['disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'completed' : '' }}">
                        <div class="status-dot {{ in_array($proposal->status, ['disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'active' : '' }}"></div>
                        <div class="status-text">
                            <strong>Persetujuan Manager</strong>
                            <small>{{ in_array($proposal->status, ['disetujui_manager', 'ditolak_manager', 'disetujui_manager_divisi', 'ditolak_manager_divisi', 'diterima']) ? 'Selesai' : 'Menunggu' }}</small>
                        </div>
                    </div>

                    <div class="status-item {{ $proposal->status === 'diterima' ? 'completed' : '' }}">
                        <div class="status-dot {{ $proposal->status === 'diterima' ? 'active' : '' }}"></div>
                        <div class="status-text">
                            <strong>Diterima</strong>
                            <small>{{ $proposal->status === 'diterima' ? 'Diterima' : 'Menunggu' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($proposal->status === 'pending')
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Aksi</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('mahasiswa.proposal.edit', $proposal) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Proposal
                    </a>
                    <form action="{{ route('mahasiswa.proposal.destroy', $proposal) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="fas fa-trash"></i> Hapus Proposal
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Info Box -->
        <div class="card">
            <div class="card-header">
                <h5>Informasi Penting</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <small><i class="fas fa-info-circle"></i> Proposal hanya bisa diedit jika masih status "Pending"</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-timeline {
        position: relative;
        padding-left: 20px;
    }

    .status-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #d1d5db;
        position: absolute;
        left: -20px;
        top: 4px;
        transition: all 0.3s ease;
    }

    .status-dot.active {
        background-color: #2563eb;
        width: 14px;
        height: 14px;
        left: -21px;
        top: 3px;
    }

    .status-text strong {
        display: block;
        color: #374151;
        font-size: 0.9rem;
    }

    .status-text small {
        color: #9ca3af;
        font-size: 0.8rem;
    }

    .feedback-item {
        padding: 15px;
        background-color: #f3f4f6;
        border-radius: 8px;
        border-left: 3px solid #2563eb;
    }

    .feedback-item h6 {
        margin-bottom: 10px;
        color: #374151;
    }

    .feedback-item p {
        margin: 0;
        color: #1f2937;
        font-size: 0.95rem;
    }
</style>
@endsection
