<?php
// session_start() должен быть в вызывающем файле (например, company.php)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <title>A1 - оператор мобильной связи</title>
    <link rel="stylesheet" href="../styles/hf.css">
</head>
<body>
    <header>
        <a href="../pages/index.php" class="logo"></a>
        <nav>
            <!-- Для гостей и авторизованных -->
            <?php if (!isset($_SESSION['user']) || (isset($_SESSION['user']['position_id']) && $_SESSION['user']['position_id'] != 4)): ?>
                <a href="../pages/tariffs.php" class="header_button_1">
                    Частным клиентам
                </a>
            <?php endif; ?>

            <?php if (!isset($_SESSION['user']) || (isset($_SESSION['user']['position_id']) && $_SESSION['user']['position_id'] != 3)): ?>
                <a href="../pages/btariffs.php" class="header_button_1">
                    Бизнес клиентам
                </a>
            <?php endif; ?>

            <a href="../pages/company.php" class="header_button_1">
                О компании
            </a>

            <?php if(isset($_SESSION['user'])): ?>
                <a href="../pages/index.php" class="header_button_1">
                    Личный кабинет
                </a>
                <a href="../login.php" class="header_button_2">
                    Выйти
                </a>
            <?php else: ?>
                <a href="../login.php" class="header_button_2">
                    Войти
                </a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>