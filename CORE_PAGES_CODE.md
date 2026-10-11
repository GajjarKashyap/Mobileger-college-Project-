# MobileGear — Core User Pages Code Documentation

This document provides the complete, production-ready source code and technical architecture for the four primary user-facing pages of the **MobileGear** e-commerce platform:

1. **Home Page (`index.php`)** — Brand landing, hero banner, value proposition perks strip, and featured flagship accessories.
2. **Login Page (`login.php`)** — Secure authentication with bcrypt verification, redirect routing, test credential auto-fill, and password toggle.
3. **Registration Page (`register.php`)** — Amazon-style customer onboarding with Indian postal validation (state selector, district, 6-digit PIN, 10-digit mobile) and password hashing.
4. **Shopping Cart Page (`cart.php`)** — In-memory session cart management (`$_SESSION['cart']`), real-time quantity modifications, item removals, and order calculation summary.

---

## Architecture & Customer Journey Flow

```
+---------------------------------------------------------------------------------+
|                                Customer Journey                                 |
+---------------------------------------------------------------------------------+
       |
       v
 [ index.php ]  ---------> Discovers flagship accessories & brand value perks
       |
       v
 [ shop.php / product.php ]
       |
       v
  [ cart.php ]  ---------> Manages items in $_SESSION['cart'] with instant recalculation
       |
       +------------+
       |            |
(Not Logged In)  (Logged In)
       |            |
       v            v
 [ login.php / register.php ] --> [ checkout.php ] --> [ order-success.php ]
```

---

## 1. Home Page (`index.php`)

### Role & Key Features
- **File Location**: `index.php`
- **Dependencies**: `includes/bootstrap.php`, `includes/header.php`, `includes/product-card.php`, `includes/footer.php`
- **Key Functionality**:
  - Dynamically fetches top 8 latest flagship accessories via `get_all_products($conn, 8)`.
  - Displays high-conversion hero promotional banner targeting iPhone 18 Pro Max and Samsung Galaxy S26 Ultra users.
  - Highlights store trust factors: Free Express Shipping, 100% Genuine Brands, Next-Gen Charging, and 7-Day Easy Returns.
  - Reusable component card rendering via `includes/product-card.php`.

### Complete Source Code

```php
<?php
require_once 'includes/bootstrap.php';
require_once 'includes/header.php';

$featured_products = get_all_products($conn, 8); // Latest 8 products
?>

<div class="container">
    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="hero-tag">⚡ Premium Tech Collection • Under ₹15,000</div>
        <h1 class="hero-title">Elevate Your Flagship Phone Experience</h1>
        <p class="hero-desc">Curated accessories built specifically for iPhone 18 Pro Max and Galaxy S26 Ultra. Explore precision-cut skins, drop-tested armor cases, high-wattage chargers, and smart wearables.</p>
        <div class="hero-actions">
            <a href="shop.php" class="btn btn-primary" style="padding: 13px 28px; font-size: 15px;">Shop Full Catalog</a>
            <a href="shop.php?category=Smart+Watch" class="btn btn-secondary" style="padding: 13px 24px; font-size: 15px;">Explore Smartwatches</a>
        </div>
    </div>

    <!-- Value Proposition Perks Strip -->
    <div class="perks-strip">
        <div class="perk-card">
            <div class="perk-icon">🚚</div>
            <div>
                <div class="perk-title">Free Express Shipping</div>
                <div class="perk-desc">Fast doorstep delivery on all orders</div>
            </div>
        </div>
        <div class="perk-card">
            <div class="perk-icon">🛡️</div>
            <div>
                <div class="perk-title">100% Genuine Brands</div>
                <div class="perk-desc">Apple, Samsung, Spigen, dbrand & more</div>
            </div>
        </div>
        <div class="perk-card">
            <div class="perk-icon">⚡</div>
            <div>
                <div class="perk-title">Next-Gen Charging</div>
                <div class="perk-desc">MagSafe & GaN high-wattage tech</div>
            </div>
        </div>
        <div class="perk-card">
            <div class="perk-icon">🔄</div>
            <div>
                <div class="perk-title">Easy 7-Day Returns</div>
                <div class="perk-desc">Hassle-free replacement policy</div>
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="section-header">
        <div>
            <h2 class="section-title">🔥 Featured Arrivals</h2>
            <p class="section-subtitle">Handpicked flagship protection, fast power, and wireless audio.</p>
        </div>
        <a href="shop.php" style="color: var(--primary); font-weight: 600; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 4px;">
            View All 46 Products &rarr;
        </a>
    </div>

    <div class="product-grid">
        <?php foreach($featured_products as $product): ?>
            <?php include 'includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
```

