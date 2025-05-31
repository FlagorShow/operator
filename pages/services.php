<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дополнительные услуги</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <style>
        .services-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .services-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .package-block {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-width: 250px;
            text-align: center;
        }
        .greeting {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-service {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        .btn-service:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
        }
        .back-button {
            display: inline-block;
            background-color: #e53e3e;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            margin: 30px auto;
            transition: all 0.3s ease;
        }
        .back-button:hover {
            background-color: #c53030;
            transform: translateY(-2px);
        }
        .carousel-control-prev, 
        .carousel-control-next {
            position: relative;
            display: inline-block;
            margin: 20px 10px;
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 8px;
            cursor: pointer;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .services-grid {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="services-container">
        <h2 class="greeting">Дополнительные услуги</h2>
        
        <div class="services-grid">
            <div class="package-block">
                <h2>СМС</h2>
                <p class="details">Пакеты SMS для общения</p>
                <a href="sms_packages.php" class="btn-service">Выбрать</a>
            </div>
            
            <div class="package-block">
                <h2>Минуты</h2>
                <p class="details">Дополнительные минуты для звонков</p>
                <a href="minutes_packages.php" class="btn-service">Выбрать</a>
            </div>
            
            <div class="package-block">
                <h2>Интернет</h2>
                <p class="details">Дополнительные ГБ интернета</p>
                <a href="data_packages.php" class="btn-service">Выбрать</a>
            </div>
        </div>

        <a href="index.php" class="back-button">Назад</a>
    </div>
    
    <?php include '../templates/footer.php'; ?>
</body>
</html>