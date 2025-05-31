<?php
session_start();
require 'config/config.php'; // Убедитесь, что путь к config.php правильный

$error = '';
$full_name_val = '';
$login_val = '';
$phone_val = '';
$email_val = ''; // New variable for email
$position_id_val = '';
$birth_date_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? ''); // Get email from POST
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $position_id = $_POST['position'] ?? '';
    $birth_date = $_POST['birth_date'] ?? null;

    // Preserve input values
    $full_name_val = $full_name;
    $login_val = $login;
    $phone_val = $phone;
    $email_val = $email; // Preserve email
    $position_id_val = $position_id;
    $birth_date_val = $birth_date;

    // Validation
    if (empty($full_name)) {
        $error = 'Введите ФИО';
    } elseif (empty($login)) {
        $error = 'Введите логин';
    } elseif (empty($email)) { // Email validation
        $error = 'Введите Email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный формат Email';
    } elseif (empty($phone)) {
        $error = 'Введите номер телефона';
    } elseif (!preg_match('/^\+7[0-9]{10}$/', $phone)) {
        $error = 'Номер телефона должен быть в формате +7XXXXXXXXXX';
    } elseif (empty($password)) {
        $error = 'Введите пароль';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (empty($position_id)) {
        $error = 'Выберите тип клиента';
    } elseif (!empty($birth_date)) {
        $date = DateTime::createFromFormat('Y-m-d', $birth_date);
        if (!$date || $date->format('Y-m-d') !== $birth_date) {
            $error = 'Некорректная дата рождения';
        }
    }

    if (empty($error)) {
        try {
            // Check for existing login, phone, or email
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE login = ? OR phone_number = ? OR email = ?");
            $stmt_check->execute([$login, $phone, $email]);

            if ($stmt_check->fetchColumn() > 0) {
                // More specific error messages could be implemented here
                // For instance, query which field caused the conflict.
                $stmt_check_login = $pdo->prepare("SELECT COUNT(*) FROM user WHERE login = ?");
                $stmt_check_login->execute([$login]);
                if ($stmt_check_login->fetchColumn() > 0) {
                    $error = 'Пользователь с таким логином уже существует';
                } else {
                    $stmt_check_phone = $pdo->prepare("SELECT COUNT(*) FROM user WHERE phone_number = ?");
                    $stmt_check_phone->execute([$phone]);
                    if ($stmt_check_phone->fetchColumn() > 0) {
                        $error = 'Пользователь с таким телефоном уже существует';
                    } else {
                         $error = 'Пользователь с таким email уже существует';
                    }
                }
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO user
                    (full_name, login, email, phone_number, password, position_id, birth_date, tariff_id) -- Added email
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?) -- Added placeholder for email
                ");

                $default_tariff = ($position_id == 4) ? 55 : 5; // As per existing logic
                $stmt->execute([
                    $full_name,
                    $login,
                    $email, // Save email
                    $phone,
                    $hashed_password,
                    $position_id,
                    $birth_date ? $birth_date : null,
                    $default_tariff
                ]);

                $_SESSION['registration_success'] = 'Регистрация прошла успешно! Теперь вы можете войти.';
                header('Location: login.php');
                exit();
            }
        } catch (PDOException $e) {
            error_log('Ошибка при регистрации: ' . $e->getMessage());
            // Check for unique constraint violation specifically for email if possible (depends on DB error code)
            if ($e->getCode() == '23000') { // Integrity constraint violation
                 $error = 'Пользователь с таким логином, email или телефоном уже существует.';
            } else {
                $error = 'Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png">
    <title>Регистрация</title>
    <style>
        body {
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container { width: 100%; max-width: 400px; }
        .auth-box { background: rgba(255, 255, 255, 0.95); padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .auth-header { text-align: center; margin-bottom: 1.5rem; }
        .auth-header img { width: 80px; margin-bottom: 1rem; }
        .auth-header h1 { color: #000; font-size: 24px; margin: 0; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; color: #000; font-weight: 500; margin-bottom: 0.5rem; }
        .auth-input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 16px; box-sizing: border-box; }
        .auth-input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); }
        button[type="submit"] { background: #000; color: #fff; width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 16px; margin-top: 1rem; cursor: pointer; transition: background 0.3s; }
        button[type="submit"]:hover { background: #333; }
        .back-button { display: block; width: 100%; padding: 12px; border: 2px solid #000; border-radius: 6px; background: transparent; color: #000; text-align: center; margin-top: 1rem; text-decoration: none; box-sizing: border-box; transition: all 0.3s; }
        .back-button:hover { background: #f0f0f0; }
        .alert { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="images/logo.png" alt="Логотип">
                <h1>Регистрация</h1>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php" novalidate>
                <div class="form-group">
                    <label for="full_name">ФИО</label>
                    <input type="text" id="full_name" name="full_name" class="auth-input"
                           value="<?php echo htmlspecialchars($full_name_val); ?>" required>
                </div>

                <div class="form-group">
                    <label for="birth_date">Дата рождения</label>
                    <input type="date" id="birth_date" name="birth_date" class="auth-input"
                           value="<?php echo htmlspecialchars($birth_date_val); ?>">
                </div>

                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" id="login" name="login" class="auth-input"
                           value="<?php echo htmlspecialchars($login_val); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label> <input type="email" id="email" name="email" class="auth-input"
                           placeholder="your.email@example.com"
                           value="<?php echo htmlspecialchars($email_val); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="phone" class="auth-input"
                           pattern="\+7[0-9]{10}" placeholder="+7XXXXXXXXXX"
                           value="<?php echo htmlspecialchars($phone_val); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="auth-input" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="auth-input" required>
                </div>

                <div class="form-group">
                    <label for="position">Тип клиента</label>
                    <select id="position" name="position" class="auth-input" required>
                        <option value="">Выберите тип клиента</option>
                        <option value="3" <?php echo ($position_id_val == '3') ? 'selected' : ''; ?>>Частный клиент</option>
                        <option value="4" <?php echo ($position_id_val == '4') ? 'selected' : ''; ?>>Бизнес клиент</option>
                    </select>
                </div>

                <button type="submit">Зарегистрироваться</button>
                <a href="login.php" class="back-button">Назад к входу</a>
            </form>
        </div>
    </div>
</body>
</html>