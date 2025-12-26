<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";
require "data/recycling-centers.php";

$materialFilter = $_GET['material'] ?? 'all';

$filtered = array_filter($recyclingCenters, function($center) use ($materialFilter) {
    if ($materialFilter === 'all') return true;
    return in_array($materialFilter, $center['materials']);
});
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Recycling Map</h1>
            <p class="text-slate-600">Find nearby recycling centers and drop-off points</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm mb-6 flex flex-wrap gap-2">
        <?php
        $materials = ['all' => 'All', 'paper' => 'Paper', 'plastic' => 'Plastic', 'metal' => 'Metal', 'glass' => 'Glass', 'textile' => 'Textile', 'ewaste' => 'E-waste', 'organic' => 'Organic', 'mixed' => 'Mixed'];
        foreach ($materials as $key => $label):
            $active = $materialFilter === $key ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200';
        ?>
            <a href="?material=<?= urlencode($key) ?>" class="px-3 py-2 rounded-lg text-sm font-semibold transition <?= $active ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php foreach ($filtered as $center): ?>
            <div class="glass rounded-2xl p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-slate-800"><?= htmlspecialchars($center['name']) ?></h3>
                    <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full"><?= count($center['materials']) ?> materials</span>
                </div>
                <p class="text-sm text-slate-500 mb-3">Lat: <?= $center['lat'] ?>, Lng: <?= $center['lng'] ?></p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($center['materials'] as $mat): ?>
                        <span class="px-2 py-1 bg-slate-100 rounded-full text-xs text-slate-700"><?= htmlspecialchars($mat) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
