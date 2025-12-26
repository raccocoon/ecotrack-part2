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

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;

    if ($action === 'delete' && $user_id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
        header("Location: users.php?msg=deleted");
        exit;
    }
}

// Get users with stats
$users = $pdo->prepare("
    SELECT u.*,
           COUNT(DISTINCT r.id) as total_logs,
           COALESCE(SUM(r.weight_kg), 0) as total_waste,
           COALESCE(SUM(r.points_earned), 0) as total_points,
           COUNT(DISTINCT t.id) as total_transactions
    FROM users u
    LEFT JOIN recycling_logs r ON u.id = r.user_id
    LEFT JOIN transactions t ON u.id = t.user_id
    WHERE u.role = 'member'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users->execute();
$userList = $users->fetchAll(PDO::FETCH_ASSOC);

include "../partials/header.php";
?>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-10">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
                User Management
            </h1>
            <p class="text-slate-600">
                View and manage registered users
            </p>
        </div>
        <a href="dashboard.php" class="bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 transition">
            ← Back to Dashboard
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700">
            ✓ User deleted successfully
        </div>
    <?php endif; ?>

    <!-- USER STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Total Users</h3>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2"><?= count($userList) ?></p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Active Today</h3>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">--</p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Total Waste</h3>
            <p class="text-3xl font-extrabold text-orange-600 mt-2">
                <?= number_format(array_sum(array_column($userList, 'total_waste')), 1) ?> kg
            </p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">Total Points</h3>
            <p class="text-3xl font-extrabold text-purple-600 mt-2">
                <?= number_format(array_sum(array_column($userList, 'total_points'))) ?>
            </p>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Registered Users</h2>

        <?php if (empty($userList)): ?>
            <p class="text-slate-500">No users registered yet</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-slate-200">
                        <tr class="text-sm text-slate-500 uppercase font-bold">
                            <th class="pb-4">User</th>
                            <th class="pb-4">Waste Logged</th>
                            <th class="pb-4">Points Earned</th>
                            <th class="pb-4">Transactions</th>
                            <th class="pb-4">Joined</th>
                            <th class="pb-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($userList as $user): ?>
                            <tr class="text-sm">
                                <td class="py-4">
                                    <div>
                                        <p class="font-bold text-slate-800">
                                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                        </p>
                                        <p class="text-slate-500"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="font-semibold text-blue-600">
                                        <?= number_format($user['total_waste'], 1) ?> kg
                                    </span>
                                    <p class="text-xs text-slate-400"><?= $user['total_logs'] ?> logs</p>
                                </td>
                                <td class="py-4">
                                    <span class="font-semibold text-emerald-600">
                                        <?= number_format($user['total_points']) ?> pts
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="font-semibold text-orange-600">
                                        <?= $user['total_transactions'] ?>
                                    </span>
                                </td>
                                <td class="py-4 text-slate-500">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="py-4">
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this user? This action cannot be undone.')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                            Delete
                                        </button>
                                    </form>
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
