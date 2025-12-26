<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/db.php";

$transaction_id = $_GET['tx'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get transaction details
$stmt = $pdo->prepare("
    SELECT t.*, u.first_name, u.last_name, u.email
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = ? AND t.user_id = ?
");
$stmt->execute([$transaction_id, $user_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die("Transaction not found");
}

// Get transaction items
$stmt = $pdo->prepare("
    SELECT ti.*, p.name
    FROM transaction_items ti
    JOIN products p ON ti.product_id = p.id
    WHERE ti.transaction_id = ?
");
$stmt->execute([$transaction_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = $transaction['total_amount'] + $transaction['points_discount'] - 10.00;
$shipping = 10.00;
$points_discount = $transaction['points_discount'];
$points_used = $transaction['points_used'];

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .header h1 { color: #10b981; margin: 0; }
        .info { display: table; width: 100%; margin-bottom: 30px; }
        .info-left, .info-right { display: table-cell; width: 50%; }
        .info-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .totals { text-align: right; margin-top: 20px; }
        .totals div { margin: 5px 0; }
        .total-final { font-size: 18px; font-weight: bold; color: #10b981; margin-top: 10px; padding-top: 10px; border-top: 2px solid #e5e7eb; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>EcoTrack</h1>
        <p>Order Receipt</p>
        <p>Transaction #' . str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) . '</p>
    </div>

    <div class="info">
        <div class="info-left">
            <strong>Customer:</strong><br>
            ' . htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']) . '<br>
            ' . htmlspecialchars($transaction['email']) . '
        </div>
        <div class="info-right">
            <strong>Date:</strong><br>
            ' . date('d M Y, h:i A', strtotime($transaction['created_at'])) . '<br>
            <strong>Status:</strong> Completed
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>';

foreach ($items as $item) {
    $html .= '
            <tr>
                <td>' . htmlspecialchars($item['name']) . '</td>
                <td style="text-align: center;">' . $item['quantity'] . '</td>
                <td style="text-align: right;">RM ' . number_format($item['price_each'], 2) . '</td>
                <td style="text-align: right;">RM ' . number_format($item['price_each'] * $item['quantity'], 2) . '</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="totals">
        <div>Subtotal: RM ' . number_format($subtotal, 2) . '</div>
        <div>Shipping: RM ' . number_format($shipping, 2) . '</div>';

if ($points_used > 0) {
    $html .= '<div style="color: #10b981;">Points Discount (' . number_format($points_used) . ' pts): - RM ' . number_format($points_discount, 2) . '</div>';
}

$html .= '
        <div class="total-final">Total Paid: RM ' . number_format($transaction['total_amount'], 2) . '</div>
    </div>

    <div class="footer">
        <p>Thank you for supporting sustainable living!</p>
        <p>Questions? Contact us at support@ecotrack.com</p>
    </div>
</body>
</html>';

// For now, output as HTML (can be printed to PDF by browser)
// To use Dompdf, install via: composer require dompdf/dompdf
// Then uncomment the code below:

/*
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("receipt-" . $transaction_id . ".pdf", ["Attachment" => true]);
*/

// Temporary: Output HTML that can be printed to PDF
header('Content-Type: text/html; charset=utf-8');
echo $html;
echo '<script>window.print();</script>';
