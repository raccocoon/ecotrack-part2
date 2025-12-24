<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>


<h1>Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?></p>
<p>Role: <?= htmlspecialchars($_SESSION["user_role"]) ?></p>


