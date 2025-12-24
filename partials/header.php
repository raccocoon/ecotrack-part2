<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoTrack</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); }
.bg-gradient-mesh {
    background-color: #f0fdf4;
    background-image:
        radial-gradient(at 0% 0%, rgba(16,185,129,0.1) 0, transparent 50%),
        radial-gradient(at 100% 100%, rgba(5,150,105,0.1) 0, transparent 50%);
}
</style>
</head>

<body class="bg-gradient-mesh min-h-screen">

<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    <a href="index.php">
        <img src="assets/ecotrack-logo.png" class="h-20">
    </a>

    <div class="flex gap-3">

    <a href="index.php"
       class="px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
        Home
    </a>

    <?php if (isset($_SESSION["user_id"])): ?>

        <a href="dashboard.php"
           class="bg-white/60 border border-emerald-100 px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
            Dashboard
        </a>

        <a href="logout.php"
           class="px-5 py-2 rounded-full font-semibold text-red-600 hover:bg-red-50 transition">
            Logout
        </a>

    <?php else: ?>

        <?php if (basename($_SERVER["PHP_SELF"]) === "login.php"): ?>

            <a href="register.php"
               class="bg-white/60 border border-emerald-100 px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
                Register
            </a>

        <?php else: ?>

            <a href="login.php"
               class="bg-white/60 border border-emerald-100 px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
                Login
            </a>

        <?php endif; ?>

    <?php endif; ?>

</div>
</nav>
