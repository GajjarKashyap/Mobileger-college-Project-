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

#`/opt/lampp/htdocs/mobilegear`
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

#
