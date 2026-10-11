# MobileGear — Presentation-Ready Backend Code (PPT Slides Format)

This document contains the clean, production-grade **backend PHP logic** and **semantic HTML markup** for the 6 core pages of the MobileGear e-commerce platform.

> **Designed for PPT Presentation Slides**:
> - **Zero CSS / Styling Bloat**: All inline styles (`style="..."`), CSS stylesheets, and design SVGs have been stripped out.
> - **Focus on Engineering Concepts**: Demonstrates PDO prepared statements, input sanitization, password hashing (`bcrypt`), ACID transactions, session state handling, and the Post-Redirect-Get (PRG) pattern.
> - **Slide-by-Slide Layout**: Formatted with slide titles, purpose summaries, clean code blocks, and presenter talking points for evaluations or viva.

---

## Slide 1: System Architecture & Database Foundation

### Backend Database Connector (`includes/bootstrap.php`)
```php
<?php
// Initialize session and define database credentials
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1';
$db   = 'mobilegear_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/functions.php';
```

### Essential Helper Functions (`includes/functions.php`)
```php
<?php
// Sanitize user inputs to prevent XSS attacks
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Indian Rupee currency formatter
function format_price($price) {
    return '₹' . number_format($price, 2);
}

// Fetch single product by ID (Parameterized Query)
function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

// Fetch products with optional limit
function get_all_products($conn, $limit = null) {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    return $conn->query($sql)->fetchAll();
}
```

> **Presenter Talking Points**:
> - Uses **PDO** (PHP Data Objects) instead of legacy `mysqli_query` for superior security and database independence.
> - `PDO::ATTR_EMULATE_PREPARES => false` ensures true server-side parameterized queries, completely immune to SQL injection.

---

## Slide 2: Home Page (`index.php`)

### Purpose
Fetches featured products dynamically from MySQL, initializes global dependencies, and renders dynamic product cards.

### Backend PHP & View Markup
```php
<?php
require_once 'includes/bootstrap.php';
require_once 'includes/header.php';

// Fetch top 8 featured flagship accessories from MySQL
$featured_products = get_all_products($conn, 8);
?>

<main class="container">
    <section class="hero-banner">
        <h1>Elevate Your Flagship Phone Experience</h1>
        <p>Curated accessories built specifically for iPhone 18 Pro Max and Galaxy S26 Ultra.</p>
        <a href="shop.php">Shop Full Catalog</a>
        <a href="shop.php?category=Smart+Watch">Explore Smartwatches</a>
    </section>

    <section class="featured-arrivals">
        <h2>Featured Arrivals</h2>
        <div class="product-grid">
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= format_price($product['price']) ?></p>
                    <a href="product.php?id=<?= $product['id'] ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
```

> **Presenter Talking Points**:
> - Dynamic backend query loads fresh product inventory directly from MySQL without hardcoded HTML.
> - `htmlspecialchars()` protects against Cross-Site Scripting (XSS) when outputting dynamic database fields.

---

## Slide 3: Login Page (`login.php`)

### Purpose
Authenticates users using parameterized SQL queries, verifies hashed passwords with bcrypt, initializes session state, and redirects safely.

### Backend PHP Controller Logic
```php
<?php
require_once 'includes/bootstrap.php';

// Redirect user if session already exists
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    // Parameterized prepared query to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Cryptographic verification of bcrypt hash
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Smart redirect to requested checkout or default home
        $redirect = isset($_GET['redirect']) ? sanitize_input($_GET['redirect']) : 'index.php';
        header("Location: " . $redirect);
        exit;
    } else {
        $error = "Invalid email or password. Please verify your credentials.";
    }
}
?>
```

### Clean Form View Markup
```html
<main class="auth-container">
    <h2>Sign In to MobileGear</h2>

    <?php if(isset($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Sign In to Account</button>
    </form>

    <p>New customer? <a href="register.php">Create Your Customer Account</a></p>
</main>
```

