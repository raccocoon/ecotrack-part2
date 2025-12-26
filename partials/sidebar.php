<?php
// Detect current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
$currentSection = $_GET['section'] ?? 'home'; // Default to home section
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
?>

<aside class="w-64 bg-white border-r border-gray-200 fixed h-full z-20 overflow-y-auto">

    <!-- LOGO -->
    <div class="p-6 sticky top-0 bg-white z-10 flex flex-col items-center justify-center border-b border-gray-50 text-center">
        <img src="/ecotrack-part2/assets/images/ecotrack-logo.png"
             alt="EcoTrack Logo"
             class="h-20 w-auto object-contain mb-2">

        <p class="tagline-font text-xl text-green-600 font-bold leading-tight">
            "Track Your Impact,<br>Shape Our Future"
        </p>
    </div>

    <!-- MENU -->
    <nav class="pb-10 space-y-1">

        <?php
        function navItem($file, $label, $icon, $pageId = null) {
            global $currentPage, $currentSection;
            // Also highlight shop.php when on product-details.php
            $isShopActive = ($currentPage === 'shop.php' || $currentPage === 'product-details.php') && $file === 'shop.php';

            // For dashboard sections, let JavaScript handle active states
            if ($currentPage === 'dashboard.php' && $pageId) {
                $active = "sidebar-link"; // JavaScript will handle active state
            } else {
                $active = ($currentPage === $file || $isShopActive) ? "active-link" : "sidebar-link";
            }

            if ($pageId && $currentPage === 'dashboard.php') {
                $link = "onclick=\"showPage('$pageId', this)\" href=\"#\"";
            } else {
                $link = "href='/ecotrack-part2/$file'";
            }
            echo "<a $link class='flex items-center px-6 py-3 text-gray-700 $active' data-page-id='$pageId'><i class='fas $icon w-6'></i> $label</a>";
        }

        navItem('dashboard.php', 'Home / Feed', 'fa-home', 'home');
        navItem('dashboard.php', 'User Dashboard', 'fa-chart-pie', 'dashboard');
        navItem('log-waste.php', 'Log Waste', 'fa-plus-circle', 'tracker');
        navItem('recycling-map.php', 'Recycling Map', 'fa-map-marked-alt');
        navItem('challenges.php', 'Community Challenges', 'fa-trophy');
        navItem('segregation-guide.php', 'Waste Segregation Guide', 'fa-book-open');
        navItem('quiz.php', 'Quizzes', 'fa-graduation-cap');
        navItem('shop.php', 'Eco-Store', 'fa-store');
        navItem('cart.php', 'My Cart', 'fa-shopping-cart');
        ?>

        <?php if ($isAdmin): ?>
            <div class="border-t border-gray-100 my-4 pt-4">
                <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">System</p>
                <a href="/ecotrack-part2/admin/dashboard.php" class="flex items-center px-6 py-3 text-gray-600 sidebar-link">
                    <i class="fas fa-user-shield w-6"></i> Admin Panel
                </a>
            </div>
        <?php endif; ?>

    </nav>
</aside>
