<?php
require 'db.php';

echo "<link rel='stylesheet' href='style.css'>";

echo "<h2>Список усіх товарів:</h2>";

$results = $db->query("SELECT * FROM products");

echo "<form method='POST' action='add_to_cart.php'>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Назва</th><th>Ціна</th><th>Кількість</th><th>Дія</th></tr>";

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . $row['price'] . "</td>";
    echo "<td>
            <input type='number' name='quantity[{$row['id']}]' min='0' max='99' value='0'>
          </td>";
    echo "<td>
            <button type='submit' name='add' value='{$row['id']}'>Додати</button>
          </td>";
    echo "</tr>";
}

echo "</table>";
echo "</form>";
echo "<p><a href='index.php'>Повернутися на головну</a></p>";
?>