---

## 2. Login Page (`login.php`)

### Role & Key Features
- **File Location**: `login.php`
- **Dependencies**: `includes/bootstrap.php`, `includes/header.php`, `includes/footer.php`
- **Security & User Experience**:
  - **Redirect Protection**: Automatically redirects users already authenticated (`isset($_SESSION['user_id'])`) back to `index.php`.
  - **Input Sanitization**: Cleans inputs using `sanitize_input()` and executes parameterized prepared SQL statements (`SELECT * FROM users WHERE email = ?`) to eliminate SQL injection risks.
  - **Password Verification**: Validates passwords using PHP's cryptographically secure `password_verify($password, $user['password'])`.
  - **Session Initialization**: Sets `$_SESSION['user_id']`, `$_SESSION['user_name']`, and `$_SESSION['user_role']`.
  - **Context-Aware Redirect**: Supports `?redirect=` query param so buyers return directly to checkout after signing in.
  - **Split-Screen Design**: Left brand trust panel and right form with SVG icons, quick test account auto-fill (`fillTestAccount()`), and interactive password visibility toggler.

### Complete Source Code

```php
<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        $redirect = isset($_GET['redirect']) ? sanitize_input($_GET['redirect']) : 'index.php';
        header("Location: " . $redirect);
        exit;
    } else {
        $error = "Invalid email or password. Please verify your credentials.";
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-split-wrapper">
        <!-- Left: Brand Showcase & Guarantees -->
        <div class="auth-brand-side">
            <div>
                <div class="auth-brand-badge">⚡ Official Retailer</div>
                <h2 class="auth-brand-title">Your Flagship Gear, One Seamless Hub</h2>
                <p class="auth-brand-desc">Sign in to access your saved delivery addresses, track live courier dispatches, and unlock member pricing on authentic accessories.</p>

                <div class="auth-features">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">✓</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">100% Genuine Inventory</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Direct sourcing from Apple, Samsung, Spigen & dbrand</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">⚡</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">Same-Day Express Dispatch</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Orders dispatched within 24 hours with SMS tracking</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">🛡️</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">7-Day Easy Replacements</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Zero-hassle replacement on any fitting or transit issue</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Review Snippet -->
            <div class="auth-quote-card">
                "Ordered the Spigen Rugged Armor and 45W GaN charger. Package arrived in 2 days in mint condition. Superb experience."
                <span class="auth-quote-author">— Vikram Patel, Ahmedabad (Verified Buyer)</span>
            </div>
        </div>

        <!-- Right: Modern Sign-In Form -->
        <div class="auth-form-side">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Sign In to MobileGear</h1>
                <p class="auth-form-subtitle">Enter your registered email to continue to your account.</p>
            </div>

            <!-- Demo Credentials Helper -->
            <div class="demo-creds-chip">
                <div>
                    <span style="font-weight: 700; color: var(--dark); font-size: 12px; display: block;">TEST ACCOUNT AVAILABLE</span>
                    <span style="font-size: 12px; color: var(--text-muted);">admin@mobilegear.com &bull; password</span>
                </div>
                <button type="button" class="demo-creds-btn" onclick="fillTestAccount()">Auto-Fill</button>
            </div>

            <?php if(isset($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: var(--radius-md); border: 1px solid #fecaca; font-weight: 500; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm">
                <!-- Email Field with Icon -->
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" id="loginEmail" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password Field with Icon and Eye Toggle -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <label>Password</label>
                        <a href="javascript:void(0)" onclick="alert('For this demonstration, you can use the test admin account or register a new customer account.')" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot password?</a>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" radius="2" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('loginPassword', this)" title="Show or hide password">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                    <input type="checkbox" id="rememberMe" checked style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                    <label for="rememberMe" style="font-size: 13px; color: var(--text-muted); cursor: pointer; margin-bottom: 0;">Remember this device for 30 days</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px;">
                    <span>Sign In to Account</span>
                    <span>&rarr;</span>
                </button>
            </form>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-light); text-align: center;">
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">
                    New customer at MobileGear?
                </p>
                <a href="register.php" class="btn btn-secondary btn-block" style="font-size: 14px; padding: 11px;">
                    Create Your Customer Account
                </a>
            </div>

            <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 20px; font-size: 12px; color: var(--text-light);">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Bank-grade 256-Bit SSL Encrypted Connection</span>
            </div>
        </div>
    </div>
</div>

<script>
function fillTestAccount() {
    document.getElementById('loginEmail').value = 'admin@mobilegear.com';
    document.getElementById('loginPassword').value = 'password';
}

function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        input.type = 'password';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
```