> **Presenter Talking Points**:
> - Never stores or compares plain text passwords; uses PHP's native `password_verify()` matching bcrypt hashes.
> - Smart routing: Users attempting to checkout are redirected back to `checkout.php` after logging in via `?redirect=checkout.php`.

---

## Slide 4: Registration Page (`register.php`)

### Purpose
Captures customer profiles with Indian postal validation (district, state, 6-digit PIN, 10-digit mobile), validates uniqueness, hashes passwords, and auto-logs in the user.

### Backend PHP Controller Logic
```php
<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize_input($_POST['name']);
    $email    = sanitize_input($_POST['email']);
    $phone    = sanitize_input($_POST['phone']);
    $address  = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $state    = sanitize_input($_POST['state']);
    $pincode  = sanitize_input($_POST['pincode']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    
    // Server-side validation
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $error = "Please enter a valid 10-digit Indian mobile number.";
    } elseif (!empty($pincode) && !preg_match('/^[0-9]{6}$/', trim($pincode))) {
        $error = "Please enter a valid 6-digit postal PIN code.";
    } else {
        // Check for duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email address already exists.";
        } else {
            // Hash password using default algorithm (bcrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, address, district, state, pincode, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $address, $district, $state, $pincode, $hashed_password])) {
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed due to a server error.";
            }
        }
    }
}
?>
```

### Clean Form View Markup
```html
<main class="register-container">
    <h2>Create Customer Account</h2>

    <?php if(isset($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label>Full Name *</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <label>Mobile Number (10 Digits) *</label>
        <input type="tel" name="phone" pattern="[0-9]{10}" maxlength="10" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

        <label>Email Address *</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Password (Min. 6 chars) *</label>
        <input type="password" name="password" minlength="6" required>

        <label>Confirm Password *</label>
        <input type="password" name="confirm_password" minlength="6" required>

        <fieldset>
            <legend>Delivery Address Details (Amazon 1-Click Fast Checkout)</legend>

            <label>Flat, House No., Building, Street *</label>
            <textarea name="address" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>

            <label>District / City *</label>
            <input type="text" name="district" required value="<?= htmlspecialchars($_POST['district'] ?? '') ?>">

            <label>State *</label>
            <select name="state" required>
                <option value="">Select State</option>
                <?php foreach($indian_states as $st): ?>
                    <option value="<?= $st ?>" <?= (isset($_POST['state']) && $_POST['state'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>

            <label>PIN Code (6 Digits) *</label>
            <input type="text" name="pincode" pattern="[0-9]{6}" maxlength="6" required value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>">
        </fieldset>

        <button type="submit">Create Account & Start Shopping</button>
    </form>
</main>
```

> **Presenter Talking Points**:
> - Enforces regex checks for Indian postal standards (`/^[0-9]{10}$/` for mobile, `/^[0-9]{6}$/` for PIN).
> - Auto-initiates session immediately upon successful insertion (`$conn->lastInsertId()`), removing friction for new buyers.

---

## Slide 5: Shopping Cart Page (`cart.php`)

### Purpose
High-performance in-memory session cart management (`$_SESSION['cart']`), processing `add`, `update`, and `remove` actions with the Post-Redirect-Get (PRG) pattern.

### Backend PHP Controller Logic
```php
<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // 1. Add item to cart
    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $product = get_product_by_id($conn, $product_id);
        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image_url'],
                    'quantity' => $quantity
                ];
            }
        }
    // 2. Update quantities in batch
    } elseif ($action === 'update') {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $product_id => $qty) {
                $qty = (int)$qty;
                if ($qty > 0 && isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] = $qty;
                }
            }
        }
    // 3. Remove item from cart
    } elseif ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    
    // Post-Redirect-Get (PRG) pattern to prevent duplicate form reposts
    header("Location: cart.php");
    exit;
}

$total = 0;
?>
```

