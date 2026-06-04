# 🛒 Point of Sale (POS) System

A full-featured web-based Point of Sale system built with **Laravel**, designed for small to medium businesses. It supports real-time sales processing, inventory management, role-based access control, report generation, and a Progressive Web App (PWA) experience for cashiers.

---

## 🌐 Live Deployment

> **[https://pos-finalproject.onrender.com/](https://pos-finalproject.onrender.com/)**

| Role     | Email                      | Password   |
|----------|----------------------------|------------|
| Admin    | admin@pointsale.com        | password   |
| Cashier  | cashier@pointsale.com      | password   |

---

## 👨‍💻 Developers

| Name | GitHub |
|------|--------|
| Khurt S. Calicdan | [@KhurtSC](https://github.com/KhurtSC) |
| Mark Allen A. Medrano | [@Aizz27](https://github.com/Aizz27) |
| King Charles Arwin C. Volante | [@codew-kicha](https://github.com/codew-kicha) |

---

## 📖 About the System

This POS system is built as a Laravel final project. It simulates a real-world point of sale environment where a **cashier** processes customer transactions and an **admin** manages the entire business — products, users, reports, and logs.

The system is divided into two main panels:

- **Admin Panel** — full control over products, categories, users, sales history, report exports, and activity monitoring.
- **Cashier Panel** — a fast, responsive POS interface for building carts, applying discounts, processing payments, and printing receipts.

---

## ✨ Features

### Authentication
- Secure login and logout with session handling
- Passwords stored with bcrypt hashing
- Role-based access: **Admin** and **Cashier**
- Admin-only user registration

### Admin Panel
- **Dashboard** — real-time KPI cards (revenue, sales count, top products) and 7-day revenue and transaction charts
- **Product Management** — full CRUD with image upload, QR label printing, CSV import, and soft delete
- **Category Management** — full CRUD for product categories
- **User Management** — create, edit, and delete admin and cashier accounts
- **Sales History** — view and filter all transactions by date or cashier; void any completed sale
- **Reports** — generate sales reports by date range, exportable as CSV, XLSX (multi-sheet), or PDF
- **Activity Logs** — a complete audit trail of every login, sale, void, edit, and system event

### Cashier Panel
- **POS Cart Screen** — search products, scan QR codes, set quantities, and build a cart in real time
- **Discounts** — apply percentage or flat-amount discounts per transaction
- **Payment Methods** — Cash, Card, GCash, or Other
- **Change Calculation** — automatic change computation from amount tendered
- **Receipt** — printable receipt with sale reference number and QR code after every transaction

### REST API
- `GET /api/products` — list available products
- `POST /api/products` — create a product
- `PUT /api/products/{id}` — update a product
- `DELETE /api/products/{id}` — delete a product
- `POST /api/sales` — process a sale (DB transaction, stock deduction, activity log, email alert)
- `GET /api/sales` — list all sales
- `GET /api/sales/{id}` — view a single sale
- `POST /api/sales/{id}/void` — void a completed sale
- `GET /api/reports` — fetch report data as JSON
- `GET /api/notifications` — fetch recent activity for real-time bell notifications

### Additional Features
- 📊 **Charts & Analytics** — Chart.js-powered revenue and transaction graphs
- 🔔 **Real-time Notifications** — browser polls every 15 seconds for new activity
- 📧 **Email Alerts** — low-stock email sent to all admins after every sale
- 🌙 **Dark Mode** — toggleable theme saved to localStorage
- 📦 **CSV Import** — bulk product import via spreadsheet upload
- 📄 **Multi-format Export** — CSV, XLSX (3-sheet workbook), PDF, JSON
- 🔍 **Search & Filters** — product search, date range filters, cashier filters
- 📝 **Activity Logs** — every system event is recorded with who, what, and when
- 📱 **PWA Support** — service worker and web manifest for offline-capable cashier screen
- 🏷️ **QR Code Generation** — printable product labels and receipt QR codes

---

## 🗄️ Database Design

| Table | Description |
|-------|-------------|
| `users` | Admins and cashiers with role column |
| `categories` | Product categories |
| `products` | Products with stock, price, image, and soft delete |
| `sales` | Transaction records with payment details and status |
| `sale_items` | Individual line items per sale (snapshot of price at time of purchase) |
| `activity_logs` | Full audit log of all system events |

### Relationships
- `Category` → has many → `Products`
- `Product` → has many → `SaleItems`
- `Sale` → has many → `SaleItems`
- `Sale` → belongs to → `User` (cashier)
- `SaleItem` → belongs to → `Product` (with soft delete support)

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.x + Laravel 11 |
| Frontend | Blade Templates + Tailwind CSS |
| Database | MySQL / SQLite |
| Charts | Chart.js |
| QR Codes | `simplesoftwareio/simple-qrcode` |
| Excel Export | `phpoffice/phpspreadsheet` |
| PDF Export | `barryvdh/laravel-dompdf` |
| Version Control | Git + GitHub |
| Deployment | Render |

---

## 🚀 Installation & Setup

### Requirements
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL or SQLite

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/Aizz27/POS_finalproject.git
cd POS_finalproject

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file and configure
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pos_db
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations and seed the database
php artisan migrate --seed

# 8. Link storage for product images
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Start the development server
php artisan serve
```

Then visit `http://localhost:8000` and log in with:
- **Admin:** `admin@pointsale.com` / `password`
- **Cashier:** `cashier@pointsale.com` / `password`

---

## 📁 Project Structure (Custom Code)

```
app/
├── Exports/
│   └── ReportExport.php          # XLSX multi-sheet export builder
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                # Dashboard, Products, Categories, Users, Sales, Reports, Logs
│   │   ├── Api/                  # REST API — Products, Sales, Reports, Notifications
│   │   ├── Auth/                 # Login, logout, registration
│   │   └── Cashier/              # POS cart screen, cashier dashboard, receipt
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── CashierMiddleware.php
├── Models/                       # User, Category, Product, Sale, SaleItem, ActivityLog
└── Services/
    └── ActivityLogger.php        # Centralized event logging service

database/
├── migrations/                   # 6 custom tables
└── seeders/DatabaseSeeder.php    # Demo users and products

resources/views/
├── admin/                        # All admin panel pages
├── cashier/                      # POS screen, dashboard, receipt
└── components/                   # Layout, sidebar, topbar, alert, modal

public/
├── manifest.webmanifest          # PWA manifest
└── service-worker.js             # Offline caching

routes/
├── web.php                       # Web routes with role middleware groups
└── api.php                       # REST API routes
```

---

## 📜 License

This project was developed as a **Final Project** for a Laravel Web Development course. All rights reserved by the developers.