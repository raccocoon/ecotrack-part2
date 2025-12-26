<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if ($quantity < 1) {
    $quantity = 1;
}

// Verify cart item belongs to user
$stmt = $pdo->prepare("SELECT c.product_id, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
$stmt->execute([$cart_id, $user_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: ../cart.php?error=notfound");
    exit;
}

if ($quantity > $item['stock']) {
    header("Location: ../cart.php?error=stock");
    exit;
}

$stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
$stmt->execute([$quantity, $cart_id]);

header("Location: ../cart.php?msg=updated");
exit;