### Clean Form View Markup
```html
<main class="cart-container">
    <h2>Shopping Cart</h2>

    <?php if(empty($_SESSION['cart'])): ?>
        <p>Your cart is empty. <a href="shop.php">Discover accessories</a></p>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="update">
            
            <table>
                <thead>
                    <tr>
                        <th>Item Details</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['cart'] as $id => $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= format_price($item['price']) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" max="10">
                            </td>
                            <td><?= format_price($subtotal) ?></td>
                            <td>
                                <button type="submit" form="remove_<?= $id ?>">✕</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="shop.php">Continue Shopping</a>
            <button type="submit">Update Quantities</button>
        </form>

        <section class="order-summary">
            <h3>Order Summary</h3>
            <p>Subtotal (<?= count($_SESSION['cart']) ?> items): <?= format_price($total) ?></p>
            <p>Shipping: <strong>FREE</strong></p>
            <p><strong>Total Amount: <?= format_price($total) ?></strong></p>
            <a href="checkout.php">Proceed to Checkout &rarr;</a>
        </section>

        <!-- Dedicated hidden removal forms -->
        <?php foreach($_SESSION['cart'] as $id => $item): ?>
            <form id="remove_<?= $id ?>" action="cart.php" method="POST" style="display:none;">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= $id ?>">
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
```

> **Presenter Talking Points**:
> - Session storage provides instant cart operations without unnecessary database write queries before an order is confirmed.
> - The **PRG Pattern** (`header("Location: cart.php"); exit;`) eliminates the browser's "Confirm Form Resubmission" alert on page reloads.

---

## Slide 6: Customer Profile Page (`profile.php`)

### Purpose
Enables customers to maintain saved shipping addresses and phones for Amazon 1-Click checkout, and reviews past orders and status.

### Backend PHP Controller Logic
```php
<?php
require_once 'includes/bootstrap.php';

// Authentication guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle Profile & Address Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name     = sanitize_input($_POST['name']);
    $phone    = sanitize_input($_POST['phone']);
    $address  = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $state    = sanitize_input($_POST['state']);
    $pincode  = sanitize_input($_POST['pincode']);

    if (empty($name)) {
        $error_msg = "Full name cannot be empty.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $error_msg = "Please enter a valid 10-digit mobile number.";
    } elseif (!empty($pincode) && !preg_match('/^[0-9]{6}$/', trim($pincode))) {
        $error_msg = "Please enter a valid 6-digit PIN code.";
    } else {
        $stmt_update = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, district = ?, state = ?, pincode = ? WHERE id = ?");
        if ($stmt_update->execute([$name, $phone, $address, $district, $state, $pincode, $user_id])) {
            $_SESSION['user_name'] = $name;
            $success_msg = "Your delivery address & personal details have been updated successfully!";
        } else {
            $error_msg = "Failed to update profile. Please try again.";
        }
    }
}

// Fetch fresh user profile details
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

// Fetch customer's previous order history
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();
?>
```

### Clean Form & Table View Markup
```html
<main class="profile-container">
    <h2>Customer Profile</h2>

    <?php if($success_msg): ?><p class="alert success"><?= $success_msg ?></p><?php endif; ?>
    <?php if($error_msg): ?><p class="alert error"><?= $error_msg ?></p><?php endif; ?>

    <!-- Address & Phone Update Form -->
    <section class="profile-form">
        <h3>Saved Delivery Address & Phone</h3>
        <form action="profile.php" method="POST">
            <input type="hidden" name="action" value="update_profile">

            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label>Mobile Number</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" pattern="[0-9]{10}" maxlength="10">

            <label>Email Address</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

            <label>Street / Flat / House No.</label>
            <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

            <label>District / City</label>
            <input type="text" name="district" value="<?= htmlspecialchars($user['district'] ?? '') ?>">

            <label>State</label>
            <select name="state">
                <option value="">Select State</option>
                <?php foreach($indian_states as $st): ?>
                    <option value="<?= $st ?>" <?= ($user['state'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>

            <label>PIN Code</label>
            <input type="text" name="pincode" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>" pattern="[0-9]{6}" maxlength="6">

            <button type="submit">Save & Update Address</button>
        </form>
    </section>

    <!-- Order History Table -->
    <section class="order-history">
        <h3>Your Order History (<?= count($orders) ?> orders)</h3>
        <?php if(empty($orders)): ?>
            <p>No orders placed yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date Placed</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Shipping Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td><?= format_price($order['total_amount']) ?></td>
                            <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
```

