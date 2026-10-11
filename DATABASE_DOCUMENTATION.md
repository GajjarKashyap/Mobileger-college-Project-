# MobileGear — Database Architecture & ER Diagram Documentation

This document outlines the complete relational database design, data dictionary, integrity constraints, and Entity-Relationship (ER) diagram for the **MobileGear** mobile accessories e-commerce platform.

---

## 1. Entity-Relationship (ER) Diagram

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "places (1:N)"
    ORDERS ||--|{ ORDER_ITEMS : "contains (1:N)"
    PRODUCTS ||--o{ ORDER_ITEMS : "referenced_in (1:N)"

    USERS {
        int id PK "Auto Increment"
        string full_name "Customer full name"
        string email UK "Unique login email"
        string phone "10-digit mobile number"
        text address "Street/Flat address"
        string district "City or District"
        string state "Indian State"
        string pincode "6-digit postal code"
        string password "Bcrypt hashed password"
        string role "customer or admin"
        timestamp created_at "Registration date"
    }

    PRODUCTS {
        int id PK "Auto Increment"
        string name "Product title"
        string category "Accessory category"
        string brand "Manufacturer brand"
        text description "Detailed description"
        decimal price "Price in INR"
        string image_url "CDN Image URL"
        timestamp created_at "Creation timestamp"
    }

    ORDERS {
        int id PK "Auto Increment"
        int user_id FK "References USERS.id"
        decimal total_amount "Total order value (INR)"
        string status "pending, completed, cancelled"
        text shipping_address "Snapshot delivery address"
        timestamp created_at "Order placement date"
    }

    ORDER_ITEMS {
        int id PK "Auto Increment"
        int order_id FK "References ORDERS.id"
        int product_id FK "References PRODUCTS.id"
        int quantity "Units purchased"
        decimal price "Historical unit price (INR)"
    }
```

---

## 2. Table Specifications & Data Dictionary

### Table 1: `users`
Stores customer and administrator profile details, authentication credentials, and saved shipping destinations.

| Column Name | Data Type | Nullable | Key | Default | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | No | **PK** | `AUTO_INCREMENT` | Unique identifier for each account |
| `full_name` | `VARCHAR(100)` | No | | `None` | Full name of the user |
| `email` | `VARCHAR(100)` | No | **UK** | `None` | Unique email used for authentication |
| `phone` | `VARCHAR(20)` | Yes | | `NULL` | 10-digit primary mobile contact number |
| `address` | `TEXT` | Yes | | `NULL` | Flat / House No., Building, Street |
| `district` | `VARCHAR(100)` | Yes | | `NULL` | City / District name |
| `state` | `VARCHAR(100)` | Yes | | `NULL` | Indian State / Union Territory |
| `pincode` | `VARCHAR(10)` | Yes | | `NULL` | 6-digit postal PIN code |
| `password` | `VARCHAR(255)` | No | | `None` | Secure Bcrypt-hashed password string |
| `role` | `ENUM('customer','admin')` | Yes | | `'customer'` | Access control level |
| `created_at` | `TIMESTAMP` | No | | `CURRENT_TIMESTAMP` | Account creation timestamp |

---

### Table 2: `products`
Houses all 46 curated mobile accessories with category classification, brand compatibility, pricing, and CDN imagery.

| Column Name | Data Type | Nullable | Key | Default | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | No | **PK** | `AUTO_INCREMENT` | Unique product ID |
| `name` | `VARCHAR(200)` | No | | `None` | Display title of the accessory |
| `category` | `VARCHAR(50)` | No | | `None` | e.g., Back Cover, Skin, Charger, Power Bank, Audio, Speaker, Smart Watch |
| `brand` | `VARCHAR(50)` | No | | `None` | e.g., Apple, Samsung, Spigen, dbrand, Anker, JBL, Sony, boAt, Titan |
| `description` | `TEXT` | No | | `None` | Product specifications, build quality, and device compatibility |
| `price` | `DECIMAL(10,2)` | No | | `None` | Retail price in INR (Strictly under ₹15,000) |
| `image_url` | `VARCHAR(255)` | Yes | | `'placeholder.png'` | Direct CDN image URL |
| `created_at` | `TIMESTAMP` | No | | `CURRENT_TIMESTAMP` | Catalog entry timestamp |

---

### Table 3: `orders`
Tracks customer orders, snapshot shipping addresses, billing totals, and fulfillment status.

| Column Name | Data Type | Nullable | Key | Default | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | No | **PK** | `AUTO_INCREMENT` | Unique order reference number |
| `user_id` | `INT(11)` | No | **FK** | `None` | ID of the customer who placed the order (`users.id`) |
| `total_amount` | `DECIMAL(10,2)` | No | | `None` | Total billable amount in INR |
| `status` | `ENUM('pending','completed','cancelled')` | Yes | | `'pending'` | Current fulfillment stage |
| `shipping_address` | `TEXT` | No | | `None` | Complete destination address captured at checkout |
| `created_at` | `TIMESTAMP` | No | | `CURRENT_TIMESTAMP` | Order timestamp |

---

### Table 4: `order_items`
Bridge table capturing the individual products and quantities included in an order, preserving historical purchase pricing.

| Column Name | Data Type | Nullable | Key | Default | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | No | **PK** | `AUTO_INCREMENT` | Unique line-item ID |
| `order_id` | `INT(11)` | No | **FK** | `None` | References `orders.id` (Parent order) |
| `product_id` | `INT(11)` | No | **FK** | `None` | References `products.id` (Purchased item) |
| `quantity` | `INT(11)` | No | | `None` | Units ordered (1 to 10) |
| `price` | `DECIMAL(10,2)` | No | | `None` | Locked-in unit price at time of purchase in INR |

---

## 3. Relational Constraints & Cascade Rules

| Constraint Name | Source Table | Foreign Key | Target Table | Target Key | Cascade Rule |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `fk_orders_user` | `orders` | `user_id` | `users` | `id` | `ON DELETE CASCADE` |
| `fk_orderitems_order` | `order_items` | `order_id` | `orders` | `id` | `ON DELETE CASCADE` |
| `fk_orderitems_product` | `order_items` | `product_id` | `products` | `id` | `ON DELETE CASCADE` |

---

## 4. Key Architectural Highlights

1. **Normalized Design (3NF)**: Redundancy is minimized while maintaining complete relational consistency between users, catalog items, and cart transactions.
2. **Indian Address Integration**: The `users` table accommodates full Indian postal logistics (`phone`, `address`, `district`, `state`, `pincode`) enabling Amazon-style 1-click checkout prefilling.
3. **Decoupled Pricing**: Decoupled `price` columns in `products` vs `order_items` ensure that seasonal sales or updates never distort historical receipts.
