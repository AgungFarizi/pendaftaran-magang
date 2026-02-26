@extends('layouts.app')

@section('title', 'Proposal Saya - Tellinter')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-file-pdf"></i> Proposal Saya</h1>
            <p>Kelola proposal magang Anda</p>
        </div>
        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Buat Proposal Baru
        </a>
    </div>
</div>

@if($proposals->count() > 0)
    <div class="row">
        @foreach($proposals as $proposal)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title">{{ $proposal->judul }}</h5>
                                <p class="text-muted mb-2 small">Divisi: {{ $proposal->divisi }}</p>
                            </div>
                            @if($proposal->status === 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @elseif($proposal->status === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif($proposal->status === 'disetujui_operator')
                                <span class="badge bg-info">Proses Manager</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($proposal->status) }}</span>
                            @endif
                        </div>

                        <p class="card-text">{{ Str::limit($proposal->deskripsi, 150) }}</p>

                        <div class="proposal-meta mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-users"></i> {{ $proposal->members()->count() + 1 }} Anggota
                            </small>
                        </div>

                        <div>
                            <a href="{{ route('mahasiswa.proposal.show', $proposal) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                            @if($proposal->status === 'pending')
                                <a href="{{ route('mahasiswa.proposal.edit', $proposal) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('mahasiswa.proposal.destroy', $proposal) }}" method="POST" 
                                      style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <h5>Belum Ada Proposal</h5>
        <p class="mb-3">Anda belum membuat proposal magang. Buat proposal sekarang untuk mendaftar!</p>
        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Buat Proposal Pertama
        </a>
    </div>
@endif
@endsection
