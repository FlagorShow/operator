<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT * FROM packages");
$stmt->execute();
$result = $stmt->get_result();
$packages = $result->fetch_all(MYSQLI_ASSOC);

// Группируем услуги по категориям
$categories = [
    'data' => [],
    'minutes' => [],
    'sms' => []
];

foreach ($packages as $package) {
    $categories[$package['type']][] = $package;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дополнительные услуги</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/services.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('../images/background.jpg');
            background-size: cover;
        }
        .services-container {
            flex: 1;
        }
        .category-title {
            margin-top: 30px;
            margin-bottom: 20px;
            color: white;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .carousel-item {
            padding: 20px;
        }
        .package-block {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin: 0 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            background: rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container services-container">
        <h1 class="text-center my-4" style="color: white;">Дополнительные услуги</h1>
        
        <?php foreach ($categories as $type => $packages): ?>
            <h3 class="category-title">
                <?= $type == 'data' ? 'Интернет пакеты' : ($type == 'minutes' ? 'Пакеты минут' : 'SMS пакеты') ?>
            </h3>
            
            <?php if (empty($packages)): ?>
                <div class="alert alert-info">Нет доступных услуг в этой категории</div>
            <?php else: ?>
                <div id="<?= $type ?>Carousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <?php 
                        $chunked_packages = array_chunk($packages, 3);
                        foreach ($chunked_packages as $index => $chunk): 
                        ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="d-flex justify-content-around">
                                    <?php foreach ($chunk as $package): ?>
                                        <div class="package-block">
                                            <h3><?= htmlspecialchars($package['type'] == 'data' ? 'Интернет' : ($package['type'] == 'minutes' ? 'Минуты' : 'SMS')) ?> пакет</h3>
                                            <p class="package-amount">
                                                <?= htmlspecialchars($package['amount']) ?>
                                                <?= htmlspecialchars($package['type'] == 'data' ? 'ГБ' : ($package['type'] == 'minutes' ? 'минут' : 'SMS')) ?>
                                            </p>
                                            <p class="package-price"><?= htmlspecialchars($package['price']) ?> руб.</p>
                                            <p class="package-description"><?= htmlspecialchars($package['description']) ?></p>
                                            <a href="connect_service.php?id=<?= $package['id'] ?>" class="btn btn-primary">Подключить</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#<?= $type ?>Carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#<?= $type ?>Carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary px-5">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>