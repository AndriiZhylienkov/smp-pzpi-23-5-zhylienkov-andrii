<?php
require 'db.php';

echo "<link rel='stylesheet' href='style.css'>";

define('MIN_AGE', 7);
define('MAX_AGE', 150);

$user = $db->querySingle("SELECT * FROM user_profile WHERE id = 1", true);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);

    if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄ]/u', $name)) {
        $errors[] = "Імʼя має містити хоча б одну літеру.";
    }

    if ($age < MIN_AGE || $age > MAX_AGE) {
        $errors[] = "Вік має бути від " . MIN_AGE . " до " . MAX_AGE . ".";
    }

    if (empty($errors)) {
        if ($user) {
            $stmt = $db->prepare("UPDATE user_profile SET name = :name, age = :age WHERE id = 1");
        } else {
            $stmt = $db->prepare("INSERT INTO user_profile (id, name, age) VALUES (1, :name, :age)");
        }

        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':age', $age, SQLITE3_INTEGER);
        $stmt->execute();

        header("Location: products.php");
        exit;
    }
}

?>

<h2>Налаштування профілю</h2>

<?php
if (!empty($errors)) {
    echo "<ul style='color: red;'>";
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul>";
}
?>

<form method="post">
    <label>Імʼя:<br>
        <input type="text" name="name" required value="<?= htmlspecialchars($user['name'] ?? '') ?>">
    </label><br><br>
    <label>Вік:<br>
        <input type="number" name="age" required min="<?= MIN_AGE ?>" max="<?= MAX_AGE ?>" value="<?= htmlspecialchars($user['age'] ?? '') ?>">
    </label><br><br>
    <button type="submit">Зберегти</button>
</form>

<p><a href="index.php">На головну</a></p>
