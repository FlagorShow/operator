<?php
// Убедитесь, что сессия еще не стартовала
if (session_status() === PHP_SESSION_NONE) {
    // Настройка параметров сессии ДО ее запуска
    ini_set('session.gc_maxlifetime', 1800); // 30 минут
    session_set_cookie_params(1800);

    // Старт сессии
    session_start();
}

// Проверка времени бездействия
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > 1800) {
        session_unset();
        session_destroy();
        header('Location: ../login.php?timeout=1');
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();

// Подключение к БД
$host = 'localhost';
$dbname = 'operator';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>