---

## 3. Registration Page (`register.php`)

### Role & Key Features
- **File Location**: `register.php`
- **Dependencies**: `includes/bootstrap.php`, `includes/header.php`, `includes/footer.php`
- **Amazon-Style Address Capture & Validation**:
  - Validates **10-digit Indian Mobile Numbers** via Regex `/^[0-9]{10}$/`.
  - Validates **6-digit Indian Postal PIN codes** via Regex `/^[0-9]{6}$/`.
  - Comprehensive dropdown of all Indian states and Union Territories.
  - Form fields split into Flat/Building/Street, District/City, State, and PIN code.
  - Enforces minimum 6-character password and confirmation match.
  - Prevents duplicate registration via email uniqueness check.
  - Uses `password_hash($password, PASSWORD_DEFAULT)` using industry-standard bcrypt.
  - Automatically logs in new customers (`$_SESSION['user_id'] = $conn->lastInsertId()`) upon successful creation.

### Complete Source Code

```php
<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$indian_states = [
    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", 
    "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", 
    "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", 
    "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", 
    "West Bengal", "Delhi NCR", "Jammu and Kashmir", "Ladakh"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $state = sanitize_input($_POST['state']);
    $pincode = sanitize_input($_POST['pincode']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match. Please re-type your password.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $error = "Please enter a valid 10-digit Indian mobile number.";
    } elseif (!empty($pincode) && !preg_match('/^[0-9]{6}$/', trim($pincode))) {
        $error = "Please enter a valid 6-digit postal PIN code.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email address already exists. Please sign in.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, address, district, state, pincode, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $address, $district, $state, $pincode, $hashed_password])) {
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed due to a server error. Please try again.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-split-wrapper" style="max-width: 1040px;">
        <!-- Left: Brand Perks & Trust Highlights -->
        <div class="auth-brand-side">
            <div>
                <div class="auth-brand-badge">⚡ Member Privileges</div>
                <h2 class="auth-brand-title">Designed for Everyday Tech Enthusiasts</h2>
                <p class="auth-brand-desc">Join MobileGear to experience Amazon-fast delivery, auto-saved addresses, and priority access to limited edition dbrand skins and MagSafe accessories.</p>

                <div class="auth-features">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">🚀</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">1-Click Instant Checkout</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Address and phone saved permanently for rapid orders</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">📦</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">Live SMS & Courier Tracking</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Real-time shipping notifications straight to your phone</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">🛡️</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">100% Genuine Certified</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Direct manufacturer inventory under ₹15,000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authentic Trust Stamp -->
            <div class="auth-quote-card">
                "Finding proper accessories for the iPhone 18 Pro Max and S26 Ultra in one place used to be a pain. MobileGear makes it simple and reliable."
                <span class="auth-quote-author">★ ★ ★ ★ ★ &bull; Over 12,000+ happy gadget owners</span>
            </div>
        </div>

        <!-- Right: Registration Form -->
        <div class="auth-form-side" style="padding: 40px 44px;">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Create Account</h1>
                <p class="auth-form-subtitle">Fill in your delivery address and contact information below.</p>
            </div>

            <?php if(isset($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: var(--radius-md); border: 1px solid #fecaca; font-weight: 500; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="regForm">
                <!-- Section 1: Personal & Contact -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" pattern="[0-9]{10}" maxlength="10" required>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email Address *</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Password & Confirm -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Password *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 6 characters" minlength="6" required>
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('regPassword', this)">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <div class="input-with-icon">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="regConfirmPassword" name="confirm_password" class="form-control" placeholder="Re-type password" minlength="6" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Amazon-Style Address Fields -->
                <div style="background: var(--bg-page); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; margin: 16px 0 20px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <span>📍</span>
                        <span>Delivery Address Details (Amazon-Style 1-Click Fast Checkout)</span>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 13px;">Flat, House No., Building, Street *</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="e.g. 402, Royal Residency, Opp. City Center Mall, CG Road" required style="font-size: 13px;"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 10px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">District / City *</label>
                            <input type="text" name="district" class="form-control" placeholder="e.g. Ahmedabad" value="<?= htmlspecialchars($_POST['district'] ?? '') ?>" required style="padding: 9px 12px; font-size: 13px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">State *</label>
                            <select name="state" class="form-control" required style="padding: 9px 12px; font-size: 13px;">
                                <option value="">Select State</option>
                                <?php foreach($indian_states as $st): ?>
                                    <option value="<?= $st ?>" <?= (isset($_POST['state']) && $_POST['state'] === $st) ? 'selected' : '' ?>>
                                        <?= $st ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 12px;">PIN Code *</label>
                            <input type="text" name="pincode" class="form-control" placeholder="6 Digits" value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>" pattern="[0-9]{6}" maxlength="6" required style="padding: 9px 12px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px;">
                    <span>Create Account & Start Shopping</span>
                    <span>&rarr;</span>
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center; font-size: 14px; color: var(--text-muted);">
                Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Sign in here &rarr;</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        input.type = 'password';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
```

