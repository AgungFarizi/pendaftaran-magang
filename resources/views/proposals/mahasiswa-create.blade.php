@extends('layouts.app')

@section('title', 'Buat Proposal - Tellinter')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Buat Proposal Baru</h1>
    <p>Isi data proposal magang Anda dengan lengkap dan jelas</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('mahasiswa.proposal.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Proposal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                               id="judul" name="judul" value="{{ old('judul') }}" required>
                        @error('judul')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="divisi" class="form-label">Divisi/Departemen <span class="text-danger">*</span></label>
                        <select class="form-select @error('divisi') is-invalid @enderror" id="divisi" name="divisi" required>
                            <option value="">-- Pilih Divisi --</option>
                            <option value="IT" {{ old('divisi') == 'IT' ? 'selected' : '' }}>IT / Teknologi Informasi</option>
                            <option value="HRD" {{ old('divisi') == 'HRD' ? 'selected' : '' }}>HRD / Sumber Daya Manusia</option>
                            <option value="Marketing" {{ old('divisi') == 'Marketing' ? 'selected' : '' }}>Marketing / Pemasaran</option>
                            <option value="Finance" {{ old('divisi') == 'Finance' ? 'selected' : '' }}>Finance / Keuangan</option>
                            <option value="Operasional" {{ old('divisi') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                        </select>
                        @error('divisi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Proposal <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="6" required>{{ old('deskripsi') }}</textarea>
                        <small class="text-muted">Jelaskan alasan, tujuan, dan manfaat magang Anda</small>
                        @error('deskripsi')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="durasi_minggu" class="form-label">Durasi Magang (Minggu) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('durasi_minggu') is-invalid @enderror" 
                               id="durasi_minggu" name="durasi_minggu" value="{{ old('durasi_minggu') }}" min="1" max="24" required>
                        @error('durasi_minggu')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dokumen_proposal" class="form-label">Upload Dokumen Proposal (PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('dokumen_proposal') is-invalid @enderror" 
                               id="dokumen_proposal" name="dokumen_proposal" accept=".pdf" required>
                        <small class="text-muted">File harus berformat PDF, maksimal 5MB</small>
                        @error('dokumen_proposal')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anggota Magang <span class="text-danger">*</span></label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tambahkan anggota kelompok magang Anda. Anda adalah ketua kelompok.
                        </div>

                        <div id="members-container">
                            <div class="member-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="members[0][nama]" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="members[0][nim]" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addMember()">
                            <i class="fas fa-plus-circle"></i> Tambah Anggota
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Buat Proposal
                        </button>
                        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-outline-secondary">
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
                <h5>Petunjuk Proposal</h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <h6><i class="fas fa-check-circle text-success"></i> Yang Harus Dipersiapkan</h6>
                    <ul class="small text-muted">
                        <li>Dokumen proposal dalam format PDF</li>
                        <li>Data lengkap anggota kelompok</li>
                        <li>Deskripsi yang jelas dan terperinci</li>
                        <li>Durasi magang yang realistis</li>
                    </ul>
                </div>

                <div class="info-item mb-3">
                    <h6><i class="fas fa-file-pdf text-danger"></i> Format Dokumen</h6>
                    <p class="small text-muted">Dokumen harus berisi:</p>
                    <ul class="small text-muted">
                        <li>Halaman judul</li>
                        <li>Tujuan dan manfaat</li>
                        <li>Rencana kerja</li>
                        <li>Jadwal kegiatan</li>
                        <li>Tanda tangan ketua</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <small><i class="fas fa-exclamation-triangle"></i> Pastikan semua data sudah benar sebelum submit. Perubahan data hanya bisa dilakukan jika status masih "Pending".</small>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Status Proposal</h5>
            </div>
            <div class="card-body">
                <div class="status-timeline">
                    <div class="status-item">
                        <div class="status-dot active"></div>
                        <div class="status-text">
                            <strong>Dibuat</strong>
                            <small>Anda membuat proposal</small>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-text">
                            <strong>Review Operator</strong>
                            <small>Operator melihat proposal</small>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-text">
                            <strong>Persetujuan Manager</strong>
                            <small>Manager menyetujui proposal</small>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-text">
                            <strong>Diterima</strong>
                            <small>Proposal diterima</small>
                        </div>
                    </div>
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
    }

    .status-dot.active {
        background-color: #2563eb;
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

    .member-item {
        background-color: #f9fafb;
        position: relative;
    }

    .member-item .btn-remove {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>

<script>
    let memberCount = 0;

    function addMember() {
        memberCount++;
        const container = document.getElementById('members-container');
        const memberHtml = `
            <div class="member-item mb-3 p-3 border rounded" id="member-${memberCount}">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="members[${memberCount}][nama]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="members[${memberCount}][nim]" required>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger float-end mt-2" onclick="removeMember(${memberCount})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', memberHtml);
    }

    function removeMember(id) {
        const element = document.getElementById(`member-${id}`);
        if (element) {
            element.remove();
        }
    }
</script>
@endsection
