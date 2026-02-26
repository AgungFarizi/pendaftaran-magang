@extends('layouts.guest')

@section('title', 'Daftar - Tellinter')

@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1><i class="fas fa-graduation-cap"></i> Tellinter</h1>
            <p>Pendaftaran Magang</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="register-form" id="registerForm">
            @csrf

            <!-- Step 1: Pilih Role -->
            <div id="step1" class="register-step active">
                <h5 class="step-title">
                    <span class="step-number">1</span> Pilih Peran Anda
                </h5>

                <div class="role-options">
                    <label class="role-option">
                        <input type="radio" name="peran" value="Mahasiswa" required>
                        <div class="role-content">
                            <h6><i class="fas fa-graduation-cap"></i> Mahasiswa</h6>
                            <p>Daftar untuk mengikuti program magang</p>
                            <span class="badge bg-success">Pendaftaran Terbuka</span>
                        </div>
                    </label>

                    <label class="role-option">
                        <input type="radio" name="peran" value="Operator" required>
                        <div class="role-content">
                            <h6><i class="fas fa-user-tie"></i> Operator</h6>
                            <p>Kelola dan review proposal magang</p>
                            <span class="badge bg-warning">Perlu Kode Akses</span>
                        </div>
                    </label>

                    <label class="role-option">
                        <input type="radio" name="peran" value="Pembimbing Lapang" required>
                        <div class="role-content">
                            <h6><i class="fas fa-users"></i> Pembimbing Lapang</h6>
                            <p>Verifikasi kehadiran dan aktivitas magang</p>
                            <span class="badge bg-warning">Perlu Kode Akses</span>
                        </div>
                    </label>

                    <label class="role-option">
                        <input type="radio" name="peran" value="Manager" required>
                        <div class="role-content">
                            <h6><i class="fas fa-crown"></i> Manager</h6>
                            <p>Persetujuan proposal dan kelola operator</p>
                            <span class="badge bg-danger">Perlu Kode Akses</span>
                        </div>
                    </label>

                    <label class="role-option">
                        <input type="radio" name="peran" value="Manager Divisi" required>
                        <div class="role-content">
                            <h6><i class="fas fa-crown"></i> Manager Divisi</h6>
                            <p>Persetujuan proposal divisi dan kelola pembimbing</p>
                            <span class="badge bg-danger">Perlu Kode Akses</span>
                        </div>
                    </label>
                </div>

                @error('peran')
                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                @enderror

                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-primary" onclick="nextStep()">
                        Lanjutkan <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Verifikasi Kode (untuk admin) -->
            <div id="step2" class="register-step" style="display: none;">
                <h5 class="step-title">
                    <span class="step-number">2</span> Verifikasi Kode Akses
                </h5>

                <div class="alert alert-info">
                    <i class="fas fa-lock"></i> Masukkan kode akses yang telah diberikan oleh administrator sistem.
                </div>

                <div class="mb-3">
                    <label for="access_code" class="form-label">Kode Akses <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="text" class="form-control @error('access_code') is-invalid @enderror" 
                               id="access_code" name="access_code" placeholder="Masukkan kode akses..." 
                               autocomplete="off">
                    </div>
                    @error('access_code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-primary" onclick="verifyCode()">
                        <i class="fas fa-check"></i> Verifikasi Kode
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </div>

            <!-- Step 3: Data Diri -->
            <div id="step3" class="register-step" style="display: none;">
                <h5 class="step-title">
                    <span class="step-number">3</span> Data Diri
                </h5>

                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                           id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Field khusus Mahasiswa -->
                <div id="mahasiswa-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                               id="nim" name="nim" value="{{ old('nim') }}">
                        @error('nim')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="universitas" class="form-label">Universitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('universitas') is-invalid @enderror" 
                               id="universitas" name="universitas" value="{{ old('universitas') }}">
                        @error('universitas')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror" 
                               id="jurusan" name="jurusan" value="{{ old('jurusan') }}">
                        @error('jurusan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('no_telepon') is-invalid @enderror" 
                               id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}">
                        @error('no_telepon')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Field khusus Pembimbing/Manager Divisi -->
                <div id="divisi-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                        <select class="form-select @error('divisi') is-invalid @enderror" id="divisi" name="divisi">
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
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                    </div>
                    <div id="password-strength" style="margin-top: 8px;"></div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input @error('agree') is-invalid @enderror" 
                           id="agree" name="agree" required>
                    <label class="form-check-label" for="agree">
                        Saya setuju dengan <a href="#" target="_blank">syarat dan ketentuan</a>
                    </label>
                    @error('agree')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Daftar Akun
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </div>
        </form>

        <div class="register-footer">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .register-container {
        width: 100%;
        max-width: 600px;
    }

    .register-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .register-header h1 {
        color: #2563eb;
        font-size: 2rem;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .register-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .register-step {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-title {
        color: #111827;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .step-number {
        background-color: #2563eb;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .role-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .role-option {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .role-option:hover {
        border-color: #2563eb;
        background-color: #f3f4f6;
    }

    .role-option input[type="radio"] {
        margin-right: 15px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .role-option input[type="radio"]:checked + .role-content {
        color: #2563eb;
    }

    .role-option input[type="radio"]:checked ~ .role-content h6 {
        color: #2563eb;
    }

    .role-content {
        flex: 1;
    }

    .role-content h6 {
        margin: 0 0 5px 0;
        color: #374151;
        font-weight: 600;
    }

    .role-content p {
        margin: 0 0 8px 0;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .role-content .badge {
        font-size: 0.75rem;
    }

    .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-color: #d1d5db;
        padding: 12px 15px;
        border-radius: 8px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .input-group-text {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #6b7280;
    }

    .btn-primary, .btn-success {
        background-color: #2563eb;
        border-color: #2563eb;
        font-weight: 600;
        padding: 12px;
        border-radius: 8px;
    }

    .btn-primary:hover, .btn-success:hover {
        background-color: #1e40af;
        border-color: #1e40af;
    }

    .btn-outline-secondary {
        border-color: #d1d5db;
        color: #6b7280;
        font-weight: 600;
    }

    .btn-outline-secondary:hover {
        background-color: #f3f4f6;
        color: #374151;
    }

    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
    }

    .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }

    .register-footer {
        text-align: center;
        margin-top: 20px;
    }

    .register-footer p {
        color: #6b7280;
        margin: 0;
    }

    .register-footer a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .register-footer a:hover {
        text-decoration: underline;
    }

    .alert {
        border: none;
        border-radius: 8px;
    }

    .alert-info {
        background-color: #cffafe;
        color: #164e63;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 576px) {
        .register-card {
            padding: 25px;
        }

        .register-header h1 {
            font-size: 1.5rem;
        }

        .role-content p {
            display: none;
        }

        .role-content .badge {
            display: block;
            margin-top: 5px;
        }
    }
</style>

<script>
    const peranSelect = document.querySelector('input[name="peran"]:checked');
    
    function nextStep() {
        const peran = document.querySelector('input[name="peran"]:checked');
        
        if (!peran) {
            alert('Silakan pilih peran terlebih dahulu');
            return;
        }

        document.getElementById('step1').style.display = 'none';

        // Jika admin, minta verifikasi kode
        if (['Operator', 'Pembimbing Lapang', 'Manager', 'Manager Divisi'].includes(peran.value)) {
            document.getElementById('step2').style.display = 'block';
        } else {
            // Jika mahasiswa, langsung ke step 3
            showStep3();
        }

        // Show/hide field divisi
        updateFieldsDisplay();
    }

    function previousStep() {
        const step2 = document.getElementById('step2').style.display !== 'none';
        const step3 = document.getElementById('step3').style.display !== 'none';

        if (step3) {
            document.getElementById('step3').style.display = 'none';
            const peran = document.querySelector('input[name="peran"]:checked');
            if (['Operator', 'Pembimbing Lapang', 'Manager', 'Manager Divisi'].includes(peran.value)) {
                document.getElementById('step2').style.display = 'block';
            } else {
                document.getElementById('step1').style.display = 'block';
            }
        } else if (step2) {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
        }
    }

    function verifyCode() {
        const code = document.getElementById('access_code').value.trim();
        
        if (!code) {
            alert('Silakan masukkan kode akses');
            return;
        }

        // Validasi kode (di backend sebenarnya akan divalidasi ulang)
        const validCodes = {
            'Operator': ['OP2024TELLINTER', 'OPERATOR-SECRET-001'],
            'Pembimbing Lapang': ['PL2024TELLINTER', 'PEMBIMBING-SECRET-001'],
            'Manager': ['MGR-SUPER-ADMIN-2024', 'MANAGER-MASTER-KEY'],
            'Manager Divisi': ['MGRDEPT-SUPER-ADMIN-2024', 'MANAGERDEPT-MASTER-KEY']
        };

        const peran = document.querySelector('input[name="peran"]:checked').value;
        
        if (!validCodes[peran] || !validCodes[peran].includes(code)) {
            alert('Kode akses tidak valid. Silakan hubungi administrator.');
            return;
        }

        showStep3();
    }

    function showStep3() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
        updateFieldsDisplay();
    }

    function updateFieldsDisplay() {
        const peran = document.querySelector('input[name="peran"]:checked').value;
        document.getElementById('mahasiswa-fields').style.display = peran === 'Mahasiswa' ? 'block' : 'none';
        document.getElementById('divisi-fields').style.display = ['Pembimbing Lapang', 'Manager Divisi'].includes(peran) ? 'block' : 'none';
    }

    // Update field display ketika role berubah
    document.querySelectorAll('input[name="peran"]').forEach(radio => {
        radio.addEventListener('change', updateFieldsDisplay);
    });

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('password-strength');
        let strength = '';
        let color = '';

        if (password.length < 8) {
            strength = '<small class="text-danger"><i class="fas fa-times"></i> Minimal 8 karakter</small>';
            color = 'danger';
        } else if (password.length < 12) {
            strength = '<small class="text-warning"><i class="fas fa-circle"></i> Kekuatan: Lemah</small>';
        } else if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            strength = '<small class="text-warning"><i class="fas fa-circle"></i> Kekuatan: Sedang</small>';
        } else {
            strength = '<small class="text-success"><i class="fas fa-check-circle"></i> Kekuatan: Bagus</small>';
        }

        strengthDiv.innerHTML = strength;
    });

    // Form submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok');
            return;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter');
            return;
        }
    });

    // Initialize
    updateFieldsDisplay();
</script>
@endsection
