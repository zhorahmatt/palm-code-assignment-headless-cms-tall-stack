# Headless CMS - TALL Stack

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

<p align="center">
    <strong>A modern headless CMS built with the TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire)</strong>
</p>

## 🚀 Features

- **Content Management**: Create, edit, and manage posts, pages, and categories
- **Role-Based Access Control (RBAC)**: Comprehensive permission system with roles and permissions
- **User Management**: Admin panel for managing users, roles, and permissions
- **Responsive Design**: Modern, mobile-first UI built with Tailwind CSS
- **Real-time Updates**: Interactive components powered by Livewire
- **RESTful API**: Headless architecture with API endpoints for content consumption
- **Authentication**: Secure login system with email verification
- **Content Sanitization**: Built-in content security and sanitization

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Breeze
- **API Documentation**: L5-Swagger (OpenAPI)

## 🌐 Live Demo

**Coming Soon**: Live preview will be available at [your-domain.com]

**Demo Credentials**:
- **Super Admin**: admin@example.com / password
- **Editor**: editor@example.com / password

## 📋 Prerequisites

Before running this project locally, make sure you have:

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL/SQLite database
- Git

## 🏃‍♂️ Local Installation

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd headless-cms-tall-stack
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Instrall Node Dependencies

```bash
npm install
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=headless_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migration and Seeder
```bash
php artisan migrate --seed
```

### 7. Build Assets
```bash
npm run dev
```

### 8. Start the Dev Server
```bash
php artisan serve
```
Visit http://localhost:8000 in your browser.

### 9. Access Admin Panel
- Navigate to http://localhost:8000/admin/dashboard
- Login with: admin@example.com / password


API documentation is available at /api/documentation when running the application.

To regenerate API docs:
```bash
php artisan l5-swagger:generate
```
