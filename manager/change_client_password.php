<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_login = $_POST['client_login'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($client_login)) {
        $error = 'Введите логин клиента';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Новый пароль и подтверждение не совпадают';
    } elseif (strlen($new_password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE login = ?");
        $update_stmt->bind_param("ss", $hashed_password, $client_login);
        
        if ($update_stmt->execute()) {
            $success = 'Пароль клиента успешно изменен';
        } else {
            $error = 'Ошибка при обновлении пароля';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена пароля клиенту</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            background-image: url('../images/background.jpg');
            background-size: cover;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
        }
        .form-label {
            color: white;
            font-weight: 500;
        }
        .alert {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Смена пароля клиенту</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Логин клиента</label>
                    <input type="text" class="form-control" name="client_login" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Новый пароль</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Подтвердите новый пароль</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="settings.php" class="btn btn-secondary">Назад</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>