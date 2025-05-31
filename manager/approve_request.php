<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['new_name'])) {
    header("Location: client_requests.php");
    exit();
}

$client_id = $_GET['id'];
$new_full_name = urldecode($_GET['new_name']);

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Обновляем ФИО клиента
$update_stmt = $conn->prepare("UPDATE user SET full_name = ?, documents_verified = 'Да' WHERE id = ?");
$update_stmt->bind_param("si", $new_full_name, $client_id);

if ($update_stmt->execute()) {
    $_SESSION['success'] = 'Заявка успешно подтверждена. ФИО клиента обновлено.';
} else {
    $_SESSION['error'] = 'Ошибка при подтверждении заявки';
}

header("Location: client_requests.php");
exit();
?>