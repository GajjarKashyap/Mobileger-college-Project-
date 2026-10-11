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
