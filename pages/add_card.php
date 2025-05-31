<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$user_id = $_SESSION['user']['id'];

$error = '';
$success = '';

function luhnCheck($number) {
    $number = str_replace(' ', '', $number);
    $sum = 0;
    $numDigits = strlen($number);
    $parity = $numDigits % 2;
   
    for ($i = 0; $i < $numDigits; $i++) {
        $digit = $number[$i];
        if ($i % 2 == $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }
    return ($sum % 10) == 0;
}

function getBankByCardNumber($number) {
    $prefix = substr($number, 0, 6);
    $banks = [
        '220220' => 'Сбербанк',
        '220015' => 'Альфа-Банк',
        '220070' => 'Тинькофф Банк',
        '220432' => 'Озон Банк',
        '220431' => 'Яндекс Банк',
        '220028' => 'МТС Банк',
        '220030' => 'Райфайзен Банк'
    ];
    return $banks[$prefix] ?? 'Неизвестный банк';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = str_replace(' ', '', $_POST['card_number'] ?? '');
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvc = $_POST['cvc'] ?? '';
   
    $valid = true;
   
    if (!luhnCheck($card_number)) {
        $error = "Неверный номер карты";
        $valid = false;
    }
   
    if (empty($expiry_date) || !preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
        $error = "Неверный срок действия карты";
        $valid = false;
    }
   
    if (empty($cvc) || !preg_match('/^\d{3}$/', $cvc)) {
        $error = "Неверный CVC код";
        $valid = false;
    }
   
    if ($valid) {
        $bank = getBankByCardNumber($card_number);
       
        $stmt = $conn->prepare("INSERT INTO user_cards (user_id, bank, payment_system, card_number, expiry_date, cvc) VALUES (?, ?, 'МИР', ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $bank, $card_number, $expiry_date, $cvc);
       
        if ($stmt->execute()) {
            $_SESSION['success'] = "Карта успешно добавлена";
            header("Location: replenish.php");
            exit();
        } else {
            $error = "Ошибка при добавлении карты: " . $conn->error;
        }
    }
}

include '../templates/header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление карты</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/add_card.css">
</head>
<body>
    <div class="container">
        <div class="add-card-container">
            <div class="header-section">
                <a href="replenish.php" class="back-button">Назад</a>
                <h1>Добавление карты</h1>
                <a href="index.php" class="home-button">Домой</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="card-form">
                <div class="form-group">
                    <label for="card_number">Реквизиты карты</label>
                    <input type="text" id="card_number" name="card_number" class="form-input"
                           placeholder="Номер карты" required>
                </div>
               
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="expiry_date" name="expiry_date" class="form-input"
                               placeholder="ММ/ГГ" required>
                    </div>
                   
                    <div class="form-group">
                        <input type="text" id="cvc" name="cvc" class="form-input"
                               placeholder="CVV2/CVC2" maxlength="3" required>
                    </div>
                </div>
               
                <button type="submit" id="submit-btn" class="add-card-btn" disabled>Прикрепить карту</button>
               
                <div class="secure-info">
                    Это абсолютно безопасно - данные вашей карты будут надежно храниться в зашифрованном виде
                </div>
            </form>
           
            <div class="payment-systems">
                <img src="../images/mir.svg" alt="МИР">
                <img src="../images/visa.svg" alt="Visa">
                <img src="../images/msc.svg" alt="MasterCard">
            </div>
        </div>
    </div>
   
    <?php include '../templates/footer.php'; ?>
    <script src="../scripts/add_card.js" defer></script>
</body>
</html>