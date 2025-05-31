<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['client_id']) || !isset($_POST['new_tariff_id'])) {
    header("Location: client_requests.php");
    exit();
}

$client_id = $_POST['client_id'];
$new_tariff_id = $_POST['new_tariff_id'];

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Получаем текущий тариф клиента
$current_stmt = $conn->prepare("SELECT tariff_id FROM user WHERE id = ?");
$current_stmt->bind_param("i", $client_id);
$current_stmt->execute();
$current_result = $current_stmt->get_result();
$current_tariff = $current_result->fetch_assoc();

if ($current_tariff) {
    // Обновляем тариф
    $update_stmt = $conn->prepare("UPDATE user SET tariff_id = ? WHERE id = ?");
    $update_stmt->bind_param("ii", $new_tariff_id, $client_id);
    
    if ($update_stmt->execute()) {
        // Записываем в историю
        $history_stmt = $conn->prepare("INSERT INTO tariff_history (user_id, old_tariff_id, new_tariff_id) VALUES (?, ?, ?)");
        $history_stmt->bind_param("iii", $client_id, $current_tariff['tariff_id'], $new_tariff_id);
        $history_stmt->execute();
        
        $_SESSION['success'] = 'Тариф клиента успешно изменен';
    } else {
        $_SESSION['error'] = 'Ошибка при изменении тарифа';
    }
}

header("Location: view_client.php?id=" . $client_id);
exit();
?>