<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require "../config/bootstrap.php";

// Get filter parameters
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$status_filter = $_GET['status'] ?? 'all';

// Build query
$query = "
    SELECT t.*,
           u.first_name, u.last_name, u.email,
           COUNT(ti.id) as item_count,
           GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
    LEFT JOIN products p ON ti.product_id = p.id
    WHERE t.created_at >= ? AND t.created_at <= ?
";
$params = [$date_from . ' 00:00:00', $date_to . ' 23:59:59'];

if ($status_filter !== 'all') {
    $query .= " AND t.status = ?";
    $params[] = $status_filter;
}

$query .= " GROUP BY t.id ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary stats
$totalRevenue = array_sum(array_column($transactions, 'total_amount'));
$totalPointsUsed = array_sum(array_column($transactions, 'points_used'));
$totalTransactions = count($transactions);
$avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

include "../partials/header.php";
?>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-10">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
                Transaction Management
            </h1>
            <p class="text-slate-600">
                View and manage all transactions
            </p>
        </div>
        <a href="dashboard.php" class="bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 transition">
            ← Back to Dashboard
        </a>
    </div>

    <!-- FILTERS -->
    <div class="glass rounded-3xl p-6 shadow-xl mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="<?= $date_from ?>"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="<?= $date_to ?>"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                <select name="status"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- SUMMARY STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Total Revenue</h3>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2">RM <?= number_format($totalRevenue, 2) ?></p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Total Orders</h3>
            <p class="text-3xl font-extrabold text-blue-600 mt-2"><?= number_format($totalTransactions) ?></p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Points Used</h3>
            <p class="text-3xl font-extrabold text-orange-600 mt-2"><?= number_format($totalPointsUsed) ?> pts</p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Avg Order Value</h3>
            <p class="text-3xl font-extrabold text-purple-600 mt-2">RM <?= number_format($avgOrderValue, 2) ?></p>
        </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Transactions</h2>

        <?php if (empty($transactions)): ?>
            <p class="text-slate-500">No transactions found for the selected period</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-slate-200">
                        <tr class="text-sm text-slate-500 uppercase font-bold">
                            <th class="pb-4">Order ID</th>
                            <th class="pb-4">Customer</th>
                            <th class="pb-4">Items</th>
                            <th class="pb-4">Amount</th>
                            <th class="pb-4">Points Used</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4">Date</th>
                            <th class="pb-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="text-sm">
                                <td class="py-4">
                                    <span class="font-mono text-slate-600">
                                        #<?= str_pad($tx['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div>
                                        <p class="font-bold text-slate-800">
                                            <?= htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']) ?>
                                        </p>
                                        <p class="text-slate-500 text-xs"><?= htmlspecialchars($tx['email']) ?></p>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div>
                                        <p class="font-semibold text-slate-800"><?= $tx['item_count'] ?> item(s)</p>
                                        <p class="text-slate-500 text-xs line-clamp-1">
                                            <?= htmlspecialchars(substr($tx['product_names'], 0, 40)) ?>...
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 font-bold text-emerald-600">
                                    RM <?= number_format($tx['total_amount'], 2) ?>
                                </td>
                                <td class="py-4">
                                    <?php if ($tx['points_used'] > 0): ?>
                                        <span class="font-semibold text-orange-600">
                                            <?= number_format($tx['points_used']) ?> pts
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold
                                        <?php
                                        switch($tx['status']) {
                                            case 'completed': echo 'bg-emerald-100 text-emerald-700'; break;
                                            case 'pending': echo 'bg-yellow-100 text-yellow-700'; break;
                                            case 'failed': echo 'bg-red-100 text-red-700'; break;
                                            default: echo 'bg-slate-100 text-slate-700';
                                        }
                                        ?>">
                                        <?= htmlspecialchars($tx['status']) ?>
                                    </span>
                                </td>
                                <td class="py-4 text-slate-500">
                                    <?= date('d M Y', strtotime($tx['created_at'])) ?>
                                    <br>
                                    <span class="text-xs"><?= date('h:i A', strtotime($tx['created_at'])) ?></span>
                                </td>
                                <td class="py-4">
                                    <a href="../receipt.php?tx=<?= $tx['id'] ?>" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 font-semibold">
                                        View Receipt
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include "../partials/footer.php"; ?>
