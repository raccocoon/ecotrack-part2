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

include "../partials/header.php";
?>

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
