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

// Получаем данные пользователя
$user_sql = "SELECT u.*, t.monthly_cost FROM user u
             JOIN tariff t ON u.tariff_id = t.id
             WHERE u.id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Получаем карты пользователя
$cards_sql = "SELECT * FROM user_cards WHERE user_id = ?";
$stmt = $conn->prepare($cards_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Обработка пополнения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'], $_POST['card_id'])) {
    $amount = (float)$_POST['amount'];
    $card_id = (int)$_POST['card_id'];

    if ($amount < 100 || $amount > 15000) {
        $_SESSION['error'] = "Сумма должна быть от 100 до 15000 рублей";
    } else {
        // Проверяем баланс карты
        $card_sql = "SELECT balance FROM user_cards WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($card_sql);
        $stmt->bind_param("ii", $card_id, $user_id);
        $stmt->execute();
        $card = $stmt->get_result()->fetch_assoc();

        if ($card && $card['balance'] >= $amount) {
            // Обновляем баланс карты
            $update_card_sql = "UPDATE user_cards SET balance = balance - ? WHERE id = ?";
            $stmt = $conn->prepare($update_card_sql);
            $stmt->bind_param("di", $amount, $card_id);
            $stmt->execute();

            // Обновляем баланс пользователя
            $update_user_sql = "UPDATE user SET balance = balance + ? WHERE id = ?";
            $stmt = $conn->prepare($update_user_sql);
            $stmt->bind_param("di", $amount, $user_id);
            $stmt->execute();

            $_SESSION['success'] = "Оплата прошла успешно!";
            header("Location: replenish.php");
            exit();
        } else {
            $_SESSION['error'] = "Недостаточно средств на карте";
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
    <title>Пополнение баланса</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .replenish-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            justify-content: center;
        }
        .back-home {
            margin-right: 15px;
            padding: 8px 15px;
            background-color: #e53e3e;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-home:hover {
            background-color: #c53030;
            transform: translateY(-2px);
        }
        .phone-display {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }
        .amount-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 16px;
        }
        .amount-input::-webkit-outer-spin-button,
        .amount-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .amount-input {
            -moz-appearance: textfield;
        }
        .balance-info {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            color: white;
        }
        .balance-info p {
            margin: 5px 0;
            font-size: 18px;
        }
        .cards-container {
            display: flex;
            gap: px;
            margin: 20px 0;
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .card {
            min-width: 50%;
            width: 50%;
            height: 150px;
            background: transparent;
            border-radius: 10px;
            padding: 15px;
            position: relative;
            border: 2px solid #007bff;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .card.selected {
            background-color: white;
            color: black;
        }
        .card-bank {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 30px;
            color: white;
            text-align: left;
        }
        .card.selected .card-bank {
            color: black;
        }
        .card-number {
            position: absolute;
            bottom: 40px;
            left: 15px;
            font-size: 18px;
            color: white;
        }
        .card.selected .card-number {
            color: black;
        }
        .card-payment {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 50px;
        }
        .new-card {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px dashed #007bff;
            min-width: calc(50% - 7.5px);
            width: calc(50% - 7.5px);
        }
        .new-card .plus {
            font-size: 30px;
            color: #007bff;
        }
        .replenish-btn {
            display: block;
            width: 105%;
            padding: 12px;
            background: white;
            color: black;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .payment-systems {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        .payment-systems img {
            height: 30px;
        }
        h1 {
            color: white;
            text-align: center;
            margin: 0;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="replenish-container">
            <div class="header-section">
                <a href="index.php" class="back-home">Домой</a>
                <h1>Пополнение баланса</h1>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="phone-display">
                <?= htmlspecialchars($_SESSION['user']['phone_number']) ?>
            </div>

            <form method="POST">
                <input type="number" name="amount" class="amount-input"
                       placeholder="Сумма пополнения от 100 до 15000 ₽" min="100" max="15000" required>

                <div class="balance-info">
                    <p>Баланс: <?= htmlspecialchars($user['balance']) ?> ₽</p>
                    <p>Абонентская плата: <?= htmlspecialchars($user['monthly_cost']) ?> ₽</p>
                </div>

                <h3>С какой карты пополнить</h3>

                <div class="cards-container">
                    <?php foreach ($cards as $card): ?>
                        <div class="card" onclick="selectCard(this, <?= $card['id'] ?>)">
                            <div class="card-bank"><?= htmlspecialchars($card['bank']) ?></div>
                            <div class="card-number">**** <?= substr($card['card_number'], -4) ?></div>
                            <img src="../images/mir.svg" alt="МИР" class="card-payment">
                            <input type="radio" name="card_id" value="<?= $card['id'] ?>" required style="display: none;">
                        </div>
                    <?php endforeach; ?>
                    <div class="card new-card" onclick="window.location.href='add_card.php'">
                        <div class="plus">+</div>
                    </div>
                </div>

                <button type="submit" class="replenish-btn">Пополнить</button>
            </form>

            <div class="payment-systems">
                <img src="../images/mir.svg" alt="МИР">
                <img src="../images/visa.svg" alt="Visa">
                <img src="../images/msc.svg" alt="MasterCard">
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

    <script>
        function selectCard(cardElement, cardId) {
            // Снимаем выделение со всех карт
            document.querySelectorAll('.card').forEach(card => {
                card.classList.remove('selected');
            });

            // Выделяем выбранную карту
            cardElement.classList.add('selected');

            // Устанавливаем значение radio-кнопки
            const radioInput = cardElement.querySelector('input[type="radio"]');
            radioInput.checked = true;
        }
    </script>
</body>
</html>