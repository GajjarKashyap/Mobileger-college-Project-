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
