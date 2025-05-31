<?php
session_start();

$error = '';
$success_message = ''; // Variable to hold success messages

// Show message from successful registration
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Show message from successful password reset
if (isset($_SESSION['password_reset_success'])) {
    $success_message = $_SESSION['password_reset_success'];
    unset($_SESSION['password_reset_success']);
}

// Показ сообщения о тайм-ауте
if (isset($_GET['timeout'])) {
    $error = 'Сессия истекла из-за бездействия. Пожалуйста, войдите снова.';
}

require 'config/config.php'; // Убедитесь, что путь к config.php правильный

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Пожалуйста, введите логин и пароль.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT u.*, p.name as position_name FROM user u JOIN position p ON u.position_id = p.id WHERE u.login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID upon successful login to prevent session fixation
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'login' => $user['login'], // Storing login might be useful
                    'full_name' => $user['full_name'],
                    'email' => $user['email'], // Storing email
                    'phone_number' => $user['phone_number'],
                    'position_id' => $user['position_id'],
                    'position_name' => $user['position_name'],
                    'balance' => $user['balance'],
                    'tariff_id' => $user['tariff_id']
                ];
                // Add last activity timestamp
                $_SESSION['last_activity'] = time();

                // Redirect based on user role
                if ($user['position_id'] == 1) { // Admin
                    header('Location: admin/index.php');
                } elseif ($user['position_id'] == 2) { // Manager
                    header('Location: manager/index.php');
                } elseif ($user['position_id'] == 3 || $user['position_id'] == 4) { // Clients
                    header('Location: pages/index.php');
                }
                exit();
            } else {
                $error = 'Неверный логин или пароль.';
            }
        } catch (PDOException $e) {
            error_log("PDO Error on login.php: " . $e->getMessage());
            $error = 'Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.';
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
    <title>Авторизация</title>
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
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .auth-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem; /* Increased padding for better spacing */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem; /* Consistent margin */
        }
        .auth-header img {
            width: 80px;
            margin-bottom: 1rem; /* Consistent margin */
        }
        .auth-header h1 {
            color: #000;
            font-size: 24px; /* 1.5rem */
            margin-bottom: 0.5rem;
        }
        .auth-subtitle {
            color: #555; /* Slightly muted subtitle color */
            font-size: 16px; /* 1rem */
            margin-bottom: 1.5rem;
        }
        .auth-form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 1rem; /* Consistent margin */
        }
        .form-group label {
            display: block;
            color: #000;
            font-weight: 500;
            margin-bottom: 0.5rem; /* Consistent margin */
        }
        .auth-input {
            width: 100%;
            padding: 12px; /* Increased padding */
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .auth-input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }
        button[type="submit"] {
            background: #000;
            color: #fff;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            margin-top: 1rem; /* Consistent margin */
            cursor: pointer;
            transition: background 0.3s;
        }
        button[type="submit"]:hover {
            background: #333;
        }
        .links-container {
            margin-top: 1.5rem; /* Consistent margin */
            display: flex;
            flex-direction: column;
            gap: 10px; /* Space between links */
        }
        .secondary-button, .forgot-password-button, .support-button {
            display: block;
            text-align: center;
            color: #000;
            text-decoration: none;
            padding: 12px; /* Consistent padding */
            border: 1px solid #000;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s;
            font-size: 15px; /* Slightly smaller font for secondary actions */
        }
        .secondary-button:hover, .forgot-password-button:hover, .support-button:hover {
            background: #f0f0f0;
        }
        .alert {
            padding: 0.75rem 1.25rem; /* Consistent padding */
            margin-bottom: 1rem; /* Consistent margin */
            border-radius: 0.25rem; /* Consistent border-radius */
            text-align: center;
            font-size: 15px;
        }
        .alert-danger { /* Specific class for errors */
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .alert-success { /* Specific class for success messages */
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="images/logo.png" alt="Logo">
                <h1>Добро пожаловать!</h1>
                <p class="auth-subtitle">Войдите в свой аккаунт</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" id="login" name="login" class="auth-input" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="auth-input" required>
                </div>
                <button type="submit">Войти</button>
                <div class="links-container">
                    <a href="register.php" class="secondary-button">Регистрация</a>
                    <a href="forgot_password.php" class="forgot-password-button">Забыли пароль?</a>
                    <a href="tel:88008008080" class="support-button">Позвонить в поддержку</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>