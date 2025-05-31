<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Определение приветствия по времени суток
$hour = date('H');
if ($hour < 6) $greeting = 'Доброй ночи';
elseif ($hour < 12) $greeting = 'Доброе утро';
elseif ($hour < 18) $greeting = 'Добрый день';
else $greeting = 'Добрый вечер';

// Получаем данные менеджера
$stmt = $conn->prepare("SELECT u.full_name, u.balance, t.name as tariff_name 
                        FROM user u 
                        LEFT JOIN tariff t ON u.tariff_id = t.id 
                        WHERE u.id = ?");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();

// Получаем количество активных заявок
$requests_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user WHERE last_submission_time IS NOT NULL AND documents_verified = 'Нет'");
$requests_stmt->execute();
$requests_count = $requests_stmt->get_result()->fetch_assoc()['count'];

// Получаем количество клиентов
$clients_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user WHERE position_id IN (3,4)");
$clients_stmt->execute();
$clients_count = $clients_stmt->get_result()->fetch_assoc()['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет менеджера</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            background-image: url('../images/background.jpg');
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            flex: 1;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 3rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .stats-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .action-button {
            background: #007bff;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
            display: block;
            width: 100%;
            margin-top: 15px;
            color: white;
            text-decoration: none;
            text-align: center;
        }
        .action-button:hover {
            background: #0056b3;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            color: white;
        }
        .manager-info {
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
    
    <div class="dashboard-container">
        <div class="manager-info">
            <h3><?= $greeting ?>, <?= htmlspecialchars($manager['full_name']) ?>!</h3>
            <p class="mb-1">Ваш баланс: <?= htmlspecialchars($manager['balance']) ?> руб.</p>
            <p class="mb-0">Ваш тариф: <?= htmlspecialchars($manager['tariff_name']) ?></p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $clients_count ?></div>
                    <div class="stats-title">Клиентов в системе</div>
                    <a href="clients.php" class="action-button">Управление клиентами</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $requests_count ?></div>
                    <div class="stats-title">Активных заявок</div>
                    <a href="client_requests.php" class="action-button">Просмотреть заявки</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">4</div>
                    <div class="stats-title">Действий доступно</div>
                    <a href="settings.php" class="action-button">Настройки</a>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <h4 class="stats-title">Управление тарифами</h4>
                    <p>Настройка и изменение корпоративных тарифов</p>
                    <a href="tariffs.php" class="action-button">Перейти к тарифам</a>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stats-card">
                    <h4 class="stats-title">Управление услугами</h4>
                    <p>Дополнительные пакеты для клиентов</p>
                    <a href="services.php" class="action-button">Перейти к услугам</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>