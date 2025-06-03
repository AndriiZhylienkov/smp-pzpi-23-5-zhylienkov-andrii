<?php
require 'db.php';
echo "<link rel='stylesheet' href='style.css'>";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = "Будь ласка, заповніть усі поля.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result && password_verify($password, $result['password_hash'])) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            header("Location: main.php?page=home");
            exit;
        } else {
            $errors[] = "Невірний логін або пароль.";
        }
    }
}
?>

<h2>Вхід до системи</h2>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <label>Логін:<br>
        <input type="text" name="username" required>
    </label><br><br>

    <label>Пароль:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <button type="submit">Увійти</button>
</form>

<p><a href="main.php?page=register">Створити акаунт</a></p>
