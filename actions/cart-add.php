<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../shop.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;
$payment_type = $_POST['payment_type'] ?? 'cash';

if ($quantity < 1) {
    $quantity = 1;
}

// Check if product exists and has stock
$stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product || $product['stock'] < $quantity) {
    header("Location: ../product-details.php?id=$product_id&error=stock");
    exit;
}

// Check if item already in cart
$stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update quantity
    $new_qty = $existing['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_qty, $existing['id']]);
} else {
    // Insert new cart item
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, payment_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $product_id, $quantity, $payment_type]);
}

header("Location: ../product-details.php?id=$product_id&msg=added");
exit;
