<?php
session_start();
require "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email === "" || $password === "") {
        $error = "Email and password are required";
    } else {

        $stmt = $pdo->prepare(
            "SELECT id, first_name, last_name, email, password, role
             FROM users
             WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            // Store session data
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["first_name"];
            $_SESSION["user_role"] = $user["role"];

            // Redirect based on role
            if ($user["role"] === "admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;

        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | EcoTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>
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
    <a href="index.php">
        <img src="assets/ecotrack-logo.png" class="h-20">
    </a>

    <div class="flex gap-3">
        <a href="index.php" class="px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
            Home
        </a>
        <a href="register.php" class="bg-white/60 border border-emerald-100 px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
            Register
        </a>
    </div>
</nav>

<!-- LOGIN -->
<main class="flex items-center justify-center px-6 min-h-[calc(100vh-96px)]">
<div class="w-full max-w-md">

<div class="text-center mb-8">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        Welcome Back
    </h1>
    <p class="text-slate-600">
        Continue your eco-friendly journey
    </p>
</div>

<div class="glass shadow-2xl rounded-3xl p-8">

<?php if ($error): ?>
    <p class="mb-4 text-red-600 text-sm text-center">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST" novalidate class="space-y-6">

    <div>
        <label class="text-xs font-bold text-slate-500 uppercase ml-1">
            Email Address
        </label>
        <input type="email" name="email" required
               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500
                      outline-none transition">
    </div>

    <div>
        <label class="text-xs font-bold text-slate-500 uppercase ml-1">
            Password
        </label>
        <input type="password" name="password" required
               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500
                      outline-none transition">
    </div>


    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-slate-600">
            <input type="checkbox" class="rounded border-slate-300 text-emerald-600">
            Remember me
        </label>
        <a href="#" class="text-emerald-600 font-semibold hover:underline">
            Forgot password?
        </a>
    </div>

    <button type="submit"
            class="w-full bg-emerald-600 text-white py-4 rounded-xl font-extrabold
                   hover:bg-emerald-700 shadow-xl shadow-emerald-200
                   transition-all hover:-translate-y-1">
        Sign In
    </button>

    <p class="text-center text-sm text-slate-500">
        Don’t have an account?
        <a href="register.php" class="text-emerald-600 font-semibold hover:underline">
            Create one
        </a>
    </p>

</form>
</div>
</div>
</main>

</body>
</html>
