@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="welcome-section">
    <h2>Selamat Datang, Owner STM!</h2>
    <div class="dashboard-grid">
        <div class="dashboard-item">
            <a href="{{ route('outlet.index') }}" class="nav-link nav-dash">
                <i class="nav-icon fas fa-store"></i>
                <p class="ml-2">
                    Outlet
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="{{ route('kategori.index') }}" class="nav-link nav-dash">
                <i class="nav-icon fas fa-box"></i>
                <p class="ml-2">
                    Kategori
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="{{ route('stok.index') }}" class="nav-link nav-dash">
                <i class="nav-icon fas fa-clipboard-list"></i> 
                <p class="ml-2">
                    Stok
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="{{ route('menu.index') }}" class="nav-link nav-dash">
                <i class="nav-icon fas fa-folder-open"></i> 
                <p class="ml-2">
                    Menu
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="" class="nav-link nav-dash">
                <i class="nav-icon fas fa-file-invoice"></i> 
                <p class="ml-2">
                    Struk
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="" class="nav-link nav-dash">
                <i class="nav-icon fas fa-money-check-alt"></i> 
                <p class="ml-2">
                    Transaksi
                </p>
            </a>
        </div>
        <div class="dashboard-item">
            <a href="" class="nav-link nav-dash">
                <i class="nav-icon fas fa-chart-line"></i> 
                <p class="ml-2">
                    Laporan
                </p>
            </a>
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
        padding-bottom: 30px;
    }

    .dashboard-item {
        color: #343a40;
        background-color: #d8d8d8;
        padding: 10px;
        text-align: center;
        border-radius: 10px;
        font-size: 18px;
        cursor: pointer;
    }

    .nav-dash {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #343a40;
    }

    .nav-dash:hover {
        color: black; 
    }

    .dashboard-item:hover {
        background-color: #bcbcbc;
    }
</style>
@endsection
