<?php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link rel="stylesheet" href="../styles/hf.css">
</head>
<body>
<footer>
    <div class="footer_container">
        <div class="footer_column">
            <img src="../images/logo.png" alt="A1 Logo" class="footer_logo">
            <p class="footer_text">2005—2025 &copy;</p>
        </div>
        <div class="footer_column">
            <?php 
            // Показывать "Частным клиентам" если:
            // - Пользователь не авторизован ИЛИ
            // - Пользователь авторизован и не является бизнес-клиентом (position_id != 4)
            if (!isset($_SESSION['user']) || 
                (isset($_SESSION['user']['position_id']) && $_SESSION['user']['position_id'] != 4)) { 
            ?>
                <a href="../pages/tariffs.php" class="footer_link">Частным клиентам</a>
            <?php } ?>
            
            <?php 
            // Показывать "Бизнес клиентам" если:
            // - Пользователь не авторизован ИЛИ
            // - Пользователь авторизован и не является частным клиентом (position_id != 3)
            if (!isset($_SESSION['user']) || 
                (isset($_SESSION['user']['position_id']) && $_SESSION['user']['position_id'] != 3)) { 
            ?>
                <a href="../pages/btariffs.php" class="footer_link">Бизнес клиентам</a>
            <?php } ?>
            
            <a href="../pages/company.php" class="footer_link">О компании</a>
            <a href="../pages/index.php" class="footer_link">Личный кабинет</a>
        </div>
        <div class="footer_column">
            <a href="tel:88008008080" class="footer_phone">8 (800) 800-80-80</a>
            <a href="mailto:maximov140705@list.ru" class="footer_phone">maximov140705@list.ru</a>
            <p class="footer_text">Круглосуточно</p>
            <p class="footer_address"><strong>Адрес</strong></p>
            <p class="footer_text">г. Екатеринбург, ул. Репина, д. 94, офис 1</p>
        </div>
    </div>
</footer>
</body>
</html>