<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Impact | EcoTrack</title>

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
    <div class="text-center mb-16">
        <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
            Our Environmental Impact
        </h1>
        <p class="text-xl text-slate-600 max-w-3xl mx-auto">
            Every small action matters. EcoTrack empowers individuals and communities
            to create measurable environmental change through responsible waste tracking.
        </p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">

        <div class="glass rounded-3xl p-8 shadow-xl text-center">
            <div class="text-4xl font-extrabold text-emerald-600 mb-2">
                12,500 kg
            </div>
            <p class="text-slate-600 font-semibold">
                Waste Diverted from Landfills
            </p>
        </div>

        <div class="glass rounded-3xl p-8 shadow-xl text-center">
            <div class="text-4xl font-extrabold text-emerald-600 mb-2">
                5,000+
            </div>
            <p class="text-slate-600 font-semibold">
                Active Eco-Warriors
            </p>
        </div>

        <div class="glass rounded-3xl p-8 shadow-xl text-center">
            <div class="text-4xl font-extrabold text-emerald-600 mb-2">
                18
            </div>
            <p class="text-slate-600 font-semibold">
                Recycling Centers Mapped
            </p>
        </div>

    </div>

    <!-- EXPLANATION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">

        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 mb-4">
                Turning Data into Positive Change
            </h2>
            <p class="text-slate-600 mb-4">
                EcoTrack transforms everyday recycling actions into meaningful insights.
                By tracking waste categories and quantities, users become more aware of
                their consumption habits.
            </p>
            <p class="text-slate-600">
                Over time, these small decisions contribute to reduced landfill usage,
                lower carbon emissions, and a cleaner environment for future generations.
            </p>
        </div>

        <div class="glass rounded-3xl p-8 shadow-xl">
            <ul class="space-y-4 text-slate-600">
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-emerald-600 mt-1"></i>
                    Encourages responsible waste segregation
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-emerald-600 mt-1"></i>
                    Promotes environmental awareness through data
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-emerald-600 mt-1"></i>
                    Supports sustainable community behavior
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-emerald-600 mt-1"></i>
                    Aligns with global sustainability goals
                </li>
            </ul>
        </div>

    </div>

    <!-- CTA -->
    <div class="glass rounded-3xl p-10 shadow-2xl text-center max-w-4xl mx-auto">
        <h2 class="text-3xl font-extrabold text-slate-900 mb-4">
            Be Part of the Impact
        </h2>
        <p class="text-slate-600 mb-8">
            Join EcoTrack and contribute to a healthier planet by tracking your recycling actions.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="register.php"
               class="px-8 py-4 rounded-xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition">
                Get Started
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
