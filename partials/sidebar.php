<?php
// Detect current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
?>

<aside class="w-64 bg-white border-r border-slate-200 min-h-screen fixed left-0 top-0">

    <!-- LOGO -->
    <div class="px-6 py-6 border-b">
        <img src="/ecotrack-part2/assets/images/ecotrack-logo.png"
             alt="EcoTrack Logo"
             class="h-16 mx-auto mb-3">

        <p class="text-center text-emerald-600 text-sm font-semibold italic">
            Track Your Impact,<br>Shape Our Future
        </p>
    </div>

    <!-- MENU -->
    <nav class="px-4 py-6 space-y-1 text-sm">

        <?php
        function navItem($file, $label, $icon) {
            global $currentPage;
            $active = $currentPage === $file
                ? "bg-emerald-100 text-emerald-700 font-semibold"
                : "text-slate-600 hover:bg-slate-100";
            echo "
            <a href='/ecotrack-part2/$file'
               class='flex items-center gap-3 px-4 py-3 rounded-xl transition $active'>
                <span>$icon</span>
                $label
            </a>";
        }

        navItem('dashboard.php', 'Home / Feed', '🏠');
        navItem('dashboard.php', 'User Dashboard', '📊');
        navItem('log-waste.php', 'Log Waste', '➕');
        navItem('recycling-map.php', 'Recycling Map', '🗺️');
        navItem('challenges.php', 'Community Challenges', '🏆');
        navItem('segregation-guide.php', 'Waste Segregation Guide', '📘');
        navItem('quizzes.php', 'Quizzes', '🎓');
        navItem('eco-store.php', 'Eco-Store', '🛍️');
        navItem('cart.php', 'My Cart', '🛒');
        ?>

        <?php if ($isAdmin): ?>
            <div class="mt-6 pt-4 border-t text-xs text-slate-400 uppercase px-4">
                System
            </div>

            <a href="/ecotrack-part2/admin/dashboard.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100">
                ⚙️ Admin Panel
            </a>
        <?php endif; ?>

    </nav>
</aside>
