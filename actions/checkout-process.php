<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../checkout.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$points_to_use = intval($_POST['points_to_use'] ?? 0);

// Get user points
$stmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_points = $user['points'] ?? 0;

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.quantity, p.id as product_id, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: ../cart.php");
    exit;
}

// Calculate total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 10.00;

// Calculate points discount
$points_discount = 0;
if ($points_to_use > 0 && $points_to_use <= $user_points) {
    $max_discount = $subtotal * 0.5; // Max 50% discount
    $points_discount = min($points_to_use / 100, $max_discount);
}

$total = $subtotal + $shipping - $points_discount;

// Validate stock
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock']) {
        header("Location: ../checkout.php?error=stock");
        exit;
    }
}

try {
    $pdo->beginTransaction();

    // Create transaction with points info
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_amount, points_used, points_discount, status) VALUES (?, ?, ?, ?, 'completed')");
    $stmt->execute([$user_id, $total, $points_to_use, $points_discount]);
    $transaction_id = $pdo->lastInsertId();

    // Create transaction items and update stock
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, price_each) VALUES (?, ?, ?, ?)");
        $stmt->execute([$transaction_id, $item['product_id'], $item['quantity'], $item['price']]);

        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    // Deduct points if used
    if ($points_to_use > 0) {
        $stmt = $pdo->prepare("UPDATE users SET points = points - ? WHERE id = ?");
        $stmt->execute([$points_to_use, $user_id]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    header("Location: ../receipt.php?tx=$transaction_id");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../checkout.php?error=failed");
    exit;
}
