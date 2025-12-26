<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/bootstrap.php";

$user_id = $_SESSION['user_id'];
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

// Get user points
$stmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_points = $user['points'] ?? 0;

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [
    'Reusable Products',
    'Household Items',
    'Recycling Tools',
    'Lifestyle Accessories',
    'Energy Saving',
    'Educational Items'
];
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <!-- Points Banner -->
    <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-100 mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-emerald-800">Eco-Store Marketplace</h2>
            <p class="text-emerald-600 text-sm">Use your points to redeem discounts or buy eco-friendly tools.</p>
        </div>
        <div class="text-right">
            <span class="block text-xs text-gray-500 uppercase font-bold">Your Balance</span>
            <span class="text-2xl font-bold text-gray-800"><?= number_format($user_points) ?> Pts</span>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <a href="shop.php?category=all" 
           class="<?= $category === 'all' ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition">
            All Items
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="shop.php?category=<?= urlencode($cat) ?>" 
               class="<?= $category === $cat ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?> px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition">
                <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <?php
                $displayPrice = $product['sale_price'] ?? $product['price'];
                $onSale = $product['sale_price'] !== null && $product['sale_price'] < $product['price'];
            ?>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-lg transition group">
                <div class="h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center relative overflow-hidden">
                    <span class="text-6xl text-gray-300 group-hover:scale-110 transition">dYOñ</span>
                    <?php if ($onSale): ?>
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Sale</span>
                    <?php endif; ?>
                </div>
                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-xs text-gray-500 mb-3"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</p>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2">
                            <?php if ($onSale): ?>
                                <span class="text-sm text-slate-400 line-through">RM <?= number_format($product['price'], 2) ?></span>
                            <?php endif; ?>
                            <span class="font-bold text-emerald-700 text-lg">RM <?= number_format($displayPrice, 2) ?></span>
                        </div>
                        <?php if (($product['points_cost'] ?? 0) > 0): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                or <span class="font-semibold text-orange-600"><?= number_format($product['points_cost']) ?> pts</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="product-details.php?id=<?= $product['id'] ?>"
                       class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-600 hover:text-white transition flex items-center justify-center">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-12">
            <p class="text-slate-500 text-lg">No products found</p>
        </div>
    <?php endif; ?>
</main>

<?php include "partials/footer.php"; ?>
