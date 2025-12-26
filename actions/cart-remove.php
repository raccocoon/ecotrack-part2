<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_GET['id'] ?? 0;

// Verify cart item belongs to user before deleting
$stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->execute([$cart_id, $user_id]);

header("Location: ../cart.php?msg=removed");
exit;