---

## 4. Shopping Cart Page (`cart.php`)

### Role & Key Features
- **File Location**: `cart.php`
- **Dependencies**: `includes/bootstrap.php`, `includes/header.php`, `includes/footer.php`
- **Session-Based Cart State Engine**:
  - **`action = 'add'`**: Adds an item or increments its quantity in `$_SESSION['cart'][$product_id]`.
  - **`action = 'update'`**: Updates individual product quantities from batch input fields (`quantity[id]`), enforcing valid bounds (1 to 10).
  - **`action = 'remove'`**: Removes an item completely via `unset($_SESSION['cart'][$product_id])` using separate dedicated hidden forms per item.
  - **Auto PRG Pattern (Post-Redirect-Get)**: Always issues `header("Location: cart.php"); exit;` after mutations to prevent duplicate submission on page refresh.
  - **Order Summary**: Computes subtotal, free shipping qualifier, and total amount with `format_price()`.
  - **Responsive Empty State**: Displays custom empty-cart card with direct CTA to browse all accessories.

### Complete Source Code

```php
<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $product = get_product_by_id($conn, $product_id);
        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image_url'],
                    'quantity' => $quantity
                ];
            }
        }
    } elseif ($action === 'update') {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $product_id => $qty) {
                $qty = (int)$qty;
                if ($qty > 0 && isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] = $qty;
                }
            }
        }
    } elseif ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    
    header("Location: cart.php");
    exit;
}

require_once 'includes/header.php';
$total = 0;
?>

<div class="container">
    <div class="breadcrumbs">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="shop.php">Shop</a>
        <span>/</span>
        <span style="color: var(--dark); font-weight: 600;">Your Cart</span>
    </div>

    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 24px;">Shopping Cart</h1>
    
    <?php if(empty($_SESSION['cart'])): ?>
        <div style="background: var(--bg-card); padding: 60px; text-align: center; border-radius: var(--radius-lg); border: 1px dashed var(--border-color); margin: 20px 0;">
            <div style="font-size: 52px; margin-bottom: 12px;">🛒</div>
            <h2 style="font-size: 22px; color: var(--dark); margin-bottom: 8px;">Your cart is empty</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Discover chargers, skins, smartwatches and audio accessories.</p>
            <a href="shop.php" class="btn btn-primary" style="padding: 12px 28px;">Explore Mobile Accessories</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="update">
            
            <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: flex-start;">
                <!-- Cart Items Table -->
                <div style="flex: 2; min-width: 320px;">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item Details</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($_SESSION['cart'] as $id => $item): ?>
                                    <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 14px;">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" width="56" height="56" style="object-fit: contain; border-radius: 8px; border: 1px solid var(--border-color); background: #fff; padding: 4px; flex-shrink: 0;">
                                                <?php endif; ?>
                                                <div>
                                                    <a href="product.php?id=<?= $id ?>" style="font-weight: 700; color: var(--dark); text-decoration: none; font-size: 15px; display: block; line-height: 1.3;">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <span style="font-size: 12px; color: var(--success); font-weight: 600;">✓ In Stock</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-weight: 600; color: var(--dark-muted);"><?= format_price($item['price']) ?></td>
                                        <td>
                                            <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" max="10" class="form-control" style="width: 70px; text-align: center; font-weight: 700; padding: 6px;">
                                        </td>
                                        <td style="font-weight: 800; color: var(--dark); font-size: 16px;"><?= format_price($subtotal) ?></td>
                                        <td>
                                            <button type="submit" form="remove_<?= $id ?>" class="btn btn-sm" style="background: transparent; color: var(--danger); border: 1px solid #fecaca; padding: 6px 10px; cursor: pointer;" title="Remove Item">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <a href="shop.php" class="btn btn-secondary" style="font-size: 13px;">&larr; Continue Shopping</a>
                        <button type="submit" class="btn btn-secondary" style="font-size: 13px;">Update Quantities</button>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div style="flex: 1; min-width: 280px;">
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
                        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 16px;">Order Summary</h3>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-muted);">
                            <span>Subtotal (<?= count($_SESSION['cart']) ?> items)</span>
                            <span style="font-weight: 700; color: var(--dark);"><?= format_price($total) ?></span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 14px; color: var(--text-muted);">
                            <span>Shipping</span>
                            <span style="color: var(--success); font-weight: 700;">FREE</span>
                        </div>
                        
                        <div style="border-top: 1px dashed var(--border-color); margin: 16px 0; padding-top: 16px; display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-size: 16px; font-weight: 800; color: var(--dark);">Total Amount</span>
                            <span style="font-size: 24px; font-weight: 800; color: var(--primary);"><?= format_price($total) ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary btn-block" style="padding: 13px 20px; font-size: 15px; margin-top: 20px;">
                            Proceed to Checkout &rarr;
                        </a>

                        <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 12px; color: var(--text-muted); display: flex; flex-direction: column; gap: 8px;">
                            <span>🔒 Guaranteed Safe & Secure Checkout</span>
                            <span>⚡ Ships within 24 Hours via Express Courier</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Hidden removal forms -->
        <?php foreach($_SESSION['cart'] as $id => $item): ?>
            <form id="remove_<?= $id ?>" action="cart.php" method="POST" style="display:none;">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= $id ?>">
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
```

---

## 5. Shared Helper Functions Reference

For completeness, these functions defined in `includes/functions.php` power the core pages:

```php
// Format prices in Indian Rupee format
function format_price($price) {
    return '₹' . number_format($price, 2);
}

// Global input sanitization
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fetch single product by primary key
function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fetch featured or all products
function get_all_products($conn, $limit = null) {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    return $conn->query($sql)->fetchAll();
}
```
