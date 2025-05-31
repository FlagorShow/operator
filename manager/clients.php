<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Получаем частных клиентов
$private_stmt = $conn->prepare("SELECT u.id, u.full_name, u.phone_number, u.balance, t.name as tariff_name 
                        FROM user u 
                        LEFT JOIN tariff t ON u.tariff_id = t.id 
                        WHERE u.position_id = 3");
$private_stmt->execute();
$private_clients = $private_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Получаем бизнес-клиентов
$business_stmt = $conn->prepare("SELECT u.id, u.full_name, u.phone_number, u.balance, t.name as tariff_name 
                        FROM user u 
                        LEFT JOIN tariff t ON u.tariff_id = t.id 
                        WHERE u.position_id = 4");
$business_stmt->execute();
$business_clients = $business_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление клиентами</title>
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
        .clients-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            flex: 1;
        }
        .client-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            min-width: 300px;
        }
        .client-info {
            margin-bottom: 15px;
        }
        .carousel-inner {
            padding: 20px 0;
        }
        .category-title {
            margin-top: 30px;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="clients-container">
        <h2 class="mb-4">Управление клиентами</h2>
        
        <?php if (empty($private_clients) && empty($business_clients)): ?>
            <div class="alert alert-info">Нет клиентов в системе</div>
        <?php else: ?>
            <!-- Частные клиенты -->
            <h3 class="category-title">Частные клиенты</h3>
            <?php if (empty($private_clients)): ?>
                <div class="alert alert-info">Нет частных клиентов</div>
            <?php else: ?>
                <div id="privateCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <?php 
                        $chunked_clients = array_chunk($private_clients, 3);
                        foreach ($chunked_clients as $index => $chunk): 
                        ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="d-flex justify-content-around">
                                    <?php foreach ($chunk as $client): ?>
                                        <div class="client-card">
                                            <div class="client-info">
                                                <h5><?= htmlspecialchars($client['full_name']) ?></h5>
                                                <p>Телефон: <?= htmlspecialchars($client['phone_number']) ?></p>
                                                <p>Тариф: <?= htmlspecialchars($client['tariff_name']) ?></p>
                                                <p>Баланс: <?= htmlspecialchars($client['balance']) ?> руб.</p>
                                            </div>
                                            <a href="view_client.php?id=<?= $client['id'] ?>" class="btn btn-primary">Информация о клиенте</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#privateCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#privateCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Бизнес клиенты -->
            <h3 class="category-title">Бизнес клиенты</h3>
            <?php if (empty($business_clients)): ?>
                <div class="alert alert-info">Нет бизнес клиентов</div>
            <?php else: ?>
                <div id="businessCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <?php 
                        $chunked_clients = array_chunk($business_clients, 3);
                        foreach ($chunked_clients as $index => $chunk): 
                        ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="d-flex justify-content-around">
                                    <?php foreach ($chunk as $client): ?>
                                        <div class="client-card">
                                            <div class="client-info">
                                                <h5><?= htmlspecialchars($client['full_name']) ?></h5>
                                                <p>Телефон: <?= htmlspecialchars($client['phone_number']) ?></p>
                                                <p>Тариф: <?= htmlspecialchars($client['tariff_name']) ?></p>
                                                <p>Баланс: <?= htmlspecialchars($client['balance']) ?> руб.</p>
                                            </div>
                                            <a href="view_client.php?id=<?= $client['id'] ?>" class="btn btn-primary">Информация о клиенте</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#businessCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#businessCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>