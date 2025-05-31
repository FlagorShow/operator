<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$user_id = $_SESSION['user']['id'];
$error = '';
$success = '';

// Получаем текущий баланс
$stmt = $conn->prepare("SELECT balance FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Получаем карты менеджера
$cards_stmt = $conn->prepare("SELECT * FROM user_cards WHERE user_id = ?");
$cards_stmt->bind_param("i", $user_id);
$cards_stmt->execute();
$cards = $cards_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = intval($_POST['amount']);
    $card_id = intval($_POST['card_id']);
    
    if ($amount <= 0) {
        $error = 'Сумма должна быть больше 0';
    } else {
        // Проверяем наличие карты
        $card_exists = false;
        foreach ($cards as $card) {
            if ($card['id'] == $card_id) {
                $card_exists = true;
                break;
            }
        }
        
        if (!$card_exists) {
            $error = 'Выбранная карта не найдена';
        } else {
            // Обновляем баланс
            $new_balance = $user['balance'] + $amount;
            $update_stmt = $conn->prepare("UPDATE user SET balance = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_balance, $user_id);
            
            if ($update_stmt->execute()) {
                // Записываем транзакцию
                $transaction_stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, type) VALUES (?, ?, 'recharge')");
                $transaction_stmt->bind_param("ii", $user_id, $amount);
                $transaction_stmt->execute();
                
                $success = "Баланс успешно пополнен на $amount руб.";
                $user['balance'] = $new_balance;
            } else {
                $error = 'Ошибка при пополнении баланса';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пополнение баланса</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            background-image: url('../images/background.jpg');
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .recharge-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            flex: 1;
        }
        .balance-info {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="recharge-container">
        <h2 class="text-center mb-4">Пополнение баланса</h2>
        
        <div class="balance-info">
            Текущий баланс: <strong><?= htmlspecialchars($user['balance']) ?> руб.</strong>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Сумма пополнения (руб.)</label>
                <input type="number" class="form-control" name="amount" required min="1" value="100">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Выберите карту</label>
                <select class="form-select" name="card_id" required>
                    <?php foreach ($cards as $card): ?>
                        <option value="<?= $card['id'] ?>">
                            <?= htmlspecialchars($card['bank']) ?>: **** **** **** <?= substr($card['card_number'], -4) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Пополнить баланс</button>
            </div>
        </form>
        
        <div class="mt-4 text-center">
            <a href="index.php" class="btn btn-secondary">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>