<?php
require 'db.php';

echo "<link rel='stylesheet' href='style.css'>";

$user_id = 1;

$stmt = $db->prepare("
    SELECT 
        p.name,
        p.price,
        c.quantity,
        (p.price * c.quantity) AS total
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = :uid
");
$stmt->bindValue(':uid', $user_id, SQLITE3_INTEGER);
$results = $stmt->execute();

$hasItems = false;
$grandTotal = 0;

echo "<h2>Ваш кошик:</h2>";

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Назва товару</th><th>Ціна</th><th>Кількість</th><th>Вартість</th></tr>";

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $hasItems = true;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . $row['price'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "<td>" . $row['total'] . "</td>";
    echo "</tr>";
    $grandTotal += $row['total'];
}

echo "</table>";

if ($hasItems) {
    echo "<h3>Разом до сплати: {$grandTotal}</h3>";
    echo "<p><a href='main.php?page=receipt'>Переглянути чек</a></p>";
} else {
    echo "<p>Кошик порожній.</p>";
}

?>
