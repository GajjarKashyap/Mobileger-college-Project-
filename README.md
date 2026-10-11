# 📱 MobileGear — Mobile Accessories E-Commerce Platform

A complete, full-stack **E-Commerce Web Application** built for online mobile accessories retail. Designed as an academic/college capstone project featuring secure authentication, catalog browsing, category filtering, session-driven shopping cart, checkout flow, customer order management, and a dedicated admin portal with sales analytics.

---

## 📌 Project Overview

**MobileGear** provides an end-to-end shopping experience for mobile accessories (back covers, skins, chargers, power banks, audio, smart watches, and Bluetooth speakers). The system adheres to standard MVC-style clean procedural and object-oriented PHP paradigms with PDO prepared statements for high security against SQL injection.

- **Frontend**: Clean, responsive UI with modern CSS variables, responsive grid/flexbox layouts, and dynamic client-side interactions.
- **Backend**: Native PHP using PDO (PHP Data Objects), password hashing (`bcrypt`), CSRF protection, and session-based state management.
- **Database**: Relational MariaDB/MySQL database with full referential integrity and cascading deletes.

---

## 🚀 Key Features

### 🛒 Customer Facing
- **Dynamic Catalog & Filtering**: Browse products by category (Back Covers, Skins, Chargers, Power Banks, Audio, Speakers, Smart Watches) with brand and price metadata.
- **Product Details**: Comprehensive product view with specifications, high-res images, pricing in INR (₹), and instant "Add to Cart".
- **Cart Management**: Real-time session cart (`$_SESSION['cart']`) with quantity update, item removal, and auto-computed subtotals and grand totals.
- **Checkout & Address Validation**: Multi-field delivery address capture with Indian state selector, district, and 6-digit postal code validation.
- **User Authentication**:
  - Secure Registration with validation.
  - Login with bcrypt password verification (`password_verify`).
  - Session security and Post-Redirect-Get (PRG) patterns.
- **Customer Profile**: User dashboard showing account details and complete order history with real-time status badges (*Pending*, *Completed*, *Cancelled*).

### ⚙️ Admin Portal (`/admin`)
- **Dashboard Overview**: Key metrics including Total Orders, Total Sales (from completed orders), and Total Registered Customers.
- **Product Catalog Management**: View full inventory and add new products with category, brand, description, price, and image URL.
- **Order Management**: Monitor customer orders, view purchased items, and update order statuses.
- **Customer Directory**: View registered customer profiles and contact details.

---

## 🛠️ Technology Stack

| Component | Technology |
|---|---|
| **Backend Language** | PHP (7.4+ or 8.x) |
| **Database** | MySQL / MariaDB (InnoDB Engine) |
| **Database Driver** | PHP Data Objects (PDO) with prepared statements |
| **Frontend** | HTML5, CSS3 (Modern Flexbox & CSS Grid), Vanilla JavaScript |
| **Local Server** | Apache (XAMPP / LAMPP / WAMP / MAMP) |
| **Architecture** | Relational Database, RESTful/PRG flows, Session-based State |

---

## 📂 Project Directory Structure

```text
mobilegear/
├── admin/                     # Admin control panel
│   ├── customers.php          # View registered customer list
│   ├── index.php              # Admin dashboard with KPI metrics
│   ├── orders.php             # Order review and status updates
│   ├── product-add.php        # Form to add new products to inventory
│   └── products.php           # Inventory product listing & management
├── assets/                    # Static assets
│   ├── css/
│   │   └── style.css          # Core stylesheet (responsive styling & variables)
│   ├── images/                # Local product and banner image storage
│   └── js/
│       └── main.js            # Client-side JavaScript interactions
├── includes/                  # Reusable backend components & modules
│   ├── bootstrap.php          # Session initializer & core dependency loader
│   ├── database.php           # PDO database connection handler
│   ├── footer.php             # Global HTML footer component
│   ├── functions.php          # Helper utilities (sanitization, formatters, queries)
│   ├── header.php             # Global navigation bar & header component
│   └── product-card.php       # Reusable product card component
├── cart.php                   # Shopping cart page
├── checkout.php               # Order placement & shipping address page
├── database.sql               # Complete MySQL database schema & sample data
├── index.php                  # Landing page (hero banner & featured products)
├── login.php                  # Customer & Admin login page
├── logout.php                 # Session termination & logout handler
├── order-success.php          # Order confirmation & receipt display
├── product.php                # Single product detail view
├── profile.php                # Customer profile & past orders
├── register.php               # Customer registration with address validation
├── shop.php                   # Product catalog with category filter
├── CORE_PAGES_CODE.md         # Source code documentation for core pages
├── DATABASE_DOCUMENTATION.md  # Database architecture & ER diagram documentation
├── PPT_BACKEND_CODE.md        # Presentation-ready backend slides & viva notes
└── README.md                  # Project setup and user guide
```

---

## ⚙️ Installation & Setup Guide

