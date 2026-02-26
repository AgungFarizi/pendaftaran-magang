@extends('layouts.app')

@section('title', 'Dashboard - Tellinter')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
    <p>Selamat datang, {{ auth()->user()->nama_lengkap }}! ({{ auth()->user()->peran }})</p>
</div>

@if(auth()->user()->peran === 'Mahasiswa')
    @include('dashboard.mahasiswa')
@elseif(auth()->user()->peran === 'Operator')
    @include('dashboard.operator')
@elseif(auth()->user()->peran === 'Pembimbing Lapang')
    @include('dashboard.pembimbing')
@elseif(auth()->user()->peran === 'Manager')
    @include('dashboard.manager')
@elseif(auth()->user()->peran === 'Manager Divisi')
    @include('dashboard.manager-dept')
@endif
@endsection