> **Presenter Talking Points**:
> - Provides two-way profile synchronization: Updates write to MySQL and immediately update active session variables (`$_SESSION['user_name']`).
> - Orders are retrieved relationally by matching `orders.user_id = users.id`.

---

## Slide 7: Checkout Page (`checkout.php`)

### Purpose
Validates session cart & user credentials, pre-fills address from profile, and commits orders inside an **ACID database transaction**.

### Backend PHP Controller Logic
```php
<?php
require_once 'includes/bootstrap.php';

// Verification guards
if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Prefill saved delivery address from customer profile
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user_data = $user_stmt->fetch();

$parts = [
    $user_data['address'] ?? '',
    $user_data['district'] ?? '',
    $user_data['state'] ?? '',
    !empty($user_data['pincode']) ? 'PIN: ' . $user_data['pincode'] : '',
    !empty($user_data['phone']) ? 'Mobile: ' . $user_data['phone'] : ''
];
$prefilled_address = implode(', ', array_filter(array_map('trim', $parts)));

// Calculate cart total
$total = 0;
foreach($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Order placement using atomic ACID database transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = sanitize_input($_POST['address']);
    
    if ($address) {
        try {
            $conn->beginTransaction();
            
            // Step 1: Insert Master Order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
            $stmt->execute([$_SESSION['user_id'], $total, $address]);
            $order_id = $conn->lastInsertId();
            
            // Step 2: Insert individual order line items
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt_item->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }
            
            // Step 3: Commit transaction atomically
            $conn->commit();
            
            // Step 4: Clear cart session
            $_SESSION['cart'] = [];
            
            header("Location: order-success.php?id=" . $order_id);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Failed to place order. Transaction rolled back.";
        }
    } else {
        $error = "Please provide a shipping address.";
    }
}
?>
```

### Clean Form View Markup
```html
<main class="checkout-container">
    <h2>Secure 1-Click Checkout</h2>

    <?php if(isset($error)): ?>
        <p class="alert error"><?= $error ?></p>
    <?php endif; ?>

    <form action="checkout.php" method="POST">
        <label>Recipient Name</label>
        <input type="text" value="<?= htmlspecialchars($user_data['full_name']) ?>" readonly>

        <label>Mobile Number</label>
        <input type="text" value="<?= htmlspecialchars($user_data['phone'] ?? 'Not provided') ?>" readonly>

        <label>Shipping Destination (Auto-Filled from Profile)</label>
        <textarea name="address" rows="4" required><?= htmlspecialchars($prefilled_address) ?></textarea>

        <button type="submit">Place Order &bull; <?= format_price($total) ?></button>
    </form>

    <section class="order-summary">
        <h3>Order Items (<?= count($_SESSION['cart']) ?>)</h3>
        <ul>
            <?php foreach($_SESSION['cart'] as $item): ?>
                <li>
                    <?= htmlspecialchars($item['name']) ?> &times; <?= $item['quantity'] ?> = <?= format_price($item['price'] * $item['quantity']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p>Delivery: <strong>FREE</strong></p>
        <p><strong>Total Due: <?= format_price($total) ?></strong></p>
    </section>
</main>
```

> **Presenter Talking Points**:
> - **ACID Compliance**: Uses `$conn->beginTransaction()`, `$conn->commit()`, and `$conn->rollBack()` so an order and its items are inserted together. If any item fails, the entire transaction rolls back, preventing orphaned orders or financial corruption.
> - Preserves historical price: Line items insert `price` into `order_items` at the moment of checkout, ensuring future catalog price changes do not distort historic order invoices.
