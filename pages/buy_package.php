<?php
session_start();
require_once '../config/config.php';

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

$last_purchase = $_SESSION['last_package_purchase'] ?? 0;
if (time() - $last_purchase < 60) {
    $_SESSION['error'] = "Подключать услуги можно не чаще чем раз в минуту";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$package_id = (int)$_POST['package_id'];

// Получаем информацию о пакете
$package = $conn->query("SELECT * FROM packages WHERE id = $package_id")->fetch_assoc();
if (!$package) {
    $_SESSION['error'] = "Пакет не найден";
    header("Location: services.php");
    exit();
}

// Получаем баланс пользователя
$user = $conn->query("SELECT balance, data_balance, sms_balance, minutes_balance FROM user WHERE id = $user_id")->fetch_assoc();

if ($user['balance'] < $package['price']) {
    $_SESSION['error'] = "Недостаточно средств на балансе";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Начинаем транзакцию
$conn->begin_transaction();

try {
    // Списание средств
    $new_balance = $user['balance'] - $package['price'];
    $conn->query("UPDATE user SET balance = $new_balance WHERE id = $user_id");

    // Добавление пакета
    $expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));
    $conn->query("INSERT INTO user_packages (user_id, package_id, expiry_date) VALUES ($user_id, $package_id, '$expiry_date')");

    // Обновление баланса ресурсов
    switch ($package['type']) {
        case 'data':
            $conn->query("UPDATE user SET data_balance = data_balance + {$package['amount']} WHERE id = $user_id");
            break;
        case 'sms':
            $conn->query("UPDATE user SET sms_balance = sms_balance + {$package['amount']} WHERE id = $user_id");
            break;
        case 'minutes':
            $conn->query("UPDATE user SET minutes_balance = minutes_balance + {$package['amount']} WHERE id = $user_id");
            break;
    }

    // Обновляем данные в сессии
    $_SESSION['user']['balance'] = $new_balance;
    $_SESSION['last_package_purchase'] = time();
    $_SESSION['success'] = "Услуга успешно подключена!";

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Ошибка при подключении услуги";
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>