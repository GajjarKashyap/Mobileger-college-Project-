<?php
require_once '../includes/bootstrap.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $category = sanitize_input($_POST['category']);
    $brand = sanitize_input($_POST['brand']);
    $description = sanitize_input($_POST['description']);
    $price = (float)$_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO products (name, category, brand, description, price, image_url) VALUES (?, ?, ?, ?, ?, 'placeholder.png')");
    $stmt->execute([$name, $category, $brand, $description, $price]);
    
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - MobileGear Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav { background: #333; color: #fff; padding: 15px; }
        .admin-nav a { color: #fff; text-decoration: none; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="admin-nav">
        <div class="container">
            <a href="index.php">MobileGear Admin</a>
            <a href="products.php" style="font-weight: bold;">Products</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="../index.php" style="float: right;">View Site</a>
        </div>
    </div>
    
    <div class="container" style="padding-top: 40px;">
        <h2>Add New Product</h2>
        <div class="form-container" style="margin-left: 0;">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category (e.g. Back Cover, Skin, Charger)</label>
                    <input type="text" name="category" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Brand (e.g. Apple, Samsung, JBL)</label>
                    <input type="text" name="brand" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="products.php" class="btn">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
