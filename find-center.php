<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find a Recycling Center | EcoTrack</title>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Leaflet -->
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
#map { height: 420px; border-radius: 1.5rem; }
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
    <a href="index.php">
        <img src="assets/images/ecotrack-logo.png" class="h-20" alt="EcoTrack Logo">
    </a>

    <div class="flex gap-3">
        <a href="index.php"
       class="px-5 py-2 rounded-full bg-white/60 border border-emerald-100 font-semibold text-emerald-700 hover:bg-emerald-50 transition">
        ← Back to Home
        </a>
    </div>
</nav>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-8">

    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Find Recycling Centers
    </h1>
    <p class="text-slate-600 mb-6">
        Locate nearby recycling points and start making an impact today.
    </p>

    <!-- MAP -->
    <div class="glass p-4 shadow-xl mb-10">
        <div id="map"></div>
    </div>

    <!-- CENTER LIST -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="glass rounded-2xl p-6 shadow-lg">
            <h3 class="font-bold text-lg text-slate-900">
                Kuching Central Recycling
            </h3>
            <p class="text-sm text-slate-600">
                Open 8am – 5pm · Plastic, Paper, Metal
            </p>
            <button class="mt-4 px-4 py-2 rounded-lg border border-emerald-600 text-emerald-700 hover:bg-emerald-50">
                Navigate
            </button>
        </div>

        <div class="glass rounded-2xl p-6 shadow-lg">
            <h3 class="font-bold text-lg text-slate-900">
                UniMAS Green Point
            </h3>
            <p class="text-sm text-slate-600">
                Open 24 Hours · Glass Only
            </p>
            <button class="mt-4 px-4 py-2 rounded-lg border border-emerald-600 text-emerald-700 hover:bg-emerald-50">
                Navigate
            </button>
        </div>

    </div>

</main>

<!-- LEAFLET SCRIPT -->
<script>
const map = L.map('map').setView([1.5535, 110.3593], 12);

// OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Markers
L.marker([1.5535, 110.3593])
    .addTo(map)
    .bindPopup("<b>Kuching Central Recycling</b><br>Plastic, Paper, Metal");

L.marker([1.4635, 110.4275])
    .addTo(map)
    .bindPopup("<b>UniMAS Green Point</b><br>Glass Only");

L.marker([1.49224, 110.38379])
    .addTo(map)
    .bindPopup("<b>Tzu Chi Stutong Baru Recycling Center</b><br>Plastic, Paper, Metal, Glass");
</script>

</body>
</html>
