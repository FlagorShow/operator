<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

// Определяем тип пользователя
$is_business = ($_SESSION['user']['position_id'] == 4); // 4 - Бизнес клиент

// Перенаправляем на соответствующие тарифы
if ($is_business) {
    header("Location: btariffs.php");
} else {
    header("Location: tariffs.php");
}
exit();
?>