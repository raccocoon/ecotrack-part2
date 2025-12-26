<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "config/bootstrap.php";

$user_id = $_SESSION['user_id'];

// Get user points
$stmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_points = $user['points'] ?? 0;

// Get cart items with sale price
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.sale_price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $price = $item['sale_price'] ?? $item['price'];
    $subtotal += $price * $item['quantity'];
}

$shipping = 10.00;
$points_discount = 0;
$max_points_discount = min($user_points / 100, $subtotal * 0.5); // 100 points = RM 1, max 50% discount
$total = $subtotal + $shipping;

// Get user info
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>

<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Checkout</h1>
    <p class="text-slate-600 mb-8">Complete your order</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="actions/checkout-process.php" class="space-y-6" id="checkoutForm" novalidate>
                <!-- Contact Information -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 text-lg">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required
                                   class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required
                                   class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 text-lg">Shipping Address</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                            <input type="text" name="address" id="address" required pattern="[A-Za-z0-9\\s,.-]+"
                                   class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                            <p class="text-xs text-red-600 mt-1 hidden" id="address-error"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">City</label>
                                <input type="text" name="city" id="city" required
                                       class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                                <p class="text-xs text-red-600 mt-1 hidden" id="city-error"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" id="postal_code" required pattern="[0-9]{5}" maxlength="5"
                                       placeholder="e.g. 93000"
                                       class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                                <p class="text-xs text-red-600 mt-1 hidden" id="postal_code-error"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method (Dummy) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 text-lg">Payment Method</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>Demo Mode:</strong> This is a dummy payment. No real transaction will occur.
                        </p>
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="card" checked class="text-emerald-600">
                            <span class="font-semibold">dY'3 Credit/Debit Card</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="ewallet" class="text-emerald-600">
                            <span class="font-semibold">dY\"ñ E-Wallet</span>
                        </label>
                    </div>
                </div>

                <!-- Points Discount -->
                <?php if ($user_points > 0): ?>
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 text-lg">Use Points for Discount</h3>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-emerald-800">
                            You have <strong><?= number_format($user_points) ?> points</strong>. Use 100 points = RM 1 discount (max 50% off)
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Points to Use</label>
                        <input type="number" name="points_to_use" value="0" min="0" max="<?= floor($max_points_discount * 100) ?>" step="100"
                               class="w-full border border-gray-300 rounded-lg p-3 bg-gray-50 focus:ring-2 focus:ring-emerald-500 outline-none"
                               onchange="updateDiscount(this.value)">
                        <p class="text-xs text-gray-500 mt-1">Max: <?= number_format(floor($max_points_discount * 100)) ?> points (RM <?= number_format($max_points_discount, 2) ?> discount)</p>
                        <div id="discount-preview" class="mt-2 text-sm font-semibold text-emerald-600"></div>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" 
                        class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 shadow-lg transition">
                    Complete Order
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 h-fit shadow-sm">
            <h3 class="font-bold text-gray-700 mb-4 text-lg">Order Summary</h3>
            
            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                <?php foreach ($cart_items as $item): ?>
                    <?php $price = $item['sale_price'] ?? $item['price']; ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600"><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                        <span class="font-semibold">RM <?= number_format($price * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span id="subtotal-display">RM <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Shipping</span>
                    <span>RM <?= number_format($shipping, 2) ?></span>
                </div>
                <div class="flex justify-between text-sm text-emerald-600" id="points-discount-row" style="display:none;">
                    <span>Points Discount</span>
                    <span id="points-discount-display">- RM 0.00</span>
                </div>
                <div class="flex justify-between font-bold text-lg text-gray-800 pt-2 border-t">
                    <span>Total</span>
                    <span id="total-display">RM <?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const subtotal = <?= $subtotal ?>;
const shipping = <?= $shipping ?>;
const maxDiscount = <?= $max_points_discount ?>;

function updateDiscount(points) {
    points = parseInt(points) || 0;
    const discount = Math.min(points / 100, maxDiscount);
    const newTotal = subtotal + shipping - discount;
    
    document.getElementById('discount-preview').textContent = 
        points > 0 ? `Discount: RM ${discount.toFixed(2)}` : '';
    
    if (discount > 0) {
        document.getElementById('points-discount-row').style.display = 'flex';
        document.getElementById('points-discount-display').textContent = `- RM ${discount.toFixed(2)}`;
        document.getElementById('total-display').textContent = `RM ${newTotal.toFixed(2)}`;
    } else {
        document.getElementById('points-discount-row').style.display = 'none';
        document.getElementById('total-display').textContent = `RM ${(subtotal + shipping).toFixed(2)}`;
    }
}

// Form validation
const form = document.getElementById('checkoutForm');
const addressInput = document.getElementById('address');
const cityInput = document.getElementById('city');
const postalCodeInput = document.getElementById('postal_code');

function showError(input, message) {
    const errorElement = document.getElementById(input.id + '-error');
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
    input.classList.add('border-red-500');
    input.classList.remove('border-gray-300');
}

function clearError(input) {
    const errorElement = document.getElementById(input.id + '-error');
    errorElement.classList.add('hidden');
    input.classList.remove('border-red-500');
    input.classList.add('border-gray-300');
}

addressInput.addEventListener('blur', function() {
    if (this.value.trim() === '') {
        showError(this, 'Address is required');
    } else if (!/^[A-Za-z0-9\\s,.-]+$/.test(this.value)) {
        showError(this, 'Address can only contain letters, numbers, spaces, and basic punctuation');
    } else {
        clearError(this);
    }
});

addressInput.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        clearError(this);
    }
});

cityInput.addEventListener('blur', function() {
    if (this.value.trim() === '') {
        showError(this, 'City is required');
    } else {
        clearError(this);
    }
});

cityInput.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        clearError(this);
    }
});

postalCodeInput.addEventListener('blur', function() {
    if (this.value.trim() === '') {
        showError(this, 'Postal code is required');
    } else if (!/^[0-9]{5}$/.test(this.value)) {
        showError(this, 'Postal code must be exactly 5 digits');
    } else {
        clearError(this);
    }
});

postalCodeInput.addEventListener('input', function() {
    if (/^[0-9]{5}$/.test(this.value)) {
        clearError(this);
    }
});

form.addEventListener('submit', function(e) {
    let hasError = false;
    
    if (addressInput.value.trim() === '') {
        showError(addressInput, 'Address is required');
        hasError = true;
    } else if (!/^[A-Za-z0-9\\s,.-]+$/.test(addressInput.value)) {
        showError(addressInput, 'Address can only contain letters, numbers, spaces, and basic punctuation');
        hasError = true;
    }
    
    if (cityInput.value.trim() === '') {
        showError(cityInput, 'City is required');
        hasError = true;
    }
    
    if (postalCodeInput.value.trim() === '') {
        showError(postalCodeInput, 'Postal code is required');
        hasError = true;
    } else if (!/^[0-9]{5}$/.test(postalCodeInput.value)) {
        showError(postalCodeInput, 'Postal code must be exactly 5 digits');
        hasError = true;
    }
    
    if (hasError) {
        e.preventDefault();
    }
});
</script>

<?php include "partials/footer.php"; ?>
