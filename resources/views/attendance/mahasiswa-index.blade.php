@extends('layouts.app')

@section('title', 'Absensi Saya - Tellinter')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-check"></i> Absensi Saya</h1>
            <p>Kelola data kehadiran magang Anda</p>
        </div>
        @php
            $hasApprovedProposal = auth()->user()->proposals()
                ->where('status', 'diterima')
                ->exists();
        @endphp
        @if($hasApprovedProposal)
            <a href="{{ route('mahasiswa.attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Input Absensi
            </a>
        @endif
    </div>
</div>

@php
    $hasApprovedProposal = auth()->user()->proposals()
        ->where('status', 'diterima')
        ->exists();
@endphp

@if(!$hasApprovedProposal)
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Akses Terbatas</h4>
        <p>Menu absensi hanya tersedia setelah proposal magang Anda diterima. Silakan ajukan proposal terlebih dahulu dan tunggu persetujuan dari pihak perusahaan.</p>
        <hr>
        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-sm btn-warning">
            <i class="fas fa-file-pdf"></i> Lihat Proposal Saya
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
@else
    @if($attendances->count() > 0)
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Data Kehadiran</h5>
                    <span class="badge bg-info">{{ $attendances->count() }} Hari</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Keterangan</th>
                                <th>Status Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                                    <td><strong>{{ $attendance->jam_masuk }}</strong></td>
                                    <td>{{ $attendance->jam_keluar ?? '-' }}</td>
                                    <td>{{ $attendance->keterangan ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status_verifikasi === 'diverifikasi')
                                            <span class="badge bg-success">Diverifikasi</span>
                                        @elseif($attendance->status_verifikasi === 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">Menunggu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->status_verifikasi === 'menunggu')
                                            <a href="{{ route('mahasiswa.attendance.edit', $attendance) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Hadir</h6>
                        <h3 class="mb-0">{{ $attendances->where('status_verifikasi', 'diverifikasi')->count() }}</h3>
                        <small class="text-muted">Hari terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Menunggu Verifikasi</h6>
                        <h3 class="mb-0">{{ $attendances->where('status_verifikasi', 'menunggu')->count() }}</h3>
                        <small class="text-muted">Hari pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Ditolak</h6>
                        <h3 class="mb-0">{{ $attendances->where('status_verifikasi', 'ditolak')->count() }}</h3>
                        <small class="text-muted">Hari ditolak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Presentase Kehadiran</h6>
                        <h3 class="mb-0">{{ $attendances->count() > 0 ? round(($attendances->where('status_verifikasi', 'diverifikasi')->count() / $attendances->count()) * 100) : 0 }}%</h3>
                        <small class="text-muted">Dari total</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <h5>Belum Ada Data Absensi</h5>
            <p class="mb-3">Anda belum mengisi data absensi. Mulai input absensi harian Anda sekarang!</p>
            <a href="{{ route('mahasiswa.attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Input Absensi Pertama
            </a>
        </div>
    @endif
@endif
@endsection
