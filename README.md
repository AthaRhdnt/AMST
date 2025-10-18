<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

> **DISCLAIMER:**
> Repositori ini berisi kode sumber proyek yang merupakan bagian dari **Tugas Akhir (Skripsi)**.
>
> **Penting:** Aplikasi ini adalah hasil rancangan dan implementasi dalam konteks studi akademik dan **belum tentu digunakan secara resmi** sebagai sistem operasional oleh pihak STM – Es Teh Manis.
---

# 📦 Aplikasi Manajemen Stok Barang dan Transaksi Penjualan (AMST)

## 📝 Tinjauan Proyek (Project Overview)

**Aplikasi Manajemen Stok Barang dan Transaksi Penjualan (AMST)** adalah sistem *back-end* berbasis web yang dikembangkan untuk mendigitalisasi dan meningkatkan efisiensi operasional gerai **STM – Es Teh Manis**.

Sistem ini secara langsung mengatasi masalah yang disebabkan oleh proses manual—seperti rekapitulasi transaksi yang lambat dan kesulitan pemilik dalam memantau stok dan penjualan secara *real-time*. **AMST** menyediakan platform yang **lebih cepat, akurat, dan terpusat** untuk manajemen inventaris, pencatatan transaksi, dan pelaporan *real-time*.

## ✨ Fitur Utama (Key Features)

Fungsionalitas utama proyek ini diimplementasikan menggunakan arsitektur Model-View-Controller (MVC) dan meliputi:

* **Sistem Otentikasi:** Login yang aman untuk berbagai level pengguna dan tampilan Dasbor ringkasan.
* **Manajemen Inventaris:** Pengelolaan lengkap terhadap barang, stok, dan kategori menu.
* **Pencatatan Transaksi:** Proses efisien untuk merekam penjualan, termasuk penanganan keranjang belanja.
* **Manajemen Pembelian:** Pelacakan dan pencatatan pembelian inventaris untuk pemenuhan stok.
* **Sistem Pelaporan:** Pembuatan laporan *real-time* untuk data stok, keuangan, dan riwayat transaksi.
* **Manajemen Gerai:** Pengelolaan data untuk beberapa gerai penjualan (multi-outlet).

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun sebagai aplikasi *back-end* yang fokus pada pemrosesan data, logika bisnis, dan integrasi dengan basis data.

| Kategori | Teknologi | Versi / Rincian |
| :--- | :--- | :--- |
| **Framework** | **Laravel** | Laravel Framework 11.37.0 |
| **Bahasa Utama** | **PHP** | PHP 8.2.4 |
| **Basis Data** | **MySQL** | Digunakan sebagai sistem manajemen basis data utama. |

## ⚙️ Instalasi & Penyiapan

Untuk menjalankan proyek ini secara lokal, Anda perlu menyiapkan lingkungan **Laravel 11** standar.

1.  **Kloning repositori:**
    ```bash
    git clone [GANTI-DENGAN-LINK-REPOSITORI-ANDA]
    cd [NAMA-REPOSITORI-ANDA]
    ```
2.  **Instalasi dependensi (Composer & NPM):**
    ```bash
    composer install
    npm install
    ```
3.  **Konfigurasi Lingkungan:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  **Basis Data & Migrasi:**
    ```bash
    # Pastikan file .env telah dikonfigurasi dengan kredensial basis data Anda
    php artisan migrate --seed
    ```
5.  **Jalankan aplikasi:**
    ```bash
    php artisan serve
    ```
