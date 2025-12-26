<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";

$userId = $_SESSION["user_id"];

// Mark all as read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$userId]);

$notifications = $pdo->prepare("
    SELECT message, type, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$notifications->execute([$userId]);
$items = $notifications->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Notifications</h1>
            <p class="text-slate-600">Latest updates about your activities</p>
        </div>
    </div>

    <div class="space-y-3">
        <?php if (empty($items)): ?>
            <div class="glass rounded-3xl p-8 shadow-xl text-center text-slate-500">
                No notifications yet.
            </div>
        <?php else: ?>
            <?php foreach ($items as $note): ?>
                <div class="bg-white border border-slate-100 rounded-xl p-4 flex justify-between items-center shadow-sm">
                    <div>
                        <p class="font-semibold text-slate-800"><?= htmlspecialchars($note['message']) ?></p>
                        <p class="text-xs text-slate-400"><?= date('d M Y, h:i A', strtotime($note['created_at'])) ?></p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full <?= $note['type'] === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                        <?= htmlspecialchars($note['type']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
