<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Waste Segregation Guide</h1>
        <p class="text-slate-600">Learn which bin to use for different materials</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-blue-500 p-6 text-center">
            <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-3xl mb-4"><i class="fas fa-copy"></i></div>
            <h3 class="text-xl font-bold text-gray-800">Paper (Blue)</h3>
            <ul class="text-sm text-gray-600 mt-4 space-y-2 text-left bg-gray-50 p-4 rounded-lg">
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Newspapers & Magazines</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Cardboard Boxes</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Office Paper</li>
                <li><i class="fas fa-times text-red-500 mr-2"></i>Pizza Boxes (Greasy)</li>
            </ul>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-orange-800 p-6 text-center">
            <div class="w-20 h-20 mx-auto bg-orange-100 rounded-full flex items-center justify-center text-orange-800 text-3xl mb-4"><i class="fas fa-wine-bottle"></i></div>
            <h3 class="text-xl font-bold text-gray-800">Glass (Brown)</h3>
            <ul class="text-sm text-gray-600 mt-4 space-y-2 text-left bg-gray-50 p-4 rounded-lg">
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Glass Bottles</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Jars (Cleaned)</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Cosmetic Bottles</li>
                <li><i class="fas fa-times text-red-500 mr-2"></i>Mirrors or Ceramics</li>
            </ul>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-orange-500 p-6 text-center">
            <div class="w-20 h-20 mx-auto bg-orange-50 rounded-full flex items-center justify-center text-orange-500 text-3xl mb-4"><i class="fas fa-can-food"></i></div>
            <h3 class="text-xl font-bold text-gray-800">Plastic & Metal (Orange)</h3>
            <ul class="text-sm text-gray-600 mt-4 space-y-2 text-left bg-gray-50 p-4 rounded-lg">
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Plastic Bottles</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Aluminum Cans</li>
                <li><i class="fas fa-check text-emerald-500 mr-2"></i>Food Containers</li>
                <li><i class="fas fa-times text-red-500 mr-2"></i>Styrofoam</li>
            </ul>
        </div>
    </div>
</main>
