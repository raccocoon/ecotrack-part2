<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How It Works | EcoTrack</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
}
.bg-gradient-mesh {
    background-color: #f0fdf4;
    background-image:
        radial-gradient(at 0% 0%, rgba(16,185,129,0.12) 0, transparent 50%),
        radial-gradient(at 100% 100%, rgba(5,150,105,0.12) 0, transparent 50%);
}
</style>
</head>

<body class="bg-gradient-mesh min-h-screen">

<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    <a href="index.php">
        <img src="assets/images/ecotrack-logo.png" class="h-20" alt="EcoTrack Logo">
    </a>

    <a href="index.php"
       class="px-5 py-2 rounded-full bg-white/60 border border-emerald-100 font-semibold text-emerald-700 hover:bg-emerald-50 transition">
        ← Back to Home
    </a>
</nav>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-16">

    <!-- HEADER -->
    <div class="text-center mb-14">
        <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
            How EcoTrack Works
        </h1>
        <p class="text-xl text-slate-600 max-w-3xl mx-auto">
            EcoTrack helps you understand and reduce your environmental impact through
            simple tracking, meaningful insights, and community-driven sustainability.
        </p>
    </div>

    <!-- STEPS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-20">

        <!-- Register -->
        <div class="glass rounded-3xl p-8 text-center shadow-xl hover:scale-105 transition">
            <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-2xl">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h3 class="font-extrabold text-lg text-emerald-700 mb-2">
                Register
            </h3>
            <p class="text-slate-600 text-sm">
                Create a free account to begin tracking your eco-friendly actions.
            </p>
        </div>

        <!-- Log -->
        <div class="glass rounded-3xl p-8 text-center shadow-xl hover:scale-105 transition">
            <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-2xl">
                <i class="fa-solid fa-recycle"></i>
            </div>
            <h3 class="font-extrabold text-lg text-emerald-700 mb-2">
                Log Activities
            </h3>
            <p class="text-slate-600 text-sm">
                Record recycling activities such as plastic, paper, and glass disposal.
            </p>
        </div>

        <!-- Impact -->
        <div class="glass rounded-3xl p-8 text-center shadow-xl hover:scale-105 transition">
            <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-2xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3 class="font-extrabold text-lg text-emerald-700 mb-2">
                View Impact
            </h3>
            <p class="text-slate-600 text-sm">
                Monitor your progress and see how your actions reduce waste over time.
            </p>
        </div>

        <!-- Centers -->
        <div class="glass rounded-3xl p-8 text-center shadow-xl hover:scale-105 transition">
            <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-2xl">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <h3 class="font-extrabold text-lg text-emerald-700 mb-2">
                Find Centers
            </h3>
            <p class="text-slate-600 text-sm">
                Locate nearby recycling centers and disposal points with ease.
            </p>
        </div>

    </div>

    <!-- CTA -->
    <div class="glass rounded-3xl p-10 shadow-2xl text-center max-w-4xl mx-auto">
        <h2 class="text-3xl font-extrabold text-slate-900 mb-4">
            Ready to Start Making an Impact?
        </h2>
        <p class="text-slate-600 mb-8">
            Join thousands of eco-warriors taking small steps toward a greener future.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php"
               class="px-8 py-4 rounded-xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition">
                Create Free Account
            </a>
            <a href="login.php"
               class="px-8 py-4 rounded-xl bg-white border border-emerald-200 text-emerald-700 font-extrabold hover:bg-emerald-50 transition">
                Login
            </a>
        </div>
    </div>

</main>

</body>
</html>
