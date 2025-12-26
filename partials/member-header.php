<?php
// Ensure DB + schema
if (!isset($pdo)) {
    require __DIR__ . "/../config/bootstrap.php";
}

// Get user info
$stmt = $pdo->prepare("SELECT first_name, points FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$user_name = $user_data['first_name'] ?? 'User';
$user_points = (int)($user_data['points'] ?? 0);

// Calculate level based on points (every 500 pts)
$user_level = max(1, floor($user_points / 500) + 1);
$level_names = [
    1 => 'Eco-Beginner',
    2 => 'Eco-Learner',
    3 => 'Eco-Enthusiast',
    4 => 'Eco-Champion',
    5 => 'Eco-Warrior',
    6 => 'Eco-Hero',
    7 => 'Eco-Legend'
];
$user_level_name = $level_names[min($user_level, 7)] ?? 'Eco-Master';

// Notifications
$notifStmt = $pdo->prepare("
    SELECT id, message, type, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$notifStmt->execute([$_SESSION['user_id']]);
$notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);

$unreadStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unread_count = (int)$unreadStmt->fetchColumn();
?>

<header class="ml-64 flex justify-between items-center mt-8 mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($user_name) ?>!</h2>
        <p class="text-sm text-gray-500">Level <?= $user_level ?> <?= $user_level_name ?></p>
    </div>
    <div class="flex items-center space-x-4">
        <a href="notifications.php" class="relative p-2 text-gray-400 hover:text-emerald-600 transition" title="Notifications">
            <i class="fas fa-bell text-xl"></i>
            <?php if ($unread_count > 0): ?>
                <span class="absolute -top-1 -right-1 px-1 text-[10px] bg-red-500 text-white rounded-full">
                    <?= $unread_count ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="profile.php" class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold border-2 border-emerald-200 hover:bg-emerald-200 transition cursor-pointer" title="Profile">
            <?= strtoupper(substr($user_name, 0, 2)) ?>
        </a>
    </div>
</header>
