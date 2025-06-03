<?php

$user_id = $_SESSION['user_id'];
$profileFile = 'user_profile.php';
$allProfiles = file_exists($profileFile) ? include($profileFile) : [];

$profile = $allProfiles[$user_id] ?? [];
$errors = [];
$success = false;

if (!function_exists('calculateAge')) {
    function calculateAge($birthDate) {
        $today = new DateTime();
        $dob = DateTime::createFromFormat('Y-m-d', $birthDate);
        return $dob ? $today->diff($dob)->y : 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $birthDate = $_POST['birth_date'] ?? '';
    $about = trim($_POST['about'] ?? '');

    if ($firstName === '' || strlen($firstName) < 2) {
        $errors[] = "Ім’я має містити щонайменше 2 символи.";
    }

    if ($lastName === '' || strlen($lastName) < 2) {
        $errors[] = "Прізвище має містити щонайменше 2 символи.";
    }

    if ($birthDate === '' || calculateAge($birthDate) < 16) {
        $errors[] = "Користувачеві має бути не менше 16 років.";
    }

    if (strlen($about) < 50) {
        $errors[] = "Стисла інформація повинна містити не менше 50 символів.";
    }

    $photoPath = $profile['photo'] ?? '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
            $errors[] = "Фото повинне бути JPG, PNG або GIF.";
        } else {
            if (!is_dir('uploads')) {
                mkdir('uploads');
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoName = "user{$user_id}_" . time() . '.' . $ext;
            $photoPath = 'uploads/' . $photoName;
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
        }
    }

    if (empty($errors)) {
        $allProfiles[$user_id] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_date' => $birthDate,
            'about' => $about,
            'photo' => $photoPath
        ];
        file_put_contents($profileFile, "<?php\nreturn " . var_export($allProfiles, true) . ";\n");
        $profile = $allProfiles[$user_id];
        $success = true;
    }
}
?>

<h2>Мій профіль</h2>

<?php if ($success): ?>
    <p style="color: green;">Профіль збережено успішно!</p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Ім’я:<br>
        <input type="text" name="first_name" required value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>">
    </label><br><br>

    <label>Прізвище:<br>
        <input type="text" name="last_name" required value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>">
    </label><br><br>

    <label>Дата народження:<br>
        <input type="date" name="birth_date" required value="<?= htmlspecialchars($profile['birth_date'] ?? '') ?>">
    </label><br><br>

    <label>Стисла інформація:<br>
        <textarea name="about" rows="5" cols="40" required><?= htmlspecialchars($profile['about'] ?? '') ?></textarea>
    </label><br><br>

    <label>Фото:<br>
        <input type="file" name="photo" accept="image/*">
    </label><br><br>

    <?php if (!empty($profile['photo']) && file_exists($profile['photo'])): ?>
        <img src="<?= htmlspecialchars($profile['photo']) ?>" alt="Фото користувача" width="150"><br><br>
    <?php endif; ?>

    <button type="submit">Зберегти</button>
</form>

