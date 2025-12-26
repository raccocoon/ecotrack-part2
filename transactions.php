<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/bootstrap.php";

$user_id = $_SESSION['user_id'];

// Get all transactions for user
$stmt = $pdo->prepare("
    SELECT t.id, t.total_amount, t.status, t.created_at,
           COUNT(ti.id) as item_count
    FROM transactions t
    LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
    WHERE t.user_id = ?
    GROUP BY t.id
    ORDER BY t.created_at DESC
");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Transaction History</h1>
    <p class="text-slate-600 mb-8">View all your past orders</p>

    <?php if (empty($transactions)): ?>
        <div class="glass rounded-3xl p-12 text-center shadow-xl">
            <div class="text-6xl mb-4">📦</div>
            <h2 class="text-2xl font-bold text-slate-700 mb-2">No orders yet</h2>
            <p class="text-slate-500 mb-6">Start shopping to see your order history here</p>
            <a href="shop.php" class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($transactions as $tx): ?>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">
                                Order #<?= str_pad($tx['id'], 6, '0', STR_PAD_LEFT) ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                <?= date('d M Y, h:i A', strtotime($tx['created_at'])) ?>
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase">
                            <?= htmlspecialchars($tx['status']) ?>
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600"><?= $tx['item_count'] ?> item(s)</p>
                            <p class="text-2xl font-bold text-emerald-600">RM <?= number_format($tx['total_amount'], 2) ?></p>
                        </div>
                        <div class="flex gap-2">
                            <a href="receipt.php?tx=<?= $tx['id'] ?>" 
                               class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition">
                                View Receipt
                            </a>
                            <a href="receipt-pdf.php?tx=<?= $tx['id'] ?>" 
                               class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                                📄 PDF
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="glass rounded-2xl p-6 text-center shadow-lg">
                <p class="text-sm text-gray-500 uppercase font-bold">Total Orders</p>
                <p class="text-4xl font-extrabold text-emerald-600 mt-2"><?= count($transactions) ?></p>
            </div>
            <div class="glass rounded-2xl p-6 text-center shadow-lg">
                <p class="text-sm text-gray-500 uppercase font-bold">Total Spent</p>
                <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                    RM <?= number_format(array_sum(array_column($transactions, 'total_amount')), 2) ?>
                </p>
            </div>
            <div class="glass rounded-2xl p-6 text-center shadow-lg">
                <p class="text-sm text-gray-500 uppercase font-bold">Items Purchased</p>
                <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                    <?= array_sum(array_column($transactions, 'item_count')) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include "partials/footer.php"; ?>
