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
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<!-- MAIN -->
<main class="ml-64 px-10 py-8">

    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Your Dashboard
    </h1>
    <p class="text-slate-600 mb-8">
        Track your eco-friendly activities and progress
    </p>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Weekly Activity
            </h3>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">
                --
            </p>
        </div>

        <div class="glass rounded-3xl p-6 shadow-xl">
            <h3 class="text-sm uppercase font-bold text-slate-500">
                Carbon Saved
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

    </div>

    <!-- PLACEHOLDER CONTENT -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-4">
            Welcome to EcoTrack
        </h2>
        <p class="text-slate-600">
            This dashboard will later include:
        </p>

        <ul class="list-disc list-inside text-slate-600 mt-4 space-y-2">
            <li>Browse eco-friendly products</li>
            <li>View transaction history</li>
            <li>Track weekly environmental impact</li>
            <li>Download receipts</li>
        </ul>
    </div>

</main>

<?php include "partials/footer.php"; ?>
