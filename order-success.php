<?php
require_once 'includes/bootstrap.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = null;
$order_items = [];

if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT o.*, u.full_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt_items = $conn->prepare("
            SELECT oi.*, p.name, p.image_url, p.brand, p.category 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$order_id]);
        $order_items = $stmt_items->fetchAll();
    }
}

require_once 'includes/header.php';
?>

<style>
/* Animated Checkmark CSS */
.success-animation-wrap {
    margin: 10px auto 24px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkmark-svg {
    width: 86px;
    height: 86px;
    border-radius: 50%;
    display: block;
    stroke-width: 3.5;
    stroke: #10b981;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #10b981;
    animation: check-fill 0.4s ease-in-out 0.4s forwards, check-scale 0.3s ease-in-out 0.8s both;
}

.checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 3.5;
    stroke-miterlimit: 10;
    stroke: #10b981;
    fill: none;
    animation: check-stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3.5;
    stroke: #10b981;
    stroke-linecap: round;
    stroke-linejoin: round;
    animation: check-stroke 0.35s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
}

@keyframes check-stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes check-scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.12, 1.12, 1);
    }
}

@keyframes check-fill {
    100% {
        box-shadow: inset 0px 0px 0px 45px rgba(16, 185, 129, 0.12);
    }
}
</style>

<div class="container" style="max-width: 840px;">
    <!-- Success Banner -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px 30px; text-align: center; box-shadow: var(--shadow-sm); margin-bottom: 28px;">
        
        <!-- Animated Tick -->
        <div class="success-animation-wrap">
            <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <span class="badge badge-success" style="font-size: 13px; padding: 6px 14px; margin-bottom: 12px;">Order Confirmed &bull; Preparing for Dispatch</span>
        <h1 style="font-size: 30px; font-weight: 800; color: var(--dark); margin: 8px 0;">Thank You for Your Order!</h1>
        <p style="color: var(--text-muted); font-size: 15px; max-width: 500px; margin: 0 auto 16px;">
            We've received your order <strong>#<?= $order_id ?></strong>. A confirmation message has been dispatched to your mobile.
        </p>

        <!-- Estimated Delivery Banner -->
        <div style="display: inline-flex; align-items: center; gap: 10px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 22px; border-radius: var(--radius-full); font-size: 14px; font-weight: 600;">
            <span>🚚</span>
            <span>Estimated Delivery: <?= date('l, d M Y', strtotime('+3 days')) ?></span>
        </div>
    </div>

    <?php if($order): ?>
        <!-- Order Breakdown Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-light); flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--dark);">Order Summary (#<?= $order['id'] ?>)</h3>
                    <span style="font-size: 13px; color: var(--text-muted);">Placed on <?= date('d F Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div>
                    <span class="badge" style="background: #ecfdf5; color: #047857; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        Status: <?= htmlspecialchars($order['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Items Purchased List -->
            <div style="margin-bottom: 24px;">
                <?php foreach($order_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-light); gap: 14px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <?php if(!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" width="52" height="52" style="object-fit: contain; border-radius: 8px; border: 1px solid var(--border-color); background: #fff; padding: 4px; flex-shrink: 0;">
                            <?php endif; ?>
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--primary);"><?= htmlspecialchars($item['brand']) ?></span>
                                <h4 style="font-size: 14px; font-weight: 700; color: var(--dark); line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></h4>
                                <span style="font-size: 12px; color: var(--text-muted);">Qty: <?= $item['quantity'] ?> &bull; Unit: <?= format_price($item['price']) ?></span>
                            </div>
                        </div>
                        <span style="font-weight: 800; font-size: 15px; color: var(--dark); white-space: nowrap;">
                            <?= format_price($item['price'] * $item['quantity']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Shipping Destination & Financials -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; background: var(--bg-page); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div>
                    <h5 style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">📍 Delivering To</h5>
                    <p style="font-weight: 700; color: var(--dark); font-size: 14px; margin-bottom: 4px;"><?= htmlspecialchars($order['full_name']) ?></p>
                    <p style="font-size: 13px; color: var(--dark-muted); line-height: 1.5;"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>

                <div style="display: flex; flex-direction: column; justify-content: flex-end; text-align: right;">
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Payment Method: <strong>Cash on Delivery (COD) / UPI</strong></div>
                    <div style="font-size: 13px; color: var(--success); font-weight: 600; margin-bottom: 8px;">Delivery Charges: <strong>FREE</strong></div>
                    <div style="font-size: 22px; font-weight: 800; color: var(--dark);">
                        Total: <span style="color: var(--primary);"><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
        <a href="shop.php" class="btn btn-primary" style="padding: 13px 26px; font-size: 15px;">
            &larr; Continue Shopping
        </a>
        <a href="profile.php" class="btn btn-secondary" style="padding: 13px 24px; font-size: 15px;">
            📦 View in Order History
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
