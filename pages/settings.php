<?php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/config.php';
$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$error = '';
$success = '';

// Получаем ФИО пользователя для отображения
$stmt = $conn->prepare("SELECT full_name FROM user WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки профиля</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            background-image: url('../images/background.jpg');
            background-size: cover;
            min-height: 100vh;
        }
        .settings-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
        }
        .service-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .service-card:hover {
            transform: translateY(-5px);
        }
        .service-title {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .service-description {
            color: #4a5568;
            margin-bottom: 20px;
        }
        .btn-service {
            background: #007bff;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-service:hover {
            background: #0056b3;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .current-user-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <div class="container settings-container">
        <div class="current-user-info">
            <h3>Текущий пользователь</h3>
            <p class="mb-0"><?= htmlspecialchars($current_user['full_name']) ?></p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Карточка персональных данных -->
            <div class="col-md-6">
                <div class="service-card">
                    <h4 class="service-title">📝 Персональные данные</h4>
                    <p class="service-description">
                        Обновите ваши личные данные и загрузите документы для верификации
                    </p>
                    <a href="settings_personal.php" class="btn btn-service w-100">Редактировать</a>
                </div>
            </div>

            <!-- Карточка смены пароля -->
            <div class="col-md-6">
                <div class="service-card">
                    <h4 class="service-title">🔒 Безопасность</h4>
                    <p class="service-description">
                        Измените пароль для повышения безопасности вашего аккаунта
                    </p>
                    <a href="change_password.php" class="btn btn-service w-100">Сменить пароль</a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary px-5">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>