<?php
require 'db.php';
echo "<link rel='stylesheet' href='style.css'>";

$user_id = 1;

echo "<h2>Підсумковий рахунок</h2>";

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

$total = 0;
$hasItems = false;

echo "<table>";
echo "<tr><th>Назва</th><th>Ціна</th><th>Кількість</th><th>Сума</th></tr>";

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $hasItems = true;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . $row['price'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "<td>" . $row['total'] . "</td>";
    echo "</tr>";
    $total += $row['total'];
}
echo "</table>";

if ($hasItems) {
    echo "<h3>Разом до сплати: <strong>{$total}</strong></h3>";

    $clear = $db->prepare("DELETE FROM cart WHERE user_id = :uid");
    $clear->bindValue(':uid', $user_id, SQLITE3_INTEGER);
    $clear->execute();

    echo "<p>Дякуємо за покупку!</p>";
} else {
    echo "<p>Ваш кошик порожній.</p>";
}

?>
