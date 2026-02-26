@extends('layouts.app')

@section('title', 'Input Absensi - Tellinter')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-calendar-check"></i> Input Absensi</h1>
    <p>Catat kehadiran magang Anda hari ini</p>
</div>

@php
    $hasApprovedProposal = auth()->user()->proposals()
        ->where('status', 'diterima')
        ->exists();
@endphp

@if(!$hasApprovedProposal)
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Akses Terbatas</h4>
        <p>Menu absensi hanya tersedia setelah proposal magang Anda diterima.</p>
        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-sm btn-warning">Kembali</a>
    </div>
@else
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('mahasiswa.attendance.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                   id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_masuk" class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" 
                                           id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}" required>
                                    @error('jam_masuk')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_keluar" class="form-label">Jam Keluar <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam_keluar') is-invalid @enderror" 
                                           id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar') }}" required>
                                    @error('jam_keluar')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="4">{{ old('keterangan') }}</textarea>
                            <small class="text-muted">Catatan tambahan (opsional) - misalnya: izin, sakit, atau kegiatan khusus</small>
                            @error('keterangan')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Absensi
                            </button>
                            <a href="{{ route('mahasiswa.attendance.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Petunjuk Input Absensi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <small><i class="fas fa-info-circle"></i> Isi data kehadiran Anda dengan jujur dan akurat</small>
                    </div>

                    <h6 class="mb-2">Yang Perlu Diperhatikan:</h6>
                    <ul class="small text-muted">
                        <li>Input absensi setiap hari kerja</li>
                        <li>Jam masuk dan keluar harus valid</li>
                        <li>Absensi akan diverifikasi oleh pembimbing</li>
                        <li>Jika salah, bisa diedit selama menunggu verifikasi</li>
                    </ul>

                    <hr>

                    <h6 class="mb-2">Status Verifikasi:</h6>
                    <div class="mb-2">
                        <small>
                            <span class="badge bg-warning">Menunggu</span> - Pembimbing belum verifikasi
                        </small>
                    </div>
                    <div class="mb-2">
                        <small>
                            <span class="badge bg-success">Diverifikasi</span> - Pembimbing sudah konfirmasi
                        </small>
                    </div>
                    <div>
                        <small>
                            <span class="badge bg-danger">Ditolak</span> - Ada kesalahan data
                        </small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Waktu Standar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small><strong>Jam Masuk:</strong> 08:00 - 09:00</small>
                    </div>
                    <div class="mb-2">
                        <small><strong>Jam Istirahat:</strong> 12:00 - 13:00</small>
                    </div>
                    <div>
                        <small><strong>Jam Keluar:</strong> 16:30 - 17:00</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