### 1. Prerequisites
Ensure you have the following installed on your machine:
- [XAMPP](https://www.apachefriends.org/) (or LAMPP / WAMP / MAMP) with:
  - **Apache** web server
  - **PHP** 7.4, 8.0, 8.1, or 8.2+
  - **MySQL / MariaDB**

---

### 2. Place Project in Server Root
Move or clone the project directory into your web server's root folder:

- **Windows (XAMPP)**: `C:\xampp\htdocs\mobilegear`
- **Linux (LAMPP)**: `/opt/lampp/htdocs/mobilegear`
- **macOS (XAMPP/MAMP)**: `/Applications/XAMPP/xamppfiles/htdocs/mobilegear` or `/Applications/MAMP/htdocs/mobilegear`

```bash
# Example using Git:
git clone https://github.com/GajjarKashyap/Mobileger-college-Project-.git /opt/lampp/htdocs/mobilegear
```

---

### 3. Start Apache & MySQL
Open your XAMPP/LAMPP Control Panel and start both **Apache** and **MySQL** services:

```bash
# On Linux LAMPP via terminal (optional):
sudo /opt/lampp/lampp start
```

---

### 4. Import the Database (`database.sql`)

The repository includes a ready-to-run database export: `database.sql`.

#### Option A: Via phpMyAdmin (Recommended)
1. Open your web browser and go to: `http://localhost/phpmyadmin/`
2. Click on the **Import** tab in the top navigation bar.
3. Click **Browse / Choose File** and select `database.sql` from the `mobilegear/` folder.
4. Click **Import** (or **Go**) at the bottom.
5. The database `mobilegear` and all four tables (`users`, `products`, `orders`, `order_items`) with pre-seeded products will be automatically created.

#### Option B: Via Command Line / Terminal
```bash
# Run using MySQL CLI:
mysql -u root -p < /opt/lampp/htdocs/mobilegear/database.sql
# (Press Enter when prompted for password if using default empty root password)
```

---

### 5. Verify Database Configuration
Check `includes/database.php` to verify your local MySQL credentials:

```php
<?php
// includes/database.php
$host = "127.0.0.1";
$username = "root";       // Default XAMPP username is "root"
$password = "";           // Default XAMPP password is empty ""
$dbname = "mobilegear";   // Database name
?>
```

> **Note**: If your MySQL server has a password (e.g., in WAMP or production), update `$password = "your_password";` accordingly.

---

### 6. Run the Application

Open your browser and navigate to:

- 🛍️ **Customer Storefront**:
  ```text
  http://localhost/mobilegear/
  ```

- 🛡️ **Admin Dashboard**:
  ```text
  http://localhost/mobilegear/admin/
  ```

---

## 🔑 Default Login Credentials

The pre-seeded database contains both an administrator and a test customer account:

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Administrator** | `admin@mobilegear.com` | `password` | Full Admin Dashboard & Storefront |
| **Customer** | `abc@123.gmail.com` | `password` | Storefront, Cart, Checkout, Order History |

> You can also register a brand new customer account at `http://localhost/mobilegear/register.php`.

---

## 🗄️ Database Architecture & Schema

The database consists of 4 relational tables:

```text
+-------------------+       1:N       +-------------------+
|       USERS       | --------------> |      ORDERS       |
+-------------------+                 +-------------------+
                                                | 1:N
                                                v
+-------------------+       1:N       +-------------------+
|     PRODUCTS      | --------------> |    ORDER_ITEMS    |
+-------------------+                 +-------------------+
```

- **`users`**: Customer profiles and admin accounts (`role` enum: `'customer'`, `'admin'`). Stores hashed passwords using PHP `password_hash()` (bcrypt).
- **`products`**: Product inventory containing title, category, brand, description, price, and image URLs.
- **`orders`**: Customer orders with total price, order status (`'pending'`, `'completed'`, `'cancelled'`), delivery address snapshot, and timestamps.
- **`order_items`**: Line items linked to each order with quantity and captured purchase price (`ON DELETE CASCADE`).

---

## 📚 College Evaluation & Viva Documentation

The repository includes dedicated Markdown documentation to help with project reports, viva preparation, and presentations:

- 📄 [`DATABASE_DOCUMENTATION.md`](DATABASE_DOCUMENTATION.md) — Comprehensive database dictionary, constraint analysis, and Mermaid ER diagram.
- 📄 [`CORE_PAGES_CODE.md`](CORE_PAGES_CODE.md) — In-depth walkthrough of key customer journeys and architecture.
- 📄 [`PPT_BACKEND_CODE.md`](PPT_BACKEND_CODE.md) — Presentation-ready code slides (zero-CSS backend logic) with examiner talking points.

---

## 🔒 Security Best Practices Implemented

- **Prepared Statements (PDO)**: Complete immunity against SQL Injection for all user-supplied inputs.
- **Bcrypt Hashing**: Passwords stored using industry-standard `password_hash(PASSWORD_BCRYPT)`.
- **Cross-Site Scripting (XSS) Prevention**: Output sanitization using `htmlspecialchars()`.
- **Role-Based Access Control (RBAC)**: Restricted `/admin` area requiring authenticated session with `'admin'` role.
- **Foreign Key Constraints**: Data integrity guaranteed at the engine level with cascading deletes.

---

## 👨‍💻 Author

- **Developer**: Gajjar Kashyap
- **GitHub**: [@GajjarKashyap](https://github.com/GajjarKashyap)
- **Repository**: [Mobileger-college-Project-](https://github.com/GajjarKashyap/Mobileger-college-Project-.git)
