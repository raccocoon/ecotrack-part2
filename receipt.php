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
    header("Location: transactions.php");
    exit;
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
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Success Message -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 mb-8 text-center">
            <div class="text-5xl mb-3">✅</div>
            <h2 class="text-2xl font-bold text-emerald-800 mb-2">Order Completed!</h2>
            <p class="text-emerald-700">Thank you for your purchase. Your order has been confirmed.</p>
        </div>

        <!-- Receipt -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-lg p-8" id="receipt">
            <!-- Header -->
            <div class="text-center mb-8 pb-6 border-b">
                <img src="assets/images/ecotrack-logo.png" class="h-16 mx-auto mb-3" alt="EcoTrack">
                <h1 class="text-2xl font-bold text-gray-800">Order Receipt</h1>
                <p class="text-sm text-gray-500">Transaction #<?= str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) ?></p>
            </div>

            <!-- Customer Info -->
            <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Customer</p>
                    <p class="font-semibold"><?= htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']) ?></p>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($transaction['email']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Date</p>
                    <p class="font-semibold"><?= date('d M Y, h:i A', strtotime($transaction['created_at'])) ?></p>
                    <p class="text-sm text-gray-600">Status: <span class="text-emerald-600 font-semibold">Completed</span></p>
                </div>
            </div>

            <!-- Items -->
            <table class="w-full mb-6">
                <thead class="border-b">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="pb-3">Item</th>
                        <th class="pb-3 text-center">Qty</th>
                        <th class="pb-3 text-right">Price</th>
                        <th class="pb-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($items as $item): ?>
                        <tr class="border-b">
                            <td class="py-3"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="py-3 text-center"><?= $item['quantity'] ?></td>
                            <td class="py-3 text-right">RM <?= number_format($item['price_each'], 2) ?></td>
                            <td class="py-3 text-right font-semibold">RM <?= number_format($item['price_each'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="space-y-2 mb-6">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Shipping</span>
                    <span>RM <?= number_format($shipping, 2) ?></span>
                </div>
                <?php if ($points_used > 0): ?>
                    <div class="flex justify-between text-sm text-emerald-600">
                        <span>Points Discount (<?= number_format($points_used) ?> pts)</span>
                        <span>- RM <?= number_format($points_discount, 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t">
                    <span>Total Paid</span>
                    <span>RM <?= number_format($transaction['total_amount'], 2) ?></span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-500 pt-6 border-t">
                <p>Thank you for supporting sustainable living!</p>
                <p class="mt-1">Questions? Contact us at support@ecotrack.com</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-6">
            <a href="receipt-pdf.php?tx=<?= $transaction['id'] ?>" 
               class="flex-1 bg-emerald-600 text-white text-center font-bold py-3 rounded-xl hover:bg-emerald-700 transition">
                📄 Download PDF
            </a>
            <a href="transactions.php" 
               class="flex-1 bg-white border border-gray-200 text-gray-700 text-center font-bold py-3 rounded-xl hover:bg-gray-50 transition">
                View All Orders
            </a>
            <a href="shop.php" 
               class="flex-1 bg-white border border-gray-200 text-gray-700 text-center font-bold py-3 rounded-xl hover:bg-gray-50 transition">
                Continue Shopping
            </a>
        </div>
    </div>
</main>

<?php include "partials/footer.php"; ?>
