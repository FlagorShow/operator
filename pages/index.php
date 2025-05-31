<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные пользователя и тарифа
$user_id = $_SESSION['user']['id'];
$user_data = [];

$sql = "SELECT u.*, t.name as tariff_name, t.monthly_cost, 
        th.change_date as last_change_date 
        FROM user u
        JOIN tariff t ON u.tariff_id = t.id
        LEFT JOIN (
            SELECT user_id, MAX(change_date) as max_date
            FROM tariff_history
            WHERE user_id = ?
            GROUP BY user_id
        ) as latest ON u.id = latest.user_id
        LEFT JOIN tariff_history th ON th.user_id = latest.user_id AND th.change_date = latest.max_date
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

$stmt->bind_param("ii", $user_id, $user_id);
if (!$stmt->execute()) {
    die("Ошибка выполнения запроса: " . $stmt->error);
}

$result = $stmt->get_result();

// Если данные не найдены - перенаправление
if ($result->num_rows === 0) {
    echo '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="2; url=../login.php">
        <title>Ошибка</title>
        <style>
            body {
                background: #f0f0f0;
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            .error-message {
                padding: 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        </style>
    </head>
    <body>
        <div class="error-message">
            <h2>Ошибка авторизации</h2>
            <p>Данные пользователя не найдены</p>
            <p>Перенаправление на страницу входа через 2 секунды...</p>
        </div>
    </body>
    </html>';
    exit();
}

$user_data = $result->fetch_assoc();

// Валидация и установка значений по умолчанию
$last_change_date = $user_data['last_change_date'] ?? date('Y-m-d H:i:s');
$next_charge_date = (new DateTime($last_change_date))->modify('+1 month');

// Обновляем данные в сессии
$_SESSION['user'] = array_merge($_SESSION['user'], [
    'full_name' => $user_data['full_name'] ?? 'Не указано',
    'phone_number' => $user_data['phone_number'] ?? 'Не указан',
    'balance' => $user_data['balance'] ?? 0,
    'tariff_name' => $user_data['tariff_name'] ?? 'Не выбран',
    'monthly_cost' => $user_data['monthly_cost'] ?? 0,
    'next_charge_date' => $next_charge_date->format('d.m.Y'),
    'data_balance' => $user_data['data_balance'] ?? 0,
    'sms_balance' => $user_data['sms_balance'] ?? 0,
    'minutes_balance' => $user_data['minutes_balance'] ?? 0,
    'documents_verified' => $user_data['documents_verified'] ?? 'Нет'
]);

include '../templates/header.php';

// Определение приветствия
$hour = date('H');
$greeting = match(true) {
    $hour >= 5 && $hour < 12 => "Доброе утро",
    $hour >= 12 && $hour < 18 => "Добрый день",
    $hour >= 18 && $hour < 23 => "Добрый вечер",
    default => "Доброй ночи"
};

$documents_verified = $_SESSION['user']['documents_verified'] === 'Да';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .btn-settings {
            background-color: white;
            color: black;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
            margin: 5px;
        }
        .btn-settings:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-disabled {
            background-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            pointer-events: none;
        }
        .tariff-info, .next-charge {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            text-align: center;
            color: black;
            font-size: 18px;
            font-weight: bold;
        }
        .resources {
            display: flex;
            justify-content: space-around;
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .resources p {
            margin: 0;
            color: black;
            font-size: 16px;
            font-weight: bold;
        }
        .user-info {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .info-block {
            background: white;
            padding: 15px;
            border-radius: 8px;
            flex: 1;
            margin: 0 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .greeting {
            color: white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .phone-number {
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
        }
        .phone-number:hover {
            color: #007bff;
        }
        .copy-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }
        .replenish-section {
            margin: 20px 0;
            text-align: center;
        }
        .replenish-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .replenish-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .verification-alert {
            background-color:rgb(187, 159, 0);
            color:rgb(0, 0, 0);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgb(151, 0, 0);
        }
    </style>
</head>
<body>
    <div class="copy-notification" id="copyNotification">Номер телефона скопирован в буфер обмена</div>

    <div class="container">
        <?php
        $nameParts = explode(' ', $_SESSION['user']['full_name']);
        $displayName = $nameParts[1] ?? $_SESSION['user']['full_name'];
        ?>
        <h2 class="greeting"><?= htmlspecialchars($greeting) ?>, <?= htmlspecialchars($displayName) ?>!</h2>
        
        <?php if (!$documents_verified): ?>
            <div class="verification-alert">
                <strong>Доступ к функциям личного кабинета ограничен. Отправьте документы на проверку</strong><br>
                Для доступа ко всем функциям личного кабинета необходимо подтвердить вашу личность.
            </div>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="settings.php" class="btn-settings">Настройки</a>
            <a href="replenish.php" class="btn-settings <?= !$documents_verified ? 'btn-disabled' : '' ?>">Пополнить баланс</a>
            <a href="select_tariffs.php" class="btn-settings <?= !$documents_verified ? 'btn-disabled' : '' ?>">Тарифы</a>
            <a href="services.php" class="btn-settings <?= !$documents_verified ? 'btn-disabled' : '' ?>">Услуги</a>
        </div>
        
        <div class="user-info">
            <div class="info-block">
                <p class="phone-number" onclick="copyPhoneNumber('<?= htmlspecialchars($_SESSION['user']['phone_number']) ?>')">
                    <?= htmlspecialchars($_SESSION['user']['phone_number']) ?>
                </p>
            </div>
            <div class="info-block">
                <p class="full-name"><?= htmlspecialchars($_SESSION['user']['full_name']) ?></p>
            </div>
            <div class="info-block">
                <p class="balance">Баланс: <?= htmlspecialchars($_SESSION['user']['balance']) ?> ₽</p>
            </div>
        </div>
        
        <div class="resources">
            <p><?= htmlspecialchars($_SESSION['user']['data_balance']) ?> ГБ</p>
            <p><?= htmlspecialchars($_SESSION['user']['sms_balance']) ?> СМС</p>
            <p><?= htmlspecialchars($_SESSION['user']['minutes_balance']) ?> минут</p>
        </div>

        <div class="tariff-info">
            Ваш тариф: <?= htmlspecialchars($_SESSION['user']['tariff_name']) ?> (<?= htmlspecialchars($_SESSION['user']['monthly_cost']) ?> ₽/мес)
        </div>

        <div class="next-charge">
            Следующее списание: <?= htmlspecialchars($_SESSION['user']['next_charge_date']) ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyPhoneNumber(phoneNumber) {
            const textarea = document.createElement('textarea');
            textarea.value = phoneNumber;
            textarea.style.position = 'fixed';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                const notification = document.getElementById('copyNotification');
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 2000);
            } catch (err) {
                console.error('Ошибка при копировании: ', err);
            } finally {
                document.body.removeChild(textarea);
            }
        }
    </script>
</body>
</html>

<?php
include '../templates/footer.php';
$conn->close();
?>
