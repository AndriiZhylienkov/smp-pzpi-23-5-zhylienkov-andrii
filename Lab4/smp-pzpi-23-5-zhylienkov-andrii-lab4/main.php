<?php
session_start();
require 'header.php';

$page = $_GET['page'] ?? 'home';
$public_pages = ['login', 'register'];

if (!isset($_SESSION['user_id']) && !in_array($page, $public_pages)) {
    require 'page404.php';
} else {
    switch ($page) {
        case 'home':
            require 'index.php';
            break;
        case 'products':
            require 'products.php';
            break;
        case 'cart':
            require 'cart.php';
            break;
        case 'profile':
            require 'profile.php';
            break;
        case 'receipt':
            require 'receipt.php';
            break;
        case 'login':
            require 'login.php';
            break;
        case 'register':
            require 'register.php';
            break;
        case 'logout':
            require 'logout.php';
            break;
        default:
            require 'page404.php';
    }
}

require 'footer.php';
