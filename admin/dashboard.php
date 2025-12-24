<?php
session_start();

/*
|--------------------------------------------------------------------------
| Admin Access Protection
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | EcoTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-gradient-mesh {
            background-color: #f0fdf4;
            background-image:
                radial-gradient(at 0% 0%, rgba(16,185,129,0.1) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(5,150,105,0.1) 0, transparent 50%);
        }
        .glass {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-gradient-mesh min-h-screen">

<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    <div class="flex items-center gap-3">
        <img src="../assets/ecotrack-logo.png" class="h-16" alt="EcoTrack Logo">
        <span class="font-extrabold text-emerald-700 text-lg">
            Admin Panel
        </span>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-sm text-slate-600">
            Hello, <?= htmlspecialchars($_SESSION["user_name"]) ?>
        </span>
        <a href="../logout.php"
           class="px-5 py-2 rounded-full bg-red-100 text-red-600 font-semibold hover:bg-red-200 transition">
            Logout
        </a>
    </div>
</nav>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-10">

    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Admin Dashboard
    </h1>
    <p class="text-slate-600 mb-8">
        Manage users, products, and system data
    </p>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Total Users
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                --
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Transactions
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                --
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Reports
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                --
            </p>
        </div>

    </div>

    <!-- PLACEHOLDER CONTENT -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-4">
            System Overview
        </h2>
        <p class="text-slate-600">
            This section will contain admin features such as:
        </p>

        <ul class="list-disc list-inside text-slate-600 mt-4 space-y-2">
            <li>View registered members</li>
            <li>Manage products and prices</li>
            <li>View transactions by date range</li>
            <li>Generate
