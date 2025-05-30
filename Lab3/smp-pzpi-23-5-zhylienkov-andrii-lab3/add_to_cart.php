<?php
require 'db.php';

echo "<link rel='stylesheet' href='style.css'>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $product_id = (int)$_POST['add'];
    $quantity = isset($_POST['quantity'][$product_id]) ? (int)$_POST['quantity'][$product_id] : 0;

    if ($quantity < 1 || $quantity > 99) {
        echo "Помилка: кількість повинна бути від 1 до 99.";
        echo "<p><a href='products.php'>Повернутися</a></p>";
        exit;
    }

    $user_id = 1;

    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = :uid AND product_id = :pid");
    $stmt->bindValue(':uid', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
    $existing = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;
        $update = $db->prepare("UPDATE cart SET quantity = :qty WHERE id = :id");
        $update->bindValue(':qty', $new_qty, SQLITE3_INTEGER);
        $update->bindValue(':id', $existing['id'], SQLITE3_INTEGER);
        $update->execute();
    } else {
        $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)");
        $insert->bindValue(':uid', $user_id, SQLITE3_INTEGER);
        $insert->bindValue(':pid', $product_id, SQLITE3_INTEGER);
        $insert->bindValue(':qty', $quantity, SQLITE3_INTEGER);
        $insert->execute();
    }

    header("Location: products.php");
    exit;
}

echo "Невірний запит.";
