<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Магазин "Весна"</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="menu-bar">
    <a href="main.php?page=home">Home</a>
    <span>|</span>
    <a href="main.php?page=products">Products</a>
    <span>|</span>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="main.php?page=cart">Cart</a>
        <span>|</span>
        <a href="main.php?page=logout">Logout</a>
    <?php else: ?>
        <a href="main.php?page=login">Login</a>
    <?php endif; ?>
</div>
<hr>
