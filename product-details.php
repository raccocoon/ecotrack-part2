<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/bootstrap.php";

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get user points
$stmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_points = $user['points'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: shop.php");
    exit;
}

$message = $_GET['msg'] ?? '';

$displayPrice = $product['sale_price'] ?? $product['price'];
$onSale = $product['sale_price'] !== null && $product['sale_price'] < $product['price'];
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <?php if ($message === 'added'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-center">
            ƒo" Added to cart successfully!
        </div>
    <?php endif; ?>

    <div class="flex gap-4 mb-6">
        <a href="shop.php" class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition">
            ƒ+? Back to Shop
        </a>
    </div>

    <div class="glass rounded-3xl p-8 shadow-xl">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-slate-100 rounded-2xl h-96 flex items-center justify-center text-9xl">
                dYOñ
            </div>
            
            <div>
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold mb-3">
                    <?= htmlspecialchars($product['category']) ?>
                </span>
                <h1 class="text-3xl font-extrabold text-slate-900 mb-4"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-slate-600 mb-6"><?= htmlspecialchars($product['description']) ?></p>
                
                <div class="mb-6">
                    <?php if ($onSale): ?>
                        <p class="text-sm text-slate-400 line-through mb-1">RM <?= number_format($product['price'], 2) ?></p>
                    <?php endif; ?>
                    <span class="text-4xl font-extrabold text-emerald-600">RM <?= number_format($displayPrice, 2) ?></span>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-slate-500">Stock: <?= $product['stock'] ?> available</p>
                    <?php if (($product['points_cost'] ?? 0) > 0): ?>
                        <p class="text-sm text-slate-500">Points balance: <span class="font-semibold text-orange-600"><?= number_format($user_points) ?> pts</span></p>
                    <?php endif; ?>
                </div>

                <form method="POST" action="actions/cart-add.php" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div>
                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>

                    <?php if (($product['points_cost'] ?? 0) > 0): ?>
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-2 block">Payment Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_type" value="cash" checked class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm">💵 Cash - RM <?= number_format($displayPrice, 2) ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_type" value="points" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm">⭐ Points - <?= number_format($product['points_cost']) ?> pts</span>
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="payment_type" value="cash">
                    <?php endif; ?>

                    <button type="submit"
                            class="w-full bg-emerald-600 text-white py-4 rounded-xl font-extrabold hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include "partials/footer.php"; ?>
