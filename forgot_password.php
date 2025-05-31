<?php
session_start();
require 'config/config.php'; // Убедитесь, что путь к config.php правильный

$notification = '';
$notification_type = ''; // 'success' или 'error' для стилизации

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');

    if (empty($login)) {
        $notification = 'Пожалуйста, введите логин.';
        $notification_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM user WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $user_id = $user['id'];
                $notification = "Для восстановления пароля обратитесь к менеджеру. Назовите ваш ID пользователя: ID = " . htmlspecialchars($user_id);
                $notification_type = 'success'; // Или можно использовать нейтральный стиль
            } else {
                $notification = "Пользователь с таким логином не найден.";
                $notification_type = 'error';
            }
        } catch (PDOException $e) {
            // Для отладки: error_log("PDO Error on forgot_password.php: " . $e->getMessage());
            $notification = 'Произошла ошибка при проверке логина. Пожалуйста, попробуйте позже.';
            $notification_type = 'error';
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
    <title>Восстановление пароля</title>
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
        .forgot-password-container {
            width: 100%;
            max-width: 400px;
        }
        .auth-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-header img { /* Логотип по центру */
            width: 80px;
            margin-bottom: 1rem;
            display: block; /* Для работы margin auto */
            margin-left: auto;
            margin-right: auto;
        }
        .form-group {
            margin-bottom: 1rem;
            text-align: left; /* Выравнивание label и input по левому краю */
        }
        .form-group label {
            display: block;
            color: #000;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .auth-input { /* Стиль для поля ввода */
            width: 100%;
            padding: 10px;
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
        /* Стиль для кнопки "Проверить" (как у кнопки "Войти") */
        button[type="submit"] {
            background: #000;
            color: #fff;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            margin-top: 1rem; /* Отступ сверху для кнопки */
            cursor: pointer;
            transition: background 0.3s;
        }
        button[type="submit"]:hover {
            background: #333;
        }
        /* Стили для уведомлений */
        .notification {
            padding: 0.75rem 1.25rem;
            margin-top: 1.5rem; /* Отступ сверху перед кнопками "Назад" и "Поддержка" */
            margin-bottom: 1rem; /* Отступ от формы или кнопок ниже */
            border: 1px solid transparent;
            border-radius: 0.25rem;
            text-align: center;
            font-size: 15px;
        }
        .notification-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .notification-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        /* Кнопки "Назад к входу" и "Позвонить в поддержку", если они снова понадобятся */
        .links-container {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .back-button, .support-button-action { /* Изменил имя класса, чтобы не конфликтовать, если есть другие support-button */
            display: block;
            text-align: center;
            color: #000;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .back-button {
            border: 2px solid #000;
            background: transparent;
        }
        .back-button:hover {
            background: #f0f0f0;
        }
        .support-button-action {
            border: 1px solid #000;
        }
        .support-button-action:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="images/logo.png" alt="Логотип">
            </div>

            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" id="login" name="login" class="auth-input"
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
                </div>
                <button type="submit">Проверить</button>
            </form>

            <?php if (!empty($notification)): ?>
                <div class="notification notification-<?php echo $notification_type; ?>">
                    <?php echo $notification; ?>
                </div>
            <?php endif; ?>

            <div class="links-container">
                <a href="login.php" class="back-button">Назад ко входу</a>
                <a href="tel:88008008080" class="support-button-action">Позвонить в поддержку</a>
            </div>

        </div>
    </div>
</body>
</html>
