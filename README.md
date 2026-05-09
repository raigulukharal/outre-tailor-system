# 🧵 OUTRE Tailor Management System

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Tailwind](https://img.shields.io/badge/Tailwind-3.x-06B6D4.svg)](https://tailwindcss.com)

## 📌 About

**OUTRE Tailor System** is a complete production-ready web application for tailor shops to manage orders, deliveries, and customer data efficiently. Built with Laravel 13, Tailwind CSS, and AJAX.

**Live Demo:** [https://outre.online](https://software.outre.online)

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 📝 **Order Management** | Create, update, delete orders (AJAX - No reload) |
| 🔍 **Live Search** | Instant search by name, phone, serial number |
| 📅 **Reminder System** | 1-day before delivery reminders with Print/PDF/WhatsApp |
| ✅ **Auto Status** | Orders auto-complete when delivery date passes |
| 📊 **Dashboard** | Real-time stats with active orders |
| 📱 **Responsive** | Works on mobile, tablet, desktop |

---

## 🔧 Installation

```bash
# 1. Clone repository
git clone https://github.com/your-username/outre-tailor-system.git
cd outre-tailor-system

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup (Update .env first)
php artisan migrate --seed

# 5. Compile assets
npm run build

# 6. Run server
php artisan serve
