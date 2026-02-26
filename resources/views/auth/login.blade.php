@extends('layouts.guest')

@section('title', 'Login - Tellinter')

@section('content')
<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h1><i class="fas fa-graduation-cap"></i> Tellinter</h1>
            <p>Sistem Pendaftaran Magang</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div class="login-footer">
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        </div>
    </div>

   
<style>
    body {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .login-container {
        display: flex;
        gap: 30px;
        max-width: 1000px;
        width: 100%;
        padding: 20px;
    }

    .login-box {
        flex: 1;
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h1 {
        color: #2563eb;
        font-size: 2rem;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .login-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .login-form .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .login-form .input-group-text {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #6b7280;
    }

    .login-form .form-control {
        border-color: #d1d5db;
        padding: 12px 15px;
    }

    .login-form .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn-login {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
        font-weight: 600;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background-color: #1e40af;
        border-color: #1e40af;
        color: white;
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
    }

    .login-footer p {
        color: #6b7280;
        margin: 0;
    }

    .login-footer a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .info-box {
        flex: 1;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .info-box h3 {
        color: #111827;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .demo-accounts {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .demo-item {
        background-color: #f3f4f6;
        padding: 12px;
        border-radius: 8px;
        border-left: 3px solid #2563eb;
    }

    .demo-item strong {
        display: block;
        color: #374151;
        margin-bottom: 4px;
    }

    .demo-item small {
        color: #6b7280;
        font-family: 'Courier New', monospace;
    }

    .demo-item code {
        background-color: #e5e7eb;
        padding: 2px 6px;
        border-radius: 4px;
        color: #1f2937;
    }

    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            gap: 20px;
        }

        .login-box, .info-box {
            padding: 25px;
        }

        .login-header h1 {
            font-size: 1.5rem;
        }
    }
</style>
@endsection
