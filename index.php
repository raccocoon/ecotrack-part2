<?php
session_start();

// If already logged in, redirect
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["user_role"] === "admin") {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoTrack</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <div class="flex items-center gap-3">
        <img src="assets/images/ecotrack-logo.png" class="h-20" alt="EcoTrack Logo">
    </div>

    <div class="hidden md:flex items-center gap-10 font-semibold text-emerald-900/70">
        <a href="how-it-works.php" class="hover:text-emerald-600">
    How it Works
</a>

<a href="impact.php" class="hover:text-emerald-600">
    Impact
</a>

<a href="find-center.php"
   class="bg-white/50 border border-emerald-100 px-5 py-2 rounded-full hover:text-emerald-600">
    Find a Center
</a>
    </div>
</nav>

<!-- HERO -->
<main class="max-w-7xl mx-auto px-8 py-10 flex flex-col lg:flex-row gap-14 items-center">

<!-- LEFT -->
<div class="lg:w-1/2 space-y-6 text-center lg:text-left">
    <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold">
        🌱 Join 5,000+ Eco-Warriors
    </span>

    <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-tight">
        Track Your Impact,<br>
        <span class="text-emerald-600">Save Our Future.</span>
    </h1>

    <p class="text-xl text-slate-600 max-w-xl">
        Track your recycling habits, compete in community challenges, and watch your local impact grow in real-time.
    </p>

    <div class="flex items-center justify-center lg:justify-start gap-4 pt-6 border-t border-emerald-100">
        <div class="flex -space-x-3">
            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1">
            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2">
            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=3">
            <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                +4k
            </div>
        </div>
        <p class="text-slate-500 italic text-sm">
            "The easiest way to track carbon footprint!"
        </p>
    </div>
</div>

<!-- RIGHT REGISTER CARD -->
<div class="lg:w-1/2 w-full max-w-lg">
<div class="glass rounded-3xl shadow-2xl p-8">

    <div class="flex bg-slate-100 rounded-xl p-1 mb-8">
        <span class="flex-1 py-2 bg-white rounded-lg text-center font-bold text-emerald-700">
            Register
        </span>
        <a href="login.php" class="flex-1 py-2 text-center font-bold text-slate-500 hover:text-emerald-600">
            Login
        </a>
    </div>

    <!-- REGISTER FORM -->
    <form method="POST" action="register.php" class="space-y-5">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase">First Name</label>
                <input name="firstName" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase">Last Name</label>
                <input name="lastName" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200">
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Email Address</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200">
        </div>

        <div>
            <label class="text-xs font-bold text-slate-500 uppercase">Password</label>
            <input type="password" name="password" minlength="8" required
                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200">
            <p class="text-xs text-slate-400 mt-1">Must be at least 8 characters</p>
        </div>

        <button class="w-full bg-emerald-600 text-white py-4 rounded-xl font-extrabold hover:bg-emerald-700">
            Create Free Account
        </button>

        <p class="text-xs text-center text-slate-400">
            By signing up, you agree to our
            <a href="#" class="text-emerald-600 underline">Terms of Service</a>.
        </p>
    </form>

</div>
</div>
</main>

</body>
</html>
