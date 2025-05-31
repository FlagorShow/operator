<?php
session_start(); // Добавляем старт сессии
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
    <title>О компании</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .main-content {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            color: white;
            display: flex;
            gap: 40px;
            align-items: center;
        }
        .text-section {
            flex: 2;
        }
        .image-section {
            flex: 1;
            text-align: center;
        }
        .image-section img {
            max-width: 300px;
            height: auto;
        }
        .header-section {
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Используем общий хедер -->
    <?php include '../templates/header.php'; ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="text-section">
            <div class="header-section">
                <h1>О компании</h1>
            </div>
            <p>
                AI -- провайдер телекоммуникационных, ИКТ- и контент-услуг в России.<br><br>
Коммерческую деятельность компания начала 16 апреля 1999 г., став первым мобильным оператором стандарта GSM в стране. С ноября 2007 г. входит в состав международной группы AI Group, являющейся европейским подразделением транснационального холдинга América Móvil, одного из крупнейших мировых провайдеров телекоммуникационных услуг с штаб-квартирой в Екатеринбурге. До августа 2019 г. компания вела операционную деятельность под брендом velcom. Абонентами мобильной связи AI в России являются более 4,8 миллионов человек, свыше 1,2 млн домохозяйств имеют возможность доступа к сети фиксированной связи по технологиям GPON и Ethernet в областных городах и крупнейших районных центрах. Кроме того, под брендом AI предоставляются услуги цифрового телевидения IPTV (VOKA), продукты для бизнеса, а также услуги хранения данных и облачные сервисы на базе собственного дата-центра, одного из крупнейших в стране. В компании работают более 2300 человек, а магазины AI расположены во всех крупных населенных пунктах страны.
            </p>
        </div>
        <div class="image-section">
            <img src="../images/logo.png" alt="Логотип AI">
        </div>
    </div>
    
    <!-- Подвал -->
    <?php include '../templates/footer.php'; ?>
</body>
</html>