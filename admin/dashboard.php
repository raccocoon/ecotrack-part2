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

// Get stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$transactionCount = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions")->fetchColumn();
$wasteLogged = $pdo->query("SELECT COALESCE(SUM(weight_kg), 0) FROM recycling_logs")->fetchColumn();

// Recent transactions
$recentTransactions = $pdo->prepare("
    SELECT t.id, t.total_amount, t.status, t.created_at, u.first_name, u.last_name
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 5
");
$recentTransactions->execute();
$transactions = $recentTransactions->fetchAll(PDO::FETCH_ASSOC);

include "../partials/header.php";
?>

<!-- Dark Header Banner -->
<div class="bg-slate-900 text-white py-6 px-8 mb-8">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold mb-1">Admin Dashboard</h1>
            <p class="text-slate-300">Manage users, products, and system data</p>
        </div>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
            Generate Report
        </button>
    </div>
</div>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 pb-10">

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Revenue
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                RM <?= number_format($revenue, 2) ?>
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Orders
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                <?= number_format($transactionCount) ?>
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Users
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                <?= number_format($userCount) ?>
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Waste Logged
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                <?= number_format($wasteLogged, 1) ?> kg
            </p>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="users.php" class="glass rounded-3xl p-6 shadow-xl hover:shadow-2xl transition block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Manage Users</h3>
                    <p class="text-sm text-slate-500">View and manage user accounts</p>
                </div>
            </div>
        </a>

        <a href="products.php" class="glass rounded-3xl p-6 shadow-xl hover:shadow-2xl transition block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Manage Products</h3>
                    <p class="text-sm text-slate-500">Add, edit, and manage store items</p>
                </div>
            </div>
        </a>

        <a href="transactions.php" class="glass rounded-3xl p-6 shadow-xl hover:shadow-2xl transition block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-receipt text-orange-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">View Transactions</h3>
                    <p class="text-sm text-slate-500">Monitor sales and generate reports</p>
                </div>
            </div>
        </a>
    </div>

    <!-- RECENT TRANSACTIONS -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Recent Transactions
            </h2>
            <a href="transactions.php" class="text-emerald-600 font-semibold hover:underline">
                View all →
            </a>
        </div>

        <?php if (empty($transactions)): ?>
            <p class="text-slate-500">No transactions yet</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-sm">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="px-6 py-4 font-bold">#<?= str_pad($tx['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-800"><?= htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']) ?></p>
                                        <p class="text-xs text-slate-400"><?= date('d M Y', strtotime($tx['created_at'])) ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">RM <?= number_format($tx['total_amount'], 2) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">
                                        <?= htmlspecialchars($tx['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-slate-400 hover:text-slate-600 transition">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
