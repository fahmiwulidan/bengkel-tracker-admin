@extends('admin.layout')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="grid-stats">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <p>Total Bengkel</p>
            <div class="stat-value">{{ $totalWorkshops ?? 0 }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <p>Total Mekanik</p>
            <div class="stat-value">{{ $totalMechanics ?? 0 }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <p>Total User</p>
            <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <p>Total Order</p>
            <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
        </div>
    </div>

    <div class="card" style="margin-top: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
            <h2 style="font-size: 22px; margin: 0; color: white;">Sistem Pemantauan Area</h2>
        </div>
        <p style="color: var(--text-muted); line-height: 1.6; font-size: 16px;">
            Halaman ini digunakan untuk mengelola data bengkel, mekanik, user, dan order secara terpusat.
            Sistem terintegrasi langsung dengan pemetaan geospasial real-time.
        </p>
        <div style="margin-top: 24px;">
            <a href="{{ route('admin.workshops.index') }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-right"></i> Kelola Bengkel Sekarang
            </a>
        </div>
    </div>
@endsection
