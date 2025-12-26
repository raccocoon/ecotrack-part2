<?php
define("BASE_URL", "/ecotrack-part2/");
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Caveat:wght@500;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); }
.bg-gradient-mesh {
    background-color: #f0fdf4;
    background-image:
        radial-gradient(at 0% 0%, rgba(16,185,129,0.1) 0, transparent 50%),
        radial-gradient(at 100% 100%, rgba(5,150,105,0.1) 0, transparent 50%);
}

/* Sidebar Styling */
.sidebar-link:hover { background-color: #f0fdf4; color: #166534; transition: 0.3s; }
.active-link { background-color: #dcfce7; color: #166534; border-right: 4px solid #22c55e; font-weight: bold; }

/* Custom Font Class for Tagline */
.tagline-font { font-family: 'Caveat', cursive; }

/* Page Transitions */
.page-section { display: none; animation: fadeIn 0.4s; }
.page-section.active { display: block; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>

<body class="bg-gray-50 font-sans text-gray-800">
