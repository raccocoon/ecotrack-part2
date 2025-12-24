<?php
require "config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST["firstName"]);
    $lastName  = trim($_POST["lastName"]);
    $email     = trim($_POST["email"]);
    $password  = $_POST["password"];

    // Check duplicate email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email already registered');</script>";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare(
        "INSERT INTO users (first_name, last_name, email, password, role)
         VALUES (?, ?, ?, ?, 'member')"
    );

    $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | EcoTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        .bg-gradient-mesh {
            background-color: #f0fdf4;
            background-image:
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.1) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(5, 150, 105, 0.1) 0, transparent 50%);
        }
    </style>
</head>

<body class="bg-gradient-mesh min-h-screen">
    
<!-- NAV -->
<nav class="flex justify-between items-center px-8 py-4 max-w-7xl mx-auto">
    
    <!-- Logo -->
    <a href="index.php">
        <img src="assets/ecotrack-logo.png"
             alt="EcoTrack Logo"
             class="h-20 w-auto object-contain">
    </a>

    <!-- Right-side navigation -->
    <div class="flex gap-3">
        <a href="index.php"
           class="px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
            Home
        </a>

        <a href="login.php"
           class="bg-white/60 border border-emerald-100 px-5 py-2 rounded-full font-semibold text-emerald-700 hover:bg-emerald-50 transition">
            Login
        </a>
    </div>

</nav>


<!-- REGISTER CARD -->
<main class="flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Join EcoTrack</h1>
            <p class="text-slate-600">
                Start tracking your environmental impact today
            </p>
        </div>

        <div class="glass border border-white shadow-2xl rounded-3xl p-8">

            <form id="registerForm" method="POST" action="register.php" class="space-y-5" novalidate>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">
                            First Name
                        </label>

                        <input id="firstName" name="firstName" type="text" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition">
                            
                        <p id="firstNameError" class="text-sm text-red-500 mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">
                            Last Name
                        </label>

                        <input id="lastName" name="lastName" type="text" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                                      focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition">
                    
                        <p id="lastNameError" class="text-sm text-red-500 mt-1 hidden"></p>              
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">
                        Email Address
                    </label>

                    <input id="email" name="email" type="email" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                                  focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition">
                    <p id="emailError" class="text-sm text-red-500 mt-1 hidden"></p>              
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">
                        Password
                    </label>

                    <input id="password" name="password" type="password" minlength="8" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                                  focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition">
                    <p id="passwordError" class="text-sm text-red-500 mt-1 hidden"></p>
                    <p class="text-xs text-slate-400 ml-1 mt-1">
                        Must be at least 8 characters
                    </p>
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

<script>
document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let valid = true;

    const firstName = document.getElementById("firstName");
    const lastName = document.getElementById("lastName");
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    const firstNameError = document.getElementById("firstNameError");
    const lastNameError = document.getElementById("lastNameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    // Reset errors
    [firstNameError, lastNameError, emailError, passwordError].forEach(el => {
        el.classList.add("hidden");
        el.textContent = "";
    });

    if (firstName.value.trim() === "") {
        firstNameError.textContent = "First name is required";
        firstNameError.classList.remove("hidden");
        valid = false;
    }

    if (lastName.value.trim() === "") {
        lastNameError.textContent = "Last name is required";
        lastNameError.classList.remove("hidden");
        valid = false;
    }

    if (!/^\S+@\S+\.\S+$/.test(email.value)) {
        emailError.textContent = "Enter a valid email address";
        emailError.classList.remove("hidden");
        valid = false;
    }

    if (password.value.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters";
        passwordError.classList.remove("hidden");
        valid = false;
    }

    if (valid) {
        e.target.submit();
    }
});
</script>


</body>
</html>
