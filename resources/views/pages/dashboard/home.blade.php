@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="welcome-section">
        <h2>Selamat Datang, Owner STM!</h2>
        <div class="dashboard-grid">
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-store"></i> Outlet
            </div>
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-file-invoice"></i> Struk
            </div>
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-clipboard-list"></i> Menu
            </div>
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-box"></i> Stok
            </div>
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-money-check-alt"></i> Transaksi
            </div>
            <div class="dashboard-item" onclick="window.location=">
                <i class="fas fa-chart-line"></i> Laporan
            </div>
        </div>
    </div>

    <style>
        .dashboard {
            display: flex;
            height: 100%;
        }

        .welcome-section {
            text-align: center;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .dashboard-item {
            background-color: #d8d8d8;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
        }
    </style>
@endsection
