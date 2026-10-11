<?php
// includes/header.php
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
$search_query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobileGear - Premium Mobile Accessories</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="main-header">
    <!-- Top Main Header Row -->
    <div class="container header-container">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <span class="logo-icon">⚡</span>
            <span>Mobile<span class="logo-accent">Gear</span></span>
        </a>

        <!-- Global Search Bar -->
        <form action="shop.php" method="GET" class="nav-search-form">
            <div class="nav-search-wrapper">
                <svg class="nav-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" placeholder="Search 46+ gadgets, cases, chargers, brands..." value="<?= $search_query ?>" class="nav-search-input" autocomplete="off">
                <button type="submit" class="nav-search-btn">
                    <span>Search</span>
                </button>
            </div>
        </form>

        <!-- Navigation Links & User Actions -->
        <nav class="main-nav">
            <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Home</span>
            </a>

            <a href="shop.php" class="nav-link <?= $current_page === 'shop.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Catalog</span>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="nav-link <?= $current_page === 'profile.php' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span><?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?></span>
                </a>
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="nav-link" style="color: #ea580c; font-weight: 700;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link" style="color: var(--text-muted);">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Sign In</span>
                </a>
            <?php endif; ?>

            <!-- Shopping Bag Cart Button -->
            <a href="cart.php" class="cart-link">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Cart</span>
                <span class="cart-badge" id="cart-count"><?= $cart_count ?></span>
            </a>
        </nav>
    </div>

    <!-- Secondary Category Sub-Nav Bar -->
    <div class="sub-header-bar">
        <div class="container sub-header-container">
            <a href="shop.php" class="sub-nav-item <?= empty($_GET['category']) && empty($_GET['search']) ? 'active' : '' ?>">
                <span>⚡ All Deals</span>
            </a>
            <a href="shop.php?search=iPhone" class="sub-nav-item">
                <span>📱 iPhone 18 Pro</span>
            </a>
            <a href="shop.php?search=Galaxy" class="sub-nav-item">
                <span>🌌 Galaxy S26</span>
            </a>
            <a href="shop.php?category=Back+Cover" class="sub-nav-item">
                <span>🛡️ Back Covers</span>
            </a>
            <a href="shop.php?category=Skin" class="sub-nav-item">
                <span>✨ Skins</span>
            </a>
            <a href="shop.php?category=Charger" class="sub-nav-item">
                <span>🔌 Chargers</span>
            </a>
            <a href="shop.php?category=Power+Bank" class="sub-nav-item">
                <span>🔋 Power Banks</span>
            </a>
            <a href="shop.php?category=Audio" class="sub-nav-item">
                <span>🎧 Earbuds</span>
            </a>
            <a href="shop.php?category=Speaker" class="sub-nav-item">
                <span>🔊 Speakers</span>
            </a>
            <a href="shop.php?category=Smart+Watch" class="sub-nav-item">
                <span>⌚ Smartwatches</span>
            </a>
        </div>
    </div>
</header>
<main class="main-content">
