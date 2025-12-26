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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - EcoTrack</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); }
.bg-gradient-mesh {
    background-color: #f0fdf4;
    background-image:
        radial-gradient(at 0% 0%, rgba(16,185,129,0.1) 0, transparent 50%),
        radial-gradient(at 100% 100%, rgba(5,150,105,0.1) 0, transparent 50%);
}
</style>
</head>

<body class="bg-gradient-mesh min-h-screen">

<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    <div class="flex items-center gap-3">
        <img src="assets/images/ecotrack-logo.png" class="h-20" alt="EcoTrack Logo">
    </div>

    <div class="hidden md:flex items-center gap-10 font-semibold text-emerald-900/70">
        <a href="index.php" class="hover:text-emerald-600">Home</a>
        <a href="login.php" class="bg-white/50 border border-emerald-100 px-5 py-2 rounded-full hover:text-emerald-600">Login</a>
    </div>
</nav>

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

</body>
</html>
