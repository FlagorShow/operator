<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/config.php';
$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Проверка наличия tariff_id
if (!isset($_GET['tariff_id'])) {
    $_SESSION['error'] = "Тариф не выбран.";
    header("Location: tariffs.php");
    exit();
}

$tariff_id = (int)$_GET['tariff_id'];
$user_id = (int)$_SESSION['user']['id'];

// Получаем информацию о пользователе
$user_sql = "SELECT position_id, tariff_id FROM user WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

if (!$user_data) {
    $_SESSION['error'] = "Пользователь не найден.";
    header("Location: tariffs.php");
    exit();
}

// Определяем тип пользователя и тарифа
$is_private_client = ($user_data['position_id'] == 3); // 3 - Клиент (частный)
$is_business_tariff = ($tariff_id >= 11); // Бизнес тарифы имеют ID >= 11

// Проверка соответствия типа пользователя и тарифа
if ($is_private_client && $is_business_tariff) {
    $_SESSION['error'] = "Частным клиентам недоступны бизнес-тарифы";
    header("Location: tariffs.php");
    exit();
}

if (!$is_private_client && !$is_business_tariff) {
    $_SESSION['error'] = "Бизнес-клиентам недоступны частные тарифы";
    header("Location: btariffs.php");
    exit();
}

// Получаем стоимость нового тарифа
$tariff_sql = "SELECT monthly_cost, minutes, data_gb, sms FROM tariff WHERE id = ?";
$tariff_stmt = $conn->prepare($tariff_sql);
$tariff_stmt->bind_param("i", $tariff_id);
$tariff_stmt->execute();
$tariff_result = $tariff_stmt->get_result();

if ($tariff_result->num_rows === 0) {
    $_SESSION['error'] = "Тариф не существует.";
    header("Location: " . ($is_private_client ? "tariffs.php" : "btariffs.php"));
    exit();
}

$tariff_data = $tariff_result->fetch_assoc();
$tariff_cost = (int)$tariff_data['monthly_cost'];

// Получаем текущий баланс пользователя
$balance_sql = "SELECT balance FROM user WHERE id = ?";
$balance_stmt = $conn->prepare($balance_sql);
$balance_stmt->bind_param("i", $user_id);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$balance_data = $balance_result->fetch_assoc();
$current_balance = (int)$balance_data['balance'];

// Проверка баланса (только для платных тарифов)
if ($tariff_cost > 0 && $current_balance < $tariff_cost) {
    $_SESSION['error'] = "Недостаточно средств на балансе!";
    header("Location: " . ($is_private_client ? "tariffs.php" : "btariffs.php"));
    exit();
}

// Начинаем транзакцию
$conn->begin_transaction();

try {
    // Записываем в историю смены тарифов
    $history_sql = "INSERT INTO tariff_history (user_id, old_tariff_id, new_tariff_id) VALUES (?, ?, ?)";
    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("iii", $user_id, $user_data['tariff_id'], $tariff_id);
    $history_stmt->execute();
    $history_stmt->close();

    // Списание средств (для платных тарифов)
    if ($tariff_cost > 0) {
        $new_balance = $current_balance - $tariff_cost;
        $update_balance_sql = "UPDATE user SET balance = ? WHERE id = ?";
        $update_balance_stmt = $conn->prepare($update_balance_sql);
        $update_balance_stmt->bind_param("ii", $new_balance, $user_id);
        $update_balance_stmt->execute();
        $update_balance_stmt->close();
    }

    // Обновление тарифа пользователя
    $update_tariff_sql = "UPDATE user SET tariff_id = ? WHERE id = ?";
    $update_tariff_stmt = $conn->prepare($update_tariff_sql);
    $update_tariff_stmt->bind_param("ii", $tariff_id, $user_id);
    $update_tariff_stmt->execute();
    $update_tariff_stmt->close();

    // Обновление балансов (минуты, интернет, SMS) согласно новому тарифу
    $update_balances_sql = "UPDATE user SET 
                          minutes_balance = ?,
                          data_balance = ?,
                          sms_balance = ?
                          WHERE id = ?";
    $update_balances_stmt = $conn->prepare($update_balances_sql);
    $update_balances_stmt->bind_param("iiii", 
        $tariff_data['minutes'], 
        $tariff_data['data_gb'], 
        $tariff_data['sms'],
        $user_id
    );
    $update_balances_stmt->execute();
    $update_balances_stmt->close();

    // Обновляем данные в сессии
    $_SESSION['user']['tariff_id'] = $tariff_id;
    $_SESSION['user']['balance'] = $tariff_cost > 0 ? $new_balance : $current_balance;
    $_SESSION['user']['minutes_balance'] = $tariff_data['minutes'];
    $_SESSION['user']['data_balance'] = $tariff_data['data_gb'];
    $_SESSION['user']['sms_balance'] = $tariff_data['sms'];

    // Фиксируем транзакцию
    $conn->commit();

    $_SESSION['success'] = "Тариф успешно изменен! Балансы обновлены.";
} catch (Exception $e) {
    // Откатываем транзакцию в случае ошибки
    $conn->rollback();
    $_SESSION['error'] = "Ошибка при изменении тарифа: " . $e->getMessage();
}

// Закрытие соединений
$user_stmt->close();
$tariff_stmt->close();
$balance_stmt->close();
$conn->close();

header("Location: " . ($is_private_client ? "tariffs.php" : "btariffs.php"));
exit();
?>