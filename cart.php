<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/bootstrap.php";

$user_id = $_SESSION['user_id'];

// Get cart items with product details
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, c.payment_type, p.id as product_id, p.name, p.price, p.sale_price, p.points_cost, p.stock, p.image_path
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cash_subtotal = 0;
$points_subtotal = 0;
foreach ($cart_items as $item) {
    if ($item['payment_type'] === 'points') {
        $points_subtotal += ($item['points_cost'] ?? 0) * $item['quantity'];
    } else {
        $price = $item['sale_price'] ?? $item['price'];
        $cash_subtotal += $price * $item['quantity'];
    }
}

$shipping = ($cash_subtotal + $points_subtotal) > 0 ? 10.00 : 0;
$cash_total = $cash_subtotal + $shipping;

$message = $_GET['msg'] ?? '';
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Shopping Cart (<?= count($cart_items) ?> Items)
    </h1>
    <p class="text-slate-600 mb-8">Review your items before checkout</p>

    <?php if ($message === 'updated'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700">
            ✓ Cart updated successfully
        </div>
    <?php elseif ($message === 'removed'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700">
            ✓ Item removed from cart
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="glass rounded-3xl p-12 text-center shadow-xl">
            <div class="text-6xl mb-4">🛒</div>
            <h2 class="text-2xl font-bold text-slate-700 mb-2">Your cart is empty</h2>
            <p class="text-slate-500 mb-6">Start shopping to add items to your cart</p>
            <a href="shop.php" class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($cart_items as $item): ?>
                    <div class="flex gap-4 bg-white p-4 rounded-xl border border-gray-100 items-center shadow-sm">
                        <div class="w-20 h-20 bg-slate-100 rounded-lg flex items-center justify-center text-4xl">
                            🌱
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="text-xs text-gray-500">Stock: <?= $item['stock'] ?> available</p>
                            <p class="text-xs <?= $item['payment_type'] === 'points' ? 'text-orange-600 font-semibold' : 'text-gray-600' ?>">
                                Payment: <?= $item['payment_type'] === 'points' ? '⭐ Points' : '💵 Cash' ?>
                            </p>
                        </div>
                        <form method="POST" action="actions/cart-update.php" class="flex items-center gap-2">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>"
                                    class="w-8 h-8 bg-gray-100 rounded text-sm hover:bg-gray-200 transition">-</button>
                            <span class="text-sm font-bold w-8 text-center"><?= $item['quantity'] ?></span>
                            <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>"
                                    class="w-8 h-8 bg-gray-100 rounded text-sm hover:bg-gray-200 transition">+</button>
                        </form>
                        <div class="font-bold text-gray-800 w-24 text-right">
                            <?php if ($item['payment_type'] === 'points'): ?>
                                <?= number_format(($item['points_cost'] ?? 0) * $item['quantity']) ?> pts
                            <?php else: ?>
                                RM <?= number_format(($item['sale_price'] ?? $item['price']) * $item['quantity'], 2) ?>
                            <?php endif; ?>
                        </div>
                        <a href="actions/cart-remove.php?id=<?= $item['cart_id'] ?>"
                           onclick="return confirm('Remove this item from cart?')"
                           class="text-red-400 hover:text-red-600 transition">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 h-fit shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 text-lg">Order Summary</h3>
                <?php if ($cash_subtotal > 0): ?>
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>Cash Subtotal</span>
                        <span>RM <?= number_format($cash_subtotal, 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($points_subtotal > 0): ?>
                    <div class="flex justify-between text-sm text-orange-600 mb-2">
                        <span>Points Subtotal</span>
                        <span><?= number_format($points_subtotal) ?> pts</span>
                    </div>
                <?php endif; ?>
                <?php if ($cash_subtotal > 0): ?>
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>Shipping</span>
                        <span>RM <?= number_format($shipping, 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between text-sm text-green-600 mb-4">
                    <span>Discount</span>
                    <span>- RM 0.00</span>
                </div>
                <?php if ($cash_subtotal > 0): ?>
                    <div class="border-t pt-4 flex justify-between font-bold text-lg text-gray-800 mb-2">
                        <span>Cash Total</span>
                        <span>RM <?= number_format($cash_total, 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($points_subtotal > 0): ?>
                    <div class="flex justify-between font-bold text-lg text-orange-600 mb-6">
                        <span>Points Total</span>
                        <span><?= number_format($points_subtotal) ?> pts</span>
                    </div>
                <?php endif; ?>
                <a href="checkout.php"
                   class="block w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 shadow-md transition text-center">
                    Checkout Now
                </a>
                <a href="shop.php" class="block w-full text-center text-emerald-600 font-semibold py-3 hover:underline">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include "partials/footer.php"; ?>
