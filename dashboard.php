<?php
session_start();

/*
|--------------------------------------------------------------------------
| Member Access Protection
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Optional: prevent admin from using member dashboard
if ($_SESSION["user_role"] !== "member") {
    header("Location: admin/dashboard.php");
    exit;
}

require "config/bootstrap.php";

$userId = $_SESSION["user_id"];

// User stats
$stmt = $pdo->prepare("SELECT first_name, points FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_points = (int)($user['points'] ?? 0);

// Calculate user level based on points
$user_level = 1;
$user_level_name = 'Eco-Beginner';
if ($user_points >= 5000) {
    $user_level = 10;
    $user_level_name = 'Eco-Master';
} elseif ($user_points >= 4000) {
    $user_level = 9;
    $user_level_name = 'Eco-Expert';
} elseif ($user_points >= 3000) {
    $user_level = 8;
    $user_level_name = 'Eco-Pro';
} elseif ($user_points >= 2500) {
    $user_level = 7;
    $user_level_name = 'Eco-Champion';
} elseif ($user_points >= 2000) {
    $user_level = 6;
    $user_level_name = 'Eco-Warrior';
} elseif ($user_points >= 1500) {
    $user_level = 5;
    $user_level_name = 'Eco-Warrior';
} elseif ($user_points >= 1000) {
    $user_level = 4;
    $user_level_name = 'Eco-Advocate';
} elseif ($user_points >= 500) {
    $user_level = 3;
    $user_level_name = 'Eco-Enthusiast';
} elseif ($user_points >= 200) {
    $user_level = 2;
    $user_level_name = 'Eco-Starter';
}

$logSummary = $pdo->prepare("
    SELECT COUNT(*) as total_logs,
           COALESCE(SUM(weight_kg),0) as total_weight,
           COALESCE(SUM(points_earned),0) as total_log_points
    FROM recycling_logs
    WHERE user_id = ?
");
$logSummary->execute([$userId]);
$logs = $logSummary->fetch(PDO::FETCH_ASSOC);

$txSummary = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$txSummary->execute([$userId]);
$total_tx = (int)$txSummary->fetchColumn();

$recentNotifications = $pdo->prepare("
    SELECT message, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$recentNotifications->execute([$userId]);
$notifications = $recentNotifications->fetchAll(PDO::FETCH_ASSOC);

// Recent challenges
$challengeStmt = $pdo->query("
    SELECT c.id, c.title, c.points_reward, c.difficulty,
           IF(uc.id IS NULL, 0, 1) AS joined
    FROM challenges c
    LEFT JOIN user_challenges uc
        ON c.id = uc.challenge_id AND uc.user_id = {$userId}
    WHERE c.status = 'active'
    ORDER BY c.start_date DESC
    LIMIT 3
");
$challengeCards = $challengeStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<!-- MAIN -->
<main class="flex-1 p-8 ml-64">

    <div id="home" class="page-section active">
        <div class="bg-green-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-2">EcoTrack Your Green Journey</h2>
                <p class="mb-6 opacity-90 max-w-lg">Track your waste, learn about sustainability, and earn rewards for saving the planet.</p>
                <button onclick="showPage('tracker', this)" class="bg-white text-green-700 font-bold py-2 px-6 rounded-lg shadow hover:bg-gray-100 transition">Log Today's Waste</button>
            </div>
            <i class="fas fa-globe-americas absolute -right-10 -bottom-20 text-9xl opacity-20 text-green-900"></i>
        </div>
        
        <h3 class="text-xl font-bold text-gray-700 mb-4">Latest Eco-Tips</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-4 hover:shadow-md transition cursor-pointer">
                <div class="w-24 h-24 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500 text-3xl"><i class="fas fa-tint"></i></div>
                <div>
                    <span class="text-xs font-bold text-blue-500 uppercase">Article</span>
                    <h4 class="font-bold text-lg">5 Ways to Save Water</h4>
                    <p class="text-sm text-gray-500">Small changes in your daily routine can save gallons...</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-4 hover:shadow-md transition cursor-pointer">
                <div class="w-24 h-24 bg-orange-100 rounded-lg flex items-center justify-center text-orange-500 text-3xl"><i class="fas fa-box-open"></i></div>
                <div>
                    <span class="text-xs font-bold text-orange-500 uppercase">Guide</span>
                    <h4 class="font-bold text-lg">Cardboard vs. Paper</h4>
                    <p class="text-sm text-gray-500">Learn the difference and how to sort them properly.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="dashboard" class="page-section">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
            <p class="text-gray-500 text-sm font-bold uppercase">Total Points</p>
            <h3 class="text-4xl font-bold text-green-600 mt-2"><?= number_format($user_points) ?></h3>
            <p class="text-xs text-green-500 mt-1"><i class="fas fa-arrow-up"></i> +120 this week</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
            <p class="text-gray-500 text-sm font-bold uppercase">Waste Saved</p>
            <h3 class="text-4xl font-bold text-blue-600 mt-2"><?= number_format($logs['total_weight'], 1) ?> kg</h3>
            <p class="text-xs text-gray-400 mt-1">Since Jan 2025</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
            <p class="text-gray-500 text-sm font-bold uppercase">Current Level</p>
            <h3 class="text-4xl font-bold text-orange-500 mt-2"><?= $user_level ?></h3>
            <p class="text-xs text-gray-400 mt-1"><?= $user_level_name ?></p>
        </div>
    </div>
    
    <?php
    // Calculate weekly progress (last 7 days)
    $weeklyData = [];
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $totalWeeklyPoints = 0;

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(points_earned), 0) as daily_points FROM recycling_logs WHERE user_id = ? AND DATE(logged_at) = ?");
        $stmt->execute([$userId, $date]);
        $dailyPoints = (float)$stmt->fetchColumn();
        $weeklyData[] = $dailyPoints;
        $totalWeeklyPoints += $dailyPoints;
    }

    $maxPoints = max($weeklyData) ?: 1; // Avoid division by zero
    ?>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Weekly Progress</h3>
        <div class="h-40 flex items-end justify-between gap-2 px-4">
            <?php foreach ($weeklyData as $index => $points): ?>
                <?php
                $heightPercent = ($points / $maxPoints) * 100;
                $isToday = $index === 6; // Assuming today is Sunday
                $bgClass = $isToday ? 'bg-green-500 shadow-lg' : 'bg-green-100 hover:bg-green-200';
                ?>
                <div class="w-full <?= $bgClass ?> rounded-t-lg relative group" style="height: <?= max($heightPercent, 5) ?>%">
                    <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold opacity-0 group-hover:opacity-100">
                        <?= number_format($points, 1) ?>kg
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-2 px-2">
            <?php foreach ($days as $day): ?>
                <span><?= $day ?></span>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-green-500 mt-2 text-center">+<?= number_format($totalWeeklyPoints) ?> this week</p>
    </div>
    </div>

    <div id="tracker" class="page-section">
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
    </div>

</main>

<script>
function showPage(pageId, element) {
    // 1. Hide all pages
    document.querySelectorAll('.page-section').forEach(page => {
        page.classList.remove('active');
    });

    // 2. Show the selected page
    document.getElementById(pageId).classList.add('active');

    // 3. Update URL without reloading
    const url = new URL(window.location);
    url.searchParams.set('section', pageId);
    window.history.pushState({}, '', url);

    // 4. Update Sidebar Styling
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active-link');
    });
    element.classList.add('active-link');
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section') || 'home';

    // Hide all pages
    document.querySelectorAll('.page-section').forEach(page => {
        page.classList.remove('active');
    });

    // Show the correct page
    document.getElementById(section).classList.add('active');

    // Update sidebar active state
    setActiveSidebarLink(section);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section') || 'home';

    // Set initial active state
    setActiveSidebarLink(section);

    if (section !== 'home') {
        // Just show the correct page without triggering showPage again
        document.querySelectorAll('.page-section').forEach(page => {
            page.classList.remove('active');
        });
        document.getElementById(section).classList.add('active');
    }
});

function setActiveSidebarLink(section) {
    // Remove active-link from all sidebar links
    document.querySelectorAll('.sidebar-link, .active-link').forEach(link => {
        link.classList.remove('active-link');
        link.classList.add('sidebar-link');
    });

    // Add active-link to the correct link
    const activeLink = document.querySelector(`a[data-page-id="${section}"]`);
    if (activeLink) {
        activeLink.classList.remove('sidebar-link');
        activeLink.classList.add('active-link');
    }
}
</script>

<?php include "partials/footer.php"; ?>
