<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";

$userId = $_SESSION["user_id"];
$message = "";

if (isset($_GET['join'])) {
    $challengeId = (int)$_GET['join'];
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_challenges (user_id, challenge_id, status, joined_at) VALUES (?, ?, 'in_progress', NOW())");
        $stmt->execute([$userId, $challengeId]);
        $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'info')")
            ->execute([$userId, "You joined a challenge!"]);
        $message = "Joined challenge successfully.";
    } catch (Exception $e) {
        $message = "Unable to join challenge.";
    }
}

// Get user's active challenges with progress
$activeChallengesStmt = $pdo->prepare("
    SELECT c.*, uc.joined_at,
           DATEDIFF(NOW(), uc.joined_at) as days_active,
           7 as duration_days, -- Default 7 days for demo
           ROUND((DATEDIFF(NOW(), uc.joined_at) / 7) * 100, 1) as progress_percent
    FROM challenges c
    JOIN user_challenges uc ON c.id = uc.challenge_id
    WHERE uc.user_id = ? AND uc.status = 'in_progress' AND c.status = 'active'
    ORDER BY uc.joined_at DESC
");
$activeChallengesStmt->execute([$userId]);
$activeChallenges = $activeChallengesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get available challenges to explore
$exploreChallengesStmt = $pdo->prepare("
    SELECT c.*, COUNT(uc2.id) as participants, IF(uc.id IS NULL, 0, 1) AS joined
    FROM challenges c
    LEFT JOIN user_challenges uc ON uc.challenge_id = c.id AND uc.user_id = ?
    LEFT JOIN user_challenges uc2 ON uc2.challenge_id = c.id
    WHERE c.status = 'active' AND (uc.id IS NULL OR uc.status != 'in_progress')
    GROUP BY c.id
    ORDER BY c.start_date DESC
");
$exploreChallengesStmt->execute([$userId]);
$exploreChallenges = $exploreChallengesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Community Challenges</h1>
            <p class="text-slate-600">Compete with others and earn extra points</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- My Active Challenges -->
    <?php if (!empty($activeChallenges)): ?>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-4">My Active Challenges</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($activeChallenges as $challenge): ?>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($challenge['title']) ?></h3>
                            <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full"><?= htmlspecialchars($challenge['difficulty']) ?></span>
                        </div>
                        <p class="text-sm text-slate-600 mb-4"><?= htmlspecialchars($challenge['description'] ?? '') ?></p>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-slate-500 mb-2">
                                <span>Day <?= min($challenge['days_active'] + 1, $challenge['duration_days']) ?> of <?= $challenge['duration_days'] ?></span>
                                <span><?= $challenge['progress_percent'] ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= min($challenge['progress_percent'], 100) ?>%"></div>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500">Reward: <?= $challenge['points_reward'] ?> pts</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Explore Challenges -->
    <div>
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Explore Challenges</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($exploreChallenges as $challenge): ?>
                <?php
                $headerColors = ['bg-blue-500', 'bg-orange-500', 'bg-green-500'];
                $headerColor = $headerColors[array_rand($headerColors)];
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="<?= $headerColor ?> h-2"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($challenge['title']) ?></h3>
                            <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full"><?= htmlspecialchars($challenge['difficulty']) ?></span>
                        </div>
                        <p class="text-sm text-slate-600 mb-3"><?= htmlspecialchars($challenge['description'] ?? '') ?></p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-slate-500"><?= $challenge['participants'] ?> participants</span>
                            <span class="text-sm text-slate-500"><?= $challenge['points_reward'] ?> pts</span>
                        </div>
                        <?php if ($challenge['joined']): ?>
                            <span class="text-emerald-600 font-semibold text-sm">Joined</span>
                        <?php else: ?>
                            <a href="?join=<?= $challenge['id'] ?>" class="inline-block w-full text-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">Join Challenge</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
