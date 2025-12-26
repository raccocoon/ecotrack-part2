<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

require "../config/bootstrap.php";

$message = "";
$error = "";

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? '';
        $stock = (int)($_POST['stock'] ?? 0);
        $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $points_cost = (int)($_POST['points_cost'] ?? 0);

        if ($name && $description && $price > 0 && $category) {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, sale_price, category, stock, points_cost)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $sale_price, $category, $stock, $points_cost]);
            $message = "Product added successfully";
        } else {
            $error = "Please fill in all required fields";
        }
    } elseif ($action === 'update') {
        $id = $_POST['product_id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? '';
        $stock = (int)($_POST['stock'] ?? 0);
        $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $points_cost = (int)($_POST['points_cost'] ?? 0);

        if ($name && $description && $price > 0 && $category) {
            $stmt = $pdo->prepare("
                UPDATE products SET name = ?, description = ?, price = ?, sale_price = ?, category = ?, stock = ?, points_cost = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $price, $sale_price, $category, $stock, $points_cost, $id]);
            $message = "Product updated successfully";
        } else {
            $error = "Please fill in all required fields";
        }
    } elseif ($action === 'delete') {
        $id = $_POST['product_id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Product deleted successfully";
    }
}

// Get all products
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$categories = [
    'Reusable Products',
    'Household Items',
    'Recycling Tools',
    'Lifestyle Accessories',
    'Energy Saving',
    'Educational Items'
];

include "../partials/header.php";
?>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto px-8 py-10">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
                Product Management
            </h1>
            <p class="text-slate-600">
                Add, edit, and manage store products
            </p>
        </div>
        <a href="dashboard.php" class="bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 transition">
            ← Back to Dashboard
        </a>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($message): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700">
            ✓ <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- ADD PRODUCT FORM -->
    <div class="glass rounded-3xl p-8 shadow-xl mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Add New Product</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="hidden" name="action" value="add">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name *</label>
                <input type="text" name="name" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Category *</label>
                <select name="category" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Price (RM) *</label>
                <input type="number" step="0.01" min="0" name="price" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Sale Price (RM)</label>
                <input type="number" step="0.01" min="0" name="sale_price" placeholder="Optional"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Points Cost</label>
                <input type="number" min="0" name="points_cost" placeholder="Optional"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Quantity</label>
                <input type="number" min="0" name="stock" value="0"
                       class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Description *</label>
                <textarea name="description" rows="3" required
                          class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none"></textarea>
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                        class="w-full bg-emerald-600 text-white py-3 rounded-xl font-extrabold hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
                    Add Product
                </button>
            </div>
        </form>
    </div>

    <!-- PRODUCTS LIST -->
    <div class="glass rounded-3xl p-8 shadow-xl">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">All Products</h2>

        <?php if (empty($products)): ?>
            <p class="text-slate-500">No products added yet</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-slate-200">
                        <tr class="text-sm text-slate-500 uppercase font-bold">
                            <th class="pb-4">Product</th>
                            <th class="pb-4">Category</th>
                            <th class="pb-4">Price</th>
                            <th class="pb-4">Sale Price</th>
                            <th class="pb-4">Points</th>
                            <th class="pb-4">Stock</th>
                            <th class="pb-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($products as $product): ?>
                            <tr class="text-sm">
                                <td class="py-4">
                                    <div>
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($product['name']) ?></p>
                                        <p class="text-slate-500 text-xs line-clamp-2"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</p>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs">
                                        <?= htmlspecialchars($product['category']) ?>
                                    </span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">
                                    RM <?= number_format($product['price'], 2) ?>
                                </td>
                                <td class="py-4">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="font-semibold text-red-600">
                                            RM <?= number_format($product['sale_price'], 2) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4">
                                    <?php if ($product['points_cost'] > 0): ?>
                                        <span class="font-semibold text-orange-600">
                                            <?= number_format($product['points_cost']) ?> pts
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 font-semibold <?= $product['stock'] < 10 ? 'text-red-600' : 'text-slate-800' ?>">
                                    <?= $product['stock'] ?>
                                </td>
                                <td class="py-4">
                                    <button onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)"
                                            class="text-blue-600 hover:text-blue-800 font-semibold mr-3">
                                        Edit
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</main>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-3xl p-8 max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold text-slate-900">Edit Product</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" id="edit_product_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name *</label>
                        <input type="text" name="name" id="edit_name" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Category *</label>
                        <select name="category" id="edit_category" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Price (RM) *</label>
                        <input type="number" step="0.01" min="0" name="price" id="edit_price" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Sale Price (RM)</label>
                        <input type="number" step="0.01" min="0" name="sale_price" id="edit_sale_price"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Points Cost</label>
                        <input type="number" min="0" name="points_cost" id="edit_points_cost"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Quantity</label>
                        <input type="number" min="0" name="stock" id="edit_stock"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Description *</label>
                    <textarea name="description" id="edit_description" rows="3" required
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none"></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-extrabold hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
                        Update Product
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="flex-1 bg-slate-100 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('edit_product_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_category').value = product.category;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_sale_price').value = product.sale_price || '';
    document.getElementById('edit_points_cost').value = product.points_cost || '';
    document.getElementById('edit_stock').value = product.stock;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include "../partials/footer.php"; ?>
