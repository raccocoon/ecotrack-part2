<?php
require __DIR__ . "/data/recycling-centers.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Recycling Center | EcoTrack</title>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

<!-- Leaflet -->
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>

<!-- Recycling Centers Data -->
<script src="data/recycling_centers.js"></script>

<style>
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.bg-gradient-mesh {
    background-color: #f0fdf4;
    background-image:
        radial-gradient(at 0% 0%, rgba(16,185,129,0.12) 0, transparent 50%),
        radial-gradient(at 100% 100%, rgba(5,150,105,0.12) 0, transparent 50%);
}
.glass {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
}
#map {
    height: 500px;
}
</style>
</head>

<body class="bg-gradient-mesh min-h-screen">

<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    <a href="index.php" class="flex items-center gap-3">
        <img src="assets/images/ecotrack-logo.png" class="h-16" alt="EcoTrack">
    </a>

    <a href="index.php"
       class="px-5 py-2 rounded-full bg-white/60 border border-emerald-100 font-semibold text-emerald-700 hover:bg-emerald-50 transition">
        ← Back to Home
    </a>
</nav>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-8">

    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Find a Recycling Center
    </h1>
    <p class="text-slate-600 mb-6">
        Locate nearby recycling centers in Kuching by material type
    </p>

    <!-- FILTERS (UI only for now) -->
    <div class="flex flex-wrap gap-3 mb-6" id="filterButtons">
    <?php
    $filters = [
        "Paper"   => "paper",
        "Plastic" => "plastic",
        "Metal"   => "metal",
        "Glass"   => "glass",
        "E-Waste" => "e-waste",
        "Textile" => "textile",
        "Organic" => "organic",
        "Mixed"   => "mixed"
    ];

    foreach ($filters as $label => $value):
    ?>
        <button
            data-filter="<?= $value ?>"
            class="filter-btn px-4 py-2 rounded-full border border-emerald-300 text-emerald-700 font-semibold transition">
            <?= $label ?>
        </button>
    <?php endforeach; ?>

    <button
        data-filter="all"
        class="filter-btn px-4 py-2 rounded-full border border-emerald-300 text-emerald-700 font-semibold transition">
        Show All
    </button>
    </div>

    <!-- MAP CARD -->
    <div class="glass rounded-3xl shadow-xl p-4">
        <div id="map" class="rounded-2xl"></div>
    </div>

</main>

<!-- Initialize Map -->
<script>
const map = L.map("map").setView([1.5535, 110.3593], 12);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors"
}).addTo(map);
</script>

<!-- Marker Creation -->
<script>
let markers = [];

function loadMarkers(filter = "all") {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    recyclingCenters.forEach(center => {
        if (
            filter === "all" ||
            center.materials.includes(filter)
        ) {
            const marker = L.marker([center.lat, center.lng])
                .addTo(map)
                .bindPopup(`
                    <div class="font-bold text-emerald-700">
                        ${center.name}
                    </div>
                    <div class="text-sm text-slate-600 mt-1">
                        Accepts: ${center.materials.join(", ")}
                    </div>
                `);

            markers.push(marker);
        }
    });
}

// Load all markers on page load
loadMarkers();

// Set "Show All" button as active on page load
document.addEventListener('DOMContentLoaded', () => {
    const showAllBtn = document.querySelector('[data-filter="all"]');
    if (showAllBtn) {
        showAllBtn.classList.remove("border-emerald-300", "text-emerald-700");
        showAllBtn.classList.add("bg-emerald-600", "text-white", "border-emerald-600");
    }
});
</script>

<script>
const buttons = document.querySelectorAll(".filter-btn");

buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        // Reset all buttons
        buttons.forEach(b => {
            b.classList.remove("bg-emerald-600", "text-white", "border-emerald-600");
            b.classList.add("border-emerald-300", "text-emerald-700");
        });

        // Activate clicked button
        btn.classList.remove("border-emerald-300", "text-emerald-700");
        btn.classList.add("bg-emerald-600", "text-white", "border-emerald-600");

        const filter = btn.dataset.filter;

        // Call map filter function
        loadMarkers(filter);
    });
});
</script>


</body>
</html>
