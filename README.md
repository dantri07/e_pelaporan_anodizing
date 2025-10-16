# e-pelaporan-anodizing

Aplikasi **e-pelaporan-anodizing** adalah sistem pelaporan berbasis web untuk proses anodizing, dibangun menggunakan Laravel.

---

## 🇮🇩 Instruksi Setup

1. **Clone repository**
   ```bash
   git clone <repo-url>
   cd e-pelaporan-anodizing
   ```
2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Copy file environment**
   ```bash
   cp .env.example .env
   ```
4. **Generate application key**
   ```bash
   php artisan key:generate
   ```
5. **Atur konfigurasi database**
   Edit file `.env` dan sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai database Anda.
6. **Jalankan migrasi & seeder**
   ```bash
   php artisan migrate --seed
   ```
7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

### Login Default

| Role       | Username     | Email                  | Password  |
|------------|-------------|------------------------|-----------|
| Admin      | admin       | admin@example.com      | password  |
| Operator   | operator    | operator@example.com   | password  |
| Technician | technician  | technician@example.com | password  |

---

## 🇬🇧 Setup Instructions

The **e-pelaporan-anodizing** app is a web-based reporting system for the anodizing process, built with Laravel.

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd e-pelaporan-anodizing
   ```
2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Copy environment file**
   ```bash
   cp .env.example .env
   ```
4. **Generate application key**
   ```bash
   php artisan key:generate
   ```
5. **Configure your database**
   Edit the `.env` file and set DB_DATABASE, DB_USERNAME, DB_PASSWORD according to your database.
6. **Run migration & seeder**
   ```bash
   php artisan migrate --seed
   ```
7. **Run the application**
   ```bash
   php artisan serve
   ```

### Default Login

| Role       | Username     | Email                  | Password  |
|------------|-------------|------------------------|-----------|
| Admin      | admin       | admin@example.com      | password  |
| Operator   | operator    | operator@example.com   | password  |
| Technician | technician  | technician@example.com | password  |
