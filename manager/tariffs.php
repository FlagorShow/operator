<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Получаем только корпоративные тарифы
$stmt = $conn->prepare("SELECT * FROM tariff WHERE id IN (555,666,777)");
$stmt->execute();
$result = $stmt->get_result();
$tariffs = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корпоративные тарифы</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/tariffs.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('../images/background.jpg');
            background-size: cover;
        }
        .tariffs-container {
            flex: 1;
        }
        .carousel-item {
            padding: 20px;
        }
        .tariff-block {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin: 0 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            background: rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container tariffs-container">
        <div class="tariff-title">Корпоративные тарифы</div>
        <div class="tariff-subtitle">Специальные предложения для бизнеса</div>
        
        <div id="tariffCarousel" class="carousel slide" data-bs-interval="false">
            <div class="carousel-inner">
                <?php 
                $chunked_tariffs = array_chunk($tariffs, 3);
                foreach ($chunked_tariffs as $index => $chunk): 
                ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row">
                            <?php foreach ($chunk as $tariff): ?>
                                <div class="col-md-4">
                                    <div class="tariff-block">
                                        <h2><?= htmlspecialchars($tariff['name']) ?></h2>
                                        <div class="price"><?= htmlspecialchars($tariff['monthly_cost']) ?> руб/мес</div>
                                        <div class="details">
                                            <?php if ($tariff['minutes']): ?>
                                                <div>Минуты: <?= htmlspecialchars($tariff['minutes']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($tariff['data_gb']): ?>
                                                <div>Интернет: <?= htmlspecialchars($tariff['data_gb']) ?> ГБ</div>
                                            <?php endif; ?>
                                            <?php if ($tariff['sms']): ?>
                                                <div>SMS: <?= htmlspecialchars($tariff['sms']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="services">
                                            <?= htmlspecialchars($tariff['additional_services']) ?>
                                        </div>
                                        <a href="connect_tariff.php?id=<?= $tariff['id'] ?>" class="btn btn-primary mt-3">Подключить</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#tariffCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Предыдущий</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#tariffCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Следующий</span>
            </button>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary px-5">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>