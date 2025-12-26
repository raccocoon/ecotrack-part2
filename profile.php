<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";

$userId = $_SESSION["user_id"];
$message = "";
$error = "";

// Fetch current user
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($first === '' || $last === '') {
        $error = "First and last name are required.";
    } else {
        $pdo->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?")
            ->execute([$first, $last, $userId]);
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $userId]);
        }
        $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'info')")
            ->execute([$userId, "Profile updated successfully."]);
        $message = "Profile updated.";

        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Profile & Settings</h1>
            <p class="text-slate-600">Update your details</div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($message): ?>
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="glass rounded-3xl p-8 shadow-xl max-w-2xl">
        <form method="POST" class="space-y-4" novalidate>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>"
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>"
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 text-slate-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">New Password (optional)</label>
                <input type="password" name="password"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none"
                       placeholder="Leave blank to keep current password">
            </div>
            <button type="submit"
                    class="w-full bg-emerald-600 text-white py-3 rounded-xl font-extrabold hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
                Save Changes
            </button>
        </form>

        <div class="border-t border-slate-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Account Actions</h3>
            <a href="logout.php"
               class="inline-block bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all hover:-translate-y-1">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </div>
</main>
