<?php
require "config/db.php";

$error = "";

$firstName = "";
$lastName  = "";
$email     = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST["firstName"] ?? "");
    $lastName  = trim($_POST["lastName"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $password  = $_POST["password"] ?? "";

    if ($firstName === "" || $lastName === "" || $email === "" || $password === "") {
        $error = "All fields are required";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";

    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";

    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered. Please login instead.";

        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (first_name, last_name, email, password, role)
                 VALUES (?, ?, ?, ?, 'member')"
            );

            $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);

            header("Location: login.php");
            exit;
        }
    }
}
?>

<?php include "partials/header.php"; ?>

<!-- REGISTER -->
<main class="flex items-center justify-center px-6 py-12">
<div class="w-full max-w-md">

<div class="text-center mb-8">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Join EcoTrack</h1>
    <p class="text-slate-600">Start tracking your environmental impact today</p>
</div>

<div class="glass shadow-2xl rounded-3xl p-8">

<?php if ($error): ?>
    <p class="mb-4 text-red-600 text-sm text-center">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST" action="register.php" class="space-y-5" novalidate>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="text-xs font-bold text-slate-500 uppercase ml-1">First Name</label>
        <input name="firstName" type="text"
               value="<?= htmlspecialchars($firstName) ?>"
               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
    </div>

    <div>
        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Last Name</label>
        <input name="lastName" type="text"
               value="<?= htmlspecialchars($lastName) ?>"
               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
    </div>
</div>

<div>
    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email Address</label>
    <input name="email" type="email"
           value="<?= htmlspecialchars($email) ?>"
           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                  focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
</div>

<div>
    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Password</label>
    <input name="password" type="password"
           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                  focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none">
    <p class="text-xs text-slate-400 ml-1 mt-1">Must be at least 8 characters</p>
</div>

<button type="submit"
        class="w-full bg-emerald-600 text-white py-4 rounded-xl font-extrabold
               hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
    Create Free Account
</button>

<div class="text-center space-y-2">
    <p class="text-xs text-slate-400">
        By signing up, you agree to our
        <a href="#" class="text-emerald-600 underline">Terms of Service</a>.
    </p>

    <p class="text-sm text-slate-500">
        Already have an account?
        <a href="login.php" class="text-emerald-600 font-semibold hover:underline">
            Sign in
        </a>
    </p>
</div>

</form>
</div>
</div>
</main>

<?php include "partials/footer.php"; ?>
