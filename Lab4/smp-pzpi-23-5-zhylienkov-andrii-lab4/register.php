<?php
require 'db.php';
echo "<link rel='stylesheet' href='style.css'>";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄ]/u', $name)) {
        $errors[] = "Імʼя має містити хоча б одну літеру.";
    }

    if ($age < 7 || $age > 150) {
        $errors[] = "Вік має бути від 7 до 150.";
    }

    if (strlen($username) < 4) {
        $errors[] = "Логін повинен містити щонайменше 4 символи.";
    }

    if (strlen($password) < 5) {
        $errors[] = "Пароль повинен містити щонайменше 5 символів.";
    }

    if ($password !== $confirm) {
        $errors[] = "Паролі не співпадають.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (name, age, username, password_hash) VALUES (:name, :age, :username, :hash)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':age', $age, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':hash', $hash, SQLITE3_TEXT);

        try {
            $stmt->execute();
            header("Location: main.php?page=login");
            exit;
        } catch (Exception $e) {
            $errors[] = "Користувач із таким логіном вже існує.";
        }
    }
}
?>

<h2>Реєстрація</h2>

<?php if ($errors): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <label>Імʼя:<br>
        <input type="text" name="name" required>
    </label><br><br>

    <label>Вік:<br>
        <input type="number" name="age" required min="16" max="150">
    </label><br><br>

    <label>Логін:<br>
        <input type="text" name="username" required>
    </label><br><br>

    <label>Пароль:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Підтвердження пароля:<br>
        <input type="password" name="confirm" required>
    </label><br><br>

    <button type="submit">Зареєструватися</button>
</form>
