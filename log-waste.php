<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";

$userId = $_SESSION["user_id"];
$error = "";
$success = "";

function calculatePoints(string $material, float $weight): int {
    $rates = [
        'plastic' => 12,
        'paper'   => 10,
        'metal'   => 15,
        'glass'   => 8,
        'organic' => 5,
        'ewaste'  => 25,
    ];
    $rate = $rates[$material] ?? 8;
    return (int)ceil($weight * $rate);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $center = trim($_POST["center"] ?? "");
    $material = $_POST["material"] ?? "";
    $weight = (float)($_POST["weight"] ?? 0);
    $date = $_POST["date"] ?? date('Y-m-d');

    if ($material === "" || $weight <= 0) {
        $error = "Please select a material and enter a weight greater than zero.";
    } else {
        $pointsEarned = calculatePoints($material, $weight);

        $stmt = $pdo->prepare("
            INSERT INTO recycling_logs (user_id, center_name, material_type, weight_kg, points_earned, logged_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $center, $material, $weight, $pointsEarned, $date]);

        $stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        $stmt->execute([$pointsEarned, $userId]);

        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'success')");
        $stmt->execute([$userId, "You earned {$pointsEarned} pts for logging {$weight}kg of {$material}."]);

        $success = "Logged successfully! +" . $pointsEarned . " pts";
    }
}

$summary = $pdo->prepare("
    SELECT COUNT(*) as total_logs,
           COALESCE(SUM(weight_kg),0) as total_weight,
           COALESCE(SUM(points_earned),0) as total_points
    FROM recycling_logs
    WHERE user_id = ?
");
$summary->execute([$userId]);
$stats = $summary->fetch(PDO::FETCH_ASSOC);

$recent = $pdo->prepare("
    SELECT center_name, material_type, weight_kg, points_earned, logged_at
    FROM recycling_logs
    WHERE user_id = ?
    ORDER BY logged_at DESC
    LIMIT 8
");
$recent->execute([$userId]);
$recentLogs = $recent->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Log Waste</h1>
            <p class="text-slate-600">Record your recycling and earn points</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass rounded-3xl p-6 shadow-xl">
            <p class="text-sm text-slate-500 font-bold uppercase">Total Logs</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2"><?= $stats['total_logs'] ?></p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <p class="text-sm text-slate-500 font-bold uppercase">Total Weight</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2"><?= number_format($stats['total_weight'], 1) ?> kg</p>
        </div>
        <div class="glass rounded-3xl p-6 shadow-xl">
            <p class="text-sm text-slate-500 font-bold uppercase">Points Earned</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2"><?= number_format($stats['total_points']) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h3 class="text-lg font-bold mb-4 text-gray-700">Log Waste Entry</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Waste Category</label>
                    <select name="material" required class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="">Select category</option>
                        <option value="plastic">Plastic</option>
                        <option value="paper">Paper</option>
                        <option value="glass">Glass</option>
                        <option value="metal">Metal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Weight (kg)</label>
                    <input type="number" step="0.1" min="0.1" name="weight" required class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition">
                    <i class="fas fa-plus-circle mr-2"></i> Save Entry
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-700">Recent Logs</h3>
                <button class="text-xs text-green-600 font-bold border border-green-200 px-3 py-1 rounded-full hover:bg-green-50">Filter</button>
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Weight</th>
                        <th class="px-6 py-3">Points</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($recentLogs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No logs yet. Add your first entry.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td class="px-6 py-4"><?= date('M d', strtotime($log['logged_at'])) ?></td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold capitalize"><?= htmlspecialchars($log['material_type']) ?></span></td>
                                <td class="px-6 py-4 font-bold"><?= number_format($log['weight_kg'],1) ?> kg</td>
                                <td class="px-6 py-4 text-green-600">+<?= $log['points_earned'] ?> pts</td>
                                <td class="px-6 py-4 text-gray-400 cursor-pointer hover:text-red-500"><i class="fas fa-trash"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
