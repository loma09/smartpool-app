# Smartpool App

Aplikasi berbasis Laravel untuk manajemen Smartpool.

## Persyaratan Sistem

Sebelum memulai, pastikan komputer Anda sudah terinstal:
- PHP >= 8.2 (atau sesuaikan dengan versi Laravel proyek ini)
- [Composer](https://getcomposer.org)
- Database Server ([Laragon](https://laragon.org) / XAMPP / MySQL)

## Panduan Instalasi & Setup

Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan proyek di komputer lokal Anda:

### 1. Clone atau Ekstrak Repositori
Jika Anda mengunduh dalam bentuk `.zip`, ekstrak terlebih dahulu. Jika menggunakan Git, jalankan:
```bash
git clone https://github.com
cd smartpool-app
```

### 2. Salin File Environment (`.env`)
Salin file konfigurasi `.env.example` menjadi `.env`:
- **Windows (PowerShell/CMD):**
  ```powershell
  copy .env.example .env
  ```
- **Linux / Mac / Git Bash:**
  ```bash
  cp .env.example .env
  ```

### 3. Konfigurasi Database
Buka file `.env` yang baru dibuat menggunakan Text Editor (seperti VS Code), lalu cari dan sesuaikan bagian konfigurasi database berikut dengan server lokal Anda (misalnya Laragon/XAMPP):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```
*Catatan: Pastikan Anda sudah membuat database kosong dengan nama yang sama seperti di `DB_DATABASE` melalui phpMyAdmin.*

### 4. Bersihkan Cache & Install Dependensi
Untuk menghindari error penguncian file (*Resource temporarily unavailable*) di Windows, bersihkan cache terlebih dahulu lalu install dependensi menggunakan mode aman:
```powershell
composer clear-cache
composer install --no-scripts
```

### 5. Generate Autoload & Application Key
Setelah instalasi selesai, buat file *autoload* dan kunci keamanan aplikasi:
```powershell
composer dump-autoload
php artisan key:generate
```

### 6. Jalankan Database Migration
Jalankan perintah ini untuk membuat tabel-tabel database yang diperlukan aplikasi:
```powershell
php artisan migrate
```
*(Opsional: Jika ada data bawaan/dummy, jalankan `php artisan migrate --seed`)*

### 7. Jalankan Server Lokal
Aplikasi sekarang siap digunakan. Nyalakan server lokal dengan perintah:
```powershell
php artisan serve
```
Buka browser Anda dan akses aplikasi melalui tautan: **http://localhost:8000**



<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
